document.addEventListener("DOMContentLoaded", function () {

    const genKeyBtn     = document.getElementById('generateKeyBtn');
    const copyKeyBtn    = document.getElementById('copyKeyBtn');
    const apiKeyField   = document.getElementById('apiKeyField');

    /* ── 1. GENERATE API KEY ─────────────────────────────── */
    if (genKeyBtn) {
        genKeyBtn.addEventListener('click', function () {
            genKeyBtn.textContent = "Generating...";
            genKeyBtn.disabled    = true;

            fetch('ajax/generate_api_key.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': CSRF_TOKEN
                }
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        apiKeyField.value        = data.apiKey;
                        genKeyBtn.style.display  = 'none';
                        copyKeyBtn.style.display = 'block';
                        alert("API Key generated successfully! Copy it now — it cannot be recovered after leaving this page.");
                    } else {
                        alert("Error: " + data.error);
                        genKeyBtn.textContent = "Generate Key";
                        genKeyBtn.disabled    = false;
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    alert("Failed to connect to server.");
                    genKeyBtn.textContent = "Generate Key";
                    genKeyBtn.disabled    = false;
                });
        });
    }

    /* ── 2. COPY KEY ─────────────────────────────────────── */
    if (copyKeyBtn) {
        copyKeyBtn.addEventListener('click', function () {
            if (!apiKeyField.value) return;
            navigator.clipboard.writeText(apiKeyField.value)
                .then(function () {
                    var orig = copyKeyBtn.textContent;
                    copyKeyBtn.textContent = "Copied!";
                    setTimeout(function () { copyKeyBtn.textContent = orig; }, 2000);
                })
                .catch(function () { alert("Failed to copy text"); });
        });
    }

    /* ── 3. DELETE ACCOUNT ───────────────────────────────── */
    var deleteBtn         = document.getElementById("delete");
    var confirmDeleteBtn  = document.getElementById("confirmDeleteBtn");

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            var deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.classList.add('active');
            } else {
                if (confirm("Are you sure? This is permanent.")) executeDelete();
            }
        });
    }

    // Wire confirm button from delete-confirmation.php
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', executeDelete);
    }

    function executeDelete() {
        fetch('ajax/update_settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF_TOKEN
            },
            body: JSON.stringify({ action: 'delete_account' })
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.href = 'login.php';
                } else {
                    alert("Error: " + data.error);
                }
            });
    }

    /* ── 4. CHANGE PASSWORD ──────────────────────────────── */
    var toggleBtn       = document.getElementById("togglePasswordBtn");
    var collapse        = document.getElementById("passwordCollapse");
    var passwordForm    = document.getElementById("passwordForm");
    var newPassword     = document.getElementById("newPassword");
    var confirmPassword = document.getElementById("confirmPassword");
    var matchError      = document.getElementById("matchError");
    var strengthBar     = document.getElementById("strengthBar");
    var strengthText    = document.getElementById("strengthText");

    if (passwordForm) {
        passwordForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (newPassword.value !== confirmPassword.value) {
                matchError.textContent = "Passwords do not match";
                return;
            }
            var current = document.getElementById("currentPassword").value;

            fetch('ajax/update_settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': CSRF_TOKEN
                },
                body: JSON.stringify({
                    action:          'change_password',
                    currentPassword: current,
                    newPassword:     newPassword.value
                })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        alert("Password updated!");
                        passwordForm.reset();
                        strengthBar.style.width  = "0%";
                        strengthText.textContent = "";
                        toggleBtn.click();
                    } else {
                        matchError.textContent = data.error || "Update failed";
                    }
                });
        });
    }

    if (toggleBtn && collapse) {
        toggleBtn.addEventListener("click", function () {
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
    }

    if (confirmPassword && newPassword) {
        confirmPassword.addEventListener("input", function () {
            matchError.textContent = confirmPassword.value !== newPassword.value
                ? "Passwords do not match." : "";
        });
    }

    if (newPassword && strengthBar) {
        newPassword.addEventListener("input", function () {
            var val      = newPassword.value;
            var strength = 0;
            if (val.length > 7)            strength++;
            if (/[A-Z]/.test(val))         strength++;
            if (/[0-9]/.test(val))         strength++;
            if (/[^A-Za-z0-9]/.test(val))  strength++;

            var levels = [
                { width: "25%",  color: "#dc2626", text: "Weak"   },
                { width: "50%",  color: "#f59e0b", text: "Fair"   },
                { width: "75%",  color: "#3b82f6", text: "Good"   },
                { width: "100%", color: "#16a34a", text: "Strong" }
            ];

            if (strength === 0) {
                strengthBar.style.width  = "0%";
                strengthText.textContent = "";
            } else {
                strengthBar.style.width      = levels[strength - 1].width;
                strengthBar.style.background = levels[strength - 1].color;
                strengthText.textContent     = levels[strength - 1].text;
            }
        });
    }
});