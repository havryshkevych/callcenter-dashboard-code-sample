<?php

namespace App\DataFixtures;

use App\Entity\Dialog;
use App\Entity\DialogRecord;
use App\Entity\Evaluation;
use App\Entity\EvaluationCriteria;
use App\Entity\Scoring;
use App\Entity\User;
use App\Entity\Zone;
use App\Enum\Dialog\Type;
use App\Enum\DialogRecord\Sender;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use JetBrains\PhpStorm\ArrayShape;

class AppFixtures extends Fixture
{
    protected Generator $faker;
    protected ObjectManager $manager;
    protected array $criterias;
    protected array $zones;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->criterias = [
            [
                "title" => 'Приветствие',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Установление контакта и выявление сути обращения',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Решение вопроса',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Завершение диалога',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Проявление неуважительного отношения к клиенту',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Нарушение качества обработки обращения Клиента',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Проявление неуважительного отношения к клиенту /компании',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Проявление нелояльности к компании',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Не предложен аналог',
                "description" => '',
                "active" => true,
            ],
            [
                "title" => 'Выявленная Критическая ошибка обнуляет диалог и влияет на Рейтинг',
                "description" => '',
                "active" => true,
            ],
        ];
        $this->zones = [
            [
                'name' => 'Изумрудная',
                'color' => '#34B308',
                'description' => '',
                'hint' => '<h6>По итогам рейтингования, ты занял место в <br><span style="background-color: #34B308;">😀 Изумрудной зоне 🌞</span></h6><p><b>Ты показываешь стабильно хорошие показатели</b><br><b>🙏 Спасибо за твою работу 😻</b></p><p>Уверен, что ты хочешь и можешь показать еще. пучше результат Для этого нам нужно еще немного твоих усилий и все получится @</p><p>📝<b>для тебя есть несколько заданий:</b><ol><li>Изучить ТОП ошибок по Всезнайке за прошлый месяц.</li><li>Не пропустить ми одного опроса по Чайной пожке в этом месяце</li></ol></p>',
                'rangeStart' => 0.0,
                'rangeEnd' => 10.0,
                'priority' => 100
            ],
            [
                'name' => 'Зеленая',
                'color' => '#61DA37',
                'description' => '',
                'hint' => '<h6>По итогам рейтингования, ты занял место в <br><span style="background-color: #61DA37;">😀 Зеленой зоне 🌞</span></h6><p><b>Ты показываешь стабильно хорошие показатели</b><br><b>🙏 Спасибо за твою работу 😻</b></p><p>Уверен, что ты хочешь и можешь показать еще. пучше результат Для этого нам нужно еще немного твоих усилий и все получится @</p><p>📝<b>для тебя есть несколько заданий:</b><ol><li>Изучить ТОП ошибок по Всезнайке за прошлый месяц.</li><li>Не пропустить ми одного опроса по Чайной пожке в этом месяце</li></ol></p>',
                'rangeStart' => 10.0,
                'rangeEnd' => 20.0,
                'priority' => 90
            ],
            [
                'name' => 'Желтая',
                'color' => '#FFC300',
                'description' => '',
                'hint' => '<h6>По итогам рейтингования, ты занял место в <br><span style="background-color: #FFC300;">😀 желтой зоне 🌞</span></h6><p><b>Ты показываешь стабильно хорошие показатели</b><br><b>🙏 Спасибо за твою работу 😻</b></p><p>Уверен, что ты хочешь и можешь показать еще. пучше результат Для этого нам нужно еще немного твоих усилий и все получится @</p><p>📝<b>для тебя есть несколько заданий:</b><ol><li>Изучить ТОП ошибок по Всезнайке за прошлый месяц.</li><li>Не пропустить ми одного опроса по Чайной пожке в этом месяце</li></ol></p>',
                'rangeStart' => 20.0,
                'rangeEnd' => 80.0,
                'priority' => 0
            ],
            [
                'name' => 'Оранжевая',
                'color' => '#FF5733',
                'description' => '',
                'hint' => '<h6>По итогам рейтингования, ты занял место в <br><span style="background-color: #FF5733;">😀 Оранжевой зоне 🌞</span></h6><p><b>Ты показываешь стабильно хорошие показатели</b><br><b>🙏 Спасибо за твою работу 😻</b></p><p>Уверен, что ты хочешь и можешь показать еще. пучше результат Для этого нам нужно еще немного твоих усилий и все получится @</p><p>📝<b>для тебя есть несколько заданий:</b><ol><li>Изучить ТОП ошибок по Всезнайке за прошлый месяц.</li><li>Не пропустить ми одного опроса по Чайной пожке в этом месяце</li></ol></p>',
                'rangeStart' => 80.0,
                'rangeEnd' => 90.0,
                'priority' => 70
            ],
            [
                'name' => 'Красная',
                'color' => '#C70039',
                'description' => '',
                'hint' => '<h6>По итогам рейтингования, ты занял место в <br><span style="background-color: #C70039;">😀 Красной зоне 🌞</span></h6><p><b>Ты показываешь стабильно хорошие показатели</b><br><b>🙏 Спасибо за твою работу 😻</b></p><p>Уверен, что ты хочешь и можешь показать еще. пучше результат Для этого нам нужно еще немного твоих усилий и все получится @</p><p>📝<b>для тебя есть несколько заданий:</b><ol><li>Изучить ТОП ошибок по Всезнайке за прошлый месяц.</li><li>Не пропустить ми одного опроса по Чайной пожке в этом месяце</li></ol></p>',
                'rangeStart' => 90.0,
                'rangeEnd' => 100.0,
                'priority' => 80
            ],
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createCriterias(Type::CHAT());
        $this->createCriterias(Type::CALL());
        $this->createZones();

        $this->createMany(User::class, 20, function (User $user) {
            $user->setEmail($this->faker->email())
                ->setCallId($this->faker->regexify('[A-Z]{1}[0-9]{5}'))
                ->setChatId($this->faker->regexify('[A-Z]{1}[0-9]{5}'))
                ->setName($this->faker->name())
                ->setDescription($this->faker->jobTitle());

            $this->createMany(Dialog::class, 20, function (Dialog $dialog) use ($user) {
                $type = $this->faker->randomElement([Type::CHAT(), Type::CALL()]);
                $dialog->setUser($user)
                    ->setType($type)
                    ->setDate($this->faker->dateTimeThisMonth());

                $chatId = $this->faker->regexify('[A-Z]{2}[0-9]{5}');
                $count = $type === Type::CHAT() ? 20 : $this->faker->randomElement([1, 2, 3]);
                $this->createMany(
                    DialogRecord::class,
                    $count,
                    function (DialogRecord $record, $dialogRecordId) use ($dialog, $chatId) {
                        $record->setDialog($dialog)
                            ->setChatId($chatId)
                            ->setSeconds($this->faker->numberBetween(0, 200))
                            ->setSender($this->faker->randomElement([Sender::CUSTOMER(), Sender::OPERATOR()]))
                            ->setReceivedAt(
                                $this->faker->dateTimeBetween($dialog->getDate(), $dialog->getDate()->modify('+1 hour'))
                            )
                            ->setSenderId(
                                $dialog->getUser()->{$dialog->getType() === Type::CHAT() ? 'getChatId' : 'getCallId'}()
                            );
                        if ($dialogRecordId === 1) {
                            $record = $record->setSender(Sender::CUSTOMER())
                                ->setReceivedAt($dialog->getDate());
                        }
                        if ($dialogRecordId === 20) {
                            $record->setSender(Sender::SYSTEM())
                                ->setReceivedAt(
                                    $this->faker->dateTimeBetween(
                                        $dialog->getDate()->modify('+1 hour'),
                                        $dialog->getDate()->modify('+2 hour')
                                    )
                                );
                        }
                    }
                );
                if ($this->faker->randomFloat(2, 0, 1) <= 0.7) {
                    $scoring = new Scoring();
                    $scoring->setDialog($dialog);
                    $this->createMany(
                        Evaluation::class,
                        count($this->criterias),
                        function (Evaluation $evaluation, $criteriaId) use ($scoring, $dialog) {
                            /**
                             * @var EvaluationCriteria $criteria
                             */
                            $criteria = $this->getReference(
                                EvaluationCriteria::class.'_'.$criteriaId.'_'.$dialog->getType()
                            );
                            $evaluation->setScoring($scoring)
                                ->setCriteria($criteria)
                                ->setValue($this->faker->randomFloat(0, min: 0, max: 1))
                                ->setComment($this->faker->sentence());
                        }
                    );
                    $this->manager->persist($scoring);
                }
            });
        });

        $manager->flush();
    }

    protected function createCriterias(Type $type): void
    {
        foreach ($this->criterias as $i => $criteria) {
            $entity = new EvaluationCriteria();
            $entity->setTitle($criteria['title'])
                ->setDescription($criteria['description'])
                ->setType($type)
                ->setSort($i + 1)
                ->setActive(true);
            $this->manager->persist($entity);
            $this->addReference(EvaluationCriteria::class.'_'.$i.'_'.$type, $entity);
        }
    }

    protected function createZones(): void
    {
        foreach ($this->zones as $zone) {
            $entity = new Zone();
            $entity->setName($zone['name'])
                ->setColor($zone['color'])
                ->setDescription($zone['description'])
                ->setHint($zone['hint'])
                ->setRangeStart($zone['rangeStart'])
                ->setRangeEnd($zone['rangeEnd'])
                ->setPriority($zone['priority'])
                ->setActive(true);
            $this->manager->persist($entity);
        }
    }

    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
        }
    }
}
