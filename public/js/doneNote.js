function erledigt() {
  let ids = changeId_offen.join(",");
  location.href = "erledigt?id=" + ids;
}

function undone() {
  let ids = changeId_erledigt.join(",");
  location.href = "unerledigt?id=" + ids;
}
