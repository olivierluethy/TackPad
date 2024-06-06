function erledigen(id) {
    location.href = "erledigt?id=" + id;
  }
  
  function nichtMehrErledigt(id) {
    location.href = "nichtmehrerledigt?id=" + id;
  }
  
  function erledigt() {
    location.href = "erledigt?id=" + changeId_offenEinzel;
  }