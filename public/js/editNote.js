// Edit Modal
var editModal = document.getElementById("editModal");
setupModalClose(editModal, 0);

let editmodal = document.getElementById("editModal");

function openBearbeiten() {
  let OpenIds = changeId_offen.join(",");
  let DoneIds = changeId_erledigt.join(",");

  if (OpenIds.length > 0) {
    openEditModal(OpenIds);
  } else if (DoneIds.length > 0) {
    openEditModal(DoneIds);
  } else {
    alert("Please select at least one task!");
    return;
  }
  editmodal.style.display = "block";
}

// JavaScript to handle edit button click
function openEditModal(taskId) {
  $.ajax({
    url: "getTaskData",
    type: "POST",
    data: { id: taskId },
    success: function (response) {
      var task = JSON.parse(response);
      $('#editModal input[name="titel"]').val(task.titel);
      $('#editModal input[name="aufgabe"]').val(task.notiz);
      $("#datum_edit").val(task.date_to_complete);

      // Convert task.prioritaet to an integer
      var priorityValue = parseInt(task.priority);

      // Set the value of the priority select field
      $("#priority_edit").val(priorityValue);
      $('#priority_edit option[value="' + task.prioritaet + '"]').prop(
        "selected",
        true
      );

      // Update the form action with the taskId
      $("#editForm").attr("action", "edit?id=" + task.id);

      $("#editModal").show();
    },
  });
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
