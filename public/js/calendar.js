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
          alert("Could not reschedule: " + ((data && data.error) || "unknown error"));
          info.revert();
        }
      })
      .catch(function () {
        alert("Network error while rescheduling. Reverting the change.");
        info.revert();
      });
  }

  // Lightweight detail view that keeps the user on the page.
  function showDetails(info) {
    var props = info.event.extendedProps || {};
    var when = info.event.start
      ? info.event.start.toLocaleString("en-GB")
      : "no date";
    var status = props.completed ? "Completed" : "Open";
    alert(
      info.event.title +
        "\n" +
        (props.note ? props.note + "\n" : "") +
        "Due: " +
        when +
        "\nStatus: " +
        status
    );
  }
});
