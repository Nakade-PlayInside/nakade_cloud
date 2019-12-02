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

use App\Entity\Bundesliga\BundesligaExecutive;
use App\Form\Model\ResultModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class CaptainResultInputType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstBoardMatch', CaptainMatchInputType::class)
                ->add('secondBoardMatch', CaptainMatchInputType::class)
                ->add('thirdBoardMatch', CaptainMatchInputType::class)
                ->add('fourthBoardMatch', CaptainMatchInputType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var ResultModel $model */
            $model = $event->getData();
            $form = $event->getForm();
            if (!$model->getExecutive()) {
                $form->add('executive', EntityType::class, [
                    'class' => BundesligaExecutive::class,
                    'placeholder' => 'bundesliga.executive.choose',
                    'help' => 'bundesliga.executive.help',
                ]);
            }

            if (!$model->getNakadeCaptainName() || !$model->getNakadeCaptainEmail()) {
                $form->add('nakadeCaptainName', TextType::class, [
                    'attr' => ['placeholder' => 'bundesliga.team.manager.placeholder'],
                ]);
                $form->add('nakadeCaptainEmail', EmailType::class, [
                        'attr' => ['placeholder' => 'bundesliga.team.manager.email.placeholder'],
                ]);
            }

            if (!$model->getOppCaptainName() || !$model->getOppCaptainEmail()) {
                $form->add('oppCaptainName', TextType::class, [
                        'attr' => ['placeholder' => 'bundesliga.team.manager.placeholder'],
                ]);
                $form->add('oppCaptainEmail', EmailType::class, [
                        'attr' => ['placeholder' => 'bundesliga.team.manager.email.placeholder'],
                ]);
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResultModel::class,
            'allow_extra_fields' => true,
        ]);
    }
}
