// Clientside Validierung
window.addEventListener("load", function() {

    document.getElementById('createForm').addEventListener('submit', function(evt) {
        console.log("It workds");

        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }


        if (document.querySelector('#titel') != null) {
            if (document.querySelector('#titel').value.trim() === '') {
                document.querySelector('#titel').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie einen Titel ein</label>");
                errors = true;
            }
        }

        if (document.querySelector('#aufgabe') != null) {
            if (document.querySelector('#aufgabe').value.trim() === '') {
                document.querySelector('#aufgabe').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie eine Aufgabe ein</label>");
                errors = true;
            }
        }

        if (document.querySelector('#datum') != null) {
            if (document.querySelector('#datum').value.trim() === '') {
                document.querySelector('#datum').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte wählen Sie ein Datum aus</label>");
                errors = true;
            }
        }

        if (document.querySelector('#prioritaet') != null) {
            if (document.querySelector('#prioritaet').value.trim() === '') {
                document.querySelector('#prioritaet').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte wählen Sie eine Priorität aus</label>");
                errors = true;
            }
        }

        if (errors) {
            evt.preventDefault();
        }

    });
});