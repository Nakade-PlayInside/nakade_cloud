// assets/js/imprint
// custom code for the imprint page
import '../css/bundesliga.scss';
import $ from 'jquery';

//remove autocomplete form input elements
$(document).ready(function () {
    $(document).on('focus', ':input', function () {
        $(this).attr('autocomplete', 'disabled');
    });
});

//showing and updating team choices on seasonSelect
$(document).ready(function () {
    var $seasonSelect = $('.js-results-form-season');
    var $teamsResultTarget = $('.js-team-result-target');
    $seasonSelect.on('change', function (e) {
        $.ajax({
            url: $seasonSelect.data('teams-result-url'),
            data: {
                seasonId: $seasonSelect.val()
            },
            success: function (html) {
                if (!html) {
                    $teamsResultTarget.find('select').remove();
                    $teamsResultTarget.addClass('d-none');

                    return;
                }
                // Replace the current field and show
                $teamsResultTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});


//showing and updating result on resultSelect
$(document).ready(function () {
    var $resultSelect = $('.js-result-select');
    var $resultTarget = $('.js-calculate-result');
    $resultSelect.on('change', function (e) {
        var $nakade = 0;
        var $opponent = 0;
        $resultSelect.each(function () {
            switch ($(this).val()) {
                case '2:0': $nakade += 2;
                    break;
                case '1:1': $nakade += 1;
                    $opponent += 1;
                    break;
                case '0:2': $opponent += 2;
                    break;
            }
        });
        $resultTarget.text($nakade+':'+$opponent)
        console.log($nakade+':'+$opponent);
        // $.ajax({
        //     url: $seasonSelect.data('teams-result-url'),
        //     data: {
        //         seasonId: $seasonSelect.val()
        //     },
        //     success: function (html) {
        //         if (!html) {
        //             $teamsResultTarget.find('select').remove();
        //             $teamsResultTarget.addClass('d-none');
        //             return;
        //         }
        //         // Replace the current field and show
        //         $teamsResultTarget
        //             .html(html)
        //             .removeClass('d-none')
        //     }
        // });
    });
});

