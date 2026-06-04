// Edit Modal
var editModal = document.getElementById("editModal");
setupModalClose(editModal, 0);

let editmodal = document.getElementById("editModal");

function openBearbeiten() {
  // Editing targets a single task. Derive the selection live from the DOM so it
  // is always current, and guard against zero/multiple selections.
  var ids = getSelectedIds("offene_tasks").concat(
    getSelectedIds("erledigte_tasks")
  );

  if (ids.length !== 1) {
    alert("Please select exactly one task to edit.");
    return;
  }

  openEditModal(ids[0]);
}

// Populate the edit modal from the values already shown on the page.
// No background fetch: the model reflects exactly what the user sees in the DOM.
function openEditModal(taskId) {
  var row = document.querySelector('tr[data-id="' + taskId + '"]');
  if (!row) {
    alert("The selected task could not be found on this page.");
    return;
  }

  // Take the current values straight from the task row's data attributes.
  $('#editModal input[name="titel"]').val(row.dataset.titel);
  $('#editModal input[name="aufgabe"]').val(row.dataset.aufgabe);
  $("#datum_edit").val(row.dataset.datum);
  $("#priority_edit").val(parseInt(row.dataset.priority, 10));

  // Point the form at the edit endpoint for this task.
  $("#editForm").attr("action", "edit?id=" + taskId);

  $("#editModal").show();
}

$(document).ready(function () {
  // Close modal when the close button is clicked
  $(".close").click(function () {
    $("#editModal").hide();
  });

  // Close modal when clicking outside of the modal
  $(window).click(function (event) {
    if (event.target.id == "editModal") {
      $("#editModal").hide();
    }
  });
});

function editNote(id) {
  location.href = "edit?id=" + id;
}
