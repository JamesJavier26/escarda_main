<div>
    {{-- Loading indicator --}}
    @if($this->hasLoadingPlaceholder())
        <div class="p-4 text-center text-gray-500">
            Loading users...
        </div>
    @else
        {{ $this->table() }}
    @endif
</div>