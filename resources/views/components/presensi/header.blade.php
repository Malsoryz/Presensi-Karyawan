@props([
    'user' => null,
])

<div class="navbar px-8 py-4 w-full fixed top-0 left-0 right-0">
    <div class="navbar-start">
        <template x-if="!$store.presensi.isLogin">
            <button 
                class="btn btn-soft"
                x-on:click="window.location.href='{{ route('login') }}'"
            >
                Log In
            </button>
        </template>
        <template x-if="$store.presensi.isLogin">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-soft">
                    Log Out
                </button>
            </form>
        </template>
    </div>
    <div class="navbar-end">
        <template x-if="$store.presensi.isDetected">
            <span class="btn btn-soft" x-text="$store.presensi.user?.name"></span>
        </template>
    </div>
</div>