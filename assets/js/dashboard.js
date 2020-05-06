import '../css/dashboard.css';
var $ = require('jquery');

$(function() {

    $("#unsubscribe").click(function(event) {
        if (!window.confirm("Etes-vous sur de vouloir vous désister ?")) {
            event.preventDefault();
        }
    });

    $(".trip-card").hover(
        function ()
        {
            $(this).css('box-shadow', '-5px 10px #888888');
        },
        function ()
        {
            $(this).css('box-shadow', '0px 0px #888888');
        })
});



