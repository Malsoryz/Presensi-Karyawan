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
        }
    }));

    Alpine.data('presencesStatus', () => ({
        status: null,
        class: null,
        check() {
            axios.get("/api/presences/status")
                .then(res => {
                    if (res.data) {
                        this.status = res.data.status;
                        this.class = res.data.class;
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
            ['x-text']() {
                return this.status;
            },
            [':class']() {
                return this.class;
            }
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
        words: '',
        author: '',
        getMotivation() {
            axios.get("/api/motivation")
                .then(res => {
                    this.words = `" ${res.data.words} "`;
                    console.log(this.words);
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
        async getBg() {
            let res = await axios.get("/api/background");

            if (!res.data || !res.data.background) {
                const parent  = this.$refs.bgpoint;
                while (parent.firstChild) {
                    parent.removeChild(parent.firstChild);
                }
                return;
            }

            while (res.data.count > 1 && this.latestUrl && this.latestUrl === res.data.background) {
                res = await axios.get("/api/background");
                console.log('while...: ', res.data.background);
            }

            this.latestUrl = res.data.background;

            const img = new Image();
            img.src = res.data.background;
            img.onload = () => {
                img.className = 'min-h-screen w-full object-cover object-center';
                const parent = this.$refs.bgpoint;
                const child = parent.querySelector('img');                        
                if (parent.contains(child)) {
                    parent.replaceChild(img, child);
                } else parent.appendChild(img);
            };
        },
        start() {
            this.getBg();
            setInterval(() => this.getBg(), 5000);
        },
        bgDom: {
            ['x-init']() {
                return this.start();
            },
            ['x-ref']: 'bgpoint',
        },
    }));
});

Alpine.start();