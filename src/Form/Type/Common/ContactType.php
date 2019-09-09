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
namespace App\Form\Type\Common;

use App\Entity\Common\ContactMail;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactType!
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                    'firstName',
                    TextType::class,
                    ['label' => 'Vorname',
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'lastName',
                    TextType::class,
                    ['label' => 'Nachname',
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'address',
                    TextType::class,
                    ['label' => 'Anschrift',
                     'required' => false,
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'zipCode',
                    TextType::class,
                    ['label' => 'PLZ',
                     'required' => false,
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'city',
                    TextType::class,
                    ['label' => 'Stadt',
                     'required' => false,
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'phone',
                    TelType::class,
                    ['label' => 'Telefon',
                     'required' => false,
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'email',
                    EmailType::class,
                    ['label' => 'eMail',
                     'help' => 'An diese Adresse wird eine Bestätigung geschickt',
                     'empty_data' => '',
                    ]
                )
                ->add(
                    'message',
                    TextareaType::class,
                    ['label' => 'Nachricht',
                     'empty_data' => '',
                    ]
                )

                ->add(
                    'captcha',
                    RecaptchaType::class
                )

                ->add(
                    'save',
                    SubmitType::class,
                    ['label' => 'Abschicken',
                    ]
                )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => ContactMail::class,
            // enable/disable CSRF protection for this form
                'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
                'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
                'csrf_token_id' => 'contact_item',
        ]);
    }
}
