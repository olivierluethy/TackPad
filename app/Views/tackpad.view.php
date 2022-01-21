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
    <link rel="stylesheet" type="text/css" href="../public/css/tackpad.css">

    <link rel="shortcut icon" href="../images/favicon.ico">
    <meta name="author" content="Olivier Luethy">

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

</head>
<body>

<!-- Navigation Part -->
<header>
    <nav>
        <?php
            if(isset($_SESSION['loggedin']) == true){
                echo "<logo id='logo'></logo>
                <a href='home'>Home</a>
                <a href='about'>About</a>
                <a class='active' href='tackpad'>TackPad</a>
                <a href='logout'>Logout</a>
            </div>";
            }else{
                echo "<logo id='logo'></logo>
                <a href='home'>Home</a>
                <a href='about'>About</a>
                <a class='active' href='tackpad'>TackPad</a>
                <a href='login'>Login</a>
            </div>";
            }?>
    </nav>
</header>

<main>

<h1>Aufgaben</h1>

<?php
if ($alle_aufgaben > 0){
    $anzahl_offen = $anzahl_offen_spaet + $anzahl_offen_nicht_spaet;

    if($anzahl_offen > 0){
        /* Offene Aufgaben */
        echo "<p>Offene Aufgaben ($anzahl_offen)</p>

        <div class='selectAll'>
            <input class='checkAll' id='checkAll' type='checkbox' onclick='checkAll(this)'><p class='checkAllText'>Select all</p>
        </div>

        <table>

        <tr>
            <th>Erledigt</th>
            <th>Titel</th>
            <th>Aufgabe</th>
            <th>Muss erledigt sein am</th>
            <th>Bearbeiten</th>
            <th>Löschen</th>
        </tr>";

        foreach ($nicht_zu_spaet_offene_tasks as $nicht_zu_spaet_offene_tasks2){
            echo "<tr class='aufgabe_nicht_zu_spaet'>
            <td style='background-color:lightgreen;'><input type='checkbox' name='forAll' onclick='erledigen(" . $nicht_zu_spaet_offene_tasks2['NoteId'] . ")'></td>
            <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['titel'] . "</td>
            <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['notiz'] . "</td>
            <td style='background-color:lightgreen;'>" . $nicht_zu_spaet_offene_tasks2['date_to_complete'] . "</td>
            <td style='background-color:lightgreen;'><a onclick='editNote(" . $nicht_zu_spaet_offene_tasks2['NoteId'] . ")'><button class='edit'><i class='fas fa-edit'></i> Bearbeiten</button></a></td>
            <td style='background-color:lightgreen;'><a onclick='deleteNote(" . $nicht_zu_spaet_offene_tasks2['NoteId'] . ")'><button class='delete'><i class='fas fa-trash'></i> Löschen</button></a></td>
            </tr>";
        }

        foreach ($zu_spaet_offene_tasks as $zu_spaet_offene_tasks2){
            echo "<tr class='aufgabe_zu_spaet'>
            <td style='background-color:lightcoral;'><input type='checkbox' name='forAll' onclick='erledigen(" . $zu_spaet_offene_tasks2['NoteId'] . ")'></td>
            <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['titel'] . "</td>
            <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['notiz'] . "</td>
            <td style='background-color:lightcoral;'>" . $zu_spaet_offene_tasks2['date_to_complete'] . "</td>
            <td style='background-color:lightcoral;'><a onclick='editNote(" . $zu_spaet_offene_tasks2['NoteId'] . ")'><button class='edit'><i class='fas fa-edit'></i> Bearbeiten</button></a></td>
            <td style='background-color:lightcoral;'><a onclick='deleteNote(" . $zu_spaet_offene_tasks2['NoteId'] . ")'><button class='delete'><i class='fas fa-trash'></i> Löschen</button></a></td>
            </tr>";
        }
        echo "</table><br>";
    }
    if ($anzahl_erledigt > 0){
        /* Erledigte Aufgaben */
        echo "<p>Erledigte Aufgaben ($anzahl_erledigt)</p>

        <table>

        <tr>
            <th>Erledigt</th>
            <th>Titel</th>
            <th>Aufgabe</th>
            <th>Bearbeiten</th>
            <th>Löschen</th>
        </tr>";
        
        foreach ($erledigte_tasks as $erledigte_tasks2){
            echo "<tr>
            <td><input type='checkbox' name='forAll' onclick='nichtMehrErledigt(". $erledigte_tasks2['NoteId'] . ")' checked></td>
            <td class='erledigt_titel'><del>" . $erledigte_tasks2['titel'] . "</del></td>
            <td class='erledigt_notiz'><del>" . $erledigte_tasks2['notiz'] . "</del></td>
            <td><a onclick='editNote(" . $erledigte_tasks2['NoteId'] . ")'><button class='edit'><i class='fas fa-edit'></i> Bearbeiten</button></a></td>
            <td><a onclick='deleteNote(" . $erledigte_tasks2['NoteId'] . ")'><button class='delete'><i class='fas fa-trash'></i> Löschen</button></a></td>
            </tr>";
        }
        echo "</table>";
    }
}else{
    echo "<h1 style='color: red';>Es wurde noch keine Aufgabe hinzugefügt</h1>";
}?>

<form action="create" method="post">
  <input type="text" id="titel" name="title" placeholder="Titel"><br>
  <input type="text" id="aufgabe" name="notice" placeholder="Aufgabe"><br><br>
  <input type="date" id="date" name="date" placeholder="Datum eingeben"><br><br>
  <button class="hinzufuegen" type="submit"><i class="fas fa-plus"></i> Aufgabe hinzufügen</button>
</form> 
<p id="warningTitel"></p>
<p id="warningAufgabe"></p>
<p id="warningDate"></p>

<button id="deleteAll" onclick="deleteAll()"><i class="fas fa-trash-alt"></i> Delete all</button>

</main>

<script src="../public/js/clientSideValidationNotice.js"></script>
<script src="../public/js/tackpad.js"></script>
</body>
</html>
