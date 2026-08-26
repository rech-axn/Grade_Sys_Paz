// GradeSys Paz - Interactive UI Helpers

document.addEventListener('DOMContentLoaded', () => {
    // 1. Modal Helper Functions
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Close modal when clicking outside modal box
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // 2. Real-time Final Grade and Remarks Calculator
    const prelimInputs = document.querySelectorAll('.calc-prelim');
    const midtermInputs = document.querySelectorAll('.calc-midterm');
    const finalsInputs = document.querySelectorAll('.calc-finals');

    function calculateRowGrade(container) {
        const pInput = container.querySelector('.calc-prelim');
        const mInput = container.querySelector('.calc-midterm');
        const fInput = container.querySelector('.calc-finals');
        const finalDisplay = container.querySelector('.calc-final-grade');
        const remarkDisplay = container.querySelector('.calc-remarks');
        const remarkInput = container.querySelector('.calc-remarks-input');

        if (!pInput || !mInput || !fInput) return;

        const p = parseFloat(pInput.value);
        const m = parseFloat(mInput.value);
        const f = parseFloat(fInput.value);

        if (!isNaN(p) && !isNaN(m) && !isNaN(f)) {
            // Formula: Prelim 30%, Midterm 30%, Finals 40%
            const computed = ((p * 0.3) + (m * 0.3) + (f * 0.4)).toFixed(2);
            if (finalDisplay) {
                if (finalDisplay.tagName === 'INPUT') {
                    finalDisplay.value = computed;
                } else {
                    finalDisplay.textContent = computed;
                }
            }

            const passed = parseFloat(computed) >= 75.0;
            const remarkText = passed ? 'Passed' : 'Failed';

            if (remarkDisplay) {
                remarkDisplay.textContent = remarkText;
                remarkDisplay.className = `badge badge-${passed ? 'success' : 'danger'}`;
            }

            if (remarkInput) {
                remarkInput.value = remarkText;
            }
        } else {
            if (finalDisplay && finalDisplay.tagName === 'INPUT') finalDisplay.value = '';
            if (remarkDisplay) remarkDisplay.textContent = 'Pending';
        }
    }

    document.querySelectorAll('.grade-calc-group').forEach(group => {
        group.addEventListener('input', () => calculateRowGrade(group));
    });

    // 3. Quick Table Search Filter
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        const targetTableId = input.getAttribute('data-table-search');
        const table = document.getElementById(targetTableId);
        if (!table) return;

        input.addEventListener('keyup', () => {
            const query = input.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 4. Mobile Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // 5. Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
