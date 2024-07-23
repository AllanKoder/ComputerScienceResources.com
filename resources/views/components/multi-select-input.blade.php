@props(['options' => [], 'name'=>'', 'selectedOptions'=>[], 'saveToStorage'=>false, 'attributes' => []])
<div {{$attributes}} x-data="multiSelectComponent('{{ $name }}', {{ json_encode($options) }}, {{ json_encode($selectedOptions) }}, {{ $saveToStorage ? 'true' : 'false' }})" 
    class="w-full max-w-xs flex flex-col gap-1 min-w-40" 
    x-on:keydown="highlightFirstMatchingOption($event.key)" 
    x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false"
    x-init="initialize()"
    x-effect='setLocalData()'
    @clear-inputs-event.window="resetInputs()">
    <div class="relative">
        <!-- trigger button  -->
        <button type="button" role="combobox" class="inline-flex w-full items-center justify-between gap-2 whitespace-nowrap border-slate-300 bg-slate-100 px-4 py-2 text-sm font-medium tracking-wide text-slate-700 transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300 dark:focus-visible:outline-blue-600 border rounded-xl" aria-haspopup="listbox" aria-controls="skillsList" 
        x-on:click="isOpen = ! isOpen" 
        x-on:keydown.down.prevent="openedWithKeyboard = true" 
        x-on:keydown.enter.prevent="openedWithKeyboard = true" 
        x-on:keydown.space.prevent="openedWithKeyboard = true" 
        x-bind:aria-label="setLabelText()" 
        x-bind:aria-expanded="isOpen || openedWithKeyboard">
            <span class="text-sm w-full font-normal text-start overflow-hidden text-ellipsis  whitespace-nowrap"  
            x-text="setLabelText()"></span>
            <!-- Chevron  -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
        </button>
        <!-- hidden input to grab the selected value  -->
        <template x-for="selected in selectedOptions" x-bind:key="`${selected}-${'{{$name}}'}`">
            <input type="hidden" name="{{$name}}[]" x-bind:value="selected">
        </template>
        <ul x-cloak x-show="isOpen || openedWithKeyboard" id="skillsList" class="absolute z-10 left-0 top-11 flex max-h-44 w-full flex-col overflow-hidden overflow-y-auto border-slate-300 bg-slate-100 py-1.5 dark:border-slate-700 dark:bg-slate-800 border rounded-xl" 
        role="listbox" 
        x-on:click.outside="isOpen = false, openedWithKeyboard = false" 
        x-on:keydown.down.prevent="$focus.wrap().next()" 
        x-on:keydown.up.prevent="$focus.wrap().previous()" 
        x-transition x-trap="openedWithKeyboard">
            <template x-for="(item, index) in options" x-bind:key="`${item.value}-${'{{$name}}'}`">
                <!-- option  -->
                <li role="option">
                    <label class="flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-900/5 has-[:focus]:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5 dark:has-[:focus]:bg-white/5 [&:has(input:checked)]:text-black dark:[&:has(input:checked)]:text-white [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75" 
                    x-bind:for="'checkboxOption' + index + '{{$name}}'">
                        <div class="relative flex items-center">
                            <input type="checkbox" class="combobox-option before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden border border-slate-300 bg-slate-100 before:absolute before:inset-0 checked:border-blue-700 checked:before:bg-blue-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-800 checked:focus:outline-blue-700 active:outline-offset-0 disabled:cursor-not-allowed dark:border-slate-700 rounded dark:bg-slate-800 dark:checked:border-blue-600 dark:checked:before:bg-blue-600 dark:focus:outline-slate-300 dark:checked:focus:outline-blue-600" 
                            x-on:change="handleOptionToggle($el)" 
                            x-on:keydown.enter.prevent="$el.checked = ! $el.checked; handleOptionToggle($el)" 
                            :value="item.value" 
                            :id="'checkboxOption' + index + '{{$name}}'" 
                            x-init="
                            $el.checked = isSelected($el.value);
                            $watch('selectedOptions', _ => $el.checked = isSelected($el.value));
                            "/>
                            <!-- Checkmark  -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="4" class="pointer-events-none invisible absolute left-1/2 top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 text-slate-100 peer-checked:visible dark:text-slate-100" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span x-text="item.label"></span>
                    </label>
                </li>
            </template>
        </ul>
    </div>
</div>

<script>
    function multiSelectComponent(name, options, selectedOptions, saveToStorage) {
        return {
            options: options,
            isOpen: false,
            openedWithKeyboard: false,
            selectedOptions: [],
            get storageID() { return `${Alpine.store('getURL')()}-stored-${name}` },
            initialize() {
                // data from local storage
                let storedOptions = JSON.parse(localStorage.getItem(this.storageID));

                // if there are stored options, use them; otherwise, use the initial selected options
                if (storedOptions && saveToStorage) {
                    this.selectedOptions = storedOptions;
                } else {
                    this.selectedOptions = selectedOptions;
                }
            },
            resetInputs() {
                this.selectedOptions = [];
            },
            isSelected(option) {
                return this.selectedOptions.includes(option);
            },
            setLocalData() {
                if (saveToStorage) {
                    localStorage.setItem(this.storageID, JSON.stringify(this.selectedOptions)); 
                }
            },
            setLabelText() {
                const count = this.selectedOptions.length;

                // if there are no selected options
                if (count === 0) return 'Please Select';

                // join the selected options with a comma
                return this.selectedOptions.join(', ');
            },
            highlightFirstMatchingOption(pressedKey) {
                // if Enter pressed, do nothing
                if (pressedKey === 'Enter') return

                // find and focus the option that starts with the pressed key
                const option = this.options.find((item) =>
                    item.label.toLowerCase().startsWith(pressedKey.toLowerCase()),
                )
                if (option) {
                    const index = this.options.indexOf(option)
                    const allOptions = document.querySelectorAll('.combobox-option')
                    if (allOptions[index]) {
                        allOptions[index].focus()
                    }
                }
            },
            handleOptionToggle(option) {
                if (option.checked) {
                    this.selectedOptions.push(option.value)
                } else {
                    // remove the unchecked option from the selectedOptions array
                    this.selectedOptions = this.selectedOptions.filter(
                        (opt) => opt !== option.value,
                    )
                }
            },
        }
    }
</script>
