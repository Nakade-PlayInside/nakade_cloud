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

use App\Entity\Bundesliga\BundesligaLineup;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniquePositionValidator extends ConstraintValidator
{
    private $player   = '';
    private $position = 1;

    public function validate($object, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UniquePosition */
        if (!assert($object instanceof BundesligaLineup)) {
            return;
        }

        if ($this->isExisting($object)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ player }}', $this->player)
            ->setParameter('{{ position }}', $this->position)
            ->addViolation();
    }

    private function isExisting(BundesligaLineup $lineup)
    {
        $allPlayers = [];
        $method = 'getPosition';
        $count = 1;

        while (method_exists($lineup, $method.$count)) {
            $myMethod = $method.$count;
            $player = $lineup->$myMethod();

            if (!$player) {
                ++$count;
                continue;
            }

            if (in_array($player, $allPlayers)) {
                $this->position = $count;
                $this->player = $player;

                return false;
            }
            $allPlayers[] = $player;
            ++$count;
        }

        return true;
    }
}
