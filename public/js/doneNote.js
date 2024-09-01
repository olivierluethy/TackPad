function erledigt() {
  let ids = changeId_offen.join(",");

  $.ajax({
    type: "GET",
    url: "erledigt",
    data: { id: ids },
    success: function(response) {
      if (response.success) {
        response.ids.forEach(function(id) {
          // Aufgabe aus der offenen Tabelle entfernen
          $("#open-tasks-container tr")
            .filter(function() {
              return $(this).find('input[type="checkbox"]').attr("onclick") === "getId_for_offen(" + id + ")";
            })
            .remove();
    
          // Aufgabe zur erledigten Tabelle hinzufügen
          $("#completed-tasks-container").append(generateCompletedTaskRow(id));
        });
        $("#open-tasks-container").load(location.href + " #open-tasks-container"); // Teil der Seite neu laden
        $("#completed-tasks-container").load(location.href + " #completed-tasks-container"); // Teil der Seite neu laden
      } else {
        alert("Error: " + response.error);
      }
    },
    error: function(xhr, status, error) {
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
          $("#open-tasks-container tr").append(generateOpenTaskRow(id));
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

function generateCompletedTaskRow(id) {
  // Hole die Daten der Aufgabe aus der "open-tasks-container"-Tabelle
  var taskRow = $("#open-tasks-container tr").filter(function() {
    return $(this).find('input[type="checkbox"]').attr("onclick") === "getId_for_offen(" + id + ")";
  });

  // Hole die Daten der Aufgabe
  var title = taskRow.find("td:nth-child(2)").text();
  var task = taskRow.find("td:nth-child(3)").text();
  var date = taskRow.find("td:nth-child(4)").text();
  var priority = taskRow.find("td:nth-child(5)").text();
  var changed = taskRow.find("td:nth-child(6)").text();

  // Erstelle eine neue Zeile für die "completed-tasks-container"-Tabelle
  var newRow = $("<tr class='erledigt'>");
  newRow.append("<td style='background-color:lightgrey;'><input type='checkbox' onclick='getId_for_erledigt(" + id + ")' class='erledigte_tasks'></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + title + "</del></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + task + "</del></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + date + "</del></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + priority + "</del></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + new Date().toLocaleDateString() + "</del></td>");
  newRow.append("<td style='background-color:lightgrey;'><del>" + changed + "</del></td>");

  return newRow;
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
