// openNav / closeNav now live in tackpad.js so they are available on every page
// with the sidebar (including the calendar view), not just the task list.

function openModal() {
  addmodal.style.display = "block";
}

// Get the modal
let modal = document.getElementById("addModal");

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

function dispose() {
  addmodal.style.display = "none";
}

function closeReallyDelete() {
  document.getElementById("deleteModal").style.display = "none";
}

// Get the modal
var deletemodal = document.getElementById("deleteModal");
// Get the modal
var addmodal = document.getElementById("addModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
  addmodal.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
  if (event.target == addmodal) {
    addmodal.style.display = "none";
  }
};
