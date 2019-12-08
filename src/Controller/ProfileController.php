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

namespace App\Controller;

use App\Entity\NewsReader;
use App\Entity\User;
use App\Form\Model\UserPasswordFormModel;
use App\Form\Model\UserResetFormModel;
use App\Form\ProfileType;
use App\Form\UserEmailType;
use App\Form\UserPasswordType;
use App\Form\UserResetType;
use App\Message\ConfirmRegistration;
use App\Message\ResetPassword;
use App\Tools\TokenGenerator;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileController!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     *
     * @IsGranted("ROLE_USER")
     */
    public function profile(): Response
    {
        return $this->render('security/profile.html.twig', [
        ]);
    }

    /**
     * Edit Profile.
     *
     * @Route("/profile/edit", name="app_profile_edit")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request): Response
    {
        $form = $this->createForm(ProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Dein Profil wurde aktualisiert!');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/edit_profile.html.twig', [
                'profileForm' => $form->createView(),
        ]);
    }

    /**
     * Change Email!
     *
     * @Route("/profile/email", name="app_profile_email")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function email(Request $request, MessageBusInterface $messageBus): Response
    {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $email = $model->email;

            /** @var User $user */
            $user = $this->getUser();

            //newsletter update
            if ($user->hasNewsletter()) {
                $newsReader = $this->getDoctrine()->getRepository(NewsReader::class)->findOneBy([
                        'email' => $user->getEmail(),
                ]);
                if ($newsReader) {
                    $newsReader->setEmail($email)
                        ->setConfirmed(false);
                }
            }

            //user update
            $user->setEmail($email)
                ->setConfirmed(false)
                ->setConfirmToken(TokenGenerator::generateToken());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            //confirmation mail
            $message = new ConfirmRegistration($user);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'Deine Email wurde aktualisiert! Bitte schau in dein Postfach.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/edit_email.html.twig', [
                'emailForm' => $form->createView(),
        ]);
    }

    /**
     * Change Password!
     *
     * @Route("/profile/changePwd", name="app_profile_changePwd")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function change(Request $request, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $profileLogger): Response
    {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();

            /** @var User $user */
            $user = $this->getUser();

            //user update
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $model->plainPassword
            ));
            $profileLogger->notice('Password of user <{user}> changed.', ['user' => $user]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Deine Passwort wurde aktualisiert!');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/edit_pwd.html.twig', [
                'passwordForm' => $form->createView(),
        ]);
    }

    /**
     * Resend confirm mail.
     *
     * @Route("/profile/resent/confirm", name="app_profile_resent_confirm")
     *
     * @IsGranted("ROLE_USER")
     */
    public function resentConfirm(MessageBusInterface $messageBus): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isConfirmed()) {
            $user->setConfirmToken(TokenGenerator::generateToken());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            //mail handling
            $message = new ConfirmRegistration($user);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'Eine Bestätigung für dein Konto wurde dir zugeschickt!');
        }

        return $this->render('security/profile.html.twig');
    }

    /**
     * This is used by an ajax call inside profile.
     *
     * @Route("/profile/toggle/news", name="app_profile_toogle_news")
     *
     * @IsGranted("ROLE_USER")
     */
    public function toggleNews(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setNewsletter(!$user->hasNewsletter());

        $entityManager = $this->getDoctrine()->getManager();
        $reader = $entityManager->getRepository(NewsReader::class)->findOneBy(['email' => $user->getEmail()]);

        //new reader
        if ($user->hasNewsletter()) {
            $subscriber = new NewsReader();
            $subscriber->setEmail($user->getEmail())
                ->setUnsubscribeToken(TokenGenerator::generateToken('unsubscribe'))
                ->setSubscribeToken(TokenGenerator::generateToken('subscribe'))
                ->setConfirmed(true)
                ->setFirstName($user->getFirstName())
                ->setLastName($user->getLastName())
            ;

            $entityManager->persist($subscriber);
        } elseif ($reader) {
            $entityManager->remove($reader);
        }

        $entityManager->flush();

        //return a response that is successful, but has no content. The 204 status code literally means "No Content".
        return new Response(null, 204);
    }

    /**
     * @Route("/profile/remove", name="app_profile_remove")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function remove(Request $request, LoggerInterface $profileLogger): Response
    {
        if ('app_profile_remove' === $request->attributes->get('_route') && $request->isMethod('POST')) {
            /** @var User $user */
            $user = $this->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $email = $user->getEmail();
            $profileLogger->alert('User <{user}> removed.', ['user' => $user]);

            $user->setFirstName('not known')
                ->setLastName('not known')
                ->setNickName('not known')
                ->setRemoved(true)
                ->setConfirmToken(uniqid())
                ->setEmail(uniqid().'@nakade.de');

            if ($user->hasNewsletter()) {
                $reader = $entityManager->getRepository(NewsReader::class)->findOneBy(['email' => $email]);
                if ($reader) {
                    $entityManager->remove($reader);
                    $profileLogger->alert('Reader <{reader}> removed.', ['reader' => $reader]);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('security/remove_profile.html.twig');
    }

    /**
     * @Route("/profile/forgotten", name="app_profile_forgotten")
     */
    public function forgotten(Request $request, MessageBusInterface $messageBus)
    {
        $form = $this->createForm(UserResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            if (!assert($model instanceof UserResetFormModel)) {
                throw new UnexpectedTypeException($model, UserResetFormModel::class);
            }
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $model->email]);
            $user->setResetToken(TokenGenerator::generateToken())
                    ->setResetAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            //mail handling
            $message = new ResetPassword($user);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'Eine Email wurde an dich gesandt!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgotten_pwd.html.twig', [
            'emailForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/reset/{token}", name="app_profile_reset")
     */
    public function reset(string $token, Request $request, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $profileLogger): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        if (!$user) {
            throw new NotFoundHttpException('Data not found!');
        }

        if (!$user->getResetAt() || $user->getResetAt()->format('+3 day') > new \DateTime()) {
            $profileLogger->critical('Password reset of {user} failed. Token expired!', ['user' => $user]);
            throw new NotFoundHttpException('Token expired!');
        }

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            if (!assert($model instanceof UserPasswordFormModel)) {
                throw new UnexpectedTypeException($model, UserPasswordFormModel::class);
            }

            //user update
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $model->plainPassword
            ))
                ->setResetToken(null)
                ->setResetAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $profileLogger->notice('Password reset of {user}.', ['user' => $user]);

            $this->addFlash('success', 'Dein Passwort wurde aktualisiert!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_pwd.html.twig', [
                'passwordForm' => $form->createView(),
        ]);
    }
}
