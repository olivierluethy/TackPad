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
    closeSpan.onclick = function() {
        closeModal(modal);
    };

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
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

function deleteAllDone(){
    location.href = "deleteAllDone";
}

function deleteAllOpen(){
    location.href = "deleteAllOpen";
}