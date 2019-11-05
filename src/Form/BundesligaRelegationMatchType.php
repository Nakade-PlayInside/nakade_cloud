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

use App\Entity\Bundesliga\BundesligaLineup;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaRelegationMatch;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Repository\Bundesliga\BundesligaSeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaRelegationMatchType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var BundesligaRelegationMatch|null $match */
        $match = $options['data'] ?? null;
        $isEdit = $match && $match->getId();

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
                ->add(
                    'board',
                    ChoiceType::class,
                    ['choices' => $this->getBoardChoices()]
                )
                ->add(
                    'color',
                    ChoiceType::class,
                    ['choices' => ['Schwarz' => 'b', 'Weiß' => 'w']]
                )
        ;

        //preset listener for adding dynamic fields
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var BundesligaRelegationMatch|null $data */
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
            'data_class' => BundesligaRelegationMatch::class,
        ]);
    }

    private function getBoardChoices()
    {
        $choices = [];
        for ($i = 1; $i <= 5; ++$i) {
            $board = sprintf('Brett %d', $i);
            $choices[$board] = $i;
        }

        return $choices;
    }

    private function getRelegationChoices($season)
    {
        return $this->entityManager->getRepository(BundesligaRelegation::class)->findBySeason($season);
    }

    private function getPlayerChoices($season)
    {
        $lineup = $this->entityManager->getRepository(BundesligaLineup::class)->findOneBy(['season' => $season]);

        return $lineup->getPlayers();
    }

    private function setupTeamsAndResultFields(FormInterface $form, BundesligaSeason $season = null)
    {
        if (null === $season) {
            $form->remove('results');
            $form->remove('player');
            $form->remove('opponent');
            $form->remove('result');
            $form->remove('winByDefault');

            return;
        }

        $resultChoices = $this->getRelegationChoices($season);
        $playerChoices = $this->getPlayerChoices($season);

        if (null === $resultChoices) {
            $form->remove('results');
            $form->remove('player');
            $form->remove('opponent');
            $form->remove('result');
            $form->remove('winByDefault');

            return;
        }

        $form->add(
            'results',
            EntityType::class,
            [
                    'class' => BundesligaResults::class,
                    'choices' => $resultChoices,
            ]
        )

            ->add(
                'player',
                EntityType::class,
                [
                            'class' => BundesligaPlayer::class,
                            'choices' => $playerChoices,
                    ]
            )

            ->add('opponent', BundesligaOpponentSelectType::class)

            ->add(
                'result',
                ChoiceType::class,
                ['choices' => ['Sieg' => '2:0', 'Unentschieden' => '1:1', 'Niederlage' => '0:2']]
            )

            ->add('winByDefault')
            ;
    }
}
