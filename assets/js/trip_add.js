import '../css/trip_add.css';
let $ = require('jquery');


$(function() {

    /**
     * Gére l'évènement 'input' sur le champ Ville - Requête AJAX
     */
    $('#trip_city').on('input', function () {
        $.ajax({
            type: "POST",
            url: "http://localhost:8000/city/ajax",
            data: {pattern: $(this).val()},
            dataType: "json",
            success: function (data) {
                let cities = JSON.parse(data);
                let datalist = $('#cities');
                datalist.empty();
                if (cities.length > 0) {
                    for (const city of cities) {
                        let option = $('<option value="' + city.name + ' (' + city.zip + ')">');
                        option.appendTo(datalist);
                    }
                } else {
                    let option = $('<option value="Aucune ville enregistrée ne correspond à votre recherche">');
                    option.appendTo(datalist);
                }

            },
            error: function () {
                alert("An error ocurred while loading cities ...");
            },
        })
    });

    /**
     * Gère l'évènement change sur le champ Ville - Requête AJAX
     */
    $('#trip_city').change(function () {
        let regex = /^(\D+)\s\((\d{5})\)$/;
        let matches = $(this).val().match(regex);
        if (matches) {
            $.ajax({
                type: "POST",
                url: "http://localhost:8000/location/ajax",
                data: {city: matches[1], zip: matches[2]},
                success: function (data) {
                    let locationSelect = $('#trip_location');
                    locationSelect.empty();
                    let option;
                    if (JSON.parse(data).length == 0) {
                        option = $('<option value="">Aucun lieu ne correspond à la ville sélectionnée</option>');
                        option.appendTo(locationSelect);
                    } else {
                        option = $('<option value="">Sélectionnez un lieu</option>');
                        option.appendTo(locationSelect);
                    }

                    for (const location of JSON.parse(data)) {
                        option = $('<option value="' + location.id + '">' + location.name + '</option>');
                        option.appendTo(locationSelect);
                    }
                },
                error: function (err) {
                    alert("An error ocurred while loading locations ...");
                }
            })


        }

    });

    /**
     * Gère l'évenement change sur le champ Lieu - Requête AJAX
     */
    $('#trip_location').change(function () {
        $.ajax({
            type: "POST",
            url: "http://localhost:8000/location/ajax2",
            data: {locationId: $(this).val()},
            success: function (data) {
                let location = JSON.parse(data);
                $('#trip_location_street').val(location.street);
                $('#trip_location_latitute').val(location.latitude);
                $('#trip_location_longitude').val(location.longitude);
            },
            error: function (err) {
                alert("An error ocurred while loading location details ...");
            }
        })
    });


});

