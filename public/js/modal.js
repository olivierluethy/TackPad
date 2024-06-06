function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

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