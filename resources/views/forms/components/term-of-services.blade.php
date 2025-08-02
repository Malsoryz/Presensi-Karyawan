<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="px-4">
        {!! str($getTermOfServices())->sanitizeHtml() !!}
    </div>
</x-dynamic-component>
