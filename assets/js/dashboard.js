import '../css/dashboard.css';
var $ = require('jquery');

$(function() {

    $("#unsubscribe").click(function(event) {
        if (!window.confirm("Etes-vous sur de vouloir vous désister ?")) {
            event.preventDefault();
        }
    });
})



