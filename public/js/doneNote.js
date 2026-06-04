// Mark the currently selected OPEN tasks as done. The selection is read fresh
// from the DOM at click time and guarded, so the action can never run against
// an empty or stale selection.
function erledigt() {
  var ids = getSelectedIds("offene_tasks");
  if (ids.length === 0) {
    return;
  }
  location.href = "erledigt?id=" + ids.join(",");
}

// Move the currently selected COMPLETED tasks back to open. Same guarantee:
// derived from the live selection, no-op when nothing is selected.
function undone() {
  var ids = getSelectedIds("erledigte_tasks");
  if (ids.length === 0) {
    return;
  }
  location.href = "unerledigt?id=" + ids.join(",");
}
