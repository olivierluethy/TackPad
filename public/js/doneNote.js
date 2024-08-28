function erledigt() {
  let ids = changeId_offen.join(",");

  $.ajax({
    type: "GET",
    url: "erledigt",
    data: { id: ids },
    success: function (response) {
      if (response.success) {
        response.ids.forEach(function (id) {
          // Aufgabe aus der offenen Tabelle entfernen
          $("#open-tasks-container tr")
            .filter(function () {
              return (
                $(this).find('input[type="checkbox"]').attr("onclick") ===
                "getId_for_offen(" + id + ")"
              );
            })
            .remove();

          // Aufgabe zur erledigten Tabelle hinzufügen
          // Hier kannst du entweder eine AJAX-Antwort verwenden oder die Aufgabe direkt erstellen
          // Hier als Platzhalter
          $("#completed-tasks-container").append(generateCompletedTaskRow(id));
        });
        alert("Tasks marked as done successfully.");
      } else {
        alert("Error: " + response.error);
      }
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      alert("An error occurred while updating the task status.");
    },
  });
}

function undone() {
  let ids = changeId_erledigt.join(",");

  $.ajax({
    type: "GET",
    url: "unerledigt",
    data: { id: ids },
    success: function (response) {
      if (response.success) {
        response.ids.forEach(function (id) {
          // Aufgabe aus der erledigten Tabelle entfernen
          $("#completed-tasks-container tr")
            .filter(function () {
              return (
                $(this).find('input[type="checkbox"]').attr("onclick") ===
                "getId_for_erledigt(" + id + ")"
              );
            })
            .remove();

          // Aufgabe zur offenen Tabelle hinzufügen
          // Hier kannst du entweder eine AJAX-Antwort verwenden oder die Aufgabe direkt erstellen
          // Hier als Platzhalter
          $("#open-tasks-container").append(generateOpenTaskRow(id));
        });
        alert("Tasks marked as undone successfully.");
      } else {
        alert("Error: " + response.error);
      }
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      alert("An error occurred while updating the task status.");
    },
  });
}

// Platzhalter-Funktion zum Erzeugen von HTML für erledigte Aufgaben
function generateCompletedTaskRow(id) {
  // Hier solltest du die Aufgabe vom Server laden oder in der Antwort speichern
  // Beispielhafte Zeile (du musst die tatsächlichen Daten und IDs einfügen)
  return `<tr class="erledigt">
            <td style='background-color:lightgrey;'>
              <input type='checkbox' onclick="getId_for_erledigt(${id})" class='erledigte_tasks'>
            </td>
            <td style='background-color:lightgrey;'><del>Title for ${id}</del></td>
            <td style='background-color:lightgrey;'><del>Task details for ${id}</del></td>
            <td style='background-color:lightgrey;'><del>Date for ${id}</del></td>
            <td style='background-color:lightgrey;'><del>Priority for ${id}</del></td>
            <td style='background-color:lightgrey;'><del>Completed on ${id}</del></td>
            <td style='background-color:lightgrey;'><del>Changed ${id}</del></td>
          </tr>`;
}

// Platzhalter-Funktion zum Erzeugen von HTML für offene Aufgaben
function generateOpenTaskRow(id) {
  // Hier solltest du die Aufgabe vom Server laden oder in der Antwort speichern
  // Beispielhafte Zeile (du musst die tatsächlichen Daten und IDs einfügen)
  return `<tr class="nicht_zu_spaet">
            <td style='background-color:lightgreen;'>
              <input type='checkbox' onclick="getId_for_offen(${id})" class='offene_tasks'>
            </td>
            <td style='background-color:lightgreen;'>Title for ${id}</td>
            <td style='background-color:lightgreen;'>Task details for ${id}</td>
            <td style='background-color:lightgreen;'>Date for ${id}</td>
            <td style='background-color:lightgreen;'>Priority for ${id}</td>
            <td style='background-color:lightgreen;'>Changed ${id}</td>
          </tr>`;
}
