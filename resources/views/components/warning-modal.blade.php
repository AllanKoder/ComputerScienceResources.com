@props([])

<div x-data="warningModalComponent()" 
    @open-warning-modal.window="openModal($event)">
    <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="modalIsOpen" @keydown.esc.window="modalIsOpen = false" 
    @keydown.enter="successCallback" @click.self="modalIsOpen = false" class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="modalTitle" style="will-change: opacity, transform;">
        <!-- Modal Dialog -->
        <div x-show="modalIsOpen" x-transition:enter="ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-xl border border-slate-300 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300" style="will-change: opacity, transform;">
            <!-- Dialog Header -->
            <div class="flex items-center justify-between border-b border-slate-300 bg-slate-100/60 px-4 py-2 dark:border-slate-700 dark:bg-slate-900/20">
                <div class="flex items-center justify-center rounded-full bg-amber-500/20 text-amber-500 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <button @click="modalIsOpen = false" aria-label="close modal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Dialog Body -->
            <div class="px-4 text-center"> 
                <h3 id="modalTitle" class="mb-2 font-semibold tracking-wide text-black dark:text-white" x-text="title"></h3>
                <p x-text="description"></p>
            </div>
            <!-- Dialog Footer -->
            <div class="flex items-center justify-center border-slate-300 p-4 dark:border-slate-700">
                <button @click="successCallback" type="button" class="w-full cursor-pointer whitespace-nowrap rounded-xl bg-amber-500 px-4 py-2 text-center text-sm font-semibold tracking-wide text-white hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 active:opacity-100 active:outline-offset-0">
                    <p x-text="closeText"></p    
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function warningModalComponent() {
        return {
            modalIsOpen: false,
            title: '',
            description: '',
            closeText: 'Close',
            successCallback() {
                this.modalIsOpen = false;
                console.log('Password recovery initiated');
            },
            openModal(event) {
                const { title, description, closeText } = event.detail;
                this.title = title || this.title;
                this.description = description || this.description;
                this.modalIsOpen = true;
                this.closeText = closeText || this.closeText;
            }
        };
    }
</script>
