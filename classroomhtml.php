<?php

function htmlHead() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Osztályok</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
HTML;
}

function formDisplay() {
    echo '<h1>Válassz osztályt</h1>';
    echo '<form method="post" class="button-group">';
    for ($i = 0; $i < 6; $i++) {
        echo "<input type='submit' name='button$i' value='" . getKlasse()[$i] . "' />";
    }
    echo '<input type="submit" name="button_all" value="*">';
    echo '<input type="submit" name="data" value="Lekérdezések">';
    echo '</form>';
}



function htmlEnd() {
    echo <<<HTML
</div>
</body>
</html>
HTML;
}


