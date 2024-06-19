// Edit Modal
var editModal = document.getElementById("editModal");
setupModalClose(editModal, 0);

let editmodal = document.getElementById("editModal");
function openBearbeiten() {
  editmodal.style.display = 'block';

  openEditModal(currentId);
}

// JavaScript to handle edit button click
function openEditModal(taskId) {
  $.ajax({
      url: 'getTaskData',
      type: 'POST',
      data: {id: taskId},
      success: function(response) {
        var task = JSON.parse(response);
        $('#editModal input[name="titel"]').val(task.titel);
        $('#editModal input[name="aufgabe"]').val(task.notiz);
        $('#datum_edit').val(task.date_to_complete);

        // Convert task.prioritaet to an integer
        var priorityValue = parseInt(task.prioritaet);

        // Set the value of the priority select field
        $('#priority_edit').val(priorityValue);
        $('#priority_edit option[value="' + task.prioritaet + '"]').prop('selected', true);

        // Update the form action with the taskId
        $('#editForm').attr('action', 'edit?id=' + task.NoteId);

        $('#editModal').show();        
      }
  });
}

$(document).ready(function() {
  // Close modal when the close button is clicked
  $('.close').click(function() {
      $('#editModal').hide();
  });

  // Close modal when clicking outside of the modal
  $(window).click(function(event) {
      if (event.target.id == 'editModal') {
          $('#editModal').hide();
      }
  });
});

function editNote(id) {
    location.href = "edit?id=" + id;
  }