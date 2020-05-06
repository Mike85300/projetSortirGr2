import '../css/trip_add.css';
let $ = require('jquery');


$(function() {

    if ($('#trip_location').val())
    {
        afficherDetailLieu($('#trip_location').val());
    }

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
                    let locations = JSON.parse(data);
                    let option;
                    if (locations.length == 0) {
                        option = $('<option value="">Aucun lieu ne correspond à la ville sélectionnée</option>');
                        option.appendTo(locationSelect);
                        $('#trip_location_street').val("");
                        $('#trip_location_latitute').val("");
                        $('#trip_location_longitude').val("");
                    }
                    else
                    {
                        for (const location of locations) {
                            option = $('<option value="' + location.id + '">' + location.name + '</option>');
                            option.appendTo(locationSelect);
                        }
                        $('#trip_location_street').val(locations[0].street);
                        $('#trip_location_latitute').val(locations[0].latitude);
                        $('#trip_location_longitude').val(locations[0].longitude);
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
                if (location)
                {
                    $('#trip_location_street').val(location.street);
                    $('#trip_location_latitute').val(location.latitude);
                    $('#trip_location_longitude').val(location.longitude);
                }
                else
                {
                    $('#trip_location_street').val("");
                    $('#trip_location_latitute').val("");
                    $('#trip_location_longitude').val("");
                }
            },
            error: function (err) {
                alert("An error ocurred while loading location details ...");
            }
        });
    });

    function afficherDetailLieu($locationId)
    {
        $.ajax({
            type: "POST",
            url: "http://localhost:8000/location/ajax2",
            data: {locationId: $locationId},
            success: function (data) {
                let location = JSON.parse(data);
                if (location)
                {
                    $('#trip_location_street').val(location.street);
                    $('#trip_location_latitute').val(location.latitude);
                    $('#trip_location_longitude').val(location.longitude);
                    if ($('#trip_city').val() == "")
                    {
                        $('#trip_city').val(location.city.name + " (" + location.city.zip + ")");
                    }
                }
                else
                {
                    $('#trip_location_street').val("");
                    $('#trip_location_latitute').val("");
                    $('#trip_location_longitude').val("");
                }
            },
            error: function (err) {
                alert("An error ocurred while loading location details ...");
            }
        });
    }

});

