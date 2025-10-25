<div x-data="{ 
    show: false, 
    message: '', 
    type: 'success',
    timeout: null 
}" 
     @notify.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        clearTimeout(timeout);
        timeout = setTimeout(() => { show = false }, 4000);
     "
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed top-4 right-4 z-[100] max-w-sm w-full"
     style="display: none;">
    
    <div class="rounded-lg shadow-2xl p-4 border-2"
         :class="{
            'bg-green-500 border-green-600': type === 'success',
            'bg-red-500 border-red-600': type === 'error',
            'bg-yellow-500 border-yellow-600': type === 'warning',
            'bg-blue-500 border-blue-600': type === 'info'
         }">
        <div class="flex items-start gap-3">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <svg x-show="type === 'success'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="type === 'error'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="type === 'warning'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg x-show="type === 'info'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Message -->
            <div class="flex-1">
                <p class="text-white font-semibold text-sm" x-text="message"></p>
            </div>

            <!-- Close Button -->
            <button @click="show = false" class="flex-shrink-0 text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>