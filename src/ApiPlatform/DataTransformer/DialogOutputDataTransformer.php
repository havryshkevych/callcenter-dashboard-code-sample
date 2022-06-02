<?php declare(strict_types=1);

namespace App\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Dialog;
use App\Entity\Evaluation;
use App\Entity\EvaluationCriteria;
use App\Entity\Scoring;
use App\Entity\User;
use App\Object\Dialog\DialogOutput;
use App\Object\Scoring\CriteriaOutput;
use App\Object\Scoring\EvaluationOutput;
use App\Object\Scoring\ScoringOutput;
use App\Object\User\UserOutput;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\SerializerInterface;

class DialogOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array $context
     * @return DialogOutput
     */
    public function transform($object, string $to, array $context = []): DialogOutput
    {
        /**
         * @var Dialog $object
         */

        $output = new DialogOutput();
        $output->setId($object->getId())
            ->setCreatedAt($object->getCreatedAt())
            ->setUpdatedAt($object->getUpdatedAt())
            ->setDate($object->getDate())
            ->setType($object->getType())
            ->setRecords($object->getRecords())
            ->setUsers($object->getUsers()->map(function(User $user){return $user->getId();})->getValues())
            ->setRecordsUrl($object->getRecordsUrl())
            ->setDuration($object->getDuration())
            ->setFirstAnswerSpeed($object->getFirstAnswerSpeed())
            ->setAverageSpeedAnswer($object->getAverageSpeedAnswer())
            ->setServiceLevelWarning($object->isServiceLevelWarning())
            ->setServiceLevelAverageAnswerSpeedWarning($object->isServiceLevelAverageAnswerSpeedWarning())
            ->setScoring($this->transformScoring($object))
        ;
        return $output;
    }

    private function transformScoring(Dialog $data): Collection
    {
        $scoringOutputCollection = new ArrayCollection();
        if (empty($data->getScoring())) {
            return $scoringOutputCollection;
        }

        foreach ($data->getScoring() as $scoring) {
            $scoringOutput = new ScoringOutput();
            $evaluations = [];
            foreach ($scoring->getEvaluations() as $evaluation) {
                $evaluations[] = (new EvaluationOutput())->setId($evaluation->getId())
                    ->setValue($evaluation->getValue())
                    ->setComment($evaluation->getComment())
                    ->setCriteria($this->transformCriteria($evaluation->getCriteria()));
            }
            $scoringOutput->setEvaluations($evaluations)
                ->setUserId($scoring->getUser()?->getId())
                ->setScore($scoring->getScore());
            $scoringOutputCollection->add($scoringOutput);
        }

        return $scoringOutputCollection;
    }


    private function transformCriteria(EvaluationCriteria $criteria): CriteriaOutput
    {
        return (new CriteriaOutput())->setTitle($criteria->getTitle())
            ->setDescription($criteria->getDescription())
            ->setCritical($criteria->isCritical())
            ->setSort($criteria->getSort());
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return DialogOutput::class === $to && $data instanceof Dialog;
    }
}