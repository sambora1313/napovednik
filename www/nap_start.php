<html>

<head>
    <title>Napovednik dneva</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <style>
        .hidden {
            display: none;
        }
    </style>
    <br /><br />

    <div class="container" style="width:80vw; max-width: 400px;">
        <h3 align="center">Napovednik dneva</h3>
        <h4 align="center">Izberi datum: <input style="margin: 10px;" type="text" id="datepicker"></h4>
        <h4 id="datumIzbran" align="center"></h4>
        <br />
        <div id="termini" class="hidden" style="text-align: center;">
            <button id="jut">Jut</button>
            <button id="dop">Dop</button>
            <button id="pop">Pop</button>
            <button id="noc">Noč</button>
        </div>
    </div>


    <script>
        $("#datepicker").datepicker({
            dateFormat: 'DD, yy_mm_dd',
            dayNames: ["nedelja", "ponedeljek", "torek", "sreda", "četrtek", "petek", "sobota"],
            dayNamesMin: ["Ne", "Po", "To", "Sre", "Čet", "Pet", "So"],
            onSelect: function() {
                let datum = this.value;
                let dan = datum.split(',');
                dan = dan[0];

                $('#termini').removeClass('hidden');

                return izberiTermin(datum, dan);
            }
        });

        function izberiTermin(datum, dan) {



            $('#datumIzbran').html("Izbral si dan: " + datum);
            //alert(datum);

            datum = datum.split(',');
            datum = datum[1];

            $('button').on("click", function(e) {

                termin = e.target.id;

                $.ajax({
                    url: "nap_tabela.php",
                    method: "post",
                    dataType: "text",
                    data: {
                        dan: dan,
                        datum: datum,
                        termin: termin
                    },
                    success: function(result) {

                        $('#termini').addClass('hidden');
                        $('body').append(result);
                    }
                });

            });

        }
    </script>


</body>