/**
 * StudentHub Portal - Accessible JavaScript Client Enhancements
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. Password Strength & Match Validation for Registration
    // -------------------------------------------------------------
    const regForm = document.getElementById('registrationForm');
    const pwdInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthIndicator = document.getElementById('strengthIndicator');
    const matchError = document.getElementById('matchError');

    if (pwdInput && strengthIndicator) {
        pwdInput.addEventListener('input', () => {
            const val = pwdInput.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444', '#f97316', '#eab308', '#10b981'];
            const labels = ['Weak (requires min 8 chars)', 'Fair (add capital & number)', 'Good', 'Strong'];

            strengthIndicator.style.color = colors[score - 1] || '#ef4444';
            strengthIndicator.textContent = val.length > 0 ? labels[score - 1] || 'Too short' : '';
        });
    }

    if (confirmInput && pwdInput && matchError) {
        const checkMatch = () => {
            if (confirmInput.value && confirmInput.value !== pwdInput.value) {
                matchError.style.display = 'block';
                confirmInput.style.borderColor = '#ef4444';
            } else {
                matchError.style.display = 'none';
                confirmInput.style.borderColor = '#334155';
            }
        };
        confirmInput.addEventListener('input', checkMatch);
        pwdInput.addEventListener('input', checkMatch);
    }

    // -------------------------------------------------------------
    // 2. Client Inactivity Session Warning (Simulated client mirror)
    // -------------------------------------------------------------
    const timeoutDisplay = document.getElementById('sessionTimeoutCounter');
    if (timeoutDisplay) {
        let timeLeft = 300; // 5 minutes in seconds

        const updateTimer = () => {
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timeoutDisplay.textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;

            if (timeLeft <= 0) {
                window.location.href = 'timeout.php';
            } else {
                timeLeft--;
            }
        };

        setInterval(updateTimer, 1000);
        ['mousemove', 'keydown', 'scroll', 'click'].forEach(evt => {
            window.addEventListener(evt, () => {
                // Keep visually in sync with server last_activity updates on fetch or reload
            }, { passive: true });
        });
    }

    // -------------------------------------------------------------
    // 3. Quick Credential Autofill Buttons (Testing convenience)
    // -------------------------------------------------------------
    window.fillCredentials = function(role) {
        const emailField = document.getElementById('loginEmail');
        const passField = document.getElementById('loginPassword');
        if (!emailField || !passField) return;

        if (role === 'admin') {
            emailField.value = 'admin@charusat.edu.in';
            passField.value = 'Admin@123';
        } else if (role === 'student') {
            emailField.value = '25cs062@charusat.edu.in';
            passField.value = 'Student@123';
        }
    };
});
