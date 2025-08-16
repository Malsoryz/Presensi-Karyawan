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
        isPresence: false,
    }));

    Alpine.data('datetime', () => ({
        time: null,
        date: null,
        updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            this.time = `${hours}:${minutes}:${seconds}`;
        },
        startTime() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },
        getDate() {
            axios.get("/api/datetime")
                .then(res => {
                    this.date = res.data;
                })
                .catch(err => {
                    console.error('Error date request: ', err);
                });
        },
        dateDom: {
            ['x-init']() {
                this.getDate();
            },
            ['x-text']() {
                return this.date;
            }
        },
        clockDom: {
            ['x-init']() {
                this.startTime();
            },
            ['x-text']() {
                return this.time;
            }
        },
    }))
    
    Alpine.data('userData', () => ({
        intervalId: null,
        updateUser() {
            // Cegah polling ganda
            if (this.intervalId) return;

            this.getData();
            this.intervalId = setInterval(() => {
                console.log('memuat ulang');
                this.getData();
            }, 3000); // 3 detik
        },
        getData() {
            axios.get("/api/authuser")
                .then(res => {
                    if (res.data.user) {
                        Object.assign(this.$x.user, res.data.user);
                    }
                    this.$x.message = res.data.message;
                    this.$x.isDetected = res.data.is_detected;
                    this.$x.isLogin = res.data.is_login;
                    if (res.data.is_presence) {
                        this.$x.isPresence = res.data.is_presence;
                    }

                    if (this.$x.isDetected) {
                        console.log(`user ${this.$x.user.name} terdeteksi`);
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                        console.log('Polling dihentikan karena user terdeteksi');
                    }
                })
                .catch(err => {
                    console.error('Gagal melakukan request: ', err);
                });
        },
    }));

    Alpine.data('presencesStatus', () => ({
        status: null,
        check() {
            axios.get("/api/presences/status")
                .then(res => {
                    if (res.data) {
                        this.status = res.data;
                    }
                })
                .catch(err => {
                    console.error('Status request error: ', err);
                });
        },
        start() {
            this.check();
            setInterval(() => this.check(), 5000); // 5 detik
        },
        statusDom: {
            ['x-init']() {
                this.start();
            },
            ['x-html']() {
                return this.status;
            },
        },
    }));

    Alpine.data('presencesData', () => ({
        todayPresences: [],
        userAccumulation: {},
        intervalId: null,
        refresh() {
            if (this.intervalId) return;

            console.log('refresh data presensi');
            this.getData();
            this.intervalId = setInterval(() => {
                console.log('refresh data presensi');
                this.getData();
            }, 10000); // 10 detik
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
                })
                .catch(err => {
                    console.error('Gagal melakukan request: ', err);
                });
            } else console.log('data user belum ada');
        },
        formatTime($iso) {
            const date = new Date($iso);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        },
        detailDom: {
            ['x-effect']() {
                if (this.$x.user.name) {
                    this.refresh();
                }
            }
        },
    }));

    Alpine.data('refreshQrCode', () => ({
        qrCode: '',
        start() {
            console.log('Membuat qr code baru');
            this.loadQrCode();
            setInterval(() => {
                console.log('membuat qr code baru');
                this.loadQrCode();
            }, 60000); // 1 menit
        },
        loadQrCode() {
            axios.get("/api/qrcode")
                .then(res => {
                    this.qrCode = res.data;
                })
                .catch(err => {
                    console.error('Gagal memuat svg: ', err);
                });
        },
        qrDom: {
            ['x-init']() {
                this.start();
            },
            ['x-html']() {
                return this.qrCode;
            },
            ['x-show']() {
                return this.qrCode;
            }
        },
    }));

    Alpine.data('motivation', () => ({
        motivation: '',
        author: '',
        getMotivation() {
            axios.get("/api/motivation")
                .then(res => {
                    this.motivation = `" ${res.data.motivation} "`;
                    console.log(this.motivation);
                    this.author = res.data.author;
                    console.log(this.author);
                })
                .catch(err => {
                    console.error('Kata-kata tidak ditemukan: ', err);
                });
        },
        start() {
            this.getMotivation();
            setInterval(() => {
                this.getMotivation();
            }, 60000); // 1 menit
        },
    }));

    Alpine.data('background', () => ({
        latestUrl: null,
        image: null,
        async getBg() {
            let res = await axios.get("/api/background");
            
            if (res.data.length === 0) {
                return;
            }
            
            const backgrounds = res.data;
            const filterBg = backgrounds.filter(item => item !== this.latestUrl);

            if (filterBg.length === 0) {
                return;
            }

            const background = filterBg[Math.floor(Math.random() * filterBg.length)];

            this.latestUrl = background;

            const img = new Image();
            img.src = background;
            img.onload = () => {
                this.image = img.src;
                console.log(this.image);
            };
        },
        start() {
            this.getBg();
            setInterval(() => this.getBg(), 8000);
        },
        bgDom: {
            ['x-init']() {
                return this.start();
            },
            ['x-bind:style']() {
                return this.image ? {
                    'background-image': `url('${this.image}')`
                } : null;
            }
        },
    }));

    Alpine.data('generatePresenceUrl', () => ({
        url: null,
        getUrl() {
            axios.get("/api/mobile/presence")
                .then(res => {
                    if (res.data) {
                        this.url = res.data;
                    }
                })
                .catch(err => {
                    console.error('Error request url: ', err);
                });
        },
        start() {
            this.getUrl();
            setInterval(() => this.getUrl(), 60000);
        },
        buttonDom: {
            ['x-init']() {
                this.start();
            },
            ['x-on:click']() {
                window.location.href = this.url;
            }
        },
    }));
});

Alpine.start();