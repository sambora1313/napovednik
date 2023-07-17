<div class="table-responsive">
    <table id="napovednik" class="table table-bordered">
        <tr>
            <th width="10%">Ura</th>
            <th width="10%">Tip oddaje</th>
            <th width="10%">Ime</th>
            <th min-width="50%">Opombe</th>
            <th width="auto">Uredi</th>
            <th width="auto">Dodaj ali briši</th>

        </tr>
        <?php
        while ($row = mysqli_fetch_array($result)) {

            $ura = $row["ura"];
            $uraID = str_replace('.', '-', $ura);
            $uraID = "id_" . $uraID;

            $rowVersion = $row["lastVersion"];

            $inputID = "input_" . $uraID;
            //print_r($inputID);

        ?>
            <tr <?php echo 'version="' . $rowVersion . '" id="' . $uraID . '"' ?>>
                <td class="ura"><?php echo $ura ?></td>
                <td class="tip"><?php echo $row["tip"]; ?></td>
                <td class="ime"><?php echo $row["ime"]; ?></td>
                <td class="opombe"><?php echo $row["opombe"]; ?></td>
                <td><input type="button" name="uredi" value="Uredi" id="<?php echo $inputID ?>" class="btn btn-info btn-xs view_data" /></td>
                <td width="auto"><button class="btn btn-primary dodaj" name="Dodaj">+</button>
                    <button type="button" class="btn btn-danger" name="Briši" onclick="SomeDeleteRowFunction(this)">-</button>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr id="rowEmpty">
            <td class="ura"></td>
            <td class="tip"></td>
            <td class="ime"></td>
            <td class="opombe"></td>
            <td><input type="button" name="uredi" value="Uredi" id="" class="btn btn-info btn-xs view_data uredi" /></td>
            <td width="auto"><button class="btn btn-primary dodaj" name="Dodaj">+</button>
                <button type="button" class="btn btn-danger" name="Briši" onclick="SomeDeleteRowFunction(this)">-</button>
            </td>
        </tr>
    </table>
</div>

<div id="dataModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="nap_v_bazo.php">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Podatki rubrike/oddaje</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" id="podatki_oddaje">



                    </div>
                </div>
                <div class="modal-footer">
                    <input onclick="modalSave()" type="sumbit" class="btn btn-default" value="Shrani" data-dismiss="modal">
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // gumb uredi
    $('body').on('click', '.view_data', function(e) {

        let input = $(this).attr("id");
        let name = $(this).attr("name");
        tip = $(e.target).closest('tbody tr').children('td.tip').html();
        ime = $(e.target).closest('tbody tr').children('td.ime').html();
        opombe = $(e.target).closest('tbody tr').children('td.opombe').html();
        uraId = $(e.target).closest('tbody tr').attr('id');
        ura = $(e.target).closest('tbody tr').children('td.ura').html()

        // ko odprem okno zaznamujem z novo verzijo
        rowVersion = $(e.target).closest('tbody tr').attr('version');
        rowVersionNew = rowVersion;
        rowVersionNew++;

        console.log('Nova verzija vrstice bo: ' + rowVersionNew);
        // alert(ura);

        $.ajax({
            url: "index_modal_select.php",
            method: "post",
            data: {

                input: input,
                name: name,
                ime: ime,
                ura: ura,
                opombe: opombe

            },
            success: function(data) {
                $('#podatki_oddaje').html(data);

                $('#seznamOddaj').find('td.ime.drugo').html(ime);

                // preveri ali tip obstaja in označi primerno med options
                if ($('#tipi').find('option[value="' + tip + '"]').length > 0) {
                    $('#tipi').find('option[value="' + tip + '"]').prop('selected', 'selected');
                    $('#tipi').find('td.tip.drugo').html(tip);
                } else {
                    $('#tipi').find('option[value="Drugo ..."]').prop('selected', 'selected');
                    $('#tipi').find('td.tip.drugo').prop('contenteditable', 'true');
                }

                // preveri ali taka oddaja obstaja in označi primerno med options
                if ($('#seznamOddaj').find('option[value="' + ime + '"]').length > 0) {
                    $('#seznamOddaj').find('option[value="' + ime + '"]').prop('selected', 'selected');

                } else {
                    $('#seznamOddaj').find('option[value="Drugo ..."]').prop('selected', 'selected');
                    $('#seznamOddaj').find('td.ime.drugo').prop('contenteditable', 'true');
                }

                $('#dataModal').modal("show");

            }
        });
    });



    $(document).ready(function() {

        function modalClean() {
            $('#dataModal').find('td.ime').html('');
            $('#dataModal').find('td.ime').html('');
            $('#dataModal').find('td.opombe').html('');
            $('#dataModal').find('td.avtor').html('');
            $('#dataModal').find('td.opis').html('');
        }

        // zaznava spremembe select menuja in potem klic v bazo, da dobim default podatke oddaje/rubrike
        $('#podatki_oddaje').on('change', 'select', function() {

            let selected = $(this).find(":selected").val();

            let selectedIsOddaja = $(this).closest('tr').attr('id') === 'seznamOddaj';
            // alert(selectedIsOddaja + ' ' + selected);

            if (selected === "Drugo" && selectedIsOddaja) {

                // alert('Drugo!')
                modalClean();

            } else if (selectedIsOddaja) {

                $.ajax({
                    url: "index_modal_update.php",
                    method: "post",
                    data: {
                        selected: selected
                    },
                    dataType: 'json',
                    success: function(result) {

                        console.log(result);

                        let avtor = result[3];
                        let opis = result[4];

                        $('#dataModal').find('td.ime').html(selected);
                        $('#dataModal').find('td.opombe').html('');
                        $('#dataModal').find('td.avtor').html(avtor);
                        $('#dataModal').find('td.opis').html(opis);
                    }

                });
                //   return selected;
            } else if (selected === "Drugo") {

                $('#dataModal').find('td.tip').html('');

            } else {

                selected = $(this).find(":selected").val();
                //alert(selected);
                $('#dataModal').find('td.tip').html(selected);

            }
        });

    });


    function modalSave() {

        let rowId = uraId;

        // vrednosti izpolnjenih polj:
        // let tip = $('#podatki_oddaje td.tip').find(":selected").val();
        // let ime = $('#podatki_oddaje td.ime').find(":selected").val();
        let tip = $('#podatki_oddaje td.tip').html();
        let ime = $('#podatki_oddaje td.ime').html();
        let podnaslov = $('#podatki_oddaje td.podnaslov').html();
        let povzetek = $('#podatki_oddaje td.povzetek').html();
        let opis = $('#podatki_oddaje td.opis').html();
        let napovednik = $('#podatki_oddaje td.napovednik').html();
        let opombe = $('#podatki_oddaje td.opombe').html();

        // array vseh vrednosti
        let podatkiOddaja = [ura, tip, ime, opombe, opis, napovednik, podnaslov, povzetek];
        //alert(podatkiOddaja);

        rowId = "#" + rowId;

        console.log("Ime je: " + ime);

        // alert(rowId);
        function updateRow() {

            $(rowId).attr('version', rowVersionNew);
            $(rowId).children('td.tip').html(tip);
            $(rowId).children('td.ime').html(ime);
            $(rowId).children('td.opombe').html(opombe);

        }

        $.ajax({
            url: "index_modal_save.php",
            method: "post",
            data: {
                data: podatkiOddaja,
                rowVersionNew: rowVersionNew,
            },

            success: function(result) {

                //console.log("Result is");

                //if result false do this:
                if (result == 0) {

                    alert("Vrstica je bila vmes posodobljena. Ponovno bom naložil stran in lahko poskusiva znova.:)");

                    location.reload();

                } else {
                    console.log("updateRow ...")
                    updateRow();
                }

                //console.log(result);
            }
        });

    };


    $('body').on('click', 'button.dodaj', function(e) {

        var tableRef = document.getElementById(e);
        var newRow = document.getElementById('rowEmpty');

        var thisRow = $(e.target).closest('tbody tr');

        function rowTime() {
            var thisRowTime = $(e.target).closest('tbody tr').children('td.ura');
            thisRowTime = $(thisRowTime).html();
            thisRowTime = thisRowTime.split('.');

            var thisRowHour = thisRowTime[0];
            thisRowHour = (+thisRowHour);
            var thisRowMinutes = thisRowTime[1];
            thisRowMinutes = (+thisRowMinutes);


            return newRowTime(thisRowHour, thisRowMinutes);
        }


        function newRowTime(thisRowHour, thisRowMinutes) {



            thisRowMinutes = thisRowMinutes + 5;
            // console.log(thisRowMinutes + ' to so minute');


            if (thisRowMinutes >= 60) {
                thisRowHour++;
                thisRowMinutes = '00';

                return newTime(thisRowHour, thisRowMinutes);

            } else {

                return newTime(thisRowHour, thisRowMinutes);
            }



        }

        function newTime(h, m) {
            alert(m);
            var hours = String('00' + h).slice(-2);
            var minutes = String('00' + m).slice(-2);


            time = (hours + '.' + minutes);


        };

        addRow(newRow);

        function addRow(newRow) {

            rowTime();
            $(thisRow).after($(newRow).clone().attr('id', time).addClass('newRow'));
            $('.newRow').children('td.ura').html(time);
            $('.newRow').find('td input.uredi').attr('name', time);
            $('.newRow').removeClass('newRow');

            return (time = '');

        }

    });



    window.SomeDeleteRowFunction = function SomeDeleteRowFunction(o) {
        var p = o.parentNode.parentNode;
        p.parentNode.removeChild(p);
    }
</script>