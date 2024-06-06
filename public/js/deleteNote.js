// Delete Modal
var deleteModal = document.getElementById("deleteModal");
setupModalClose(deleteModal, 0);

function realyDeleteNote() {
    var deletemodal = document.getElementById("deleteModal");
    deletemodal.style.display = "block";
  }

  function deleteNote() {
    let idToDelete;
  
    if (changeId_erledigtEinzel) {
      idToDelete = changeId_erledigtEinzel;
    } else if (changeId_offenEinzel) {
      idToDelete = changeId_offenEinzel;
    }
  
    console.log("Delete Note Function Called");
    console.log("ID to delete:", idToDelete);
  
    if (idToDelete) {
      location.href = "delete?id=" + idToDelete;
    } else {
      console.log("No ID to delete");
    }
  }

  function deleteAllNichtZuSpaetOffeneTasks() {
    location.href = "deleteAllNichtZuSpaetOffeneTasks";
  }