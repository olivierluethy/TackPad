// Delete Modal
var deleteModal = document.getElementById("deleteModal");

// Add Modal
var addModal = document.getElementById("addModal");
setupModalClose(addModal, 0);

// Function to set up modal closing event handlers
function setupModalClose(modal, modalCloseIndex) {
  // Get the <span> element that closes the modal
  var closeSpan = modal.getElementsByClassName("close")[modalCloseIndex];

  // When the user clicks on <span> (x), close the modal
  closeSpan.onclick = function () {
    closeModal(modal);
  };

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function (event) {
    if (event.target == modal) {
      closeModal(modal);
    }
  };
}
setupModalClose(deleteModal, 0);

function realyDeleteNote() {
  var deletemodal = document.getElementById("deleteModal");
  deletemodal.style.display = "block";
}

function deleteNote() {
  // Konvertiere das Array in eine durch Kommata getrennte Zeichenkette
  let ids = changeId_offen.join(",");

  $.ajax({
    type: "GET",
    url: "delete",
    data: { id: ids },
    success: function (response) {
      if (response.success) {
        // Entferne die gelöschten Aufgaben aus der Tabelle
        response.ids.forEach(function (id) {
          $("tr")
            .filter(function () {
              return (
                $(this).find('input[type="checkbox"]').attr("onclick") ===
                "getId_for_offen(" + id + ")"
              );
            })
            .remove();
        });

        closeModal(deleteModal); // Schließe das Lösch-Modal
      } else {
        alert("Error: " + response.error);
      }
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      alert("An error occurred while deleting the task.");
    },
  });
}

function deleteAllDone() {
  location.href = "deleteAllDone";
}

function deleteAllOpen() {
  location.href = "deleteAllOpen";
}

// Diese Funktion schließt das Modal
function closeModal(modal) {
  modal.style.display = "none";
}
