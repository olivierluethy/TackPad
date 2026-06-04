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
  $("#priority_edit").val(parseInt(row.dataset.priority, 10));

  // Split the stored due-date back into its date and (optional) time inputs.
  var due = (row.dataset.datum || "").trim();
  $("#datum_edit").val(due.substring(0, 10));
  $("#zeit_edit").val(due.length > 10 ? due.substring(11, 16) : "");

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

  // Submit edits over AJAX and update the affected row in place — no reload —
  // so the due date and its formatting refresh immediately and the row is
  // re-sorted into its correct position.
  $("#editForm").submit(function (event) {
    event.preventDefault();

    var taskId = ($("#editForm").attr("action") || "").split("id=")[1];
    var row = document.querySelector('tr[data-id="' + taskId + '"]');

    var title = $("#titel_edit").val();
    var task = $("#aufgabe_edit").val();
    var priority = $("#priority_edit").val();
    var date = $("#datum_edit").val();
    var time = $("#zeit_edit").val();

    $.ajax({
      type: "POST",
      url: "edit?id=" + taskId,
      data: {
        titel: title,
        aufgabe: task,
        priority: priority,
        datum: date,
        zeit: time,
      },
      dataType: "json",
      success: function (response) {
        if (!response || !response.success) {
          alert(
            "Error: " + ((response && response.error) || "could not save.")
          );
          return;
        }

        if (row) {
          updateTaskRowCells(row, {
            titel: title,
            aufgabe: task,
            datum: response.task.datum,
            prioritaet: priority,
          });

          // Re-sort the row into place within its own table.
          var table = row.closest("table");
          if (table) {
            row.parentNode.removeChild(row);
            insertTaskRowSorted(table, row);
          }
          refreshTaskCounts();
        }

        $("#editModal").hide();
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        alert("An error occurred while saving the task.");
      },
    });
  });
});

function editNote(id) {
  location.href = "edit?id=" + id;
}
