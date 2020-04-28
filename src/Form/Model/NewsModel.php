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

namespace App\Form\Model;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class NewsModel
{
    public $body = '
    <p style="text-align: left">Während der Corona-Krise verlagern wir unseren Spieltreff auf KGS.
    JEDEN <u>Montag wird ab 18 Uhr</u> auf KGS der Raum "Mommsen-Eck" erstellt und öffentlich gemacht.<br>
    Die Spieltreffleiter Tina (TinaGo), Maurice (Mo) und Holger (Mumm) helfen bei Problemen, gerne auch über WhatsApp Video oder telefonisch.<br>
    Da diese Umstände und KGS erwartunsggemäß für viele sehr ungewohnt sein werden, empfehlen wir <u>großzügige
    Zeiteinstellungen (30min + 15 Steine in 10 min Byoyomi) und freie Partien</u>.
    </p>
    
    <p>am <span style="font-size: larger; font-weight: bolder">Montag</span>, den
        <span style="font-size: larger; font-weight: bolder">{{ date }}</span> ab
        <span style="font-size: larger; font-weight: bolder">18-20h</span>
    </p>

    <ul style="text-align: left; list-style: none; line-height: 1.5em">
        <li>auf <b>KGS</b> (Client Software benötigt)</li>
        <li>RAUM:<b>Mommsen-Eck</b> (zu finden unter <i>Neue Räume</i>)</li>
        <li>Spieltreffleiter geben Hilfestellung</li>
        <li>freies Spiel</li>
        <li>großzügige Zeiteinstellung (optional)</li>
    </ul>

    <ul style="text-align: left; list-style: none;">
        <li style="margin-top: 20px"><p style="color: #C82829; font-weight: bolder; margin-bottom: 3px; ">ACHTUNG!</p>
            Für das Go-Spiel auf KGS benötigst du einen Client, den du kostenlos unter <a target="_blank" title="externe Seite!" href="https://www.gokgs.com/">KGS</a> herunterladen kannst.
            Außerdem musst du auf deinem Rechner eine aktuelle Java-Version installiert haben. Hilfestellungen dazu findest du unter
            <a target="_blank" title="externe Seite!" href="https://senseis.xmp.net/?KGS">Sensei\'s Library</a>
            Das Spielen auf KGS ist kostenlos!!!
        </li>
    </ul>
    
    ';
    public $title = 'My Title';

    public function __construct()
    {
    }
}
