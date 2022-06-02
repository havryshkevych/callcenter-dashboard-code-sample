<?php declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Dialog;
use App\Entity\DialogRecord;
use App\Entity\User;
use App\Enum\Dialog\Type;
use App\Enum\DialogRecord\Sender;
use App\Repository\DialogRecordRepository;
use App\Repository\UserRepository;
use App\Service\Storage\StorageInterface;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CallInputDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private $amazonS3Bucket,
        private IriConverterInterface $iriConverter,
        private StorageInterface $storage,
    )
    {
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Dialog
    {
        /** @var User $user */
        $user = $this->iriConverter->getItemFromIri($data['user']);

        $dialog = new Dialog();
        $dialog
            ->addUser($user)
            ->setDate(new DateTime($data['receivedAt']))
            ->setServiceLevelAverageAnswerSpeedWarning(false)
            ->setServiceLevelWarning(false)
            ->setAverageSpeedAnswer(0)
            ->setDuration(intval($data['duration']))
            ->setType(Type::CALL());


        if (!is_array($data['records'])) {
            $data['records'] = [$data['records']];
        }
        $dialogRecords = [];
        foreach ($data['records'] as $i => $record) {
            if ($record instanceof UploadedFile) {
                $fileName = 'callcenter/audio/' . uniqid('audio_') . '.' . $record->getClientOriginalExtension();
                $this->storage->put($fileName, $record);
                $dialogRecords[$i] = new DialogRecord();
                $dialogRecords[$i]
                    ->setChatId(uniqid('admin_generated_',true))
                    ->setClientId($data['clientId'] ?? null)
                    ->setSenderId($user->getCallId())
                    ->setSender(Sender::OPERATOR())
                    ->setDialog($dialog)
                    ->setReceivedAt(new DateTime($data['receivedAt']))
                    ->setSession('https://' . $this->amazonS3Bucket . '/' . $fileName);
                $dialog->addRecord($dialogRecords[$i]);
            }
        }

        return $dialog;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $format === "multipart" && $type === Dialog::class;
    }
}
