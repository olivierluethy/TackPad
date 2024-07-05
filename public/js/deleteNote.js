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
    // Konvertiere das Array in eine durch Kommata getrennte Zeichenkette
    let ids = changeId_offen.join(',');

    location.href = "delete?id=" + ids;
}

function deleteAllDone(){
    location.href = "deleteAllDone";
}

function deleteAllOpen(){
    location.href = "deleteAllOpen";
}