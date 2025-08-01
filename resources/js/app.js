import Alpine from 'alpinejs';
import './bootstrap';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    const globalStoreName = 'app';

    Alpine.magic('x', () => Alpine.store(globalStoreName));

    Alpine.store(globalStoreName, Alpine.reactive({
        user: {},
        message: '',
        isDetected: false,
        isLogin: false,
    }));
    
    Alpine.data('userData', () => ({
        intervalId: null,
        updateUser() {
            // Cegah polling ganda
            if (this.$x.intervalId) return;

            this.getData();
            this.intervalId = setInterval(() => {
                console.log('memuat ulang');
                this.getData();
            }, 3000);
        },
        getData() {
            axios.get("/api/authuser")
                .then(res => {
                    const store = this.$x;

                    if (res.data.user) {
                        Object.assign(store.user, res.data.user);
                    }
                    store.message = res.data.message;
                    store.isDetected = res.data.is_detected;
                    store.isLogin = res.data.is_login;

                    if (store.isDetected) {
                        console.log(`user ${store.user.name} terdeteksi`);
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                        console.log('Polling dihentikan karena user terdeteksi');
                    }
                })
                .catch(err => {
                    console.error('Gagal melakukan request: ', err);
                });
        }
    }));

    Alpine.data('presencesData', () => Alpine.reactive({
        todayPresences: [],
        userAccumulation: {},
        refresh() {
            console.log('refresh data presensi');
            this.getData();
            setInterval(() => {
                console.log('refresh data presensi');
                this.getData();
            }, 10000);
        },
        getData() {
            if (this.$x.user.name) {
                axios.get("/api/presences", {
                    params: { name: this.$x.user?.name }
                })
                .then(res => {
                    if (res.data.user_accumulation) {
                        Object.assign(this.userAccumulation, res.data.user_accumulation);
                    }
                    this.todayPresences = res.data.today_presences;
                    console.log(JSON.stringify(this.todayPresences, null, 2));
                    console.log(JSON.stringify(this.userAccumulation, null, 2));
                })
                .catch(err => {
                    console.error('Gagal melakukan request: ', err);
                });
            } else console.log('data user belum ada');
        }
    }));

    Alpine.data('refreshQrCode', () => Alpine.reactive({
        qrCode: '',
        start() {
            console.log('Membuat qr code baru');
            this.loadQrCode();
            setInterval(() => {
                console.log('membuat qr code baru');
                this.loadQrCode();
            }, 60000);
        },
        loadQrCode() {
            axios.get("/api/qrcode")
                .then(res => {
                    this.qrCode = res.data;
                })
                .catch(err => {
                    console.error('Gagal memuat svg: ', err);
                });
        }
    }));
});

Alpine.start();