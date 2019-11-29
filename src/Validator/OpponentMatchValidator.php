<?php
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
namespace App\Validator;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaOpponent;
use App\Form\Model\ResultModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OpponentMatchValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        if (!assert($object instanceof ResultModel)) {
            return;
        }

        if (null === $object || '' === $object) {
            return;
        }

        //get all assigned opp
        $opponents = [];
        /** @var BundesligaMatch $match */
        foreach ($object->getAllMatches() as $match) {
            if (!$match->getOpponent()) {
                continue;
            }
            $opponents[] = $match->getOpponent();
        }

        //all opp are unique
        if (count($opponents) === count(array_unique($opponents))) {
            return;
        }

        //finds the duplicate
        /** @var BundesligaOpponent $duplicate */
        $duplicates = array_values(array_unique(array_diff_key($opponents, array_unique($opponents))));
        $names = [];
        while ($duplicates) {
            $opp = array_shift($duplicates);
            $names[] = $opp->getName();
        }
        $name = implode(", ", $names);

        /* @var $constraint \App\Validator\OpponentMatch */
        $this->context->buildViolation($constraint->message)
        ->setParameter('{{ value }}', $name)
        ->addViolation();
    }
}
