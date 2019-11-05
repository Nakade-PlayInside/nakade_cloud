// assets/js/imprint
// custom code for the imprint page
import '../css/algolia.scss';

//autocompletion for teams in relegation
$(document).ready(function () {
    $('.js-user-autocomplete').each(function () {
        var autocompleteUrl = $(this).data('autocomplete-url');

        $(this).autocomplete({hint: false}, [
            {
                source: function (query, cb) {
                    $.ajax({
                        url: autocompleteUrl+'?query='+query
                    }).then(function (data) {
                            cb(data.teams);
                    });
                },
                displayKey: 'name',
                debounce: 500 // only request every 1/2 second
            }
        ]);
    })
});
