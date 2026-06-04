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
    el.style.display = visible ? "inline-block" : "none";
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
