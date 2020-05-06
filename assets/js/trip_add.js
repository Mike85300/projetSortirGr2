import '../css/trip_add.css';
let $ = require('jquery');


$(function() {

    /**
     * Charge les informations du lieu et de la ville au chargement de la page si l'utilisateur modifie une sortie.
     */
    if ($('#trip_location').val())
    {
        afficherDetailLieu($('#trip_location').val());
    }

    /**
     * Gère l'évènement 'click' sur le lien supprimer
     */
    $("#btn-remove").click(function(event) {
        if (!window.confirm("Etes-vous sur de vouloir supprimer la sortie ?")) {
            event.preventDefault();
        }
    });

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
            validValue($(this));
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
                        invalidValue($('#trip_location'));
                        option = $('<option value="">Aucun lieu enregistré</option>');
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
        else
        {
            invalidValue($(this));
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

    $('#trip_name').blur(function() {
        let value = $(this).val().trim();
        if (value.length > 0 && value.length < 50)
        {
            validValue($(this));
        } else
        {
            invalidValue($(this));
        }
    });

    $('#trip_maxRegistrationNumber').blur(function () {
        let value = $(this).val();
        if (!value) {
            removeValidInvalidStyle($(this));
        }
        else if (value > 0)
        {
            validValue($(this));
        }
        else
        {
            invalidValue($(this));
        }
    });

    $('#trip_duration').blur(function () {
        let value = $(this).val();
        if (value > 0)
        {
            validValue($(this));
        }
        else
        {
            invalidValue($(this));
        }
    });

    $('#trip_startDate_date').change(validStartDate);
    $('#trip_startDate_time_hour').change(validStartDate);
    $('#trip_startDate_time_minute').change(validStartDate);

    function validStartDate()
    {
        let startDate = new Date($('#trip_startDate_date').val() + 'T' + ($('#trip_startDate_time_hour').val() < 10 ? '0' : '') + $('#trip_startDate_time_hour').val() + ':' + ($('#trip_startDate_time_minute').val() < 10 ? '0' : '') + $('#trip_startDate_time_minute').val() + ':00');
        let registrationDeadline = new Date($('#trip_registrationDeadline_date').val() + 'T' + ($('#trip_registrationDeadline_time_hour').val() < 10 ? '0' : '') + $('#trip_registrationDeadline_time_hour').val() + ':' + ($('#trip_registrationDeadline_time_minute').val() < 10 ? '0' : '') + $('#trip_registrationDeadline_time_minute').val() + ':00');
        removeValidInvalidStyle($('#trip_startDate_date'));
        removeValidInvalidStyle($('#trip_startDate_time_hour'));
        removeValidInvalidStyle($('#trip_startDate_time_minute'));
        if (startDate && startDate > new Date())
        {
            validValue($('#trip_startDate_date'));
            validValue($('#trip_startDate_time_hour'));
            validValue($('#trip_startDate_time_minute'));
        }
        else
        {
            invalidValue($('#trip_startDate_date'));
            invalidValue($('#trip_startDate_time_hour'));
            invalidValue($('#trip_startDate_time_minute'));
        }
        if (registrationDeadline != 'Invalid Date')
        {
            validRegistrationDeadline();
        }
    }

    $('#trip_registrationDeadline_date').change(validRegistrationDeadline);
    $('#trip_registrationDeadline_time_hour').change(validRegistrationDeadline);
    $('#trip_registrationDeadline_time_minute').change(validRegistrationDeadline);

    function validRegistrationDeadline()
    {
        let startDate = new Date($('#trip_startDate_date').val() + 'T' + ($('#trip_startDate_time_hour').val() < 10 ? '0' : '') + $('#trip_startDate_time_hour').val() + ':' + ($('#trip_startDate_time_minute').val() < 10 ? '0' : '') + $('#trip_startDate_time_minute').val() + ':00');
        if (startDate != 'Invalid Date')
        {
            let registrationDeadline = new Date($('#trip_registrationDeadline_date').val() + 'T' + ($('#trip_registrationDeadline_time_hour').val() < 10 ? '0' : '') + $('#trip_registrationDeadline_time_hour').val() + ':' + ($('#trip_registrationDeadline_time_minute').val() < 10 ? '0' : '') + $('#trip_registrationDeadline_time_minute').val() + ':00');
            removeValidInvalidStyle($('#trip_registrationDeadline_date'));
            removeValidInvalidStyle($('#trip_registrationDeadline_time_hour'));
            removeValidInvalidStyle($('#trip_registrationDeadline_time_minute'));
            if (startDate > registrationDeadline)
            {
                validValue($('#trip_registrationDeadline_date'));
                validValue($('#trip_registrationDeadline_time_hour'));
                validValue($('#trip_registrationDeadline_time_minute'));
            }
            else
            {
                invalidValue($('#trip_registrationDeadline_date'));
                invalidValue($('#trip_registrationDeadline_time_hour'));
                invalidValue($('#trip_registrationDeadline_time_minute'));
            }
        }
    }



    function validValue(elt) {
        elt.removeClass("alert-danger");
        elt.addClass("alert-success");
    }

    function invalidValue(elt) {
        elt.removeClass("alert-success");
        elt.addClass("alert-danger");
    }

    function removeValidInvalidStyle(elt) {
        elt.removeClass("alert-danger");
        elt.removeClass("alert-success");
    }

});

