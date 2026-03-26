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

    // ---- Interactive Step Status Toggle ----
    document.querySelectorAll('.st-api-toggle-status').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const stepId = this.dataset.id;
            const status = this.dataset.status;
            const token  = this.dataset.token;
            const url    = `${SITE_URL}/api/steps.php?action=status&id=${stepId}&status=${status}&token=${token}&format=json`;
            
            stAjax(url, { method: 'GET' }).then(res => {
                if (res.success) {
                    showToast(res.message, 'success');
                    // Reload after a short delay to show updated state (or we could update UI dynamically)
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(res.message || 'Action failed', 'danger');
                }
            });
        });
    });

    // ---- Interactive Step Draft Toggle ----
    document.querySelectorAll('.st-api-toggle-draft').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const stepId = this.dataset.id;
            const token  = this.dataset.token;
            const url    = `${SITE_URL}/api/steps.php?action=toggle_draft&id=${stepId}&token=${token}&format=json`;
            
            stAjax(url, { method: 'GET' }).then(res => {
                if (res.success) {
                    showToast(res.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(res.message || 'Action failed', 'danger');
                }
            });
        });
    });

    // ---- Edit Step Modal Logic ----
    const editStepModal = document.getElementById('editStepModal');
    if (editStepModal) {
        const bsModal = new bootstrap.Modal(editStepModal);
        document.querySelectorAll('.st-edit-step-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('edit_step_id').value = this.dataset.id;
                document.getElementById('edit_step_title').value = this.dataset.title;
                document.getElementById('edit_step_desc').value = this.dataset.description;
                document.getElementById('edit_step_days').value = this.dataset.days || '';
                bsModal.show();
            });
        });
    }

    // ---- Edit Log Modal Logic ----
    const editLogModal = document.getElementById('editLogModal');
    if (editLogModal) {
        const bsLogModal = new bootstrap.Modal(editLogModal);
        document.querySelectorAll('.st-edit-log-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('edit_log_id').value = this.dataset.id;
                document.getElementById('edit_log_content').value = this.dataset.content;
                document.getElementById('edit_log_code').value = this.dataset.code;
                document.getElementById('edit_log_lang').value = this.dataset.lang;
                document.getElementById('edit_log_youtube').value = this.dataset.youtube;
                document.getElementById('edit_log_github').value = this.dataset.github;
                document.getElementById('edit_log_links').value = this.dataset.links;
                bsLogModal.show();
            });
        });
    }

    // ---- Edit Comment Modal Logic ----
    const editCommentModal = document.getElementById('editCommentModal');
    if (editCommentModal) {
        const bsCommentModal = new bootstrap.Modal(editCommentModal);
        document.querySelectorAll('.st-edit-comment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('edit_comment_id').value = this.dataset.id;
                document.getElementById('edit_comment_content').value = this.dataset.content;
                bsCommentModal.show();
            });
        });
    }

    // ---- Reply System Logic ----
    document.querySelectorAll('.st-reply-toggle').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            document.getElementById(`reply-form-${id}`).classList.remove('d-none');
        });
    });

    document.querySelectorAll('.st-reply-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById(`reply-form-${id}`).classList.add('d-none');
        });
    });

    // ---- Copy Code Utility ----
    document.querySelectorAll('.st-copy-code-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            const icon = this.querySelector('i');
            
            navigator.clipboard.writeText(code).then(() => {
                icon.classList.replace('bi-copy', 'bi-check2');
                this.classList.replace('text-muted', 'text-success');
                
                setTimeout(() => {
                    icon.classList.replace('bi-check2', 'bi-copy');
                    this.classList.replace('text-success', 'text-muted');
                }, 2000);
            });
        });
    });
});
