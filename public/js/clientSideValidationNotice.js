document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('createForm');
  
    if (!form) return;
  
    form.addEventListener('submit', (evt) => {
      console.log("Validation initiated");
  
      const warnings = document.querySelectorAll(".warning");
      warnings.forEach(warning => warning.remove());
  
      let hasErrors = false;
      const validations = [
        { selector: '#titel', message: 'Please enter a title' },
        { selector: '#aufgabe', message: 'Please enter a task' },
        { selector: '#datum', message: 'Please select a date' },
        { selector: '#priority', message: 'Please select a priority' }
      ];
  
      validations.forEach(({ selector, message }) => {
        const element = document.querySelector(selector);
        if (element && element.value.trim() === '') {
          element.insertAdjacentHTML("afterend", `<label class="warning" style="color: red;">${message}</label>`);
          hasErrors = true;
        }
      });
  
      if (hasErrors) {
        evt.preventDefault();
      }
    });
  });
  