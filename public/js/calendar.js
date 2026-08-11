// Calendar view — visualises task deadlines with FullCalendar v6 and persists
// drag-and-drop reschedules to the backend in real time. No jQuery dependency:
// data is fetched from the same-origin JSON endpoints exposed by the router.
document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");
  if (!calendarEl || typeof FullCalendar === "undefined") {
    return;
  }

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    height: "auto",
    firstDay: 1, // week starts on Monday
    locale: "en-gb", // clear regional format: day-month-year, 24-hour clock
    nowIndicator: true,
    // Drag-and-drop rescheduling (start only; tasks have no duration).
    editable: true,
    eventStartEditable: true,
    eventDurationEditable: false,
    // Tasks are loaded from the server as ISO 8601 events.
    events: "calendarevents",
    eventDrop: handleReschedule,
    eventClick: showDetails,
  });

  calendar.render();

  // Persist a moved event. On any failure we revert so the calendar never
  // drifts out of sync with the database.
  function handleReschedule(info) {
    var body = new URLSearchParams();
    body.set("id", info.event.id);
    body.set("start", info.event.startStr); // ISO 8601 from FullCalendar
    body.set("allDay", info.event.allDay ? "true" : "false");

    fetch("updatetaskdate", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (!data || !data.success) {
          window.TackpadToast.error("Could not reschedule: " + ((data && data.error) || "unknown error"));
          info.revert();
        }
      })
      .catch(function () {
        window.TackpadToast.error("Network error while rescheduling. Reverting the change.");
        info.revert();
      });
  }

  // Detail view in a modal (replaces the old alert): shows the task's title,
  // date/time, description, priority and status, formatted consistently with
  // the task list via the shared helpers in taskStatus.js.
  function showDetails(info) {
    var props = info.event.extendedProps || {};

    setText("event_title", info.event.title || "Task");
    // Prefer the raw stored due-date so formatting matches the list exactly;
    // fall back to the event's start instant for any legacy event.
    setText(
      "event_when",
      props.rawDate
        ? formatTaskDate(props.rawDate)
        : info.event.start
        ? info.event.start.toLocaleString("en-GB")
        : "No date set"
    );
    setText("event_note", props.note ? props.note : "—");
    setText(
      "event_priority",
      props.priority != null ? priorityLabel(props.priority) : "—"
    );
    setText("event_status", props.completed ? "Completed" : "Open");

    document.getElementById("eventModal").style.display = "block";
  }

  function setText(id, value) {
    var el = document.getElementById(id);
    if (el) {
      el.textContent = value;
    }
  }
});

// Close the calendar detail modal. Global so the inline onclick handlers in the
// modal markup can reach it.
function closeEventModal() {
  var modal = document.getElementById("eventModal");
  if (modal) {
    modal.style.display = "none";
  }
}

// Close the modal when clicking on the dark backdrop outside the dialog.
window.addEventListener("click", function (event) {
  if (event.target && event.target.id === "eventModal") {
    closeEventModal();
  }
});
