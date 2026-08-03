import './bootstrap';
import Alpine from 'alpinejs';
import { promoCarousel, initPublicCatalog } from './Components/publicCatalog';
import { profitBranchChart, branchChart } from './Components/ownerDashboard';
import { initAdminCatalog } from './Components/adminCatalog';
import { initPayment } from './Components/payment';

window.Alpine = Alpine;
window.initPublicCatalog = initPublicCatalog;
window.initAdminCatalog = initAdminCatalog;
window.initPayment = initPayment;
Alpine.data('promoCarousel', promoCarousel);
Alpine.data('profitBranchChart', profitBranchChart);
Alpine.data('branchChart', branchChart);


Alpine.data('toastManager', () => ({
    toasts: [],
    
    add(type, message) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message });
        
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },
    
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    },
    
    success(message) { this.add('success', message); },
    error(message) { this.add('error', message); },
    warning(message) { this.add('warning', message); },
    info(message) { this.add('info', message); },
}));

function addToast(type, message) {
    const colors = {
        success: 'from-emerald-600 to-emerald-700 shadow-emerald-300/30',
        error: 'from-rose-600 to-rose-700 shadow-rose-300/30',
        warning: 'from-amber-500 to-amber-600 shadow-amber-300/30',
        info: 'from-sky-500 to-sky-600 shadow-sky-300/30',
    };
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `animate-toastIn flex items-center gap-2 rounded-lg bg-gradient-to-r ${colors[type] || colors.info} px-3 py-2 text-xs font-medium text-white shadow-lg backdrop-blur-sm pointer-events-auto`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s';
        setTimeout(() => toast.remove(), 350);
    }, 3000);
}

Alpine.store('toastManager', {
    success(message) { addToast('success', message); },
    error(message) { addToast('error', message); },
    info(message) { addToast('info', message); },
    warning(message) { addToast('warning', message); },
});

Alpine.data('modal', () => ({
    show: false,
    title: '',
    message: '',
    confirmText: 'Ya',
    cancelText: 'Batal',
    onConfirm: null,
    onCancel: null,
    
    open({ title, message, confirmText, cancelText, onConfirm, onCancel }) {
        this.title = title || 'Konfirmasi';
        this.message = message || 'Apakah Anda yakin?';
        this.confirmText = confirmText || 'Ya';
        this.cancelText = cancelText || 'Batal';
        this.onConfirm = onConfirm || null;
        this.onCancel = onCancel || null;
        this.show = true;
    },
    
    confirm() {
        if (this.onConfirm) this.onConfirm();
        this.show = false;
    },
    
    cancel() {
        if (this.onCancel) this.onCancel();
        this.show = false;
    },
}));

Alpine.data('twoFactorSetup', () => ({
    loading: true,
    enabled: false,
    qrCode: null,
    secret: null,
    verifyCode: '',
    verifyError: '',
    verifying: false,
    disableCode: '',
    disableError: '',
    disabling: false,

    async load2fa() {
        this.loading = true;
        try {
            const response = await fetch('/settings/security/2fa/setup');
            const data = await response.json();
            this.enabled = data.enabled;
            this.qrCode = data.qr_code;
            this.secret = data.secret;
        } catch (e) {
            console.error('Failed to load 2FA setup', e);
        } finally {
            this.loading = false;
        }
    },

    async enable2fa() {
        if (!this.verifyCode || this.verifyCode.length !== 6) return;
        this.verifying = true;
        this.verifyError = '';
        try {
            const response = await fetch('/settings/security/2fa/enable', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content },
                body: JSON.stringify({ secret: this.secret, code: this.verifyCode }),
            });
            const data = await response.json();
            if (data.success) {
                this.enabled = true;
                this.qrCode = null;
                this.secret = null;
                this.verifyCode = '';
            } else {
                this.verifyError = data.error || 'Gagal mengaktifkan 2FA.';
            }
        } catch (e) {
            this.verifyError = 'Terjadi kesalahan. Silakan coba lagi.';
        } finally {
            this.verifying = false;
        }
    },

    async disable2fa() {
        if (!this.disableCode || this.disableCode.length !== 6) return;
        this.disabling = true;
        this.disableError = '';
        try {
            const response = await fetch('/settings/security/2fa/disable', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content },
                body: JSON.stringify({ code: this.disableCode }),
            });
            const data = await response.json();
            if (data.success) {
                this.enabled = false;
                this.disableCode = '';
            } else {
                this.disableError = data.error || 'Gagal menonaktifkan 2FA.';
            }
        } catch (e) {
            this.disableError = 'Terjadi kesalahan. Silakan coba lagi.';
        } finally {
            this.disabling = false;
        }
    },
}));

Alpine.data('themeEditor', () => ({
    colors: { primary: '#d97706', sidebar: '#3b1e10', sidebarText: '#ffffff', accent: '#f59e0b', bgBase: '#fdf8f0', bgGradient: '#fde68a', bgBlob: '#f59e0b' },
    presets: [
        { name: 'classic', label: 'Classic', primary: '#d97706', sidebar: '#3b1e10', sidebarText: '#ffffff', accent: '#f59e0b', bgBase: '#fdf8f0', bgGradient: '#fde68a', bgBlob: '#f59e0b' },
        { name: 'emerald', label: 'Emerald', primary: '#059669', sidebar: '#064e3b', sidebarText: '#ffffff', accent: '#34d399', bgBase: '#ecfdf5', bgGradient: '#a7f3d0', bgBlob: '#34d399' },
        { name: 'rose', label: 'Rose', primary: '#e11d48', sidebar: '#4c0519', sidebarText: '#ffffff', accent: '#fb7185', bgBase: '#fdf2f2', bgGradient: '#fecaca', bgBlob: '#fb7185' },
        { name: 'ocean', label: 'Ocean', primary: '#0284c7', sidebar: '#0c4a6e', sidebarText: '#ffffff', accent: '#38bdf8', bgBase: '#f0f9ff', bgGradient: '#bae6fd', bgBlob: '#38bdf8' },
        { name: 'violet', label: 'Violet', primary: '#7c3aed', sidebar: '#2e1065', sidebarText: '#ffffff', accent: '#a78bfa', bgBase: '#f5f3ff', bgGradient: '#ddd6fe', bgBlob: '#a78bfa' },
        { name: 'sunset', label: 'Sunset', primary: '#ea580c', sidebar: '#7c2d12', sidebarText: '#ffffff', accent: '#fb923c', bgBase: '#fff7ed', bgGradient: '#fed7aa', bgBlob: '#fb923c' },
        { name: 'forest', label: 'Forest', primary: '#15803d', sidebar: '#14532d', sidebarText: '#ffffff', accent: '#4ade80', bgBase: '#f0fdf4', bgGradient: '#bbf7d0', bgBlob: '#4ade80' },
        { name: 'sky', label: 'Sky', primary: '#0369a1', sidebar: '#0c4a6e', sidebarText: '#ffffff', accent: '#7dd3fc', bgBase: '#f0f9ff', bgGradient: '#e0f2fe', bgBlob: '#7dd3fc' },
        { name: 'wine', label: 'Wine', primary: '#9d174d', sidebar: '#4a0e2e', sidebarText: '#ffffff', accent: '#f472b6', bgBase: '#fdf2f8', bgGradient: '#fbcfe8', bgBlob: '#f472b6' },
        { name: 'slate', label: 'Slate', primary: '#475569', sidebar: '#1e293b', sidebarText: '#ffffff', accent: '#94a3b8', bgBase: '#f8fafc', bgGradient: '#e2e8f0', bgBlob: '#94a3b8' },
        { name: 'amber', label: 'Amber', primary: '#d97706', sidebar: '#451a03', sidebarText: '#ffffff', accent: '#fbbf24', bgBase: '#fffbeb', bgGradient: '#fde68a', bgBlob: '#fbbf24' },
        { name: 'teal', label: 'Teal', primary: '#0d9488', sidebar: '#134e4a', sidebarText: '#ffffff', accent: '#2dd4bf', bgBase: '#f0fdfa', bgGradient: '#ccfbf1', bgBlob: '#2dd4bf' },
        { name: 'coffee', label: 'Coffee', primary: '#92400e', sidebar: '#3b1e10', sidebarText: '#ffffff', accent: '#d97706', bgBase: '#fffbeb', bgGradient: '#fef3c7', bgBlob: '#d97706' },
        { name: 'lavender', label: 'Lavender', primary: '#6d28d9', sidebar: '#2e1065', sidebarText: '#ffffff', accent: '#c4b5fd', bgBase: '#f5f3ff', bgGradient: '#ede9fe', bgBlob: '#c4b5fd' },
        { name: 'cherry', label: 'Cherry', primary: '#dc2626', sidebar: '#450a0a', sidebarText: '#ffffff', accent: '#f87171', bgBase: '#fef2f2', bgGradient: '#fecaca', bgBlob: '#f87171' },
        { name: 'mono', label: 'Monochrome', primary: '#57534e', sidebar: '#292524', sidebarText: '#ffffff', accent: '#a8a29e', bgBase: '#fafaf9', bgGradient: '#e7e5e4', bgBlob: '#a8a29e' },
        { name: 'bw', label: 'Hitam Putih', primary: '#171717', sidebar: '#000000', sidebarText: '#ffffff', accent: '#525252', bgBase: '#fafafa', bgGradient: '#d4d4d4', bgBlob: '#525252' },
        { name: 'dark', label: 'Dark Mode', primary: '#eab308', sidebar: '#09090b', sidebarText: '#ffffff', accent: '#fbbf24', bgBase: '#1c1917', bgGradient: '#292524', bgBlob: '#eab308' },
    ],

    initTheme(settings) {
        this.colors.primary = settings.theme_primary || '#d97706';
        this.colors.sidebar = settings.theme_sidebar || '#3b1e10';
        this.colors.sidebarText = settings.theme_sidebar_text || '#ffffff';
        this.colors.accent = settings.theme_accent || '#f59e0b';
        this.colors.bgBase = settings.bg_base || '#fdf8f0';
        this.colors.bgGradient = settings.bg_gradient || '#fde68a';
        this.colors.bgBlob = settings.bg_blob || (settings.theme_accent || '#f59e0b');
        this.applyColors();
    },

    applyColors() {
        document.documentElement.style.setProperty('--theme-primary', this.colors.primary);
        document.documentElement.style.setProperty('--theme-sidebar', this.colors.sidebar);
        document.documentElement.style.setProperty('--theme-sidebar-text', this.colors.sidebarText);
        document.documentElement.style.setProperty('--theme-accent', this.colors.accent);
        document.documentElement.style.setProperty('--bg-base', this.colors.bgBase);
        document.documentElement.style.setProperty('--bg-gradient', this.colors.bgGradient);
        document.documentElement.style.setProperty('--bg-blob', this.colors.bgBlob);
    },

    applyPreset(preset) {
        this.colors.primary = preset.primary;
        this.colors.sidebar = preset.sidebar;
        this.colors.sidebarText = preset.sidebarText;
        this.colors.accent = preset.accent;
        this.colors.bgBase = preset.bgBase;
        this.colors.bgGradient = preset.bgGradient;
        this.colors.bgBlob = preset.bgBlob;
        this.applyColors();
    },

    isActivePreset(preset) {
        return this.colors.primary === preset.primary
            && this.colors.sidebar === preset.sidebar
            && this.colors.sidebarText === preset.sidebarText
            && this.colors.accent === preset.accent;
    },
}));

Alpine.start();
