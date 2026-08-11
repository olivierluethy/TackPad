/* =========================================================================
   Client-side validation for the add/edit task modals. Replaces the old
   injected red <label> markers with a single toast summarising what's missing
   and a red outline on each offending field (styleguide's toast/inline system).
   The server (Validator) remains the source of truth; this is fast feedback.
   ========================================================================= */
document.addEventListener("DOMContentLoaded", function () {
  wireValidation("addForm", [
    { selector: "#titel_add", label: "Title" },
    { selector: "#aufgabe_add", label: "Task" },
    { selector: "#datum_add", label: "Date" },
    { selector: "#priority_add", label: "Priority" },
  ]);

  wireValidation("editForm", [
    { selector: "#titel_edit", label: "Title" },
    { selector: "#aufgabe_edit", label: "Task" },
    { selector: "#datum_edit", label: "Date" },
    { selector: "#priority_edit", label: "Priority" },
  ]);
});

function wireValidation(formId, fields) {
  var form = document.getElementById(formId);
  if (!form) return;

  // Clear the invalid outline as soon as the user fixes a field.
  fields.forEach(function (f) {
    var el = form.querySelector(f.selector);
    if (el) {
      el.addEventListener("input", function () {
        el.classList.remove("field-invalid");
      });
    }
  });

  form.addEventListener(
    "submit",
    function (evt) {
      var missing = [];
      fields.forEach(function (f) {
        var el = form.querySelector(f.selector);
        if (el && el.value.trim() === "") {
          el.classList.add("field-invalid");
          missing.push(f.label);
        }
      });

      if (missing.length > 0) {
        evt.preventDefault();
        evt.stopImmediatePropagation(); // block the AJAX submit handler too
        window.TackpadToast.error("Please fill in: " + missing.join(", ") + ".");
      }
    },
    true // capture: run before the jQuery submit handlers
  );
}
