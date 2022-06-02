<?php declare(strict_types=1);

namespace App\Event\Listener;

use App\Entity\Dialog;
use App\Entity\DialogRecord;
use App\Entity\User;
use App\Enum\Dialog\Type;
use App\Enum\DialogRecord\Sender;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DialogRecordListener
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function postPersist(DialogRecord $dialogRecord)
    {
        if ($dialog = $dialogRecord->getDialog()) {
            match ($dialog->getType()) {
                Type::CALL() => $this->processCall($dialogRecord, $dialog),
                Type::CHAT() => $this->processChat($dialogRecord, $dialog),
            };
        }
    }

    private function processCall(DialogRecord $dialogRecord, Dialog $dialog) {

    }

    private function processChat(DialogRecord $dialogRecord, Dialog $dialog) {
        $dialogRecords = $dialog->getRecords();
        if (!$dialogRecords->contains($dialogRecord)) {
            $dialogRecords->add($dialogRecord);
        }
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->neq("sender", Sender::SYSTEM()));
        /**
         * @var DialogRecord $first
         * @var DialogRecord $last
         */
        $first = $dialogRecords->matching($criteria->orderBy(["receivedAt" => Criteria::DESC]))->first();
        $last = $dialogRecords->matching($criteria->orderBy(["receivedAt" => Criteria::DESC]))->last();
        if ($first && $last) {
            $conversationLength = $first->getReceivedAt()->getTimestamp() - $last->getReceivedAt()->getTimestamp();
            $dialog->setDuration($conversationLength);
        }

        $customer = $dialogRecords->matching(Criteria::create()
            ->andWhere(Criteria::expr()->eq("sender", Sender::CUSTOMER()))
            ->orderBy(["receivedAt" => Criteria::ASC]))->first();
        $operator = $dialogRecords->matching(Criteria::create()
            ->andWhere(Criteria::expr()->eq("sender", Sender::OPERATOR()))
            ->orderBy(["receivedAt" => Criteria::ASC]))->first();
        $firstAnswerSpeed = 0;
        if ($operator && $customer) {
            $firstAnswerSpeed = $operator->getReceivedAt()->getTimestamp() - $customer->getReceivedAt()->getTimestamp();
        }

        $dialog->setFirstAnswerSpeed($firstAnswerSpeed > 0 ? $firstAnswerSpeed : 0);
        $dialog->setServiceLevelWarning($firstAnswerSpeed > 120);

        $totalAnswers = 0;
        $seconds = 0;
        $prevMessage = null;

        $dialogRecords = $dialogRecords->matching($criteria->orderBy(["receivedAt" => Criteria::ASC]));
        foreach ($dialogRecords as $record) {
            if ($prevMessage?->getSender() === Sender::CUSTOMER() && $record->getSender() === Sender::OPERATOR()) {
                $totalAnswers++;
                $seconds += $record->getReceivedAt()->getTimestamp() - $prevMessage->getReceivedAt()->getTimestamp();
            }
            $prevMessage = $record;
        }
        $totalAnswers = $totalAnswers === 0 ? 1 : $totalAnswers;

        $dialog->setAverageSpeedAnswer(intval($seconds / $totalAnswers));

        if (empty($dialog->getUsers())) {
            $user = $this->entityManager->getRepository(User::class)->findOneByChatId($dialogRecord->getSenderId());
            if ($user) {
                $dialog->addUser($user);
            }
        }

        try {
            $meta = $this->entityManager->getClassMetadata(get_class($dialog));
            $this->entityManager->getUnitOfWork()->computeChangeSet($meta, $dialog);
            $changes = $this->entityManager->getUnitOfWork()->getEntityChangeSet($dialog);

            if (empty($changes)) {
                $this->entityManager->flush();
            }
        } catch (Exception $e) {
            // ignore
        }
    }
}
