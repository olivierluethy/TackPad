let changeId_offen = [];
let changeId_erledigt = [];
let counter_offen = 0;
let counter_erledigt = 0;

function checkAllOffeneTasks(source) {
  var checkboxes = document.getElementsByClassName("offene_tasks");
  var counter_offen = 0;

  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }

  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      counter_offen++;
    }
  }

  if (source.checked) {
    if (counter_offen == 1) {
      document.getElementById("bearbeiten").style.display = "inline-block";
      document.getElementById("loeschen").style.display = "inline-block";
      document.getElementById("freigeben").style.display = "inline-block";
      document.getElementById("erledigt").style.display = "inline-block";
      document.getElementById("deleteAllOffeneTasks").style.display = "none";
    } else if (counter_offen > 1) {
      document.getElementById("bearbeiten").style.display = "none";
      document.getElementById("loeschen").style.display = "none";
      document.getElementById("freigeben").style.display = "none";
      document.getElementById("erledigt").style.display = "inline-block";
      document.getElementById("deleteAllOffeneTasks").style.display =
        "inline-block";
    }
  } else {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  }
}

function checkAllErledigteTasks(source) {
  var checkboxes = document.getElementsByClassName("erledigte_tasks");
  var counter_erledigt = 0;

  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }

  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      counter_erledigt++;
    }
  }

  if (source.checked) {
    if (counter_erledigt == 1) {
      document.getElementById("bearbeiten").style.display = "inline-block";
      document.getElementById("loeschen").style.display = "inline-block";
      document.getElementById("freigeben").style.display = "inline-block";
      document.getElementById("erledigt").style.display = "inline-block";
      document.getElementById("deleteAllErledigteTasks").style.display = "none";
    } else if (counter_erledigt > 1) {
      document.getElementById("bearbeiten").style.display = "none";
      document.getElementById("loeschen").style.display = "none";
      document.getElementById("freigeben").style.display = "none";
      document.getElementById("erledigt").style.display = "inline-block";
      document.getElementById("deleteAllErledigteTasks").style.display =
        "inline-block";
    }
  } else {
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  }
}

function getId_for_offen(id) {
  if (changeId_offen.indexOf(id) !== -1) {
    // ID ist bereits im Array, also entfernen wir sie
    changeId_offen.splice(changeId_offen.indexOf(id), 1);
  } else {
    // ID ist nicht im Array, also fügen wir sie hinzu
    changeId_offen.push(id);
  }
  var counter_offen = 0;

  var checkboxes = document.getElementsByClassName("offene_tasks");

  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      counter_offen++;
    }
  }

  if (counter_offen == 1) {
    document.getElementById("checkAllOffeneTasks").checked = true;
    document.getElementById("bearbeiten").style.display = "inline-block";
    document.getElementById("loeschen").style.display = "inline-block";
    document.getElementById("freigeben").style.display = "inline-block";
    document.getElementById("erledigt").style.display = "inline-block";
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  } else if (counter_offen > 1) {
    document.getElementById("checkAllOffeneTasks").checked = false;
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "inline-block";
    document.getElementById("deleteAllOffeneTasks").style.display =
      "inline-block";
  } else {
    document.getElementById("checkAllOffeneTasks").checked = false;
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("erledigt").style.display = "none";
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
  }

  // Überprüfen, ob alle Checkboxen ausgewählt sind
  if (counter_offen === checkboxes.length) {
    document.getElementById("checkAllOffeneTasks").checked = true;
  } else {
    document.getElementById("checkAllOffeneTasks").checked = false;
  }
}

function getId_for_erledigt(id) {
  if (changeId_erledigt.indexOf(id) !== -1) {
    // ID ist bereits im Array, also entfernen wir sie
    changeId_erledigt.splice(changeId_erledigt.indexOf(id), 1);
  } else {
    // ID ist nicht im Array, also fügen wir sie hinzu
    changeId_erledigt.push(id);
  }
  var counter_erledigt = 0;

  var checkboxes = document.getElementsByClassName("erledigte_tasks");

  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      counter_erledigt++;
    }
  }

  if (counter_erledigt == 1) {
    document.getElementById("checkAllErledigteTasks").checked = true;
    document.getElementById("bearbeiten").style.display = "inline-block";
    document.getElementById("loeschen").style.display = "inline-block";
    document.getElementById("freigeben").style.display = "inline-block";
    document.getElementById("undo").style.display = "inline-block";
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  } else if (counter_erledigt > 1) {
    document.getElementById("checkAllErledigteTasks").checked = false;
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("undo").style.display = "inline-block";
    document.getElementById("deleteAllErledigteTasks").style.display =
      "inline-block";
  } else {
    document.getElementById("checkAllErledigteTasks").checked = false;
    document.getElementById("bearbeiten").style.display = "none";
    document.getElementById("loeschen").style.display = "none";
    document.getElementById("freigeben").style.display = "none";
    document.getElementById("undo").style.display = "none";
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
  }

  // Überprüfen, ob alle Checkboxen ausgewählt sind
  if (counter_erledigt === checkboxes.length) {
    document.getElementById("checkAllErledigteTasks").checked = true;
  } else {
    document.getElementById("checkAllErledigteTasks").checked = false;
  }
}

// Event listener for individual checkboxes
var checkboxes = document.getElementsByClassName("offene_tasks");
for (var i = 0; i < checkboxes.length; i++) {
  checkboxes[i].addEventListener("change", function () {
    getId_for_offen(this.id);
  });
}

// Event listener for individual checkboxes
var checkboxes = document.getElementsByClassName("erledigte_tasks");
for (var i = 0; i < checkboxes.length; i++) {
  checkboxes[i].addEventListener("change", function () {
    getId_for_erledigt(this.id);
  });
}
