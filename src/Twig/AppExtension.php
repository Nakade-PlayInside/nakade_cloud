<?php

namespace App\Twig;

use App\Twig\Helper\MeetingAlert;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AppExtension!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AppExtension extends AbstractExtension
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('locale_datetime', [$this, 'processLocaleDate']),
            new TwigFilter('locale_date', [$this, 'processLocale']),
            new TwigFilter('meeting_alert', [$this, 'processAlert'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Gives you a locale string of a date string eg. Mai 2019.
     *
     * @param string $value expect date string in format Y-m-d eg. 2019-03-18
     */
    public function processLocale(string $value): string
    {
        $timestamp = strtotime($value);

        //set date locale to German
        setlocale(LC_TIME, 'de_DE.utf8');
        setlocale(LC_ALL, 'de_DE.utf8');

        return strftime(_('%e.%B'), $timestamp);
    }

    /**
     * Gives you a locale string of a date string eg. Mai 2019.
     *
     * @param string $value expect date string in format Y-m-d eg. 2019-03-18
     */
    public function processAlert(string $value): string
    {
        $alert = (new MeetingAlert())->getAlert($value);

        if (empty($alert)) {
            return '';
        }

        /** @var AssetExtension $asset */
        $asset = $this->twig->getExtension(AssetExtension::class);
        $imgSrc = $asset->getAssetUrl('build/images/svg/ic_event_available_24px.svg');
        $class = 'Heute' === $alert ? 'today' : '';

        $html = '<img alt="termin" src="'.$imgSrc.'">';
        $html .= '<span class="'.$class.'">'.$alert.'</span>';

        return $html;
    }

    /**
     * Gives you a locale string of a date  eg. 1. Mai 2019 20:30.
     */
    public function processLocaleDate(\DateTimeInterface $value): string
    {
        //set date locale to German
        setlocale(LC_TIME, 'de_DE.utf8');
        setlocale(LC_ALL, 'de_DE.utf8');

        return strftime(_('%e.%B %Y %H:%M'), $value->getTimestamp());
    }
}
