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
          // Neue Aufgabe zur Tabelle der offenen Aufgaben hinzufügen
          var newTaskRow =
            '<tr class="nicht_zu_spaet">' +
            '<td style="background-color: lightgreen;">' +
            '<input type="checkbox" onclick="getId_for_offen(' +
            response.task.id +
            ')" class="offene_tasks">' +
            "</td>" +
            '<td style="background-color: lightgreen;">' +
            response.task.titel +
            "</td>" +
            '<td style="background-color: lightgreen;">' +
            response.task.aufgabe +
            "</td>" +
            '<td style="background-color: lightgreen;">' +
            new Date(response.task.datum).toLocaleDateString() +
            "</td>" +
            '<td style="background-color: lightgreen;">' +
            response.task.prioritaet +
            "</td>" +
            '<td style="background-color: lightgreen;">' +
            new Date(response.task.datum).toLocaleDateString() +
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
