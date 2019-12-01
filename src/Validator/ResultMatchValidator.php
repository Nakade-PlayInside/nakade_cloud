<?php

namespace App\Validator;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Form\Model\ResultModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResultMatchValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ResultMatch */
        if (!assert($object instanceof ResultModel)) {
            return;
        }

        if (null === $object || '' === $object) {
            return;
        }

        $unsetPairing = [];
        $winByDefault = [];
        $drawByDefault = [];

        /** @var BundesligaMatch $match */
        foreach ($object->getAllMatches() as $match) {
            if ($this->hasResult($match) && !$match->getOpponent() && !$match->getPlayer()) {
                $unsetPairing[] = $match->getBoard();
            }
            if ($this->hasResult($match) && $this->hasUnsetPlayers($match) && !$match->isWinByDefault()) {
                $winByDefault[] = $match->getBoard();
            }
            if ('1:1' === $match->getResult() && $match->isWinByDefault()) {
                $drawByDefault[] = $match->getBoard();
            }
        }

        if (empty($unsetPairing) && empty($winByDefault) && empty($drawByDefault)) {
            return;
        }

        if (count($unsetPairing) > 0) {
            $this->context->buildViolation($constraint->messageUnsetPairing)
                    ->setParameter('{{ boards }}', implode(', ', $unsetPairing))
                    ->addViolation();
        }

        if (count($winByDefault) > 0) {
            $this->context->buildViolation($constraint->messageWinByDefault)
                    ->setParameter('{{ boards }}', implode(', ', $winByDefault))
                    ->addViolation();
        }

        if (count($drawByDefault) > 0) {
            $this->context->buildViolation($constraint->messageDrawByDefault)
                    ->setParameter('{{ boards }}', implode(', ', $drawByDefault))
                    ->addViolation();
        }
    }

    private function hasResult(BundesligaMatch $match): bool
    {
        return $match->getResult() && '0:0' !== $match->getResult();
    }

    private function hasUnsetPlayers(BundesligaMatch $match): bool
    {
        return !$match->getPlayer() || !$match->getOpponent();
    }
}
