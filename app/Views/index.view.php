<?php
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <script defer src="public/js/tackpad.js"></script>
</head>

<body>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <h1>TackPad</h1>
        <h2>Hello user!</h2>
        <a href="logout">Logout</a>
    </div>
    <span class='navi' onclick="openNav()">&#9776;</span>
    <table>
        <tr>
            <td></td>
            <td>
                <main>
                    <?php
                    /* Alle Aufgaben */
                    if (count($alle_tasks) > 0){
                        $anzahl_offen = count($zu_spaet_offene_tasks) + count($nicht_zu_spaet_offene_tasks);?>

                    <div class='options'>
                        <button onclick='openModal()'><i class='fas fa-plus'></i> Hinzufügen</button>
                        <button id='bearbeiten' onclick='openBearbeiten()'><i
                                class='fas fa-edit'></i>&nbsp;Bearbeiten</button>
                        <button id='loeschen' onclick='realyDeleteNote()'><i
                                class='fas fa-trash'></i>&nbsp;Löschen</button>
                        <button id='erledigt' onclick='erledigt()'><i class='fas fa-check'></i>&nbsp;Erledigt </button>
                        <button id='freigeben'><i class='fas fa-share'></i>&nbsp;Freigeben</button>
                    </div>

                    <?php
                        /* Offene Aufgaben */
                        if($anzahl_offen > 0){
                        ?>
                    <h1>Aufgaben</h1>
                    <p>Offene Aufgaben (<?= $anzahl_offen ?>)</p>

                    <table>
                        <tr>
                            <th>
                                <input id='checkAllOffeneTasks' type='checkbox' onclick='checkAllOffeneTasks(this)'
                                    name='vehicle1' title='Alles auswählen'>
                            </th>
                            <th>Titel</th>
                            <th>Aufgabe</th>
                            <th>Datum</th>
                            <th>Priorität</th>
                            <th>Geändert</th>
                        </tr>
                        <?php
                                    foreach ($nicht_zu_spaet_offene_tasks as $nicht_zu_spaet_offene_tasks2){?>
                        <tr class="aufgabe_nicht_zu_spaet"><input type="checkbox"
                                onclick="getId_for_offen(<?= $nicht_zu_spaet_offene_tasks2['NoteId']; ?>)"
                                class="offene_tasks">


                            <td style='background-color:lightgreen;'>
                                <?= $nicht_zu_spaet_offene_tasks2['titel'];?>
                            </td>
                            <td style='background-color:lightgreen;'>
                                <?= $nicht_zu_spaet_offene_tasks2['notiz'];?>
                            </td>
                            <td style='background-color:lightgreen;'>
                                <?= $nicht_zu_spaet_offene_tasks2['date_to_complete'];?></td>
                            <td style='background-color:lightgreen;'>
                                <?= $nicht_zu_spaet_offene_tasks2['prioritaet'];?>
                            </td>
                        </tr>
                        <?php } foreach ($zu_spaet_offene_tasks as $zu_spaet_offene_tasks2){ ?>
                        <tr class='aufgabe_zu_spaet'>
                            <td style="background-color:lightcoral;"><input type="checkbox"
                                    onclick="getId_for_offen(<?= $zu_spaet_offene_tasks2[' NoteId']; ?>)"
                                    class='offene_tasks'></td>

                            <td style='background-color:lightcoral;'><?= $zu_spaet_offene_tasks2['titel'];?></td>
                            <td style='background-color:lightcoral;'><?= $zu_spaet_offene_tasks2['notiz']; ?>
                            </td>
                            <td style='background-color:lightcoral;'>
                                <?= date('d.m.Y', strtotime($zu_spaet_offene_tasks2['date_to_complete'])); ?>
                            </td>
                            <td style='background-color:lightcoral'><?php $zu_spaet_offene_tasks2['prioritaet']; ?></td>
                        </tr><?php } ?>
                    </table><br>
                    <button id='deleteAllOffeneTasks' onclick='deleteAll()'><i class='fas fa-trash-alt'></i> Delete
                        all</button>
                    <?php } /* Erledigte Aufgaben */ if (count($erledigte_tasks)> 0){?>
                    <p>Erledigte Aufgaben (<?= count($erledigte_tasks); ?>)</p>


                    <table>

                        <tr>
                            <th>
                                <input id='checkAllErledigteTasks' type='checkbox'
                                    onclick='checkAllErledigteTasks(this)' title='Alle auswählen' name='vehicle1'>
                            </th>
                            <th>Titel</th>
                            <th>Aufgabe</th>
                            <th>Datum</th>
                            <th>Priorität</th>
                            <th>Wurde erledigt am</th>
                            <th>Geändert</th>
                        </tr>
                        <?php
foreach ($erledigte_tasks as $erledigte_tasks2){?>
                        <tr>
                            <td><input type='checkbox'
                                    onclick="getId_for_erledigt(<?= $erledigte_tasks2[' NoteId']; ?>)"
                                    class='erledigte_tasks'></td>
                            <td class='erledigt_titel'><del><?= $erledigte_tasks2['titel'];?></del></td>
                            <td class='erledigt_notiz'><del><?= $erledigte_tasks2['notiz']?></del></td>
                            <td class='erledigt_datum'><del><?= $erledigte_tasks2['date_to_complete'];?></del>
                            </td>
                            <td class='erledigt_priority'><del><?= $erledigte_tasks2['prioritaet'];?></del></td>
                            <td class='erledigt_completed_at'>
                                <del><?= $erledigte_tasks2['date_when_completed']?></del>
                            </td>
                            <td class='erledigt_lastchange'><del><?= $erledigte_tasks2['last_change'];?></del>
                            </td>
                        </tr>
                        <?php }?>

                    </table><br>
                    <button id='deleteAllErledigteTasks' onclick='deleteAll()'><i class='fas fa-trash-alt'></i> Delete
                        all</button>
                    <?php } }
                else{ ?>
                    <h1 style='color: red'>Es wurde noch keine Aufgabe hinzugefügt</h1>
                    <?php }?>
                </main>
            </td>
            <td></td>
        </tr>
    </table>

    <script>
    function openBearbeiten() {
        console.log(<?= $changeId_offenEinzel; ?>);
        showEditPage(<?= $changeId_offen; ?>)
        editmodal.style.display = 'block';
    }
    </script>


    <?php
    include("editNote.view.php");
    include("addNote.view.php");
    include("addNote.view.php");
    include("realyDelete.view.php");
    ?>
</body>

</html>