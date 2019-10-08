/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// any CSS you require will output into a single css file (app.css in this case)
import '../css/app.scss';
import '../css/nav.scss';
import '../css/footer.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
import 'bootstrap'; //add functions to jQuery
//uncomment to support legacy code
global.$ = $;

//navigation active class settings
$(document).ready(function () {
    $(function () {
        var current_page_URL = location.href;

        $("a").each(function () {

            if ($(this).attr("href") !== "#") {
                var target_URL = $(this).prop("href");

                if (target_URL == current_page_URL) {
                    $('nav a').parents('li, ul').removeClass('active');
                    $(this).parent('li').addClass('active');

                    return false;
                }
            }
        });
    });
});
