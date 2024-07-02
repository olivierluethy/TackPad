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
    let idsToDelete = [];

    if (changeId_erledigtEinzel) {
        idsToDelete.push(changeId_erledigtEinzel);
    } else if (changeId_offenEinzel) {
        idsToDelete.push(changeId_offenEinzel);
    }

    if (changeId_erledigt && changeId_erledigt.length > 0) {
        idsToDelete = idsToDelete.concat(changeId_erledigt);
    }

    if (changeId_offen && changeId_offen.length > 0) {
        idsToDelete = idsToDelete.concat(changeId_offen);
    }

    console.log("Delete Note Function Called");
    console.log("IDs to delete:", idsToDelete);

    if (idsToDelete.length === 1) {
        location.href = "delete?id=" + idsToDelete[0];
    } else if (idsToDelete.length > 1) {
        location.href = "deleteMultiple?ids=" + idsToDelete.join(',');
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