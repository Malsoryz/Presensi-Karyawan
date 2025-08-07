@php
    $record = $getRecord();
@endphp

<div 
    x-data="{
        imageUrl: null,
        init() {
            const img = new Image();
            img.src = '{{ asset("storage/{$record->image_path}") }}';
            img.onload = () => {
                this.imageUrl = img.src;
            };
        },
    }" 
    x-init="init()"
>
    <template x-if="imageUrl">
        <img x-bind:src="imageUrl" alt="{{ $record->name }}"/>
    </template>
</div>
