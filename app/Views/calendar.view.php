<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TackPad &middot; Calendar</title>
    <link rel="stylesheet" type="text/css" href="public/css/tackpad.css">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- FullCalendar v6 (single global bundle: dayGrid + timeGrid + interaction) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script defer src="public/js/toast.js"></script>
    <script defer src="public/js/taskStatus.js"></script>
    <script defer src="public/js/tackpad.js"></script>
    <script defer src="public/js/profile.js"></script>
    <script defer src="public/js/calendar.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>

<body class="on-dark">
    <?php $sidebarActive = 'calendar'; include __DIR__ . '/partials/sidebar.view.php'; ?>

    <main class="calendar-main">
        <header class="calendar-header">
            <h1>Calendar</h1>
            <div class="calendar-actions">
                <a class="calendar-export" href="calendar.ics"
                    title="Download an .ics file you can import into Google, Proton, Apple or Outlook calendars">
                    <i class="fas fa-file-export"></i>&nbsp;Export (.ics)
                </a>
                <a class="calendar-back" href="home"><i class="fas fa-arrow-left"></i>&nbsp;Back to tasks</a>
            </div>
        </header>

        <p class="calendar-hint">
            Drag an event to reschedule it &mdash; changes are saved automatically.
            Switch to <strong>Week</strong> or <strong>Day</strong> view to set a specific time.
        </p>

        <div id="calendar"></div>
    </main>

    <!-- Task detail modal: shown when a calendar event is clicked. -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeEventModal()">&times;</span>
                <h2 id="event_title">Task</h2>
            </div>
            <div class="modal-body">
                <dl class="event-details">
                    <dt>Date &amp; time</dt>
                    <dd id="event_when">&mdash;</dd>
                    <dt>Description</dt>
                    <dd id="event_note">&mdash;</dd>
                    <dt>Priority</dt>
                    <dd id="event_priority">&mdash;</dd>
                    <dt>Status</dt>
                    <dd id="event_status">&mdash;</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="btn-white btn-sm" type="button" onclick="closeEventModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/profile.view.php'; ?>
</body>

</html>
