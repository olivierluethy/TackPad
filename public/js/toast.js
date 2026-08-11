/* =========================================================================
   Toast notifications — the modern replacement for alert() and the old red
   inline markers. One global API, no framework dependency, styled by the
   .toast* classes in the Tailwind build (styleguide §4 / §1.5).

       TackpadToast.success("Task added");
       TackpadToast.error("Could not save the task");
       TackpadToast.info("Rescheduled");

   Toasts stack top-right, animate in, and auto-dismiss (errors linger longer).
   A single #toast-stack container is created lazily on first use.
   ========================================================================= */
(function (window, document) {
  var ICONS = {
    success: "fa-circle-check",
    error: "fa-circle-exclamation",
    info: "fa-circle-info",
  };

  function stack() {
    var el = document.getElementById("toast-stack");
    if (!el) {
      el = document.createElement("div");
      el.id = "toast-stack";
      el.className = "toast-stack";
      el.setAttribute("aria-live", "polite");
      el.setAttribute("aria-atomic", "false");
      document.body.appendChild(el);
    }
    return el;
  }

  function show(message, variant, timeout) {
    variant = variant || "info";
    var toast = document.createElement("div");
    toast.className = "toast toast--" + variant;
    toast.setAttribute("role", variant === "error" ? "alert" : "status");

    var icon = document.createElement("i");
    icon.className = "toast__icon fas " + (ICONS[variant] || ICONS.info);
    toast.appendChild(icon);

    var msg = document.createElement("span");
    msg.className = "toast__msg";
    msg.textContent = message; // textContent — never inject user markup
    toast.appendChild(msg);

    var close = document.createElement("button");
    close.type = "button";
    close.className = "toast__close";
    close.setAttribute("aria-label", "Dismiss");
    close.innerHTML = "&times;";
    close.onclick = function () {
      dismiss(toast);
    };
    toast.appendChild(close);

    stack().appendChild(toast);

    var ms = timeout != null ? timeout : variant === "error" ? 6000 : 3500;
    if (ms > 0) {
      setTimeout(function () {
        dismiss(toast);
      }, ms);
    }
    return toast;
  }

  function dismiss(toast) {
    if (!toast || toast.dataset.leaving) {
      return;
    }
    toast.dataset.leaving = "1";
    toast.style.transition = "opacity 0.25s ease, transform 0.25s ease";
    toast.style.opacity = "0";
    toast.style.transform = "translateX(120%)";
    setTimeout(function () {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 260);
  }

  window.TackpadToast = {
    show: show,
    success: function (m, t) {
      return show(m, "success", t);
    },
    error: function (m, t) {
      return show(m, "error", t);
    },
    info: function (m, t) {
      return show(m, "info", t);
    },
  };
})(window, document);
