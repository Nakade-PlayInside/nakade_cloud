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

namespace App\DataFixtures;

use App\Entity\Bundesliga\BundesligaPlayer;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaPlayerFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $count = 0;
        foreach ($this->getData() as $names) {
            $player = new BundesligaPlayer();
            $player->setFirstName($names[0]);
            $player->setLastName($names[1]);
            $player->setDgobMember($this->faker->boolean);
            if ($this->faker->boolean()) {
                $player->setBirthDay($this->faker->dateTimeBetween('-70 years', '-15 years'));
            }
            $player->setPhone($this->getPhoneNumber());
            $player->setEmails($this->getEmails());

            $manager->persist($player);

            // store for usage later as groupName_#COUNT#
            $this->addReference(sprintf('bl_player_%d', $count), $player);
            ++$count;
        }

        $manager->flush();
    }

    private function getPhoneNumber(): array
    {
        $phone = [];
        if ($this->faker->boolean(90)) {
            while (true) {
                $phone[] = $this->faker->phoneNumber;
                if ($this->faker->boolean()) {
                    break;
                }
            }
        }

        return $phone;
    }

    private function getEmails(): array
    {
        $emails = [];
        if ($this->faker->boolean(90)) {
            while (true) {
                $emails[] = $this->faker->email;
                if ($this->faker->boolean()) {
                    break;
                }
            }
        }

        return $emails;
    }

    private function getData(): array
    {
        $data[] = ['Seolki', 'Hong'];
        $data[] = ['Matthias', 'Knöpke'];
        $data[] = ['Bernhard', 'Runge'];
        $data[] = ['Fabian', 'Ulbricht'];
        $data[] = ['Holger', 'Maerz'];
        $data[] = ['Maurice', 'Wohabi'];
        $data[] = ['Robert', 'Deutschmann'];
        $data[] = ['Sascha', 'Kompass'];
        $data[] = ['Martina', 'Maerz'];
        $data[] = ['Pandau', 'Ting'];
        $data[] = ['Marco', 'Dellermann'];
        $data[] = ['Thomas', 'Göttsche'];

        return $data;
    }
}
