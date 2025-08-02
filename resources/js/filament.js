import './bootstrap';

Alpine.data('approval', (id) => ({
    userId: id,
    isApproved: false,
    init() {
        this.check();
        setInterval(() => {
            this.check();
            if (this.isApproved) {
                window.location.href = '/login';
            }
        }, 3000);
    },
    check() {
        axios.get("/api/approval", {
            params: { id: this.userId }
        })
            .then(res => {
                console.log(`is approved ${res.data.is_approved}`);
                this.isApproved = res.data.is_approved;
            })
            .catch(err => {
                console.error('Error check is approved? ', err);
            });
    },
    approvalDom: {
        ['x-init']() {
            this.init();
        },
    },
}));