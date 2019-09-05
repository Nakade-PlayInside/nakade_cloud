<?php

namespace App\Twig;

use App\Twig\Helper\MeetingAlert;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Environment;

/**
 * Class AppExtension!
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * AppExtension constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

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
            new TwigFilter('meeting_alert', [$this, 'processAlert'], ['is_safe' => ['html']]),
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
        $alert = (new MeetingAlert())->getAlert($value);

        if (empty($alert)) {
            return '';
        }

        /** @var AssetExtension $asset */
        $asset = $this->twig->getExtension(AssetExtension::class);
        $imgSrc = $asset->getAssetUrl('build/images/svg/ic_event_available_24px.svg');
        $class = $alert==='Heute'? "today":'';

        $html = '<img alt="termin" src="'.$imgSrc.'">';
        $html .= '<span class="' . $class . '">'.$alert.'</span>';

        return $html;
    }
}
