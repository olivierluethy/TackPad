function erledigt() {
    let ids = changeId_offen.join(',');

    location.href = "erledigt?id=" + ids;
}
  
function unerledigt() {
  let ids = changeId_erledigt.join(',');
  location.href = "nichtmehrerledigt?id=" + ids;
}