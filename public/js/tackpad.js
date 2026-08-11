// Sidebar navigation — shared by every page that has the sidenav (task list and
// calendar both load this file), so the hamburger works everywhere.
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}

/* =========================================================================
   Task selection — single source of truth
   -------------------------------------------------------------------------
   The checked checkboxes ARE the selection state. There is deliberately no
   separate cached list of selected IDs: the selection is always derived from
   the DOM on demand, and every button's visibility is derived from that same
   state through the one updateToolbar() function below. Because there is only
   one source of truth, the UI, the selection, and the bulk actions can never
   drift apart (the previous bug: "Select All" updated the checkboxes but not a
   parallel IDs array, leaving stale selections behind).
   ========================================================================= */

var OPEN_CLASS = "offene_tasks";
var DONE_CLASS = "erledigte_tasks";

// Currently selected task IDs for one checkbox group, computed from the DOM.
// This is the only way any code should read the selection.
function getSelectedIds(className) {
  var boxes = document.getElementsByClassName(className);
  var ids = [];
  for (var i = 0; i < boxes.length; i++) {
    if (boxes[i].checked && boxes[i].dataset.id) {
      ids.push(boxes[i].dataset.id);
    }
  }
  return ids;
}

function setDisplay(id, visible) {
  var el = document.getElementById(id);
  if (el) {
    // inline-flex matches the .btn component (icon + label gap); the toggled
    // toolbar buttons start hidden via inline style in the markup.
    el.style.display = visible ? "inline-flex" : "none";
  }
}

// The ONE place that maps the current selection onto the toolbar and the
// "select all" checkboxes. Every selection change — individual click or
// "Select All" — funnels through here, so the UI is always a pure function of
// the current checkbox state.
function updateToolbar() {
  var openBoxes = document.getElementsByClassName(OPEN_CLASS);
  var doneBoxes = document.getElementsByClassName(DONE_CLASS);
  var openCount = getSelectedIds(OPEN_CLASS).length;
  var doneCount = getSelectedIds(DONE_CLASS).length;
  var total = openCount + doneCount;

  // Single-task actions: only meaningful when exactly one task is selected.
  setDisplay("bearbeiten", total === 1); // Edit
  setDisplay("loeschen", total === 1); // Delete
  setDisplay("freigeben", total === 1); // Release

  // Status actions operate only on their own group.
  setDisplay("erledigt", openCount >= 1); // mark open task(s) as done
  setDisplay("undo", doneCount >= 1); // mark completed task(s) as open again

  // Bulk-delete buttons appear when a whole group has a multi-selection.
  setDisplay("deleteAllOffeneTasks", openCount > 1);
  setDisplay("deleteAllErledigteTasks", doneCount > 1);

  syncSelectAll("checkAllOffeneTasks", openCount, openBoxes.length);
  syncSelectAll("checkAllErledigteTasks", doneCount, doneBoxes.length);
}

// Keep a "select all" checkbox in step with its group (checked when all are
// selected, indeterminate on a partial selection).
function syncSelectAll(id, count, total) {
  var box = document.getElementById(id);
  if (!box) {
    return;
  }
  box.checked = total > 0 && count === total;
  box.indeterminate = count > 0 && count < total;
}

// "Select All" for a group: set every checkbox, then re-derive the whole UI.
function checkAllOffeneTasks(source) {
  setAllChecked(OPEN_CLASS, source.checked);
  updateToolbar();
}

function checkAllErledigteTasks(source) {
  setAllChecked(DONE_CLASS, source.checked);
  updateToolbar();
}

function setAllChecked(className, checked) {
  var boxes = document.getElementsByClassName(className);
  for (var i = 0; i < boxes.length; i++) {
    boxes[i].checked = checked;
  }
}

// Individual checkbox handlers (invoked by each row's inline onclick, including
// rows added dynamically after creating a task). They store nothing — they just
// ask the toolbar to re-derive itself from the current checkbox state.
function getId_for_offen() {
  updateToolbar();
}

function getId_for_erledigt() {
  updateToolbar();
}

// Derive the toolbar from the actual checkbox state on load too, so that even
// if the browser restores checkboxes after navigation the UI stays consistent.
document.addEventListener("DOMContentLoaded", updateToolbar);

/* =========================================================================
   Top navigation tabs (Open / Completed)
   -------------------------------------------------------------------------
   Two panels (#panel-open, #panel-completed) are always present in the DOM;
   the tabs just toggle which one is visible, so the user can switch lists
   without scrolling. The visible tab is the single source of truth, mirrored
   onto the .active class and the [hidden] attribute of each panel.
   ========================================================================= */
function showTaskTab(which) {
  var tabs = document.getElementsByClassName("task-tab");
  for (var i = 0; i < tabs.length; i++) {
    var isActive = tabs[i].getAttribute("data-tab") === which;
    tabs[i].classList.toggle("active", isActive);
  }
  togglePanel("panel-open", which === "open");
  togglePanel("panel-completed", which === "completed");
}

function togglePanel(id, visible) {
  var panel = document.getElementById(id);
  if (panel) {
    panel.hidden = !visible;
  }
}

// On load, open the tab that actually has tasks (prefer Open). This keeps the
// first view useful even when one of the two lists is empty.
document.addEventListener("DOMContentLoaded", function () {
  if (!document.getElementById("panel-open")) {
    return; // not the task list page
  }
  var openCount = countTaskRows("open-tasks-container");
  showTaskTab(
    openCount === 0 && countTaskRows("completed-tasks-container") > 0
      ? "completed"
      : "open"
  );
  refreshTaskCounts();
});

/* =========================================================================
   Live counts + sorted insertion — keep the page accurate after AJAX changes
   (create / edit / delete) without a full reload.
   ========================================================================= */

// Number of real task rows in a table (excludes the header row).
function countTaskRows(tableId) {
  var table = document.getElementById(tableId);
  return table ? table.querySelectorAll("tr.task-row").length : 0;
}

// Recompute the tab counts and toggle each panel's empty-state message. Called
// after every AJAX mutation so the header always reflects reality.
function refreshTaskCounts() {
  var open = countTaskRows("open-tasks-container");
  var done = countTaskRows("completed-tasks-container");

  setText("open-count", open);
  setText("done-count", done);
  toggleHidden("open-empty", open > 0);
  toggleHidden("completed-empty", done > 0);
}

function setText(id, value) {
  var el = document.getElementById(id);
  if (el) {
    el.textContent = value;
  }
}

function toggleHidden(id, hide) {
  var el = document.getElementById(id);
  if (el) {
    el.hidden = hide;
  }
}

// Sort key comparison for two task rows, read from their data attributes.
// Mirrors the PHP ordering in index.view.php: priority ascending (0 = most
// urgent), then due date ascending (earliest first). parseTaskDate is defined
// in taskStatus.js, which loads before this file on the task list page.
function compareTaskRows(a, b) {
  var pa = parseInt(a.getAttribute("data-priority"), 10) || 0;
  var pb = parseInt(b.getAttribute("data-priority"), 10) || 0;
  if (pa !== pb) {
    return pa - pb;
  }
  var da = parseTaskDate(a.getAttribute("data-datum"));
  var db = parseTaskDate(b.getAttribute("data-datum"));
  return (da ? da.getTime() : 0) - (db ? db.getTime() : 0);
}

// Insert a task row into its table at the position the server-side sort would
// have placed it, so a created/edited task appears in the correct order without
// reloading. Falls back to appending when it sorts after everything else.
function insertTaskRowSorted(table, newRow) {
  var rows = table.querySelectorAll("tr.task-row");
  for (var i = 0; i < rows.length; i++) {
    if (compareTaskRows(newRow, rows[i]) < 0) {
      rows[i].parentNode.insertBefore(newRow, rows[i]);
      return;
    }
  }
  table.appendChild(newRow);
}

/* =========================================================================
   Row construction / update — one place that turns task data into a row, used
   by the create (build) and edit (update-in-place) flows. Values are written
   with textContent / setAttribute (never innerHTML) so user-supplied text can
   never inject markup. The row's status colour is derived from the same
   helpers as the server (taskStatusClass + isTaskPastDue in taskStatus.js).
   ========================================================================= */

// Sets a row's status class from its semantic state, preserving any non-status
// classes already on the row (task-row, erledigt, task-row--shared).
function applyRowStatus(row, isCompleted, dueRaw) {
  ["task-row--on-time", "task-row--overdue", "task-row--completed"].forEach(
    function (c) {
      row.classList.remove(c);
    }
  );
  row.classList.add(taskStatusClass(isCompleted, isTaskPastDue(dueRaw)));
}

// Build a brand-new OPEN task row (6 columns) matching the server layout.
function buildOpenTaskRow(data) {
  var row = document.createElement("tr");
  row.className = "task-row";
  applyRowStatus(row, false, data.datum);
  if (data.shared) {
    row.classList.add("task-row--shared");
  }
  row.setAttribute("data-id", data.id);
  row.setAttribute("data-titel", data.titel);
  row.setAttribute("data-aufgabe", data.aufgabe);
  row.setAttribute("data-datum", data.datum || "");
  row.setAttribute("data-priority", data.prioritaet);
  row.setAttribute("data-shared", data.shared ? "1" : "0");

  // Checkbox cell
  var tdCheck = document.createElement("td");
  var box = document.createElement("input");
  box.type = "checkbox";
  box.className = "offene_tasks";
  box.setAttribute("data-id", data.id);
  box.setAttribute("onclick", "getId_for_offen()");
  tdCheck.appendChild(box);
  row.appendChild(tdCheck);

  // Title cell (text + hidden share badge)
  var tdTitle = document.createElement("td");
  tdTitle.className = "cell-title";
  var titleText = document.createElement("span");
  titleText.className = "cell-title-text";
  titleText.textContent = data.titel;
  tdTitle.appendChild(titleText);
  var badge = document.createElement("i");
  badge.className = "fas fa-share-alt task-shared-badge";
  badge.title = "Shared with another user";
  badge.hidden = !data.shared;
  tdTitle.appendChild(document.createTextNode(" "));
  tdTitle.appendChild(badge);
  row.appendChild(tdTitle);

  row.appendChild(makeCell("cell-task", data.aufgabe));
  row.appendChild(makeCell("cell-date", formatTaskDate(data.datum)));
  row.appendChild(makeCell("cell-priority", priorityLabel(data.prioritaet)));
  row.appendChild(makeCell("", formatTaskDate(data.last_change)));

  return row;
}

function makeCell(className, text) {
  var td = document.createElement("td");
  if (className) {
    td.className = className;
  }
  td.textContent = text;
  return td;
}

// Build a COMPLETED task row (7 columns) matching the server layout, including
// the "Completed on" column. Used when a task is marked done without a reload.
function buildCompletedTaskRow(data) {
  var row = document.createElement("tr");
  row.className = "task-row erledigt";
  applyRowStatus(row, true, data.datum); // completed → grey
  if (data.shared) {
    row.classList.add("task-row--shared");
  }
  row.setAttribute("data-id", data.id);
  row.setAttribute("data-titel", data.titel);
  row.setAttribute("data-aufgabe", data.aufgabe);
  row.setAttribute("data-datum", data.datum || "");
  row.setAttribute("data-priority", data.prioritaet);
  row.setAttribute("data-shared", data.shared ? "1" : "0");

  var tdCheck = document.createElement("td");
  var box = document.createElement("input");
  box.type = "checkbox";
  box.className = "erledigte_tasks";
  box.setAttribute("data-id", data.id);
  box.setAttribute("onclick", "getId_for_erledigt()");
  tdCheck.appendChild(box);
  row.appendChild(tdCheck);

  var tdTitle = document.createElement("td");
  tdTitle.className = "cell-title";
  var titleText = document.createElement("span");
  titleText.className = "cell-title-text";
  titleText.textContent = data.titel;
  tdTitle.appendChild(titleText);
  var badge = document.createElement("i");
  badge.className = "fas fa-share-alt task-shared-badge";
  badge.title = "Shared with another user";
  badge.hidden = !data.shared;
  tdTitle.appendChild(document.createTextNode(" "));
  tdTitle.appendChild(badge);
  row.appendChild(tdTitle);

  row.appendChild(makeCell("cell-task", data.aufgabe));
  row.appendChild(makeCell("cell-date", formatTaskDate(data.datum)));
  row.appendChild(makeCell("cell-priority", priorityLabel(data.prioritaet)));
  row.appendChild(makeCell("cell-completed", formatTaskDate(data.completed_at)));
  row.appendChild(makeCell("", formatTaskDate(data.last_change)));

  return row;
}

// Move a task row between the open and completed tables in place (no reload),
// rebuilding it with the correct column layout for its new status.
function moveTaskRow(id, toCompleted, completedAt, lastChange) {
  var oldRow = document.querySelector('tr.task-row[data-id="' + id + '"]');
  if (!oldRow) {
    return;
  }
  var data = {
    id: id,
    titel: oldRow.dataset.titel,
    aufgabe: oldRow.dataset.aufgabe,
    datum: oldRow.dataset.datum,
    prioritaet: oldRow.dataset.priority,
    shared: oldRow.dataset.shared === "1",
    completed_at: completedAt || "",
    last_change: lastChange || "",
  };

  oldRow.parentNode.removeChild(oldRow);

  var target = document.getElementById(
    toCompleted ? "completed-tasks-container" : "open-tasks-container"
  );
  if (!target) {
    return;
  }
  var newRow = toCompleted
    ? buildCompletedTaskRow(data)
    : buildOpenTaskRow(data);
  insertTaskRowSorted(target, newRow);
}

// Update an existing row in place after an edit. Works for both open and
// completed rows because it only touches the columns common to both (title,
// task, date, priority) and the data attributes. The row's completed state is
// read from its own class so the colour stays correct.
function updateTaskRowCells(row, data) {
  row.setAttribute("data-titel", data.titel);
  row.setAttribute("data-aufgabe", data.aufgabe);
  row.setAttribute("data-datum", data.datum || "");
  row.setAttribute("data-priority", data.prioritaet);

  setCellText(row, ".cell-title-text", data.titel);
  setCellText(row, ".cell-task", data.aufgabe);
  setCellText(row, ".cell-date", formatTaskDate(data.datum));
  setCellText(row, ".cell-priority", priorityLabel(data.prioritaet));

  applyRowStatus(row, row.classList.contains("erledigt"), data.datum);
}

function setCellText(row, selector, text) {
  var el = row.querySelector(selector);
  if (el) {
    el.textContent = text;
  }
}
