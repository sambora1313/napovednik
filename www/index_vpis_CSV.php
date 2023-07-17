<?php
//index.php

$error = '';
$name = '';
$email = '';
$subject = '';
$message = '';

//dodano
$ura = '';
$ime = '';
$opis = '';
$opombe = '';

function clean_text($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if (isset($_POST["submit"])) {
    if (empty($_POST["name"])) {
        $error .= '<p><label class="text-danger">Please Enter your Name</label></p>';
    } else {
        $name = clean_text($_POST["name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $error .= '<p><label class="text-danger">Only letters and white space allowed</label></p>';
        }
    }
    if (empty($_POST["email"])) {
        $error .= '<p><label class="text-danger">Please Enter your Email</label></p>';
    } else {
        $email = clean_text($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error .= '<p><label class="text-danger">Invalid email format</label></p>';
        }
    }
    if (empty($_POST["subject"])) {
        $error .= '<p><label class="text-danger">Subject is required</label></p>';
    } else {
        $subject = clean_text($_POST["subject"]);
    }
    if (empty($_POST["message"])) {
        $error .= '<p><label class="text-danger">Message is required</label></p>';
    } else {
        $message = clean_text($_POST["message"]);
    }

    if (empty($_POST["ura"])) {
        $error .= '<p><label class="text-danger">Vpiši uro/čas predvajanja</label></p>';
    } else {
        $ura = clean_text($_POST["ura"]);
        if (!preg_match("/^[0-9][0-9]:[0-9][0-9]*$/", $ura)) {
            $error .= '<p><label class="text-danger">Dovoljen le format ure: uu:mm !</label></p>';
        }
    }


    if ($error == '') {
        $file_open = fopen("contact_data.csv", "a");
        $no_rows = count(file("contact_data.csv"));
        if ($no_rows > 1) {
            $no_rows = ($no_rows - 1) + 1;
        }
        $form_data = array(
            'sr_no'  => $no_rows,
            'name'  => $name,
            'email'  => $email,
            'subject' => $subject,
            'message' => $message,
            'ura' => $ura
        );
        fputcsv($file_open, $form_data);
        $error = '<label class="text-success">Thank you for contacting us</label>';
        $name = '';
        $email = '';
        $subject = '';
        $message = '';
        $ura = '';
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>How to Store Form data in CSV File using PHP</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<style>
    .table {
        display: table;
        border-collapse: separate;
        border-spacing: 2px;
    }

    .thead {
        display: table-header-group;
        color: white;
        font-weight: bold;
        background-color: grey;
    }

    .tbody {
        display: table-row-group;
    }

    .tr {
        display: table-row;
    }

    .td {
        display: table-cell;
        border: 1px solid black;
        padding: 1px;
    }

    .tr.editing .td INPUT {
        width: 100px;
    }
</style>

<body>
    <br />
    <div class="container">
        <h2 align="center">Vpis dneva 1</h2>
        <br />
        <div class="col-md-6" style="margin:0 auto; float:none;">
            <form method="post">
                <h3 align="center">Jutro</h3>
                <br />
                <?php echo $error; ?>
                <div class="form-group">
                    <label>Naslov</label>
                    <input type="text" name="name" placeholder="Enter Name" class="form-control" value="<?php echo $name; ?>" />
                </div>
                <div class="form-group">
                    <label>Enter Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter Email" value="<?php echo $email; ?>" />
                </div>
                <div class="form-group">
                    <label>Enter Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Enter Subject" value="<?php echo $subject; ?>" />
                </div>
                <div class="form-group">
                    <label>Enter Message</label>
                    <textarea name="message" class="form-control" placeholder="Enter Message"><?php echo $message; ?></textarea>
                </div>
                <div class="form-group" align="center">
                    <input type="submit" name="submit" class="btn btn-info" value="Submit" />
                </div>
            </form>

            <div class="table">
                <div class="thead">
                    <div class="tr">
                        <div class="td">Ura</div>
                        <div class="td">Rubrika</div>
                        <div class="td">Opis</div>
                        <div class="td">Opomba</div>
                        <div class="td"></div>
                    </div>
                </div>
                <div class="tbody">
                    <form class="tr" method="post">
                        <div class="td"><input type="text" name="ura" placeholder="Vpiši čas predvajanja" class="form-control" value="<?php echo $ura; ?>" /></div>
                        <div class="td"></div>
                        <div class="td"></div>
                        <div class="td"></div>
                        <div class="td action"><button type="button" onclick="edit(this);">edit</button></div>
                    </form>
                    <form class="tr">
                        <div class="td">1</div>
                        <div class="td">2</div>
                        <div class="td">3</div>
                        <div class="td">4</div>
                        <div class="td action"><button type="button" onclick="edit(this);">edit</button></div>
                    </form>
                    <form class="tr">
                        <div class="td">1</div>
                        <div class="td">234567890123456</div>
                        <div class="td">3</div>
                        <div class="td">4</div>
                        <div class="td action"><button type="button" onclick="edit(this);">edit</button></div>
                    </form>

                    <form class="tr">
                        <div class="td">1</div>
                        <div class="td">2</div>
                        <div class="td">34567</div>
                        <div class="td">4</div>
                        <div class="td action"><button type="button" onclick="edit(this);">edit</button></div>
                    </form>
                    <form class="tr">
                        <div class="td">1234</div>
                        <div class="td">2</div>
                        <div class="td">3</div>
                        <div class="td">4</div>
                        <div class="td action"><button type="button" onclick="edit(this);">edit</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    function edit(element) {
        var tr = jQuery(element).parent().parent();
        if (!tr.hasClass("editing")) {
            tr.addClass("editing");
            tr.find("DIV.td").each(function() {
                if (!jQuery(this).hasClass("action")) {
                    var value = jQuery(this).text();
                    jQuery(this).text("");
                    jQuery(this).append('<input type="text" value="' + value + '" />');
                } else {
                    jQuery(this).find("BUTTON").text("save");
                }
            });
        } else {
            tr.removeClass("editing");
            tr.find("DIV.td").each(function() {
                if (!jQuery(this).hasClass("action")) {
                    var value = jQuery(this).find("INPUT").val();
                    jQuery(this).text(value);
                    jQuery(this).find("INPUT").remove();
                } else {
                    jQuery(this).find("BUTTON").text("edit");
                }
            });
        }
    }
</script>

</html>