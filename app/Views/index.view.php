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
        <h2>Hello <?= $username ?>!</h2>
        <a href="logout"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
    </div>
    <span class='navi' onclick="openNav()">&#9776;</span>

    <table>
        <tr>
            <td></td>
            <td>
                <main>
                    <?php
                    // Check if there are any tasks
                    if (count($alle_tasks) > 0) {?>
                    <div class='options'>
                        <button onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add</button>
                        <button id='bearbeiten' onclick='openBearbeiten()' title="Edit your task"><i class='fas fa-edit'></i>&nbsp;Edit</button>
                        <button id='loeschen' onclick='realyDeleteNote()' title="Delete your task"><i class='fas fa-trash'></i>&nbsp;Delete</button>
                        <button id='erledigt' onclick='erledigt()' title="Mark your task as done"><i class='fas fa-check'></i>&nbsp;Done</button>
                        <button id='freigeben' title="Release your task to someone else"><i class='fas fa-share'></i>&nbsp;Release</button>

                        <button id='deleteAllErledigteTasks' title="Delete all your finished tasks" onclick='deleteAllDone()'><i class='fas fa-trash-alt'></i> Delete all</button>
                        <button id='deleteAllOffeneTasks' title="Delete all your open tasks" onclick='deleteAllOpen()'><i class='fas fa-trash-alt'></i> Delete all</button>
                    </div>

                    <?php
                    // Display open tasks if there are any
                    if ($anzahl_offen > 0) {
                    ?>
                    <h1>Tasks</h1>
                    <p>Open Tasks (<?= $anzahl_offen ?>)</p>
                    <table>
                        <tr>
                            <th><input id='checkAllOffeneTasks' type='checkbox' onclick='checkAllOffeneTasks(this)' title='Select All'></th>
                            <th>Title</th>
                            <th>Task</th>
                            <th>Date</th>
                            <th>Priority</th>
                            <th>Changed</th>
                        </tr>
                        <?php
                        // Display not too late open tasks
                        foreach ($nicht_zu_spaet_offene_tasks as $task) { ?>
                        <tr class="aufgabe_nicht_zu_spaet">
                            <td style='background-color:lightgreen;'><input type='checkbox' onclick="getId_for_offen(<?= $task['NoteId']; ?>)" class='offene_tasks'></td>
                            <td style='background-color:lightgreen;'><?= $task['titel']; ?></td>
                            <td style='background-color:lightgreen;'><?= $task['notiz']; ?></td>
                            <td style='background-color:lightgreen;'><?= date('d.m.Y', strtotime($task['date_to_complete'])); ?></td>
                            <td style='background-color:lightgreen;'><?= $task['prioritaet']; ?></td>
                            <td style='background-color:lightgreen;'><?= $task['last_change']; ?></td>
                        </tr>
                        <?php } 
                        // Display late open tasks
                        foreach ($zu_spaet_offene_tasks as $task) { ?>
                        <tr class='aufgabe_zu_spaet'>
                            <td style="background-color:lightcoral;"><input type="checkbox" onclick="getId_for_offen(<?= $task['NoteId']; ?>)" class='offene_tasks'></td>
                            <td style='background-color:lightcoral;'><?= $task['titel']; ?></td>
                            <td style='background-color:lightcoral;'><?= $task['notiz']; ?></td>
                            <td style='background-color:lightcoral;'><?= date('d.m.Y', strtotime($task['date_to_complete'])); ?></td>
                            <td style='background-color:lightcoral;'><?= $task['prioritaet']; ?></td>
                            <td style='background-color:lightcoral;'><?= $task['last_change']; ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <br>
                    <?php } 
                    // Display completed tasks if there are any
                    if (count($erledigte_tasks) > 0) { ?>
                    <p>Done Tasks (<?= count($erledigte_tasks); ?>)</p>
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
                        <?php
                        // Display completed tasks
                        foreach ($erledigte_tasks as $task) { ?>
                        <tr>
                            <td><input type='checkbox' onclick="getId_for_erledigt(<?= $task['NoteId']; ?>)" class='erledigte_tasks'></td>
                            <td class='erledigt_titel'><del><?= $task['titel']; ?></del></td>
                            <td class='erledigt_notiz'><del><?= $task['notiz']; ?></del></td>
                            <td class='erledigt_datum'><del><?= date('d.m.Y', strtotime($task['date_to_complete'])); ?></del></td>
                            <td class='erledigt_priority'><del><?= $task['prioritaet']; ?></del></td>
                            <td class='erledigt_completed_at'><del><?= $task['date_when_completed']; ?></del></td>
                            <td class='erledigt_lastchange'><del><?= $task['last_change']; ?></del></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <br>
                    <?php } 
                    // Display message if no tasks are available
                    } else { ?>
                    <div class="noData">
                    <h1>No tasks added yet</h1>
                    <button title="Add a task" onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add task</button>
                    </div>
                    <?php } ?>
                </main>
            </td>
            <td></td>
        </tr>
    </table>

    <!-- Include modals and additional views -->
    <?php
    include("editNote.view.php");
    include("addNote.view.php");
    include("reallyDelete.view.php");
    ?>
</body>

</html>
