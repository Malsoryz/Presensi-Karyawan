@props([
    'user' => null,
])

<div class="navbar px-8 py-4 w-full fixed top-0 left-0 right-0">
    <div class="navbar-start">
        <button class="btn btn-soft">Login</button>
    </div>
    <div class="navbar-end">
        <span class="btn btn-soft">{{ $user ?? 'User' }}</span>
    </div>
</div>