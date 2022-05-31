let changeId = [];
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
    location.href = "delete?id=" + changeId;
}

function openBearbeiten() {
    location.href = "showEditPage?id=" + changeId;
    // editmodal.style.display = "block";
}

function erledigt() {
    location.href = "erledigt?id=" + changeId;
}

function checkAllOffeneTasks(source) {
    checkboxes = document.getElementsByClassName('offene_tasks');
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
    checkboxes = document.getElementsByClassName('erledigte_tasks');
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

function dispose() {
    addmodal.style.display = "none";
}

function getId(id) {
    counter_offen = 0;
    counter_erledigt = 0;
    /* Check if id is already in array */
    if (changeId.includes(id)) {
        let Index = changeId.indexOf(id);
        changeId.splice(Index, 1);
    } else {
        changeId.push(id);
    }

    elements_offen = document.getElementsByClassName("offene_tasks");
    for (var i = 0; i < elements_offen.length; i++) {
        if (elements_offen[i].checked == true) {
            counter_offen++;
        }
    }
    elements_erledigt = document.getElementsByClassName("erledigte_tasks");
    for (var i = 0; i < elements_erledigt.length; i++) {
        if (elements_erledigt[i].checked == true) {
            counter_erledigt++;
            console.log("Eins erledigt!");
        }
    }
    if (elements_offen.length == counter_offen) {
        document.getElementById("checkAllOffeneTasks").checked = true;
        document.getElementById("deleteAllOffeneTasks").style.display = "block";
    } else {
        document.getElementById("checkAllOffeneTasks").checked = false;
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
    }
    if (elements_erledigt.length == counter_erledigt) {
        document.getElementById("checkAllErledigteTasks").checked = true;
        document.getElementById("deleteAllErledigteTasks").style.display = "block";
    } else {
        document.getElementById("checkAllErledigteTasks").checked = false;
        document.getElementById("deleteAllErledigteTasks").style.display = "none";
    }
    if (counter_offen == 1 || counter_erledigt == 1) {
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    } else if (counter_erledigt > 1 || counter_offen > 1) {
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else if (counter_offen == 0 && counter_erledigt == 0) {
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    }
}

// Get the modal
var deletemodal = document.getElementById("deleteModal");
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