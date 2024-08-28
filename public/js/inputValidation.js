document.addEventListener("DOMContentLoaded", () => {
  const editForm = document.getElementById("editForm");

  if (!editForm) return;

  editForm.addEventListener("submit", (evt) => {
    // Remove existing warning messages
    const warnings = editForm.querySelectorAll(".warning");
    warnings.forEach((warning) => warning.remove());

    let hasErrors = false;
    const validations = [
      { selector: "#titel_edit", message: "Please enter a title" },
      { selector: "#aufgabe_edit", message: "Please enter a task" },
      { selector: "#datum_edit", message: "Please select a date" },
      { selector: "#priority_edit", message: "Please select a priority" },
    ];

    validations.forEach(({ selector, message }) => {
      const element = editForm.querySelector(selector);
      if (element && element.value.trim() === "") {
        element.insertAdjacentHTML(
          "afterend",
          `<label class="warning" style="color: red;">${message}</label>`
        );
        hasErrors = true;
      }
    });

    if (hasErrors) {
      evt.preventDefault();
    }
  });

  const addForm = document.getElementById("addForm");

  if (!addForm) return;

  addForm.addEventListener("submit", (evt) => {
    // Remove existing warning messages
    const warnings = addForm.querySelectorAll(".warning");
    warnings.forEach((warning) => warning.remove());

    let hasErrors = false;
    const validations = [
      { selector: "#titel_add", message: "Please enter a title" },
      { selector: "#aufgabe_add", message: "Please enter a task" },
      { selector: "#datum_add", message: "Please select a date" },
      { selector: "#priority_add", message: "Please select a priority" },
    ];

    validations.forEach(({ selector, message }) => {
      const element = addForm.querySelector(selector);
      if (element && element.value.trim() === "") {
        element.insertAdjacentHTML(
          "afterend",
          `<label class="warning" style="color: red;">${message}</label>`
        );
        hasErrors = true;
      }
    });

    if (hasErrors) {
      evt.preventDefault();
    }
  });
});
