import '../css/profile.scss';

//navigation active class settings
$("#news-toggle").change(function () {

    if (this.checked === true) {
        $.ajax({ url: 'script.php?argument=value&foo=bar' });
    } else {
        alert("aus");
    }
});