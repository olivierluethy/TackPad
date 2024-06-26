let changeId_offen = [];
let changeId_offenEinzel = 0;
let changeId_erledigt = [];
let changeId_erledigtEinzel = 0;
let counter_offen = 0;
let counter_erledigt = 0;
let currentId = 0;

function checkAllOffeneTasks(source) {
    checkboxes = document.getElementsByClassName('offene_tasks');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
    elements_offen = document.getElementsByClassName("offene_tasks");
    for (var i = 0; i < elements_offen.length; i++) {
        if (elements_offen[i].checked == true) {
            counter_offen++;
        }
    }
    if (document.getElementById("checkAllOffeneTasks").checked == true && counter_offen == 1){
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else if (document.getElementById("checkAllOffeneTasks").checked == true && counter_offen > 1){
        document.getElementById("deleteAllOffeneTasks").style.display = "block";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else if (document.getElementById("checkAllOffeneTasks").checked == false && counter_offen > 1){
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    }  
    else if (document.getElementById("checkAllOffeneTasks").checked == false){
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else {
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    }
}

function checkAllErledigteTasks(source) {
    checkboxes = document.getElementsByClassName('erledigte_tasks');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
    elements_erledigt = document.getElementsByClassName("erledigte_tasks");
    for (var i = 0; i < elements_erledigt.length; i++) {
        if (elements_erledigt[i].checked == true) {
            counter_erledigt++;
        }
    }
    if (document.getElementById("checkAllErledigteTasks").checked == true && counter_offen == 1){
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    } else if (document.getElementById("checkAllErledigteTasks").checked == true && counter_erledigt > 1){
        document.getElementById("deleteAllErledigteTasks").style.display = "block";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else if (document.getElementById("checkAllErledigteTasks").checked == false && counter_erledigt > 1){
        document.getElementById("deleteAllErledigteTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else if (document.getElementById("checkAllErledigteTasks").checked == false){
        document.getElementById("deleteAllErledigteTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else {
        document.getElementById("deleteAllErledigteTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    }
}

function getId_for_offen(id) {
    currentId = id;
    counter_offen = 0;
    if (changeId_offenEinzel) {
        /* Check if id is already in array */
        if (changeId_offen.includes(id)) {
            let Index = changeId_offen.indexOf(id);
            changeId_offen.splice(Index, 1);
        } else {
            changeId_offen.push(id);
        }
    } else {
        changeId_offenEinzel = id;
    }

    elements_offen = document.getElementsByClassName("offene_tasks");
    for (var i = 0; i < elements_offen.length; i++) {
        if (elements_offen[i].checked == true) {
            counter_offen++;
        }
    }
    
    if (elements_offen.length == counter_offen && counter_offen == 1) {
        document.getElementById("checkAllOffeneTasks").checked = true;
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    } else if (elements_offen.length == counter_offen && counter_offen != 1) {
        document.getElementById("checkAllOffeneTasks").checked = true;
        document.getElementById("deleteAllOffeneTasks").style.display = "block";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
    } else if (counter_offen == 1) {
        document.getElementById("checkAllOffeneTasks").checked = false;
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    } else if (counter_offen > 1) {
        document.getElementById("checkAllOffeneTasks").checked = false;
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("loeschen").style.display = "inline-block";
    } else if (counter_offen == 0) {
        document.getElementById("checkAllOffeneTasks").checked = false;
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
        document.getElementById("freigeben").style.display = "none";
        document.getElementById("erledigt").style.display = "none";
    } else {
        document.getElementById("checkAllOffeneTasks").checked = false;
        document.getElementById("deleteAllOffeneTasks").style.display = "none";
    }
}

function getId_for_erledigt(id) {
    currentId = id;
    counter_erledigt = 0;
    /* Check if id is already in array */
    if (changeId_erledigt.includes(id)) {
        let Index = changeId_erledigt.indexOf(id);
        changeId_erledigt.splice(Index, 1);
    } else {
        changeId_erledigt.push(id);
    }
    elements_erledigt = document.getElementsByClassName("erledigte_tasks");
    for (var i = 0; i < elements_erledigt.length; i++) {
        if (elements_erledigt[i].checked == true) {
            counter_erledigt++;
        }
    }
    if (elements_erledigt.length == counter_erledigt && counter_erledigt == 1) {
        document.getElementById("checkAllErledigteTasks").checked = true;
        document.getElementById("bearbeiten").style.display = "inline-block";
        document.getElementById("loeschen").style.display = "inline-block";
        document.getElementById("freigeben").style.display = "inline-block";
        document.getElementById("erledigt").style.display = "inline-block";
    } else if (elements_erledigt.length == counter_erledigt && counter_erledigt != 1) {
        document.getElementById("checkAllErledigteTasks").checked = true;
        document.getElementById("deleteAllErledigteTasks").style.display = "block";
        document.getElementById("bearbeiten").style.display = "none";
        document.getElementById("loeschen").style.display = "none";
    } else if (counter_erledigt == 1) {
      document.getElementById("checkAllErledigteTasks").checked = false;
      document.getElementById("deleteAllErledigteTasks").style.display = "none";
      document.getElementById("bearbeiten").style.display = "inline-block";
      document.getElementById("loeschen").style.display = "inline-block";
      document.getElementById("freigeben").style.display = "inline-block";
      document.getElementById("erledigt").style.display = "inline-block";
    } else if (counter_erledigt > 1) {
      document.getElementById("checkAllErledigteTasks").checked = false;
      document.getElementById("deleteAllErledigteTasks").style.display = "none";
      document.getElementById("bearbeiten").style.display = "none";
      document.getElementById("erledigt").style.display = "none";
      document.getElementById("freigeben").style.display = "none";
      document.getElementById("loeschen").style.display = "inline-block";
    } else if (counter_erledigt == 0) {
      document.getElementById("checkAllErledigteTasks").checked = false;
      document.getElementById("deleteAllErledigteTasks").style.display = "none";
      document.getElementById("bearbeiten").style.display = "none";
      document.getElementById("loeschen").style.display = "none";
      document.getElementById("freigeben").style.display = "none";
      document.getElementById("erledigt").style.display = "none";
    } else {
      document.getElementById("checkAllErledigteTasks").checked = false;
      document.getElementById("deleteAllErledigteTasks").style.display = "none";
    }
}