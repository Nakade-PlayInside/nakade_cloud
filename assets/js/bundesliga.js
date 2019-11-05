// assets/js/imprint
// custom code for the imprint page
import '../css/bundesliga.scss';

//remove autocomplete form input elements
$(document).ready(function(){
    $( document ).on( 'focus', ':input', function(){
        $( this ).attr( 'autocomplete', 'disabled' );
    });
});

//showing and updating team choices on seasonSelect
$(document).ready(function() {
    var $seasonSelect = $('.js-results-form-season');
    var $teamsResultTarget = $('.js-team-result-target');
    $seasonSelect.on('change', function(e) {
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
