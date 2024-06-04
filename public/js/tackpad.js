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

function erledigt() {
  location.href = "erledigt?id=" + changeId_offenEinzel;
}

function checkAllOffeneTasks(source) {
  let checkboxes = document.getElementsByClassName("offene_tasks");
  for (let i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (source.checked) {
    document.getElementById("deleteAllOffeneTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  }
  toggleActionButtons();
}

function checkAllErledigteTasks(source) {
  let checkboxes = document.getElementsByClassName("erledigte_tasks");
  for (let i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (source.checked) {
    document.getElementById("deleteAllErledigteTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  }
  toggleActionButtons();
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
  toggleTaskSelection(id, "offene_tasks", changeId_offen);
}

function getId_for_erledigt(id) {
  toggleTaskSelection(id, "erledigte_tasks", changeId_erledigt);
}

function toggleTaskSelection(id, taskClass, idArray) {
  if (idArray.includes(id)) {
    idArray.splice(idArray.indexOf(id), 1);
  } else {
    idArray.push(id);
  }

  let elements = document.getElementsByClassName(taskClass);
  let counter = 0;
  for (let i = 0; i < elements.length; i++) {
    if (elements[i].checked) {
      counter++;
    }
  }

  let allSelected = elements.length === counter;
  let checkAllElement = taskClass === "offene_tasks" ? "checkAllOffeneTasks" : "checkAllErledigteTasks";
  document.getElementById(checkAllElement).checked = allSelected;

  if (taskClass === "offene_tasks") {
    changeId_offenEinzel = idArray.length === 1 ? idArray[0] : 0;
    counter_offen = counter;
  } else {
    changeId_erledigtEinzel = idArray.length === 1 ? idArray[0] : 0;
    counter_erledigt = counter;
  }

  toggleActionButtons();
}

function toggleActionButtons() {
  let totalSelected = document.querySelectorAll('.offene_tasks:checked, .erledigte_tasks:checked').length;
  let singleSelected = totalSelected === 1;
  let multipleSelected = totalSelected > 1;

  document.getElementById("bearbeiten").style.display = singleSelected ? "inline-block" : "none";
  document.getElementById("loeschen").style.display = totalSelected ? "inline-block" : "none";
  document.getElementById("freigeben").style.display = singleSelected ? "inline-block" : "none";
  document.getElementById("erledigt").style.display = singleSelected ? "inline-block" : "none";

  if (multipleSelected) {
    document.getElementById("erledigt").style.display = "none";
  }
}

// Get the modal
let modal = document.getElementById("addModal");

function openBearbeiten() {
  editmodal.style.display = 'block';
}

// Function to display a modal
function displayModal() {
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

// Function to handle modal closing
function closeModal(modal) {
  modal.style.display = "none";
}

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

// Delete Modal
var deleteModal = document.getElementById("deleteModal");
setupModalClose(deleteModal, 0);

// Add Modal
var addModal = document.getElementById("addModal");
setupModalClose(addModal, 0);

// Edit Modal
var editModal = document.getElementById("editModal");
setupModalClose(editModal, 0);