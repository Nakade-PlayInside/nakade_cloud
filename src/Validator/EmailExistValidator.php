<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailExistValidator extends ConstraintValidator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\EmailExist */

        if (null === $value || '' === $value) {
            return;
        }

        $existingUser = $this->userRepository->findOneBy([
            'email' => $value,
            'confirmed' => true,
            'disabled' => false,
            'locked' => false,
        ]);

        //exit if user email found
        if ($existingUser) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
    }
}
