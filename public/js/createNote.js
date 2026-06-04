$(document).ready(function () {
  $("#addForm").submit(function (event) {
    event.preventDefault();

    var title = $("#titel_add").val();
    var task = $("#aufgabe_add").val();
    var priority = $("#priority_add").val();
    var date = $("#datum_add").val();

    $.ajax({
      type: "POST",
      url: "create",
      data: {
        titel: title,
        aufgabe: task,
        priority: priority,
        datum: date,
      },
      success: function (response) {
        response = JSON.parse(response);

        if (response.error) {
          alert("Error: " + response.error);
        } else {
          // Derive the row's status the same way the server does: a brand new
          // task is open (not completed) and may already be past due. The colour
          // itself is owned by CSS via the status class — no inline styles here.
          var isPastDue = new Date(response.task.datum) < new Date();
          var statusClass = taskStatusClass(false, isPastDue);

          var dueDate = new Date(response.task.datum).toLocaleDateString();

          // Neue Aufgabe zur Tabelle der offenen Aufgaben hinzufügen
          var newTaskRow =
            '<tr class="task-row ' + statusClass + '">' +
            "<td>" +
            '<input type="checkbox" onclick="getId_for_offen(' +
            response.task.id +
            ')" class="offene_tasks">' +
            "</td>" +
            "<td>" +
            response.task.titel +
            "</td>" +
            "<td>" +
            response.task.aufgabe +
            "</td>" +
            "<td>" +
            dueDate +
            "</td>" +
            "<td>" +
            response.task.prioritaet +
            "</td>" +
            "<td>" +
            dueDate +
            "</td>" +
            "</tr>";

          $("#open-tasks-container").append(newTaskRow);

          $("#addForm")[0].reset();
          $("#addModal").hide();
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        alert("An error occurred while adding the task.");
      },
    });
  });
});
