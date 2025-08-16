<x-layouts.presensi 
    title="Presensi"
    x-data="userData" 
    x-init="updateUser()"
>
    <x-slot name="alert">
        {{-- {{ dd(session()->all()) }} --}}
        @if (session()->has('alert'))
            @foreach (session('alert') as $alert)
                <x-alert
                    :type="$alert->type"
                    message="{{ $alert->message }}"
                    :second-duration="$alert->duration"
                />
            @endforeach
        @endif
    </x-slot>

    <x-slot name="header">
        <div class="navbar px-4 py-4 w-full fixed top-0 left-0 right-0">
            <div class="navbar-start">
                <template x-if="$x.isLogin">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn bg-glassmorhpism glassmorphism text-glassmorphism text-white">
                            Log Out
                        </button>
                    </form>
                </template>
            </div>
            <div class="navbar-end">
                <template x-if="$x.isDetected">
                    <span 
                        class="btn bg-glassmorhpism glassmorphism text-glassmorphism text-white"
                        x-text="$x.isLogin ? $x.user?.name : 'Terakhir diketahui sebagai ' + $x.user?.name"
                    ></span>
                </template>
            </div>
        </div>
    </x-slot>

    <main class="min-h-screen w-full flex flex-col gap-6 items-center justify-center">
        <div class="flex flex-col gap-2 m-4">
            <div class="card bg-glassmorhpism glassmorphism px-3 py-6 w-full flex items-center justify-center">
                {{-- Motivasi --}}
                @if ($isPresenceAllowed && $message)
                    <div 
                        class="text-glassmorhism text-white text-center flex flex-col gap-8"
                        x-data="motivation"
                        x-init="start()"
                    >
                        <p 
                            class="break-words text-2xl"
                            x-text="motivation"
                        ></p>
                        <span class="font-bold" x-text="author"></span>
                    </div>
                @else
                    <div class="text-glassmorhism text-white text-center flex flex-col gap-8">
                        <p class="break-words text-2xl">{{ $message }}</p>
                    </div>
                @endif
            </div>
            <div 
                class="card bg-glassmorhpism glassmorphism p-2 text-center"
                x-data="presencesStatus"
            >
                {{-- Status --}}
                <span class="text-glassmorhism text-white text-sm inline-block">
                    <span 
                        x-bind="statusDom"
                        class="text-glassmorphism text-center"
                    ></span>
                </span>
            </div>
        </div>
        <div class="flex flex-col gap-3 w-full p-4">
            <template x-if="!$x.isLogin">
                <button 
                    class="flex-1 p-3 rounded-xl btn glassmorhpism bg-green-300/20 border-green-400/50 hover:border-green-400 text-glassmorphism text-green-400"
                    x-data="generatePresenceUrl"
                    x-bind="buttonDom"
                >
                    Log In dan Presensi
                </button>
            </template>
            <template x-if="$x.isLogin && !$x.isPresence">
                <button 
                    class="flex-1 p-3 rounded-xl btn glassmorhpism bg-green-300/20 border-green-400/50 hover:border-green-400 text-glassmorphism text-green-400"
                    x-data="generatePresenceUrl"
                    x-bind="buttonDom"
                >
                    Presensi
                </button>
            </template>
            <template x-if="!$x.isLogin">
                <button 
                    class="flex-1 p-3 rounded-xl btn bg-glassmorhpism glassmorphism text-glassmorphism text-white"
                    x-on:click="window.location.href='{{ route('login') }}'"
                >
                    Log In
                </button>
            </template>
        </div>
    </main>

</x-layouts.presensi>