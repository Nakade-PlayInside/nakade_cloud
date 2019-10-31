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

use App\Entity\Bundesliga\BundesligaResults;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SeasonDateValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\SeasonDate */
        if (!assert($object instanceof BundesligaResults)) {
            return;
        }
        if (!method_exists($object, 'getSeason') || !method_exists($object, 'getPlayedAt')) {
            return;
        }
        if (!$object->getPlayedAt()) {
            return;
        }

        if (!$object->getSeason()->getStartAt() || !$object->getSeason()->getEndAt()) {
            return;
        }
        $seasonStart = $object->getSeason()->getStartAt();
        $seasonEnd   = $object->getSeason()->getEndAt();
        $playDate    = $object->getPlayedAt();

        if ($playDate > $seasonStart && $seasonEnd > $playDate) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                ->setParameter('{{ playDate }}', $playDate->format('d.m.Y'))
                ->setParameter('{{ season }}', $seasonStart->format('d.m.Y') . ' - ' . $seasonEnd->format('d.m.Y'))
                ->addViolation();

    }
}
