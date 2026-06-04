// Single client-side place that maps a task's semantic state to its row status
// class. Mirrors taskStatusClass() in core/helpers.php so server-rendered and
// client-rendered rows agree. The class names map to the colour tokens defined
// once in public/css/style.scss (.task-row--*); colours never live here.
function taskStatusClass(isCompleted, isPastDue) {
  if (isCompleted) {
    return "task-row--completed";
  }

  return isPastDue ? "task-row--overdue" : "task-row--on-time";
}

// Short month names — fixed English list so the output matches PHP's date('M'),
// which is locale-independent. Used by formatTaskDate below.
var TASK_MONTHS = [
  "Jan", "Feb", "Mar", "Apr", "May", "Jun",
  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
];

// Parse a stored due-date ("YYYY-MM-DD" or "YYYY-MM-DD HH:MM[:SS]", with either
// a space or a "T" separator) into a local Date. Returns null if unparseable.
function parseTaskDate(raw) {
  if (!raw) {
    return null;
  }
  var m = String(raw)
    .trim()
    .match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/);
  if (!m) {
    var fallback = new Date(raw);
    return isNaN(fallback.getTime()) ? null : fallback;
  }
  return new Date(
    +m[1],
    +m[2] - 1,
    +m[3],
    m[4] ? +m[4] : 0,
    m[5] ? +m[5] : 0,
    0
  );
}

// Twin of taskHasTime() in core/helpers.php: a due-date carries a time when its
// string is longer than the bare "YYYY-MM-DD" (10 chars).
function taskHasTime(raw) {
  return String(raw || "").trim().length > 10;
}

// Twin of formatTaskDate() in core/helpers.php — MUST produce identical output:
// "4 Jun 2026" for date-only, "4 Jun 2026, 14:30" when a time is present.
function formatTaskDate(raw) {
  var d = parseTaskDate(raw);
  if (!d) {
    return raw ? String(raw) : "";
  }
  var base =
    d.getDate() + " " + TASK_MONTHS[d.getMonth()] + " " + d.getFullYear();
  if (!taskHasTime(raw)) {
    return base;
  }
  var hh = ("0" + d.getHours()).slice(-2);
  var mm = ("0" + d.getMinutes()).slice(-2);
  return base + ", " + hh + ":" + mm;
}

// Twin of isTaskPastDue() in core/helpers.php. Date-only deadlines are due at
// the end of their day; timed deadlines at the exact instant. Keeping this in
// lock-step with the server means a row never changes colour on reload.
function isTaskPastDue(raw) {
  var d = parseTaskDate(raw);
  if (!d) {
    return false;
  }
  if (!taskHasTime(raw)) {
    d.setHours(23, 59, 59, 0);
  }
  return d.getTime() < Date.now();
}

// Human-readable label for a stored priority value ("0".."4"). Twin of
// priorityLabel() in core/helpers.php so wording matches across views.
var TASK_PRIORITY_LABELS = {
  0: "Incredibly important",
  1: "Very important",
  2: "Important",
  3: "Moderately important",
  4: "Not important",
};

function priorityLabel(priority) {
  var key = parseInt(priority, 10);
  return TASK_PRIORITY_LABELS.hasOwnProperty(key)
    ? TASK_PRIORITY_LABELS[key]
    : String(priority);
}
