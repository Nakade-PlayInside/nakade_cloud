<?php

declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2019 Dr. Holger Maerz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Form;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Repository\Bundesliga\BundesligaSeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaResultsType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var BundesligaResults|null $results */
        $results = $options['data'] ?? null;
        $isEdit = $results && $results->getId();

        $builder->add(
            'season',
            EntityType::class,
            [
                        'class' => BundesligaSeason::class,
                        'query_builder' => function (BundesligaSeasonRepository $repository) {
                            return $repository->createQueryBuilder('s')->orderBy('s.title', 'DESC');
                        },
                ]
        )
                ->add('matchDay')
                ->add('playedAt', DateType::class, ['widget' => 'single_text', 'required' => false])
        ;

        //preset listener for adding dynamic fields
        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    /** @var BundesligaResults|null $data */
                    $data = $event->getData();
                    if (!$data) {
                        return;
                    }

                    $this->setupTeamsAndResultFields($event->getForm(), $data->getSeason());
                }
        );

        //if season was changed
        $builder->get('season')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->setupTeamsAndResultFields($form->getParent(), $form->getData());
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BundesligaResults::class,
        ]);
    }

    private function setupTeamsAndResultFields(FormInterface $form, BundesligaSeason $season = null)
    {
        if (null === $season) {
            $form->remove('home');
            $form->remove('away');
            $form->remove('boardPointsHome');
            $form->remove('boardPointsHome');

            return;
        }

        $choices = $this->getTeams($season);

        if (null === $choices) {
            $form->remove('home');
            $form->remove('away');
            $form->remove('boardPointsHome');
            $form->remove('boardPointsHome');

            return;
        }

        $form->add('home', EntityType::class, [
                    'placeholder' => 'Choose a Team',
                    'class' => BundesligaTeam::class,
                    'choices' => $choices,
        ])
            ->add('away', EntityType::class, [
                    'placeholder' => 'Choose a Team',
                    'class' => BundesligaTeam::class,
                    'choices' => $choices,
            ])
            ->add('boardPointsHome', IntegerType::class, ['required' => false])
            ->add('boardPointsAway', IntegerType::class, ['required' => false])
        ;
    }

    private function getTeams(BundesligaSeason $season = null)
    {
        if (!$season) {
            return $this->entityManager->getRepository(BundesligaTeam::class)->findAll();
        }

        return $this->entityManager->getRepository(BundesligaTeam::class)->findTeamsBySeason($season->getId());
    }
}
