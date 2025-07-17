{{-- resources/views/components/accordion.blade.php --}}
@props(['multiple' => false])

<div x-data="{
    activeAccordion: {{ $multiple ? '[]' : 'null' }},
    toggle(index) {
        if ({{ $multiple ? 'true' : 'false' }}) {
            if (this.activeAccordion.includes(index)) {
                this.activeAccordion = this.activeAccordion.filter(i => i !== index)
            } else {
                this.activeAccordion.push(index)
            }
        } else {
            this.activeAccordion = this.activeAccordion === index ? null : index
        }
    },
    isActive(index) {
        return {{ $multiple ? 'this.activeAccordion.includes(index)' : 'this.activeAccordion === index' }}
    }
}" class="space-y-2">
    {{ $slot }}
</div>