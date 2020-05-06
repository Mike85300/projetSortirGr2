import '../css/trip_cancel.css';
let $ = require('jquery');

$(function() {

    $("#cancel").click(function(event) {
        if (!window.confirm("Etes-vous sur de vouloir annuler ?")) {
            event.preventDefault();
        }
    });
})