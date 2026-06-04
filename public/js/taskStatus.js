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
