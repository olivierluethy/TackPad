// Complete / reopen tasks without a full page reload. The selection is read
// fresh from the DOM at click time and guarded, so the action can never run
// against an empty or stale selection. On success the affected rows are moved
// between the open and completed tables in place.

function erledigt() {
  var ids = getSelectedIds("offene_tasks");
  if (ids.length === 0) {
    return;
  }
  postCompletion("erledigt", ids, true, "Marked as done.");
}

function undone() {
  var ids = getSelectedIds("erledigte_tasks");
  if (ids.length === 0) {
    return;
  }
  postCompletion("unerledigt", ids, false, "Moved back to open.");
}

function postCompletion(url, ids, toCompleted, successMessage) {
  $.ajax({
    type: "POST",
    url: url,
    data: { id: ids.join(",") },
    dataType: "json",
    success: function (res) {
      if (!res || !res.success || !res.updated) {
        window.TackpadToast.error(
          (res && res.errors && res.errors[0]) || "Could not update the task."
        );
        return;
      }

      // Index the server's per-task timestamps so each row shows the exact
      // completion time the server recorded.
      var byId = {};
      res.updated.forEach(function (u) {
        byId[String(u.id)] = u;
      });

      res.updated.forEach(function (u) {
        moveTaskRow(u.id, toCompleted, u.completed_at, u.last_change);
      });

      updateToolbar();
      refreshTaskCounts();
      showTaskTab(toCompleted ? "completed" : "open");
      window.TackpadToast.success(successMessage);
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      window.TackpadToast.error("Something went wrong while updating the task.");
    },
  });
}
