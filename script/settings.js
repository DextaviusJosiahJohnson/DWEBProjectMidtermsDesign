document.addEventListener("DOMContentLoaded", function() {
    
    // API KEY ELEMENTS
    const genKeyBtn = document.getElementById('generateKeyBtn');
    const copyKeyBtn = document.getElementById('copyKeyBtn');
    const apiKeyField = document.getElementById('apiKeyField');

    /* =========================================
       1. GENERATE API KEY LOGIC
       ========================================= */
    if(genKeyBtn) {
        genKeyBtn.addEventListener('click', () => {
            genKeyBtn.textContent = "Generating...";
            genKeyBtn.disabled = true;

            fetch('ajax/generate_api_key.php')
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        // Update UI
                        apiKeyField.value = data.apiKey;
                        genKeyBtn.style.display = 'none'; // Hide Generate
                        copyKeyBtn.style.display = 'block'; // Show Copy
                        alert("API Key generated successfully!");
                    } else {
                        alert("Error: " + data.error);
                        genKeyBtn.textContent = "Generate Key";
                        genKeyBtn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Failed to connect to server.");
                    genKeyBtn.textContent = "Generate Key";
                    genKeyBtn.disabled = false;
                });
        });
    }

    /* =========================================
       2. COPY KEY LOGIC
       ========================================= */
    if(copyKeyBtn) {
        copyKeyBtn.addEventListener('click', () => {
            if(!apiKeyField.value) return;
            
            navigator.clipboard.writeText(apiKeyField.value)
                .then(() => {
                    const originalText = copyKeyBtn.textContent;
                    copyKeyBtn.textContent = "Copied!";
                    setTimeout(() => copyKeyBtn.textContent = originalText, 2000);
                })
                .catch(err => alert("Failed to copy text"));
        });
    }


    /* =========================================
       3. EXISTING SETTINGS LOGIC (Password/Delete)
       ========================================= */
    const toggleBtn = document.getElementById("togglePasswordBtn");
    const collapse = document.getElementById("passwordCollapse");
    const passwordForm = document.getElementById("passwordForm");
    const deleteBtn = document.getElementById("delete");
    const newPassword = document.getElementById("newPassword");
    const confirmPassword = document.getElementById("confirmPassword");
    const matchError = document.getElementById("matchError");
    const strengthBar = document.getElementById("strengthBar");
    const strengthText = document.getElementById("strengthText");

    // DELETE ACCOUNT
    if(deleteBtn){
        deleteBtn.addEventListener("click", () => {
            const deleteModal = document.getElementById('deleteModal');
            if(deleteModal) {
                deleteModal.classList.add('active');
                const confirmDelete = deleteModal.querySelector('.primary');
                if(confirmDelete) {
                    // Clone to remove old listeners
                    const newBtn = confirmDelete.cloneNode(true);
                    confirmDelete.parentNode.replaceChild(newBtn, confirmDelete);
                    newBtn.addEventListener('click', executeDelete);
                }
            } else {
                if(confirm("Are you sure? This is permanent.")) executeDelete();
            }
        });
    }

    // Helper to close the delete modal if user cancels
    window.closeDeleteModal = function() {
        const deleteModal = document.getElementById('deleteModal');
        if(deleteModal) deleteModal.classList.remove('active');
    }

    function executeDelete() {
        fetch('ajax/update_settings.php', {
            method: 'POST',
            body: JSON.stringify({ action: 'delete_account' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // FIXED: Redirect to login.php instead of index.php
                window.location.href = 'login.php';
            }
            else {
                alert("Error: " + data.error);
            }
        });
    }

    // CHANGE PASSWORD
    if(passwordForm){
        passwordForm.addEventListener("submit", (e) => {
            e.preventDefault();
            if (newPassword.value !== confirmPassword.value) {
                matchError.textContent = "Passwords do not match";
                return;
            }

            const current = document.getElementById("currentPassword").value;
            fetch('ajax/update_settings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'change_password',
                    currentPassword: current,
                    newPassword: newPassword.value
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Password updated!");
                    passwordForm.reset();
                    strengthBar.style.width = "0%";
                    strengthText.textContent = "";
                    toggleBtn.click();
                } else {
                    matchError.textContent = data.error || "Update failed";
                }
            });
        });
    }

    // UI INTERACTIONS
    if(toggleBtn && collapse) {
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
    }

    if(confirmPassword && newPassword) {
        confirmPassword.addEventListener("input", () => {
            if (confirmPassword.value !== newPassword.value) {
                matchError.textContent = "Passwords do not match.";
            } else {
                matchError.textContent = "";
            }
        });
    }

    if(newPassword && strengthBar) {
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
    }
});