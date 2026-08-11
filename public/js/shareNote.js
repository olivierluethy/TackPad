// Share a task with another registered TackPad user. The selected task id is
// captured when the modal opens; on success the task is copied into the other
// user's account (server-side) and this row is flagged as shared in place — no
// reload — by revealing its share badge.
var shareTaskId = null;

function openShareModal() {
  // Sharing targets exactly one task; derive the selection live from the DOM.
  var ids = getSelectedIds("offene_tasks").concat(
    getSelectedIds("erledigte_tasks")
  );

  if (ids.length !== 1) {
    window.TackpadToast.error("Please select exactly one task to share.");
    return;
  }

  shareTaskId = ids[0];

  // Reset the form/feedback each time the modal is opened.
  var form = document.getElementById("shareForm");
  if (form) {
    form.reset();
  }
  hideShareFeedback();

  document.getElementById("shareModal").style.display = "block";
}

function showShareFeedback(message, isError) {
  var el = document.getElementById("share_feedback");
  if (!el) {
    return;
  }
  el.textContent = message;
  el.classList.toggle("is-error", !!isError);
  el.hidden = false;
}

function hideShareFeedback() {
  var el = document.getElementById("share_feedback");
  if (el) {
    el.hidden = true;
  }
}

// Reveal the "shared" indicator on a task row and record it in the data, so the
// state matches what a reload would render.
function markRowShared(taskId) {
  var row = document.querySelector('tr[data-id="' + taskId + '"]');
  if (!row) {
    return;
  }
  row.classList.add("task-row--shared");
  row.setAttribute("data-shared", "1");
  var badge = row.querySelector(".task-shared-badge");
  if (badge) {
    badge.hidden = false;
  }
}

$(document).ready(function () {
  // Close handlers for the share modal.
  $("#shareModal .close").click(function () {
    $("#shareModal").hide();
  });
  $(window).click(function (event) {
    if (event.target.id === "shareModal") {
      $("#shareModal").hide();
    }
  });

  $("#shareForm").submit(function (event) {
    event.preventDefault();

    var email = $("#share_email").val();
    if (!email || email.trim() === "") {
      showShareFeedback("Please enter an email address.", true);
      return;
    }

    $.ajax({
      type: "POST",
      url: "share",
      data: { id: shareTaskId, email: email },
      dataType: "json",
      success: function (response) {
        if (response && response.success) {
          markRowShared(shareTaskId);
          $("#shareModal").hide();
          window.TackpadToast.success("Task shared.");
        } else {
          showShareFeedback(
            (response && response.error) || "Could not share the task.",
            true
          );
        }
      },
      error: function (xhr) {
        // The endpoint returns JSON (with a non-2xx status) on validation
        // errors such as "user not found"; surface that message.
        var message = "An error occurred while sharing the task.";
        try {
          var parsed = JSON.parse(xhr.responseText);
          if (parsed && parsed.error) {
            message = parsed.error;
          }
        } catch (e) {
          console.error(xhr.responseText);
        }
        showShareFeedback(message, true);
      },
    });
  });
});
