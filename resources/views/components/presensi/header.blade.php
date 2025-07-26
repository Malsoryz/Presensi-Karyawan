@props([
    'xData' => '{}',
    'xInit' => '',
    'xText' => '',
    'xIf' => '',
])

<div class="navbar px-8 py-4 w-full fixed top-0 left-0 right-0">
    <div class="navbar-start">
        <button class="btn btn-soft">Login</button>
    </div>
    <div class="navbar-end">
        <div {{ $attributes->merge([
            'x-data' => $xData,
            'x-init' => $xInit,
        ]) }}>
            <template {{ $attributes->merge([
                'x-if' => $xIf,
            ]) }}>
                <span
                    {{ $attributes->merge(['x-text' => $xText]) }}
                    class="btn btn-soft"
                ></span>
            </template>
        </div>
    </div>
</div>