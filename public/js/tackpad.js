function erledigen(id) {
    location.href = "erledigt?id=" + id;
}

function nichtMehrErledigt(id) {
    location.href = "nichtmehrerledigt?id=" + id;
}

function editNote(id) {
    location.href = "edit?id=" + id;
}

function deleteNote(id) {
    location.href = "delete?id=" + id;
}

function checkAll(source){
    checkboxes = document.getElementsByName('forAll');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }

    if(document.getElementById("checkAll").checked){
        document.getElementById("deleteAll").style.display="block";
    }else{
        document.getElementById("deleteAll").style.display="none";
    }
}

function deleteAll(){
    location.href ="deleteAll";
}