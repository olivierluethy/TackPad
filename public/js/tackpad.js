let changeId_offen = [];
let changeId_offenEinzel = 0;
let changeId_erledigt = [];
let changeId_erledigtEinzel = 0;
let counter_offen = 0;
let counter_erledigt = 0;
let currentId = 0;

function checkAllOffeneTasks(source) {
  let checkboxes = document.getElementsByClassName("offene_tasks");
  for (let i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (source.checked) {
    document.getElementById("deleteAllOffeneTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllOffeneTasks").style.display = "none";
    document.getElementById("loeschen").style.display="none";
  }
  toggleActionButtons();
}

function checkAllErledigteTasks(source) {
  let checkboxes = document.getElementsByClassName("erledigte_tasks");
  for (let i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = source.checked;
  }

  if (source.checked) {
    document.getElementById("deleteAllErledigteTasks").style.display = "block";
  } else {
    document.getElementById("deleteAllErledigteTasks").style.display = "none";
    document.getElementById("loeschen").style.display="none";
  }
  toggleActionButtons();
}

function getId_for_offen(id) {
  currentId = id;
  toggleTaskSelection(id, "offene_tasks", changeId_offen);
}

function getId_for_erledigt(id) {
  currentId = id;
  toggleTaskSelection(id, "erledigte_tasks", changeId_erledigt);
}

function toggleTaskSelection(id, taskClass, idArray) {
  if (idArray.includes(id)) {
    idArray.splice(idArray.indexOf(id), 1);
  } else {
    idArray.push(id);
  }

  let elements = document.getElementsByClassName(taskClass);
  let counter = 0;
  for (let i = 0; i < elements.length; i++) {
    if (elements[i].checked) {
      counter++;
    }
  }

  let allSelected = elements.length === counter;
  let checkAllElement = taskClass === "offene_tasks" ? "checkAllOffeneTasks" : "checkAllErledigteTasks";
  document.getElementById(checkAllElement).checked = allSelected;

  if (taskClass === "offene_tasks") {
    changeId_offenEinzel = idArray.length === 1 ? idArray[0] : 0;
    counter_offen = counter;
  } else {
    changeId_erledigtEinzel = idArray.length === 1 ? idArray[0] : 0;
    counter_erledigt = counter;
  }

  toggleActionButtons();
}

function toggleActionButtons() {
  let totalSelected = document.querySelectorAll('.offene_tasks:checked, .erledigte_tasks:checked').length;
  let singleSelected = totalSelected === 1;
  let multipleSelected = totalSelected > 1;

  document.getElementById("bearbeiten").style.display = singleSelected ? "inline-block" : "none";
  document.getElementById("loeschen").style.display = totalSelected ? "inline-block" : "none";
  document.getElementById("freigeben").style.display = singleSelected ? "inline-block" : "none";
  document.getElementById("erledigt").style.display = singleSelected ? "inline-block" : "none";

  if (multipleSelected) {
    document.getElementById("erledigt").style.display = "none";
  }
}