function erledigen(id) {
    location.href = "erledigt?id=" + id;
}

function nichtMehrErledigt(id) {
    location.href = "nichtmehrerledigt?id=" + id;
}

function editNote(id) {
    location.href = "edit?id=" + id;
}

function deleteNote(id) {
    location.href = "delete?id=" + id;
}

function checkAll(source) {
    checkboxes = document.getElementsByName('forAll');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }

    if (document.getElementById("checkAll").checked) {
        document.getElementById("deleteAll").style.display = "block";
    } else {
        document.getElementById("deleteAll").style.display = "none";
    }
}

function deleteAll() {
    location.href = "deleteAll";
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

function openBearbeiten() {
    editmodal.style.display = "block";
}

// Get the modal
var addmodal = document.getElementById("addModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    addmodal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == addmodal) {
        addmodal.style.display = "none";
    }
}

// Get the modal
var editmodal = document.getElementById("editModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[1];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    editmodal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == editmodal) {
        editmodal.style.display = "none";
    }
}