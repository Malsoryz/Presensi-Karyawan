import Alpine from 'alpinejs';
import './bootstrap';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    console.log('✅ Alpine init');
    Alpine.data('presensiTabs', () => {
        console.log('📦 Alpine data init dijalankan');
        return {
            activeTab: '',
            tabs: [
                { id: 'tab-presensi', label: 'Presensi' },
                { id: 'tab-detail', label: 'Detail' },
            ],
            init() {
                this.activeTab = this.tabs[0].id;
            }
        };
    });
});

Alpine.start();