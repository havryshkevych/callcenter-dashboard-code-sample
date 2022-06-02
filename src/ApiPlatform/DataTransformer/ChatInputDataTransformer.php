<?php declare(strict_types=1);

namespace App\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Dialog;
use App\Entity\DialogRecord;
use App\Enum\Dialog\Type;
use App\Enum\DialogRecord\Sender;
use App\Object\Chat\ClientInput;
use App\Object\Chat\OperatorInput;
use App\Object\Chat\SystemInput;
use App\Repository\DialogRecordRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\Criteria;

final class ChatInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private DialogRecordRepository $dialogRecordRepository,
        private ValidatorInterface     $validator,
        private UserRepository         $userRepository
    )
    {
    }

    /**
     * @param ClientInput|OperatorInput|SystemInput $object
     */
    public function transform($object, string $to, array $context = []): DialogRecord
    {
        $this->validator->validate($object);

        return match ($object::class) {
            ClientInput::class => $this->transformClient($object, $to, $context),
            OperatorInput::class => $this->transformOperator($object, $to, $context),
            SystemInput::class => $this->transformSystem($object, $to, $context),
        };
    }

    public function transformClient(ClientInput $object, string $to, array $context = []): DialogRecord
    {
        $dialogRecord = new DialogRecord();
        $dialogRecord->setSender(Sender::CUSTOMER());
        $dialogRecord->setClientId($object->getClientId());
        $dialogRecord->setReceivedAt(DateTime::createFromFormat('U.u', (string)microtime(true)));
        return $dialogRecord;
    }

    public function transformOperator(OperatorInput $object, string $to, array $context = []): DialogRecord
    {
        $dialogRecord = new DialogRecord();
        $dialogRecord->setSenderId($object->getFrom());
        $dialogRecord->setSender($object->getFrom() === 'sender' ? Sender::SYSTEM() : Sender::OPERATOR());
        $dialogRecord->setClientId($object->getClientId());
        $dialogRecord->setChatId($object->getChatId());
        $dialogRecord->setSession($object->getSessionId());
        if ($object->getCreated() === 0) {
            $object->setCreated(intval(microtime(true)*1000));
        }
        $dialogRecord->setReceivedAt(DateTime::createFromFormat('U.u', number_format($object->getCreated() / 1000, 4, thousands_separator: '')));

        $prevMessage = $this->dialogRecordRepository->findOneByChatId($dialogRecord->getChatId(), order: 'DESC');
        if ($prevMessage?->getDialog() instanceof Dialog) {
            $dialogRecord->setDialog($prevMessage?->getDialog());
        }

        if ($dialogRecord->getDialog()) {
            $this->processOlderRecords($dialogRecord, $dialogRecord->getDialog());
        } else {
            $this->createDialog($dialogRecord);
        }

        if ($dialog = $dialogRecord->getDialog()) {
            if ($operator = $this->userRepository->findOneByChatId($dialogRecord->getSenderId())) {
                $dialog->addUser($operator);
            }
        }

        if ($dialogRecord->getDialog()->getRecords()->last() instanceof DialogRecord
            && $dialogRecord->getDialog()->getRecords()->last()->getSender() === Sender::CUSTOMER()
            && ($dialogRecord->getReceivedAt()->getTimestamp() - $dialogRecord->getDialog()->getRecords()->last()->getReceivedAt()->getTimestamp()) > 600) {
            $dialogRecord->getDialog()->setServiceLevelAverageAnswerSpeedWarning(true);
        }

        return $dialogRecord;
    }

    private function processOlderRecords(DialogRecord $dialogRecord, ?Dialog $dialog = null)
    {
        foreach ($this->dialogRecordRepository->findByChatId($dialogRecord->getChatId(), $dialogRecord->getClientId()) as $record) {
            if ($record->getSender() === Sender::CUSTOMER()) {
                $record->setChatId($dialogRecord->getChatId());
            }
            $dialog?->addRecord($record);
        }
    }

    private function createDialog(DialogRecord $dialogRecord)
    {
        $dialog = new Dialog();
        $dialog->setType(Type::CHAT());
        $this->processOlderRecords($dialogRecord, $dialog);
        $dialog->setDate($dialogRecord->getReceivedAt());
        $dialog->setServiceLevelWarning(false);
        $dialog->setServiceLevelAverageAnswerSpeedWarning(false);

        $user = $this->userRepository->findOneByChatId($dialogRecord->getSenderId());
        if ($user) {
            $dialog->addUser($user);
        }

        $first = $dialog->getRecords()->matching(Criteria::create()
            ->andWhere(Criteria::expr()->neq("sender", Sender::SYSTEM()))->orderBy(["receivedAt" => Criteria::DESC]))->first();
        $last = $dialog->getRecords()->matching(Criteria::create()
            ->andWhere(Criteria::expr()->neq("sender", Sender::SYSTEM()))->orderBy(["receivedAt" => Criteria::DESC]))->last();
        if ($first && $last) {
            $conversationLength = $first->getReceivedAt()->getTimestamp() - $last->getReceivedAt()->getTimestamp();
            $dialog->setDuration($conversationLength);
        }

        $dialogRecord->setDialog($dialog);
    }

    public function transformSystem(SystemInput $object, string $to, array $context = []): DialogRecord
    {
        $lastMessage = $this->dialogRecordRepository->findOneByChatId($object->getChatId());

        $dialogRecord = new DialogRecord();
        $dialogRecord->setChatId($object->getChatId());
        $dialogRecord->setClientId($object->getClientId());
        $dialogRecord->setDialog($lastMessage?->getDialog());
        if ($object->getCreated() === 0) {
            $object->setCreated(intval(microtime(true)*1000));
        }
        $created = DateTime::createFromFormat('U.u', number_format($object->getCreated() / 1000, 4, thousands_separator: ''));
        $dialogRecord->setReceivedAt($created);

        $dialogRecord->setSession($object->getSessionId());
        $dialogRecord->setSender(Sender::SYSTEM());
        $dialogRecord->setSenderId(Sender::SYSTEM()->getValue());

        return $dialogRecord;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return DialogRecord::class === $to
            && (($context['input']['class'] ?? '') === ClientInput::class
            || ($context['input']['class'] ?? '') === OperatorInput::class
            || ($context['input']['class'] ?? '') === SystemInput::class);
    }
}
