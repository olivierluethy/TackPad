/* =========================================================================
   Unified auth screen — one view that toggles Login / Register, with a live
   password-strength meter and a one-click strong-password generator.

   The strength estimator is a compact, dependency-free equivalent of zxcvbn:
   it scores on length, character-class variety and obvious weak patterns
   (repeats, sequences, common words), which keeps the auth page lean (no
   ~800KB dictionary download) while still guiding users to a strong password.
   ========================================================================= */

// Estimate password strength → { score: 0..4, label, percent }.
function estimatePasswordStrength(pw) {
  pw = pw || "";
  if (pw.length === 0) {
    return { score: 0, label: "", percent: 0 };
  }

  var classes = 0;
  if (/[a-z]/.test(pw)) classes++;
  if (/[A-Z]/.test(pw)) classes++;
  if (/[0-9]/.test(pw)) classes++;
  if (/[^A-Za-z0-9]/.test(pw)) classes++;

  // Base points: length is the dominant factor, variety a multiplier.
  var points = 0;
  points += Math.min(pw.length, 16) * 0.4; // up to ~6.4
  points += (classes - 1) * 1.2; // up to 3.6
  if (pw.length >= 12) points += 1;
  if (pw.length >= 16) points += 1;

  // Penalise obvious weakness.
  if (/(.)\1\1/.test(pw)) points -= 1.5; // 3+ repeated chars
  if (/^(?:[a-z]+|[0-9]+)$/i.test(pw)) points -= 1.5; // single class only
  if (/(?:abc|123|qwe|password|admin|letmein|welcome)/i.test(pw)) points -= 2;

  var score = Math.max(0, Math.min(4, Math.round(points / 2.2)));

  var labels = ["Very weak", "Weak", "Fair", "Strong", "Very strong"];
  var percents = [12, 32, 55, 80, 100];
  return { score: score, label: labels[score], percent: percents[score] };
}

// Generate a strong random password (crypto-backed when available). Guarantees
// at least one lower, upper, digit and symbol so it always scores well.
function generateStrongPassword(length) {
  length = length || 16;
  var lower = "abcdefghijkmnpqrstuvwxyz"; // no l
  var upper = "ABCDEFGHJKLMNPQRSTUVWXYZ"; // no I, O
  var digits = "23456789"; // no 0, 1
  var symbols = "!@#$%^&*()-_=+[]{}";
  var all = lower + upper + digits + symbols;

  function pick(set) {
    var idx;
    if (window.crypto && window.crypto.getRandomValues) {
      var a = new Uint32Array(1);
      window.crypto.getRandomValues(a);
      idx = a[0] % set.length;
    } else {
      idx = Math.floor(Math.random() * set.length);
    }
    return set.charAt(idx);
  }

  var out = [pick(lower), pick(upper), pick(digits), pick(symbols)];
  for (var i = out.length; i < length; i++) {
    out.push(pick(all));
  }

  // Fisher–Yates shuffle so the guaranteed classes aren't always up front.
  for (var j = out.length - 1; j > 0; j--) {
    var k = Math.floor(Math.random() * (j + 1));
    var tmp = out[j];
    out[j] = out[k];
    out[k] = tmp;
  }
  return out.join("");
}

/* Alpine component for the auth screen. Registered on alpine:init so it is
   available before the DOM is scanned. */
document.addEventListener("alpine:init", function () {
  window.Alpine.data("authScreen", function (initialMode) {
    return {
      mode: initialMode === "register" ? "register" : "login",
      password: "",
      confirm: "",
      showPassword: false,
      strength: { score: 0, label: "", percent: 0 },

      onPasswordInput: function () {
        this.strength = estimatePasswordStrength(this.password);
      },

      generate: function () {
        var pw = generateStrongPassword(16);
        this.password = pw;
        this.confirm = pw;
        this.showPassword = true;
        this.strength = estimatePasswordStrength(pw);
      },
    };
  });
});
