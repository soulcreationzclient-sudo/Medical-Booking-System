/* ============================================
   MANUAL.JS - JavaScript Enhancements
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {

    // Initialize all components
    initSidebar();
    initAnimations();
    initTooltips();
    initSelect2();
    initDataTables();
    // initFormValidation();

});

/* ---------- Sidebar Functions ---------- */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('main.flex-fill');

    // Close sidebar on link click (mobile)
    if (sidebar) {
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
            });
        });
    }

    // Handle resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handleResize();
        }, 250);
    });
}

function handleResize() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth >= 992) {
        // Show sidebar on desktop
        if (sidebar) {
            sidebar.classList.add('show');
        }
    }
}

/* ---------- Animation Functions ---------- */
function initAnimations() {
    // Animate elements on scroll
    const animatedElements = document.querySelectorAll('.card, .stat-card, .hospital-card');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    } else {
        // Fallback for older browsers
        animatedElements.forEach(el => {
            el.classList.add('fade-in');
        });
    }
}

/* ---------- Initialize Tooltips ---------- */
function initTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => {
        new bootstrap.Tooltip(el);
    });
}

/* ---------- Initialize Select2 ---------- */
function initSelect2() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    }
}

/* ---------- Initialize DataTables ---------- */
/* ---------- Initialize DataTables ---------- */
function initDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        // Check if there are any tables to initialize
        const tables = document.querySelectorAll('.datatable');

        tables.forEach(table => {
            if (!$.fn.DataTable.isDataTable(table)) {
                $(table).DataTable({
                    // CRITICAL: Enable horizontal scrolling
                    scrollX: true,
                    scrollCollapse: true,
                    autoWidth: false,

                    // Responsive settings
                    responsive: false, // Disable responsive plugin, use scrollX instead

                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_",
                        info: "Showing _START_ to _END_ of _TOTAL_",
                        infoEmpty: "No records",
                        infoFiltered: "(filtered from _MAX_)",
                        zeroRecords: "No matching records found",
                        emptyTable: "No data available",
                        paginate: {
                            first: "««",
                            last: "»»",
                            next: "›",
                            previous: "‹"
                        }
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    order: [[0, 'asc']],
                    columnDefs: [
                        {
                            targets: 'no-sort',
                            orderable: false,
                            searchable: false
                        },
                        {
                            // Last column (Action) - disable sorting
                            targets: -1,
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',

                    // Important: Initialize with proper width calculation
                    initComplete: function() {
                        // Force table to calculate proper width
                        this.api().columns.adjust().draw();

                        // Add scroll event listener
                        $(this).closest('.table-responsive').on('scroll', function() {
                            var scrollLeft = $(this).scrollLeft();
                            if (scrollLeft > 0) {
                                $(this).addClass('is-scrolled');
                            } else {
                                $(this).removeClass('is-scrolled');
                            }
                        });
                    }
                });
            }
        });
    }
}

/* ---------- Form Validation ---------- */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}



/* ---------- Loading Spinner Helper ---------- */
function showLoader(container, message = 'Loading...') {
    const loader = `
        <div class="loader-overlay d-flex flex-column align-items-center justify-content-center py-5">
            <div class="spinner mb-3"></div>
            <p class="text-muted">${message}</p>
        </div>
    `;
    container.innerHTML = loader;
}

function hideLoader(container, content) {
    container.innerHTML = content;
}

/* ---------- Confirm Dialog Helper ---------- */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/* ---------- Format Date Helper ---------- */
function formatDate(date, format = 'short') {
    const options = {
        short: { month: 'short', day: 'numeric', year: 'numeric' },
        long: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' },
        time: { hour: '2-digit', minute: '2-digit' }
    };

    return new Date(date).toLocaleDateString('en-US', options[format]);
}

/* ---------- Export Functions for Global Use ---------- */
window.showToast = showToast;
window.showLoader = showLoader;
window.hideLoader = hideLoader;
window.confirmAction = confirmAction;
window.formatDate = formatDate;
