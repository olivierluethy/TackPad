<?php
// Funktion zur Entschlüsselung
function decrypt($data, $key, $iv)
{
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
    }
    return $decrypted;
}

/*
 * Decrypt + normalise every task once, then sort by priority (0 = most urgent
 * first) and, within the same priority, by due date (earliest first). Sorting
 * has to happen here, in PHP after decryption, because priority and date are
 * stored encrypted and therefore cannot be ordered by the SQL query. The same
 * ordering is mirrored client-side (insertTaskRowSorted in tackpad.js) so rows
 * added after a create/edit land in the right place without a reload.
 */
$normalized_tasks = [];
foreach ($alle_tasks as $task) {
    $iv = base64_decode($task['iv']);
    $normalized_tasks[] = [
        'id'                  => $task['NoteId'],
        'titel'               => decrypt($task['titel'], $encryption_key, $iv),
        'notiz'               => decrypt($task['notiz'], $encryption_key, $iv),
        'prioritaet'          => decrypt($task['prioritaet'], $encryption_key, $iv),
        'status'              => decrypt($task['status'], $encryption_key, $iv),
        'date_to_complete'    => decrypt($task['date_to_complete'], $encryption_key, $iv),
        // date_when_completed is stored as a raw unix timestamp (see Notiz::istErledigt)
        'date_when_completed' => $task['date_when_completed'] !== null
            ? decrypt($task['date_when_completed'], $encryption_key, $iv)
            : '',
        // Plain completion timestamp (preferred); legacy encrypted value above
        // is kept only as a fallback for tasks completed before this column.
        'completed_at'        => $task['completed_at'] ?? '',
        'last_change'         => $task['last_change'],
        // "shared" is plain metadata (a boolean flag), not encrypted content.
        'shared'              => !empty($task['shared']),
    ];
}

$sortByPriorityThenDate = function (array $a, array $b) {
    $priorityComparison = (int) $a['prioritaet'] <=> (int) $b['prioritaet'];
    if ($priorityComparison !== 0) {
        return $priorityComparison; // lower number = more important, comes first
    }
    return strtotime($a['date_to_complete']) <=> strtotime($b['date_to_complete']);
};

$open_tasks = array_values(array_filter($normalized_tasks, fn($t) => $t['status'] === '0'));
$completed_tasks = array_values(array_filter($normalized_tasks, fn($t) => $t['status'] === '1'));
usort($open_tasks, $sortByPriorityThenDate);
usort($completed_tasks, $sortByPriorityThenDate);

$open_tasks_counter = count($open_tasks);
$done_tasks_counter = count($completed_tasks);
// Overdue = open tasks whose deadline has passed (a subset of the open list,
// surfaced as its own top-level tab so it is reachable without scrolling).
$overdue_tasks_counter = count(array_filter(
    $open_tasks,
    fn($t) => isTaskPastDue($t['date_to_complete'])
));
$has_open_tasks = $open_tasks_counter > 0;
$has_completed_tasks = $done_tasks_counter > 0;
$has_overdue_tasks = $overdue_tasks_counter > 0;

// Robust display for the secondary date columns. Some legacy values are stored
// as raw unix timestamps (completed/changed); others as datetime strings. This
// keeps every column readable instead of falling back to 1970 or ciphertext.
$displayDate = function ($value) {
    if ($value === null || trim((string) $value) === '') {
        return '&mdash;';
    }
    if (is_numeric($value)) {
        return date('j M Y, H:i', (int) $value);
    }
    return strtotime($value) === false ? '&mdash;' : formatTaskDate($value);
};
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>TackPad</title>
    <link rel="stylesheet" type="text/css" href="public/css/tackpad.css">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script defer src="public/js/toast.js"></script>
    <script defer src="public/js/taskStatus.js"></script>
    <script defer src="public/js/tackpad.js"></script>
    <script defer src="public/js/deleteNote.js"></script>
    <script defer src="public/js/createNote.js"></script>
    <script defer src="public/js/doneNote.js"></script>
    <script defer src="public/js/editNote.js"></script>
    <script defer src="public/js/shareNote.js"></script>
    <script defer src="public/js/profile.js"></script>
    <script defer src="public/js/modal.js"></script>
    <script defer src="public/js/inputValidation.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>

<body class="on-dark">
    <?php $sidebarActive = 'tasks'; include __DIR__ . '/partials/sidebar.view.php'; ?>

    <main class="pt-14 px-3">
        <?php if (count($normalized_tasks) > 0): ?>
            <div class='options-bar'>
                <button class="btn-white" onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add</button>
                <button class="btn-white" id='bearbeiten' style="display:none" onclick='openBearbeiten()' title="Edit your task"><i
                        class='fas fa-edit'></i>&nbsp;Edit</button>
                <button class="btn-white" id='loeschen' style="display:none" onclick='realyDeleteNote()' title="Delete your task"><i
                        class='fas fa-trash'></i>&nbsp;Delete</button>
                <button class="btn-white" id='erledigt' style="display:none" onclick='erledigt()' title="Mark your task as done"><i
                        class='fas fa-check'></i>&nbsp;Done</button>
                <button class="btn-white" id='undo' style="display:none" title="Undo your task if you haven't finished it yet" onclick='undone()'><i
                        class='fas fa-undo'></i>&nbsp;Undo</button>
                <button class="btn-white" id='freigeben' style="display:none" title="Share this task with another TackPad user" onclick='openShareModal()'><i
                        class='fas fa-share'></i>&nbsp;Share</button>
                <button class="btn-white" id='deleteAllErledigteTasks' style="display:none" title="Delete all your finished tasks" onclick='realyDeleteNote()'><i
                        class='fas fa-trash-alt'></i>&nbsp;Delete all</button>
                <button class="btn-white" id='deleteAllOffeneTasks' style="display:none" title="Delete all your open tasks" onclick='realyDeleteNote()'><i
                        class='fas fa-trash-alt'></i>&nbsp;Delete all</button>
            </div>

            <!-- Top navigation tabs: switch between the two task lists without
                 scrolling. The counts are kept live by refreshTaskCounts(). -->
            <nav class="task-tabs" role="tablist">
                <button type="button" class="task-tab active" data-tab="open" onclick="showTaskTab('open')">
                    Open (<span id="open-count"><?= $open_tasks_counter ?></span>)
                </button>
                <button type="button" class="task-tab" data-tab="overdue" onclick="showTaskTab('overdue')">
                    Overdue (<span id="overdue-count"><?= $overdue_tasks_counter ?></span>)
                </button>
                <button type="button" class="task-tab" data-tab="completed" onclick="showTaskTab('completed')">
                    Completed (<span id="done-count"><?= $done_tasks_counter ?></span>)
                </button>
            </nav>

            <!-- Container für offene Aufgaben -->
            <section class="task-panel" id="panel-open">
                <table class="task-table" id="open-tasks-container">
                    <tr>
                        <th><input id='checkAllOffeneTasks' type='checkbox' onclick='checkAllOffeneTasks(this)'
                                title='Select All'></th>
                        <th>Title</th>
                        <th>Task</th>
                        <th>Date</th>
                        <th>Priority</th>
                        <th>Changed</th>
                    </tr>
                    <?php foreach ($open_tasks as $task):
                        $status_class = taskStatusClass(false, isTaskPastDue($task['date_to_complete']));
                        ?>
                        <tr class="task-row <?= $status_class ?><?= $task['shared'] ? ' task-row--shared' : '' ?>"
                            data-id="<?= htmlspecialchars($task['id']); ?>"
                            data-titel="<?= htmlspecialchars($task['titel']); ?>"
                            data-aufgabe="<?= htmlspecialchars($task['notiz']); ?>"
                            data-datum="<?= htmlspecialchars($task['date_to_complete']); ?>"
                            data-priority="<?= htmlspecialchars($task['prioritaet']); ?>"
                            data-shared="<?= $task['shared'] ? '1' : '0' ?>">
                            <td>
                                <input type='checkbox' data-id="<?= htmlspecialchars($task['id']); ?>"
                                    onclick="getId_for_offen()" class='offene_tasks'>
                            </td>
                            <td class="cell-title">
                                <span class="cell-title-text"><?= htmlspecialchars($task['titel']); ?></span>
                                <i class="fas fa-share-alt task-shared-badge" title="Shared with another user"
                                    <?= $task['shared'] ? '' : 'hidden' ?>></i>
                            </td>
                            <td class="cell-task"><?= htmlspecialchars($task['notiz']); ?></td>
                            <td class="cell-date"><?= formatTaskDate($task['date_to_complete']); ?></td>
                            <td class="cell-priority"><?= htmlspecialchars(priorityLabel($task['prioritaet'])); ?></td>
                            <td><?= $displayDate($task['last_change']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="task-empty" id="open-empty" <?= $has_open_tasks ? 'hidden' : '' ?>>No open tasks &mdash; nice work!</p>
                <p class="task-empty" id="overdue-empty" hidden>No overdue tasks &mdash; you're on top of things.</p>
            </section>

            <section class="task-panel" id="panel-completed" hidden>
                <table class="task-table" id="completed-tasks-container">
                    <tr>
                        <th><input id='checkAllErledigteTasks' type='checkbox' onclick='checkAllErledigteTasks(this)'
                                title='Select All'></th>
                        <th>Title</th>
                        <th>Task</th>
                        <th>Date</th>
                        <th>Priority</th>
                        <th>Completed on</th>
                        <th>Changed</th>
                    </tr>
                    <?php foreach ($completed_tasks as $task): ?>
                        <tr class="task-row <?= taskStatusClass(true, false) ?> erledigt"
                            data-id="<?= htmlspecialchars($task['id']); ?>"
                            data-titel="<?= htmlspecialchars($task['titel']); ?>"
                            data-aufgabe="<?= htmlspecialchars($task['notiz']); ?>"
                            data-datum="<?= htmlspecialchars($task['date_to_complete']); ?>"
                            data-priority="<?= htmlspecialchars($task['prioritaet']); ?>"
                            data-shared="<?= $task['shared'] ? '1' : '0' ?>">
                            <td>
                                <input type='checkbox' data-id="<?= htmlspecialchars($task['id']); ?>"
                                    onclick="getId_for_erledigt()" class='erledigte_tasks'>
                            </td>
                            <td class="cell-title">
                                <span class="cell-title-text"><?= htmlspecialchars($task['titel']); ?></span>
                                <i class="fas fa-share-alt task-shared-badge" title="Shared with another user"
                                    <?= $task['shared'] ? '' : 'hidden' ?>></i>
                            </td>
                            <td class="cell-task"><?= htmlspecialchars($task['notiz']); ?></td>
                            <td class="cell-date"><?= formatTaskDate($task['date_to_complete']); ?></td>
                            <td class="cell-priority"><?= htmlspecialchars(priorityLabel($task['prioritaet'])); ?></td>
                            <td class="cell-completed"><?= $displayDate($task['completed_at'] !== '' ? $task['completed_at'] : $task['date_when_completed']); ?></td>
                            <td><?= $displayDate($task['last_change']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="task-empty" id="completed-empty" <?= $has_completed_tasks ? 'hidden' : '' ?>>No completed tasks yet.</p>
            </section>

        <?php else: ?>
            <div class="no-data">
                <h1>No tasks added yet</h1>
                <button class="btn-white" title="Add a task" onclick='displayModal()'><i class='fas fa-plus'></i>&nbsp;Add task</button>
            </div>
        <?php endif; ?>
    </main>

    <!-- Include modals and additional views -->
    <?php
    include __DIR__ . "/editNote.view.php";
    include __DIR__ . "/addNote.view.php";
    include __DIR__ . "/reallyDelete.view.php";
    include __DIR__ . "/shareNote.view.php";
    include __DIR__ . "/profile.view.php";
    ?>
</body>

</html>
