<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>TackPad &middot; Calendar</title>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/nav.css">
    <link rel="stylesheet" type="text/css" href="public/css/calendar.css">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <meta name="author" content="Olivier Luethy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- FullCalendar v6 (single global bundle: dayGrid + timeGrid + interaction) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script defer src="public/js/taskStatus.js"></script>
    <script defer src="public/js/tackpad.js"></script>
    <script defer src="public/js/calendar.js"></script>
</head>

<body>
    <!-- Sidebar Navigation -->
    <div id="mySidenav" class="sidenav">
        <a class="closebtn" onclick="closeNav()">&times;</a>
        <img src="assets/icon.png" alt="">
        <h1>TackPad</h1>
        <h2>Hello <?= htmlspecialchars($username); ?>!</h2>
        <a href="home"><i class="fas fa-list-ul"></i>&nbsp;Tasks</a>
        <a href="logout"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
    </div>
    <span class='navi' onclick="openNav()">&#9776;</span>

    <main class="calendar-main">
        <header class="calendar-header">
            <h1>Calendar</h1>
            <div class="calendar-actions">
                <a class="calendar-export" href="calendar.ics" title="Download an .ics file you can import into Google, Proton, Apple or Outlook calendars">
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

    <!-- Task detail modal: shown when a calendar event is clicked. Populated by
         showDetails() in calendar.js — replaces the old alert() popup. -->
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
                    <button class="verwerfen" type="button" onclick="closeEventModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
