// Toggle Settings Menu dropdown
export function toggleSettingsMenu() {
    const submenu = document.getElementById('settingsSubmenu');
    const icon = document.getElementById('settingsToggleIcon');
    if (submenu) {
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-90');
            localStorage.setItem('settingsMenuOpen', 'true');
        } else {
            submenu.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-90');
            localStorage.setItem('settingsMenuOpen', 'false');
        }
    }
}

// Bind to window to be accessible by inline onclick attributes
window.toggleSettingsMenu = toggleSettingsMenu;

document.addEventListener('DOMContentLoaded', function() {
    // Auto-open settings if route matches (read from body data-attribute)
    const body = document.querySelector('body');
    const isSettingsRoute = body ? body.getAttribute('data-is-settings-route') === 'true' : false;
    const settingsMenuOpen = localStorage.getItem('settingsMenuOpen') === 'true';
    if (isSettingsRoute || settingsMenuOpen) {
        const submenu = document.getElementById('settingsSubmenu');
        const icon = document.getElementById('settingsToggleIcon');
        if (submenu) submenu.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-90');
    }

    // Toggle mobile menu
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('-translate-x-full');
        });
    }
    
    if (closeSidebarBtn && sidebar) {
        closeSidebarBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    }

    // Close sidebar when clicking outside (mobile & tablet)
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 1024) {
            if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                if (!sidebar.contains(e.target) && (!mobileMenuBtn || !mobileMenuBtn.contains(e.target))) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        }
    });

    // Clock Widget
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dayString = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' });
        const widget = document.getElementById('currentTime');
        if (widget) {
            widget.innerHTML = `<span class="text-slate-900">${dayString}</span> - ${timeString}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Auto-fadeout for global session flash alerts
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.maxHeight = '0px';
            alert.style.padding = '0px';
            alert.style.margin = '0px';
            alert.style.border = '0px';
            setTimeout(function() {
                alert.remove();
            }, 650);
        }, 4000); // Wait 4 seconds, then auto-fade and slide out beautifully
    });
});

// Helper to set button to loading state and disable it (to prevent double requests)
export function setButtonLoadingState(submitBtn, loadingText = 'Memproses...') {
    if (!submitBtn || submitBtn.classList.contains('form-submitting')) {
        return;
    }

    submitBtn.classList.add('form-submitting');

    // Keep the original width to avoid layout shifts
    const rect = submitBtn.getBoundingClientRect();
    if (rect.width > 0) {
        submitBtn.style.width = `${rect.width}px`;
    }

    // Save original content in dataset
    submitBtn.dataset.originalHtml = submitBtn.innerHTML;

    // Change inner HTML to a nice spinner + text
    submitBtn.innerHTML = `
        <span class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>${loadingText}</span>
        </span>
    `;

    // Disable the button in a brief timeout to avoid canceling form submit in some browsers
    setTimeout(() => {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
    }, 10);
}

// Bind helper to window context
window.setButtonLoadingState = setButtonLoadingState;

// Global SweetAlert2 Form Listeners
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!form) return;

    // 1. Intercept Logout
    if (form.classList.contains('confirm-logout')) {
        e.preventDefault();
        Swal.fire({
            title: 'Keluar Aplikasi?',
            text: 'Apakah Anda yakin ingin keluar dari aplikasi Futsal Board?',
            icon: 'question',
            width: '22rem',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // emerald-500
            cancelButtonColor: '#94a3b8', // slate-400
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl text-xs font-bold px-4.5 py-2.5',
                cancelButton: 'rounded-xl text-xs font-bold px-4.5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                setButtonLoadingState(submitBtn);
                form.submit();
            }
        });
        return;
    }

    // 2. Intercept Deletions
    if (form.classList.contains('confirm-delete')) {
        e.preventDefault();
        const message = form.getAttribute('data-message') || 'Apakah Anda yakin ingin menghapus data ini?';
        Swal.fire({
            title: 'Hapus Data?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // red-500
            cancelButtonColor: '#94a3b8', // slate-400
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl text-xs font-bold px-4.5 py-2.5',
                cancelButton: 'rounded-xl text-xs font-bold px-4.5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                setButtonLoadingState(submitBtn);
                form.submit();
            }
        });
        return;
    }

    // 3. Intercept regular POST form submissions (excluding GET/filter forms)
    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method === 'POST') {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            setButtonLoadingState(submitBtn);
        }
    }
});
