<?php declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\KnowledgeScoring;
use App\Entity\User;
use App\Service\Storage\StorageInterface;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class KnowledgeScoringInputDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private $amazonS3Bucket,
        private IriConverterInterface $iriConverter,
        private StorageInterface $storage
    )
    {
    }

    /**
     * @throws Exception
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): KnowledgeScoring
    {
        $fileUrl = null;
        if ($data['screenshot'] instanceof UploadedFile) {
            $fileName = 'callcenter/knowledge/' . uniqid('knowledge_scoring_') . '.' . $data['screenshot']->getClientOriginalExtension();
            $this->storage->put($fileName, $data['screenshot']);
            $fileUrl = 'https://' . $this->amazonS3Bucket . '/' . $fileName;
        }
        /** @var User $user */
        $user = $this->iriConverter->getItemFromIri($data['user']);
        $knowledge = new KnowledgeScoring();
        $knowledge
            ->setResult(floatval($data['result']))
            ->setName($data['name'])
            ->setDate(new DateTime($data['date']))
            ->setUser($user)
            ->setCoefficient(floatval($data['coefficient']))
            ->setScreenshot($fileUrl);
        return $knowledge;
    }
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $format === "multipart" && $type === KnowledgeScoring::class;
    }
}
