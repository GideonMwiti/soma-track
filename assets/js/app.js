/**
 * SomaTrack - Main Application JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

    // ---- Sidebar Toggle (Mobile) ----
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'st-sidebar-overlay';
        document.body.appendChild(overlay);

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // ---- Auto-dismiss alerts after 5s ----
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    // ---- Animate elements on scroll ----
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
        observer.observe(el);
    });

    // ---- Form validation styling ----
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ---- AJAX helper ----
    window.stAjax = function(url, options = {}) {
        const defaults = {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        };
        const config = { ...defaults, ...options };
        
        if (config.body instanceof FormData) {
            // Don't set Content-Type for FormData
        } else if (config.body && typeof config.body === 'object') {
            config.headers['Content-Type'] = 'application/json';
            config.body = JSON.stringify(config.body);
        }

        return fetch(url, config)
            .then(function(response) {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .catch(function(error) {
                console.error('AJAX Error:', error);
                showToast('An error occurred. Please try again.', 'danger');
                throw error;
            });
    };

    // ---- Toast notification ----
    window.showToast = function(message, type = 'info') {
        let container = document.getElementById('st-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'st-toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const icons = {
            success: 'bi-check-circle-fill',
            danger: 'bi-exclamation-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const toastHTML = `
            <div class="toast align-items-center text-bg-${type} border-0" id="${toastId}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type] || icons.info} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', function() { toastEl.remove(); });
    };

    // ---- Confirm Delete ----
    window.confirmDelete = function(message = 'Are you sure you want to delete this?') {
        return confirm(message);
    };

    // ---- Character counter for textareas ----
    document.querySelectorAll('[data-char-counter]').forEach(function(textarea) {
        const maxLen = parseInt(textarea.getAttribute('maxlength')) || 1000;
        const counter = document.createElement('small');
        counter.className = 'text-muted d-block text-end mt-1';
        counter.textContent = `0 / ${maxLen}`;
        textarea.parentNode.appendChild(counter);
        textarea.addEventListener('input', function() {
            counter.textContent = `${textarea.value.length} / ${maxLen}`;
            counter.className = textarea.value.length > maxLen * 0.9 
                ? 'text-warning d-block text-end mt-1' 
                : 'text-muted d-block text-end mt-1';
        });
    });
});
