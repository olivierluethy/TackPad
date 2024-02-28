let changeId_offen = [];
let changeId_offenEinzel = 0;
let changeId_erledigt = [];
let changeId_erledigtEinzel = 0;
let counter_offen = 0;
let counter_erledigt = 0;

function erledigen(id) {
  location.href = "erledigt?id=" + id;
}

function nichtMehrErledigt(id) {
  location.href = "nichtmehrerledigt?id=" + id;
}

function editNote(id) {
  location.href = "edit?id=" + id;
}

function realyDeleteNote() {
  deletemodal.style.display = "block";
}

function deleteNote() {
  let idToDelete;

  if (changeId_erledigtEinzel) {
    idToDelete = changeId_erledigtEinzel;
  } else if (changeId_offenEinzel) {
    idToDelete = changeId_offenEinzel;
  }

  if (idToDelete) {
    location.href = "delete?id=" + idToDelete;
  }
}

function erledigt() {
  location.href = "erledigt?id=" + changeId_offenEinzel;
}

function checkAllOffeneTasks(source) {
  checkboxes = document.getElementsByClassName("offene_tasks");
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (document.getElementById("checkAllOffeneTasks").checked) {
    document.getElementById("deleteAllOffeneTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  }
}

function checkAllErledigteTasks(source) {
  checkboxes = document.getElementsByClassName("erledigte_tasks");
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (document.getElementById("checkAllErledigteTasks").checked) {
    document.getElementById("deleteAllErledigteTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  }
}

function deleteAllNichtZuSpaetOffeneTasks() {
  location.href = "deleteAllNichtZuSpaetOffeneTasks";
}

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}

function openModal() {
  addmodal.style.display = "block";
}

function getId_for_offen(id) {
  counter_offen = 0;
  if (changeId_offenEinzel) {
    /* Check if id is already in array */
    if (changeId_offen.includes(id)) {
      let Index = changeId_offen.indexOf(id);
      changeId_offen.splice(Index, 1);
    } else {
      changeId_offen.push(id);
    }
  } else {
    changeId_offenEinzel = id;
  }

  elements_offen = document.getElementsByClassName("offene_tasks");
  for (var i = 0; i < elements_offen.length; i++) {
    if (elements_offen[i].checked == true) {
      counter_offen++;
    }
  }
  if (elements_offen.length == counter_offen) {
    document.getElementById("checkAllOffeneTasks").checked = true;
    document.getElementById("deleteAllOffeneTasks").style.display = "block";
  } else {
    document.getElementById("checkAllOffeneTasks").checked = false;
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  }
  if (counter_offen == 1) {
    document.getElementById("bearbeiten").style.display = "inline-block";
    document.getElementById("loeschen").style.display = "inline-block";
    document.getElementById("freigeben").style.display = "inline-block";
    document.getElementById("erledigt").style.display = "inline-block";
  } else if (counter_offen > 1) {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
  } else if (counter_offen == 0) {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
  }
}

function getId_for_erledigt(id) {
  counter_erledigt = 0;

  if (changeId_erledigtEinzel) {
    /* Check if id is already in array */
    if (changeId_erledigt.includes(id)) {
      let Index = changeId_erledigt.indexOf(id);
      changeId_erledigt.splice(Index, 1);
    } else {
      changeId_erledigt.push(id);
    }
  } else {
    changeId_erledigtEinzel = id;
  }

  elements_erledigt = document.getElementsByClassName("erledigte_tasks");
  for (var i = 0; i < elements_erledigt.length; i++) {
    if (elements_erledigt[i].checked == true) {
      counter_erledigt++;
    }
  }
  if (elements_erledigt.length == counter_erledigt) {
    document.getElementById("checkAllErledigteTasks").checked = true;
    document.getElementById("deleteAllErledigteTasks").style.display = "block";
  } else {
    document.getElementById("checkAllErledigteTasks").checked = false;
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  }

  if (counter_erledigt == 1) {
    document.getElementById("bearbeiten").style.display = "inline-block";
    document.getElementById("loeschen").style.display = "inline-block";
    document.getElementById("freigeben").style.display = "inline-block";
  } else if (counter_erledigt > 1) {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
  } else if (counter_erledigt == 0) {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
  }
}

// // Function to handle modal closing
// function closeModal(modal) {
//   modal.style.display = "none";
// }

// // Function to set up modal closing event handlers
// function setupModalClose(modal, modalCloseIndex) {
//   // Get the <span> element that closes the modal
//   var closeSpan = modal.getElementsByClassName("close")[modalCloseIndex];

//   // When the user clicks on <span> (x), close the modal
//   closeSpan.onclick = function () {
//     closeModal(modal);
//   };

//   // When the user clicks anywhere outside of the modal, close it
//   window.onclick = function (event) {
//     if (event.target == modal) {
//       closeModal(modal);
//     }
//   };
// }

// // Delete Modal
// var deleteModal = document.getElementById("deleteModal");
// setupModalClose(deleteModal, 0);

// // Add Modal
// var addModal = document.getElementById("addModal");
// setupModalClose(addModal, 0);

// // Edit Modal
// var editModal = document.getElementById("editModal");
// setupModalClose(editModal, 0);

// Function to display a modal
function displayModal(modal) {
  modal.style.display = "block";
}

// Function to hide a modal
function hideModal(modal) {
  modal.style.display = "none";
}

// Function to handle closing of a modal
function closeModal(modal) {
  hideModal(modal);
}

// Function to set up close behavior for a modal
function setupModalClose(modal) {
  // Get the <span> element that closes the modal
  var closeSpan = modal.getElementsByClassName("close")[0];

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

// Setup modal close behavior for delete modal
var deleteModal = document.getElementById("deleteModal");
setupModalClose(deleteModal);

// Setup modal close behavior for add modal
var addModal = document.getElementById("addModal");
setupModalClose(addModal);

// Setup modal close behavior for edit modal
var editModal = document.getElementById("editModal");
setupModalClose(editModal);
