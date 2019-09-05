<?php

namespace App\Twig;

use App\Twig\Helper\MeetingAlert;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension!
 *
 * @package App\Twig
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('locale_date', [$this, 'processLocale']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('meeting_alert', [$this, 'processAlert']),
        ];
    }

    /**
     * Gives you a locale string of a date string eg. Mai 2019.
     *
     * @param string $value expect date string in format Y-m-d eg. 2019-03-18
     *
     * @return string
     */
    public function processLocale(string $value): string
    {
        $timestamp = strtotime($value);

        //set date locale to German
        setlocale(LC_TIME, 'de_DE.utf8');

        return strftime('%e.%B', $timestamp);
    }

    /**
     * Gives you a locale string of a date string eg. Mai 2019.
     *
     * @param string $value expect date string in format Y-m-d eg. 2019-03-18
     *
     * @return string
     */
    public function processAlert(string $value): string
    {
        return (new MeetingAlert())->getAlert($value);
    }


}
