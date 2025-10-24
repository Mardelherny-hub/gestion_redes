<div wire:poll.10s="updateCount">
    @if($count > 0)
        <span class="ml-2 px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
            {{ $count }}
        </span>
    @endif
</div>