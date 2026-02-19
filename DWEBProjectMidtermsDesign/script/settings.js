
const toggleBtn = document.getElementById("togglePasswordBtn");
const collapse = document.getElementById("passwordCollapse");
const form = document.getElementById("passwordForm");

const newPassword = document.getElementById("newPassword");
const confirmPassword = document.getElementById("confirmPassword");
const matchError = document.getElementById("matchError");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

/* =========================
   Toggle Collapse
========================= */

toggleBtn.addEventListener("click", () => {
  if (collapse.classList.contains("open")) {
    collapse.style.maxHeight = null;
    collapse.classList.remove("open");
    toggleBtn.textContent = "Change Password";
  } else {
    collapse.classList.add("open");
    collapse.style.maxHeight = collapse.scrollHeight + "px";
    toggleBtn.textContent = "Cancel";
  }
});

/* =========================
   Password Match Validation
========================= */

confirmPassword.addEventListener("input", () => {
  if (confirmPassword.value !== newPassword.value) {
    matchError.textContent = "Passwords do not match.";
  } else {
    matchError.textContent = "";
  }
});

/* =========================
   Password Strength
========================= */

newPassword.addEventListener("input", () => {
  const val = newPassword.value;
  let strength = 0;

  if (val.length > 7) strength++;
  if (/[A-Z]/.test(val)) strength++;
  if (/[0-9]/.test(val)) strength++;
  if (/[^A-Za-z0-9]/.test(val)) strength++;

  const levels = [
    { width: "25%", color: "#dc2626", text: "Weak" },
    { width: "50%", color: "#f59e0b", text: "Fair" },
    { width: "75%", color: "#3b82f6", text: "Good" },
    { width: "100%", color: "#16a34a", text: "Strong" }
  ];

  if (strength === 0) {
    strengthBar.style.width = "0%";
    strengthText.textContent = "";
  } else {
    strengthBar.style.width = levels[strength - 1].width;
    strengthBar.style.background = levels[strength - 1].color;
    strengthText.textContent = levels[strength - 1].text;
  }
});

/* =========================
   Prevent Submit if Invalid
========================= */

form.addEventListener("submit", (e) => {
  if (newPassword.value !== confirmPassword.value) {
    e.preventDefault();
    matchError.textContent = "Passwords do not match.";
  }
});

