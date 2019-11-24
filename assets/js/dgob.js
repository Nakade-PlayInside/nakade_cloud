// assets/js/imprint
// custom code for the imprint page
import '../css/dgob.scss';
import $ from "jquery";

//select Season
$(document).ready(function () {
    const $seasonSelect = $('.js-season-select');
    const $seasonTarget = $('.js-season-table');

    $seasonSelect.on('change', function (e) {
        $.ajax({
            url: $seasonSelect.data('url'),
            data: {
                seasonId: $seasonSelect.val(),
                parentUrl: window.location.origin + window.location.pathname,
            },
            success: function (html) {
                if (!html) {
                    return;
                }
                // Replace the current field and show
                $seasonTarget
                    .html(html)
            }
        });
    });

});

//select MatchDay
$(document).on('click', '.js-matchDay-select', function () {
    const $matchDaySelect = $('.js-matchDay-select');
    const $seasonTarget = $('.js-season-table');
    var targetMatchDay = ($(this).data('md'))

    $.ajax({
        url: $matchDaySelect.data('url'),
        data: {
            matchDay: targetMatchDay,
        },
        success: function (html) {
            if (!html) {
                return;
            }
            // Replace the current field and show
            $seasonTarget
                .html(html)
        }
    });
});
