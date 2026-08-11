/* =========================================================================
   Profile modal — set an avatar by URL (confirm-before-apply preview) or by
   uploading a file. Persists over fetch and updates every avatar on the page
   in place (sidebar), with toast feedback. No page reload.
   ========================================================================= */

// Global open/close so the sidebar's inline onclick handlers can reach them.
function openProfileModal() {
  var m = document.getElementById("profileModal");
  if (m) m.style.display = "block";
}
function closeProfileModal() {
  var m = document.getElementById("profileModal");
  if (m) m.style.display = "none";
}

// Close on backdrop click.
window.addEventListener("click", function (event) {
  if (event.target && event.target.id === "profileModal") {
    closeProfileModal();
  }
});

// Replace every rendered avatar (sidebar) with the new src, or the monogram
// when cleared. Keeps the UI in sync with what a reload would show.
function refreshPageAvatars(src, initial) {
  document.querySelectorAll(".avatar-lg").forEach(function (el) {
    // Skip the live preview avatar inside the profile modal.
    if (el.closest("#profileModal")) return;
    if (src) {
      el.innerHTML = "";
      var img = document.createElement("img");
      img.src = src;
      img.alt = "Your avatar";
      el.appendChild(img);
    } else {
      el.textContent = initial;
    }
  });
}

document.addEventListener("alpine:init", function () {
  window.Alpine.data("profileForm", function (initialSrc, initial) {
    return {
      currentSrc: initialSrc || "",
      initial: initial || "?",
      url: "",
      preview: "",

      // Confirm-before-apply: show the pasted URL in the avatar circle first.
      previewUrl: function () {
        var value = (this.url || "").trim();
        if (!/^https?:\/\//i.test(value)) {
          window.TackpadToast.error("Please enter a valid http(s) image URL.");
          return;
        }
        var self = this;
        var probe = new Image();
        probe.onload = function () {
          self.preview = value;
        };
        probe.onerror = function () {
          self.preview = "";
          window.TackpadToast.error("That image couldn't be loaded. Check the URL.");
        };
        probe.src = value;
      },

      applyUrl: function () {
        var self = this;
        var body = new URLSearchParams();
        body.set("mode", "url");
        body.set("avatar_url", this.preview);
        postAvatar(body, null, function (data) {
          self.currentSrc = data.src;
          self.preview = "";
          self.url = "";
          refreshPageAvatars(data.src, self.initial);
          window.TackpadToast.success("Profile picture updated.");
          closeProfileModal();
        });
      },

      uploadFile: function (event) {
        var file = event.target.files && event.target.files[0];
        if (!file) return;
        var self = this;
        var form = new FormData();
        form.append("avatar", file);
        postAvatar(null, form, function (data) {
          self.currentSrc = data.src;
          self.preview = "";
          refreshPageAvatars(data.src, self.initial);
          window.TackpadToast.success("Profile picture updated.");
          event.target.value = "";
          closeProfileModal();
        });
      },

      removeAvatar: function () {
        var self = this;
        var body = new URLSearchParams();
        body.set("mode", "remove");
        postAvatar(body, null, function () {
          self.currentSrc = "";
          self.preview = "";
          refreshPageAvatars("", self.initial);
          window.TackpadToast.info("Profile picture removed.");
          closeProfileModal();
        });
      },
    };
  });
});

// Shared POST helper: pass either a URLSearchParams (urlencoded) or FormData.
function postAvatar(urlBody, formBody, onSuccess) {
  var options = { method: "POST" };
  if (formBody) {
    options.body = formBody; // browser sets multipart boundary
  } else {
    options.headers = { "Content-Type": "application/x-www-form-urlencoded" };
    options.body = urlBody.toString();
  }
  fetch("avatar", options)
    .then(function (r) {
      return r.json();
    })
    .then(function (data) {
      if (data && data.success) {
        onSuccess(data);
      } else {
        window.TackpadToast.error((data && data.error) || "Could not update the avatar.");
      }
    })
    .catch(function () {
      window.TackpadToast.error("Network error while updating the avatar.");
    });
}
