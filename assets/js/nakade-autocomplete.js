import $ from 'jquery';
//FOR DETAILS see https://symfonycasts.com/screencast/webpack-encore/autoprovide-jquery-modules#play Chapter 12-14

//autocompletion for teams in relegation
$(document).ready(function () {
    const $autoComplete = $('.js-user-autocomplete');
    if (!$autoComplete.is(':disabled')) {
        import('./components/algolia-autocomplete').then((autocomplete) => {
            autocomplete.default($autoComplete, 'teams', 'name');
        });
    }
});

//autocompletion for opponents
$(document).ready(function () {
    const $autoComplete = $('.js-opponent-autocomplete');
    if (!$autoComplete.is(':disabled')) {
        import('./components/algolia-autocomplete').then((autocomplete) => {
            autocomplete.default($autoComplete, 'opponent', function(opponent) { return opponent.firstName + " " + opponent.lastName;});
        });
    }
});
