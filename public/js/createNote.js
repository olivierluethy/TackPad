// Create a task without a full page reload. The new row is built from the same
// data the server stored, placed at the correct sorted position, and the tab
// counts are refreshed — so the list looks exactly as it would after a reload.
$(document).ready(function () {
  $("#addForm").submit(function (event) {
    event.preventDefault();

    var title = $("#titel_add").val();
    var task = $("#aufgabe_add").val();
    var priority = $("#priority_add").val();
    var date = $("#datum_add").val();
    var time = $("#zeit_add").val();

    $.ajax({
      type: "POST",
      url: "create",
      // Date and time are sent separately; the server merges them into the
      // canonical stored shape and echoes it back as response.task.datum.
      data: {
        titel: title,
        aufgabe: task,
        priority: priority,
        datum: date,
        zeit: time,
      },
      success: function (response) {
        response = JSON.parse(response);

        if (response.error) {
          alert("Error: " + response.error);
          return;
        }

        var container = document.getElementById("open-tasks-container");
        if (!container) {
          // First-ever task: there was no table to insert into (empty state),
          // so fall back to a reload to render the full task UI once.
          location.reload();
          return;
        }

        var row = buildOpenTaskRow({
          id: response.task.id,
          titel: title,
          aufgabe: task,
          // Canonical combined date/time from the server (raw, unformatted).
          datum: response.task.datum,
          prioritaet: priority,
          last_change: response.task.last_change,
          shared: false,
        });

        insertTaskRowSorted(container, row);
        refreshTaskCounts();
        showTaskTab("open");

        $("#addForm")[0].reset();
        $("#addModal").hide();
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        alert("An error occurred while adding the task.");
      },
    });
  });
});
