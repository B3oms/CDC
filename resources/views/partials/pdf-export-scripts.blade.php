<script>
(function () {
    let pdfDropdownOpenedAt = 0;

    window.closePdfDropdown = function (id) {
        const dropdown = document.getElementById(id || 'pdfOptions');
        if (dropdown) {
            dropdown.classList.remove('is-open');
        }
        const toggle = document.querySelector('[data-pdf-toggle="' + (id || 'pdfOptions') + '"]');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    };

    window.closeAllPdfDropdowns = function () {
        document.querySelectorAll('.pdf-options.is-open').forEach(function (dropdown) {
            dropdown.classList.remove('is-open');
        });
        document.querySelectorAll('[data-pdf-toggle]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('click', function (event) {
        const toggleBtn = event.target.closest('[data-pdf-toggle]');
        if (toggleBtn) {
            event.stopPropagation();
            const dropdownId = toggleBtn.getAttribute('data-pdf-toggle');
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;

            const isOpen = dropdown.classList.contains('is-open');
            window.closeAllPdfDropdowns();

            if (!isOpen) {
                dropdown.classList.add('is-open');
                toggleBtn.setAttribute('aria-expanded', 'true');
                pdfDropdownOpenedAt = Date.now();
            }
            return;
        }

        if (event.target.closest('.pdf-options')) {
            event.stopPropagation();
            return;
        }

        if (Date.now() - pdfDropdownOpenedAt < 200) {
            return;
        }

        if (!event.target.closest('.pdf-export-dropdown')) {
            window.closeAllPdfDropdowns();
        }
    });
})();
</script>
