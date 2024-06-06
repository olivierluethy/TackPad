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
  
  // Add Modal
  var addModal = document.getElementById("addModal");
  setupModalClose(addModal, 0);

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