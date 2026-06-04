<?php
/**
 * Safely escapes a string for HTML output.
 *
 * This function converts special characters to HTML entities to prevent XSS attacks.
 * By default, it uses ENT_QUOTES and UTF-8 encoding.
 *
 * @param string $value The string to be escaped.
 * @param int $flags Optional. A bitmask of one or more of the following flags, combined using the bitwise OR (|) operator:
 *                   - ENT_COMPAT: Will convert double-quotes and leave single-quotes alone.
 *                   - ENT_QUOTES: Will convert both double and single quotes.
 *                   - ENT_NOQUOTES: Will leave both double and single quotes unconverted.
 *                   - ENT_HTML401, ENT_HTML5, ENT_XML1, ENT_XHTML: Handle quotes differently according to different standards.
 *                   Default is ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401.
 * @param string $encoding Optional. An optional argument defining the encoding used in the conversion.
 *                         Default is 'UTF-8'.
 * @param bool $double_encode Optional. A boolean parameter that determines whether to convert existing html entities to html entities again.
 *                            Default is false.
 * @return string The escaped string.
 */
function e(string $value, int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, string $encoding = 'UTF-8', bool $double_encode = false): string
{
    return htmlspecialchars($value, $flags, $encoding, $double_encode);
}

/**
 * Nutze diese Funktion um auf einen POST-Wert
 * zuzugreifen.
 */
function post(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

/**
 * Maps a task's semantic state to its row status class.
 *
 * This is the single server-side place that turns business state (is the task
 * completed? is it past due?) into a presentation hook. The class names map to
 * the colour tokens defined once in public/css/style.scss (.task-row--*), so the
 * actual colours live in CSS, not here. The JS equivalent in
 * public/js/taskStatus.js mirrors this mapping for client-rendered rows.
 *
 * @param bool $isCompleted Whether the task has been marked as done.
 * @param bool $isPastDue    Whether the task's due date is in the past.
 * @return string The status modifier class for the task <tr>.
 */
function taskStatusClass(bool $isCompleted, bool $isPastDue): string
{
    if ($isCompleted) {
        return 'task-row--completed';
    }

    return $isPastDue ? 'task-row--overdue' : 'task-row--on-time';
}

/**
 * Whether a stored due-date carries a wall-clock time.
 *
 * Due dates are stored either date-only ("YYYY-MM-DD", 10 chars) or as a
 * datetime ("YYYY-MM-DD HH:MM[:SS]"). The length is the single discriminator
 * used by every formatter/overdue check on both server and client (the JS twin
 * lives in public/js/taskStatus.js — keep them in lock-step).
 *
 * @param string $raw The stored due-date.
 * @return bool True when a time component is present.
 */
function taskHasTime(string $raw): bool
{
    return strlen(trim($raw)) > 10;
}

/**
 * Decides whether a task is past due, treating date-only and timed deadlines
 * correctly so a task due *today* never flips to overdue prematurely:
 *   - date-only ("YYYY-MM-DD"): due at the very end of that day (23:59:59), so
 *     it stays "on time" all day and only goes overdue tomorrow.
 *   - timed ("YYYY-MM-DD HH:MM"): due at exactly that instant, so a deadline
 *     later today is still on time until the clock passes it.
 *
 * Mirrored by isTaskPastDue() in public/js/taskStatus.js for client-rendered
 * rows; both must agree or a row's colour would change on reload.
 *
 * @param string $raw The stored due-date.
 * @return bool True when the deadline has passed.
 */
function isTaskPastDue(string $raw): bool
{
    $raw = trim($raw);
    if ($raw === '') {
        return false;
    }

    $timestamp = strtotime($raw);
    if ($timestamp === false) {
        return false;
    }

    if (!taskHasTime($raw)) {
        // Date-only deadline: only overdue once the whole day has elapsed.
        $timestamp = strtotime(date('Y-m-d', $timestamp) . ' 23:59:59');
    }

    return $timestamp < time();
}

/**
 * Formats a stored due-date for display in one consistent style used across the
 * whole UI: "j M Y" for date-only ("4 Jun 2026") and "j M Y, H:i" when a time
 * is present ("4 Jun 2026, 14:30"). The JS twin formatTaskDate() in
 * public/js/taskStatus.js produces byte-identical output so server-rendered and
 * dynamically-inserted rows look the same without a reload.
 *
 * @param string $raw The stored due-date (or any strtotime-parsable string).
 * @return string The formatted date, or '' when the input is empty/invalid.
 */
function formatTaskDate(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }

    $timestamp = strtotime($raw);
    if ($timestamp === false) {
        return $raw;
    }

    return taskHasTime($raw)
        ? date('j M Y, H:i', $timestamp)
        : date('j M Y', $timestamp);
}

/**
 * Combines a date input ("YYYY-MM-DD") and an optional time input ("HH:MM")
 * into the canonical stored due-date. With no time it returns the bare date,
 * which keeps the task an all-day deadline; with a time it returns
 * "YYYY-MM-DD HH:MM". This is the single place add/edit funnel through so the
 * stored shape stays consistent with taskHasTime()/formatTaskDate().
 *
 * @param string $date Date portion ("YYYY-MM-DD").
 * @param string $time Optional time portion ("HH:MM").
 * @return string The combined due-date, or '' when no date was given.
 */
function combineDateTime(string $date, string $time): string
{
    $date = trim($date);
    $time = trim($time);

    if ($date === '') {
        return '';
    }

    return $time === '' ? $date : $date . ' ' . substr($time, 0, 5);
}

/**
 * Human-readable label for a stored priority value ("0".."4"). Single source of
 * truth shared by the list, the calendar detail modal (via its JS twin) and any
 * other surface, so the wording never drifts between views.
 *
 * @param string|int $priority The stored priority value.
 * @return string The label, or the raw value if it is out of range.
 */
function priorityLabel($priority): string
{
    $labels = [
        '0' => 'Incredibly important',
        '1' => 'Very important',
        '2' => 'Important',
        '3' => 'Moderately important',
        '4' => 'Not important',
    ];

    $key = (string) $priority;
    return $labels[$key] ?? $key;
}

/**
 * Stellt eine Verbindung zur Datenbank her und gibt die
 * Datenbankverbindung als PDO zurück.
 */
$dbInstance = null;

function db(): PDO
{
    global $dbInstance;

    if ($dbInstance) {
        return $dbInstance;
    }

    try {
        $dbInstance = new PDO('mysql:host=127.0.0.1;dbname=' . $db['name'], $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ]);
    } catch (PDOException $e) {
        die('Keine Verbindung zur Datenbank möglich: ' . $e->getMessage());
    }
}