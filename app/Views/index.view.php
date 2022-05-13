<?php

$alle_aufgaben = 0;
$anzahl_offen_nicht_spaet = 0;
$anzahl_offen_spaet = 0;
$anzahl_erledigt = 0;

foreach ($alle_tasks as $alle_tasks2){
    $alle_aufgaben++;
}

foreach ($nicht_zu_spaet_offene_tasks as $nicht_zu_spaet_offene_tasks2){
    $anzahl_offen_nicht_spaet++;
}

foreach ($zu_spaet_offene_tasks as $zu_spaet_offene_tasks2){
    $anzahl_offen_spaet++;
}

foreach ($erledigte_tasks as $erledigte_tasks2){
    $anzahl_erledigt++;
}

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>TackPad</title>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/nav.css">

    <link rel="shortcut icon" href="assets/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- Importing font from google -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

</head>

<body>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <h1>TackPad</h1>
  <h2>Hello user!</h2>
  <a href="#">Logout</a>
</div>
<span class='navi' onclick="openNav()">&#9776;</span>
    <table>
        <tr>
            <td></td>
            <td>
                <main>
                    <?php
                    if ($alle_aufgaben > 0){
                    $anzahl_offen = $anzahl_offen_spaet + $anzahl_offen_nicht_spaet;

                    if($anzahl_offen > 0){
                    echo "<div class='options'>
                        <button onclick='openModal()'><i class='fas fa-plus'></i> Hinzufügen</button>
                        <button id='bearbeiten' onclick='openBearbeiten()'><i class='fas fa-edit'></i> Bearbeiten</button>
                        <button id='loeschen'><i class='fas fa-trash'></i> Löschen</button>
                        <button id='freigeben'><i class='fas fa-share'></i> Freigeben</button>
                    </div>";
                    /* Offene Aufgaben */
                    echo "<h1>Aufgaben</h1>";
                    echo "<p>Offene Aufgaben ($anzahl_offen)</p>

                    <table>

                    <tr>
                        <th>
                            <input type='checkbox' name='vehicle1' title='Alles auswählen'>
                        </th>
                        <th>Titel</th>
                        <th>Aufgabe</th>
                        <th>Datum</th>
                        <th>Priorität</th>
                        <th>Geändert</th>
                    </tr>";

                    foreach ($nicht_zu_spaet_offene_tasks as $nicht_zu_spaet_offene_tasks2){
                        echo "<tr class='aufgabe_nicht_zu_spaet'>
                        <td style='background-color:lightgreen;'><input type='checkbox' class='nicht_zu_spaet_offene_tasks'></td>
                        <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['titel'] . "</td>
                        <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['notiz'] . "</td>
                        <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['date_to_complete'] . "</td>
                        <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['prioritaet'] . "</td>
                        </tr>";
                    }

                    foreach ($zu_spaet_offene_tasks as $zu_spaet_offene_tasks2){
                        echo "<tr class='aufgabe_zu_spaet'>
                        <td style='background-color:lightcoral;'><input type='checkbox' class='zu_spaet_offene_tasks'></td>
                        <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['titel'] . "</td>
                        <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['notiz'] . "</td>
                        <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['date_to_complete'] . "</td>
                        <td style='background-color:lightcoral'>" . $zu_spaet_offene_tasks2['prioritaet'] . "</td>
                        </tr>";
                    }
                    echo "</table><br>";
                    }
                    if (1 == 1){
                    /* Erledigte Aufgaben */
                    echo "<p>Erledigte Aufgaben ($anzahl_erledigt)</p>

                    <table>

                    <tr>
                        <th>
                            <input type='checkbox' title='Alle auswählen' name='vehicle1'>
                        </th>
                        <th>Titel</th>
                        <th>Aufgabe</th>
                        <th>Datum</th>
                        <th>Priorität</th>
                        <th>Geändert</th>
                        <th>Wurde erledigt am</th>
                    </tr>";

                    foreach ($erledigte_tasks as $erledigte_tasks2){
                        echo "<tr>
                        <td><input type='checkbox' class='erledigte_tasks' checked></td>
                        <td class='erledigt_titel'><del>" . $erledigte_tasks2['titel'] . "</del></td>
                        <td class='erledigt_notiz'><del>" . $erledigte_tasks2['notiz'] . "</del></td>
                        <td class='erledigt_datum'><del>" . $erledigte_tasks2['date_to_complete'] . "</del></td>
                        <td class='erledigt_priority'><del>" . $erledigte_tasks2['prioritaet'] . "</del></td>
                        <td class='erledigt_lastchange'><del>" . $erledigte_tasks2['last_change'] . "</del></td>
                        <td class='erledigt_completed_at'><del>" . $erledigte_tasks2['date_when_completed'] . "</del></td>
                        </tr>";
                    }
                    echo "</table><br>";
                    }
                }else{
                    echo "<h1 style='color: red';>Es wurde noch keine Aufgabe hinzugefügt</h1>";
                }?>
                </main>
            </td>
            <td></td>
        </tr>
    </table>

    <?php
    include("addNote.view.php");
    ?>

    <?php
    include("editNote.view.php");
    ?>

    <script src="public/js/clientSideValidationNotice.js"></script>
    <script src="public/js/tackpad.js"></script>
</body>

</html>