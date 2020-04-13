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
use App\Form\Model\TeamResultsModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamResultValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint) {
        /* @var $constraint \App\Validator\TeamResult */
        if (!assert($object instanceof TeamResultsModel)) {
            return;
        }

        if (null === $object || '' === $object) {
            return;
        }

        /** @var BundesligaResults $result */
        foreach ($object->getResults() as $result) {
            if (!$result->getBoardPointsHome() || !$result->getBoardPointsAway()) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
            if (0 === $result->getBoardPointsHome() + $result->getBoardPointsAway()) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
        }
    }
}
