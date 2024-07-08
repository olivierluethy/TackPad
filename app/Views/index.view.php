<?php
// Funktion zur Entschlüsselung
function decrypt($data, $key, $iv) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
    }
    return $decrypted;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script defer src="public/js/tackpad.js"></script>
    <script defer src="public/js/deleteNote.js"></script>
    <script defer src="public/js/doneNote.js"></script>
    <script defer src="public/js/editNote.js"></script>
    <script defer src="public/js/modal.js"></script>
    <script defer src="public/js/inputValidation.js"></script>
</head>

<body>
    <!-- Sidebar Navigation -->
    <div id="mySidenav" class="sidenav">
        <a class="closebtn" onclick="closeNav()">&times;</a>
        <img src="assets/icon.png" alt="">
        <h1>TackPad</h1>
        <h2>Hello <?= htmlspecialchars($username); ?>!</h2>
        <a href="logout"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
    </div>
    <span class='navi' onclick="openNav()">&#9776;</span>

    <main>
        <?php if (count($alle_tasks) > 0) : ?>
        <div class='options'>
            <button onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add</button>
            <button id='bearbeiten' onclick='openBearbeiten()' title="Edit your task"><i class='fas fa-edit'></i>&nbsp;Edit</button>
            <button id='loeschen' onclick='realyDeleteNote()' title="Delete your task"><i class='fas fa-trash'></i>&nbsp;Delete</button>
            <button id='erledigt' onclick='erledigt()' title="Mark your task as done"><i class='fas fa-check'></i>&nbsp;Done</button>
            <button id='undo' title="Undo your task if you haven't finished it yet" onclick='undone()'><i class='fas fa-undo'></i>&nbsp;Undo</button>
            <button id='freigeben' title="Release your task to someone else"><i class='fas fa-share'></i>&nbsp;Release</button>
            <button id='deleteAllErledigteTasks' title="Delete all your finished tasks" onclick='realyDeleteNote()'><i class='fas fa-trash-alt'></i> Delete all</button>
            <button id='deleteAllOffeneTasks' title="Delete all your open tasks" onclick='realyDeleteNote()'><i class='fas fa-trash-alt'></i> Delete all</button>
        </div>

        <?php 
        $has_open_tasks = false;
        $has_completed_tasks = false;

        // Überprüfung, ob es offene und/oder abgeschlossene Aufgaben gibt
        foreach ($alle_tasks as $task) {
            $decrypted_status = decrypt($task['status'], $encryption_key, base64_decode($task['iv']));
            if ($decrypted_status === '0') {
                $has_open_tasks = true;
            } elseif ($decrypted_status === '1') {
                $has_completed_tasks = true;
            }
        }
        ?>

        <?php if ($has_open_tasks) : ?>
        <h1>Open Tasks</h1>
        <table>
            <tr>
                <th><input id='checkAllOffeneTasks' type='checkbox' onclick='checkAllOffeneTasks(this)' title='Select All'></th>
                <th>Title</th>
                <th>Task</th>
                <th>Date</th>
                <th>Priority</th>
                <th>Changed</th>
            </tr>
            <?php foreach ($alle_tasks as $task) :
                $decrypted_status = decrypt($task['status'], $encryption_key, base64_decode($task['iv']));
                if ($decrypted_status === '0') :
                    $iv = base64_decode($task['iv']);
                    $decrypted_titel = decrypt($task['titel'], $encryption_key, $iv);
                    $decrypted_notiz = decrypt($task['notiz'], $encryption_key, $iv);
                    $decrypted_prioritaet = decrypt($task['prioritaet'], $encryption_key, $iv);
                    $decrypted_date_to_complete = decrypt($task['date_to_complete'], $encryption_key, $iv);
                    $is_past_due = strtotime($decrypted_date_to_complete) > time();
                    $row_class = $is_past_due ? 'zu_spaet' : 'nicht_zu_spaet';
                    $background_color = $is_past_due ? 'lightcoral' : 'lightgreen';
                    ?>
            <tr class="<?= $row_class ?>">
                <td style='background-color:<?= $background_color ?>;'>
                    <input type='checkbox' onclick="getId_for_offen(<?= htmlspecialchars($task['NoteId']); ?>)" class='offene_tasks'>
                </td>
                <td style='background-color:<?= $background_color ?>;'><?= htmlspecialchars($decrypted_titel); ?></td>
                <td style='background-color:<?= $background_color ?>;'><?= htmlspecialchars($decrypted_notiz); ?></td>
                <td style='background-color:<?= $background_color ?>;'><?= date('dS M Y', strtotime($decrypted_date_to_complete)); ?></td>
                <td style='background-color:<?= $background_color ?>;'><?= htmlspecialchars($decrypted_prioritaet); ?></td>
                <td style='background-color:<?= $background_color ?>;'><?= date('dS M Y', strtotime($task['last_change'])); ?></td>
            </tr>
            <?php endif; endforeach; ?>
        </table>
        <?php endif; ?>

        <?php if ($has_completed_tasks) : ?>
        <h1>Completed Tasks</h1>
        <table>
            <tr>
                <th><input id='checkAllErledigteTasks' type='checkbox' onclick='checkAllErledigteTasks(this)' title='Select All'></th>
                <th>Title</th>
                <th>Task</th>
                <th>Date</th>
                <th>Priority</th>
                <th>Completed on</th>
                <th>Changed</th>
            </tr>
            <?php foreach ($alle_tasks as $task) :
                $decrypted_status = decrypt($task['status'], $encryption_key, base64_decode($task['iv']));
                if ($decrypted_status === '1') :
                    $iv = base64_decode($task['iv']);
                    $decrypted_titel = decrypt($task['titel'], $encryption_key, $iv);
                    $decrypted_notiz = decrypt($task['notiz'], $encryption_key, $iv);
                    $decrypted_prioritaet = decrypt($task['prioritaet'], $encryption_key, $iv);
                    $decrypted_date_to_complete = decrypt($task['date_to_complete'], $encryption_key, $iv);
                    $decrypted_date_when_completed = decrypt($task['date_when_completed'], $encryption_key, $iv);
                    ?>
            <tr class="erledigt">
                <td style='background-color:lightgrey;'>
                    <input type='checkbox' onclick="getId_for_erledigt(<?= htmlspecialchars($task['NoteId']); ?>)" class='erledigte_tasks'>
                </td>
                <td style='background-color:lightgrey;'><del><?= htmlspecialchars($decrypted_titel); ?></del></td>
                <td style='background-color:lightgrey;'><del><?= htmlspecialchars($decrypted_notiz); ?></del></td>
                <td style='background-color:lightgrey;'><del><?= date('dS M Y', strtotime($decrypted_date_to_complete)); ?></del></td>
                <td style='background-color:lightgrey;'><del><?= htmlspecialchars($decrypted_prioritaet); ?></del></td>
                <td style='background-color:lightgrey;'><del><?= date('dS M Y', strtotime($decrypted_date_when_completed)); ?></del></td>
                <td style='background-color:lightgrey;'><del><?= date('dS M Y', strtotime($task['last_change'])); ?></del></td>
            </tr>
            <?php endif; endforeach; ?>
        </table>
        <?php endif; ?>

        <?php else : ?>
        <div class="noData">
            <h1>No tasks added yet</h1>
            <button title="Add a task" onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add task</button>
        </div>
        <?php endif; ?>
    </main>

    <!-- Include modals and additional views -->
    <?php
    include("editNote.view.php");
    include("addNote.view.php");
    include("reallyDelete.view.php");
    ?>
</body>

</html>
