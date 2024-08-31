@props([])

<div x-data="modalComponent()" 
    @open-confirm-modal.window="openModal($event)">
    <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.50ms x-trap.inert.noscroll="modalIsOpen" @keydown.esc.window="modalIsOpen = false" 
    @keydown.enter="successCallback" @click.self="modalIsOpen = false" class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8" role="dialog" aria-modal="true" aria-labelledby="modalTitle" style="will-change: opacity, transform;">
        <!-- Modal Dialog -->
        <div x-show="modalIsOpen" x-transition:enter="ease-out duration-200 delay-100 motion-reduce:transition-opacity" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-md border border-neutral-300 bg-white text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300" style="will-change: opacity, transform;">
            <!-- Dialog Header -->
            <div class="flex items-center jusstify-between border-b border-neutral-300 bg-neutral-50/60 p-4 dark:border-neutral-700 dark:bg-neutral-950/20">
                <h3 id="modalTitle" class="font-semibold tracking-wide text-neutral-900 dark:text-white" x-text="title"></h3>
                <span class="mx-auto"></span>
                <button @click="modalIsOpen = false" aria-label="close modal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Dialog Body -->
            <div class="px-4 py-3">
                <p x-text="description"></p>
            </div>
            <!-- Dialog Footer -->
            <div class="flex flex-col-reverse justify-between gap-2 border-t border-neutral-300 bg-neutral-50/60 p-4 dark:border-neutral-700 dark:bg-neutral-950/20 sm:flex-row sm:items-center md:justify-end">
                <button @click="failureCallback" type="button" class="cursor-pointer whitespace-nowrap px-4 bg-gray-100 py-2 text-center text-sm font-medium tracking-wide text-neutral-600 hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0 dark:text-neutral-300 dark:focus-visible:outline-white">
                    <p x-text="closeText"></p>
                </button>
                <button @click="successCallback" type="button" class="cursor-pointer whitespace-nowrap bg-black px-4 py-2 text-center text-sm font-medium tracking-wide text-neutral-100 hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0 dark:bg-white dark:text-black dark:focus-visible:outline-white">
                    <p x-text="confirmText"></p>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function modalComponent() {
        return {
            modalIsOpen: false,
            title: '',
            description: '',
            confirmText: 'Confirm',
            closeText: 'cancel',
            onSuccess: () => {},
            onFailure: () => {},
            successCallback() {
                this.modalIsOpen = false;
                this.onSuccess();
            },
            failureCallback() {
                this.modalIsOpen = false;
                this.onFailure();
            },
            openModal(event) {
                const { title, 
                    description, 
                    onSuccess, 
                    onFailure, 
                    confirmText, 
                    closeText 
                } = event.detail;
                this.title = title || this.title;
                this.description = description || this.description;
                this.modalIsOpen = true;
                this.onSuccess = onSuccess || this.onSuccess;
                this.onFailure = onFailure || this.onFailure;
                this.confirmText = confirmText || this.confirmText;
                this.closeText = closeText || this.closeText;
            }
        };
    }
</script>
