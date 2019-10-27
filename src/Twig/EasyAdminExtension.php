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

namespace App\Twig;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\TrackingInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\TwigFunction;

class EasyAdminExtension extends \Twig_Extension
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
                new TwigFunction('bundesliga_get_match_result', [$this, 'getMatchResult']),
        ];
    }

    public function getFilters()
    {
        return [
                new \Twig_SimpleFilter(
                    'filter_admin_actions',
                    [$this, 'filterActions']
                ),
        ];
    }

    public function filterActions(array $itemActions, $item)
    {
        if ($item instanceof TrackingInterface) {
            if ($this->isRemovalDenied($item->getStatus()) || $item->hasCommnent()) {
                unset($itemActions['delete']);
            } elseif ($this->isNotAllowed($item->getAuthor())) {
                unset($itemActions['delete']);
                unset($itemActions['edit']);
            }
        }

        return $itemActions;
    }

    public function getMatchResult(BundesligaMatch $item)
    {
        if (!$item->getResults() || !$item->getResults()->getPointsHome() || !$item->getResults()->getPointsAway()) {
            return '0:0';
        }

        $opponentPts = $item->getResults()->getPointsAway();
        $nakadePts = $item->getResults()->getPointsHome();
        if ($item->getResults()->getHome() === $item->getId()) {
            $opponentPts = $item->getResults()->getPointsHome();
            $nakadePts = $item->getResults()->getPointsAway();
        }

        return sprintf('%d:%d', $nakadePts, $opponentPts);
    }

    private function isRemovalDenied(string $status): bool
    {
        $deletableState = ['open', 'rejected', 'closed'];

        return !in_array($status, $deletableState);
    }

    private function isNotAllowed(User $author): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $this->tokenStorage->getToken()->getUser()->getRoles())) {
            return false;
        }

        if ($author === $this->tokenStorage->getToken()->getUser()) {
            return false;
        }

        return true;
    }
}
