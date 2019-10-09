import '../css/profile.scss';

//toggle news reader
$(document).ready(function () {
    $("#news-toggle").change(function () {
            $.ajax({url: $(this).data('url'), METHOD: 'UPDATE'});
    });
});