// Clientside Validierung
window.addEventListener("load", function () {

    document.querySelector('form').addEventListener('submit', function (evt) {

        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }


        if (document.querySelector('#titel') != null) {
            if (document.querySelector('#titel').value.trim() === '') {
                document.querySelector('#warningTitel').insertAdjacentHTML("afterend", "<p style='color: red; font-weight: bold;' class=\"warning\"> Bitte geben Sie einen Titel ein</p>");
                errors = true;
            }
        }

        if (document.querySelector('#aufgabe') != null) {
            if (document.querySelector('#aufgabe').value.trim() === '') {
                document.querySelector('#warningAufgabe').insertAdjacentHTML("afterend", "<p style='color: red; font-weight: bold;' class=\"warning\"> Bitte geben Sie eine Aufgabe ein</p>");
                errors = true;
            }
        }

        if (document.querySelector('#date') != null) {
            if (document.querySelector('#date').value.trim() === '') {
                document.querySelector('#warningDate').insertAdjacentHTML("afterend", "<p style='color: red; font-weight: bold;' class=\"warning\"> Bitte wählen Sie ein Datum aus</p>");
                errors = true;
            }
        }

        if (errors) {
            evt.preventDefault();
        }

    });
});