<div 
    class="flex flex-col gap-6"
    x-data="Alpine.reactive({
        isApproved: false,
        init() {
            this.check();
            setInterval(() => {
                this.check();
                if (this.isApproved) {
                    window.location.href = '{{ route('login') }}'
                }
            }, 3000);
        },
        check() {
            axios.get('{{ route('check.approval', ['id' => $this->id]) }}')
                .then(res => {
                    console.log(`is approved ${res.data.is_approved}`);
                    this.isApproved = res.data.is_approved;
                })
                .catch(err => {
                    console.error('Error check is approved? ', err);
                });
        },
    })"
    x-init="init()"
>
    <div class="flex w-full flex-col gap-2 text-center">
        <h1 class="text-xl font-medium dark:text-zinc-200" x-text="isApproved ? 'Akun anda telah di approve.' : 'Menunggu admin approve akun anda'"></h1>
        <p class="text-center text-sm dark:text-zinc-400" x-text="isApproved ? 'sebentar lagi anda akan di alihkan ke login' : 'sedang menunggu...'"></p>
    </div>
</div>
