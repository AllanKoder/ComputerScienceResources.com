@props(['options' => [], 'name' => '', 'selectedOption' => '', 'hasSearch' => false, 'saveToStorage' => false, 'attributes' => []])

<div {{$attributes}} x-data="singleSelectComponent('{{ $name }}', {{ json_encode($options) }}, '{{ $selectedOption }}', {{ $hasSearch ? 'true' : 'false' }}, {{ $saveToStorage ? 'true' : 'false' }})" 
    class="w-full max-w-xs flex flex-col gap-1 min-w-40" 
    x-on:keydown.esc.window="isOpen = false"
    x-init="initialize()"
    @clear-inputs-event.window="resetInputs()">
    <div class="relative">
        <!-- Trigger button -->
        <button type="button" role="combobox" class="inline-flex w-full items-center justify-between gap-2 whitespace-nowrap border-slate-300 bg-slate-100 px-4 py-2 text-sm font-medium tracking-wide text-slate-700 transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300 dark:focus-visible:outline-blue-600 border rounded-xl" aria-haspopup="listbox" aria-controls="optionsList" 
        x-on:click="toggleDropdown" 
        x-on:keydown="handleKeydownOnOptions($event)"
        x-on:keydown.down.prevent="openedWithKeyboard = true" 
        x-on:keydown.enter.prevent="openedWithKeyboard = true" 
        x-on:keydown.space.prevent="openedWithKeyboard = true" 
        x-bind:aria-label="setLabelText()" 
        x-bind:aria-expanded="isOpen || openedWithKeyboard">
            <span class="text-sm w-full font-normal text-start overflow-hidden text-ellipsis whitespace-nowrap"  
            x-text="setLabelText()"></span>
            <!-- Chevron -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
        </button>
        <!-- Hidden input to grab the selected value -->
        <input type="hidden" name="{{$name}}" x-bind:value="selectedOption">
        <ul x-cloak x-show="isOpen || openedWithKeyboard" id="optionsList" class="absolute z-10 left-0 top-11 flex max-h-44 w-full flex-col overflow-hidden overflow-y-auto border-slate-300 bg-slate-100 py-1.5 dark:border-slate-700 dark:bg-slate-800 border rounded-xl" 
        role="listbox" 
        x-on:click.outside="closeDropdown" 
        x-on:keydown.down.prevent="$focus.wrap().next()" 
        x-on:keydown.up.prevent="$focus.wrap().previous()" 
        x-transition x-trap="openedWithKeyboard">
            <!-- Search bar inside the dropdown -->
            <template x-if="hasSearch">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5" class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-neutral-600/50 dark:text-neutral-300/50" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input type="text" 
                    class="w-full outline-none border-neutral-300 border-transparent focus:border-transparent focus:ring-0 bg-neutral-50 py-2.5 pl-11 pr-4 text-sm text-neutral-600" 
                    x-ref="searchField" aria-label="Search" 
                    x-on:keydown.enter.prevent="handleSearchEnter()"
                    x-model="searchQuery" x-on:input="filterOptions()"
                    placeholder="Search/Add" />
                </div>
            </template>
            <template x-for="(item, index) in filteredOptions" x-bind:key="`${item}-${'{{$name}}'}`">
                <!-- Option -->
                <li role="option">
                    <label class="flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-900/5 has-[:focus]:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5 dark:has-[:focus]:bg-white/5 [&:has(input:checked)]:text-black dark:[&:has(input:checked)]:text-white [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75" 
                    x-bind:for="'radioOption' + index + '{{$name}}'">
                        <div class="relative flex items-center">
                            <input type="radio" 
                            x-on:change="handleOptionSelect($el)" 
                            x-on:keydown.enter.prevent="$el.checked = true; handleOptionSelect($el)" 
                            :value="item" 
                            :id="'radioOption' + index + '{{$name}}'" 
                            x-init="
                            $el.checked = isSelected($el.value);
                            $watch('selectedOption', _ => $el.checked = isSelected($el.value));
                            "
                            class="combobox-option before:content[''] peer relative size-4 cursor-pointer appearance-none overflow-hidden border border-slate-300 bg-slate-100 before:absolute before:inset-0 checked:border-blue-700 checked:before:bg-blue-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-800 checked:focus:outline-blue-700 active:outline-offset-0 disabled:cursor-not-allowed dark:border-slate-700 rounded-full dark:bg-slate-800 dark:checked:border-blue-600 dark:checked:before:bg-blue-600 dark:focus:outline-slate-300 dark:checked:focus:outline-blue-600" 
                            />
                            <!-- Checkmark -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="4" class="pointer-events-none invisible absolute left-1/2 top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 text-slate-100 peer-checked:visible dark:text-slate-100" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span x-text="item"></span>
                    </label>
                </li>
            </template>
        </ul>
    </div>
</div>

<script>
    function singleSelectComponent(name, options, selectedOption, hasSearch, saveToStorage) {
        return {
            options: options,
            filteredOptions: options,
            isOpen: false,
            openedWithKeyboard: false,
            selectedOption: selectedOption,
            searchQuery: '',
            hasSearch: hasSearch,
            saveToStorage: saveToStorage,
            get storageID() { return `${Alpine.store('getURL')()}-stored-${name}` },
            initialize() {
                this.filteredOptions = this.options;
                if (this.saveToStorage) {
                    const storedOption = localStorage.getItem(this.storageID);
                    if (storedOption) {
                        this.selectedOption = storedOption;
                    }
                }
            },
            resetInputs() {
                this.selectedOptions = [];
                localStorage.removeItem(this.storageID);
                this.initialize();
            },
            isSelected(option) {
                return this.selectedOption === option;
            },
            updateStorage() {
                if (this.saveToStorage) {
                    localStorage.setItem(this.storageID, this.selectedOption);
                }
            },
            handleOptionSelect(el) {
                this.selectedOption = el.value;
                this.isOpen = false;
                this.openedWithKeyboard = false;
                this.updateStorage();
            },
            handleSearchEnter() {
                if (this.filteredOptions.length === 1) {
                    this.selectedOption = this.filteredOptions[0];
                    this.isOpen = false;
                    this.openedWithKeyboard = false;
                    if (this.saveToStorage) {
                        localStorage.setItem(this.storageID, this.selectedOption);
                    }
                }
            },
            filterOptions() {
                this.filteredOptions = this.options.filter(option => option.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },
            setLabelText() {
                const selected = this.options.find(option => option === this.selectedOption);
                return selected ? selected : 'Select an option';
            },
            handleKeydownOnOptions(event) {
                if (event.key === 'Enter' && this.filteredOptions.length === 1) {
                    this.selectedOption = this.filteredOptions[0];
                    this.isOpen = false;
                    this.openedWithKeyboard = false;
                    if (this.saveToStorage) {
                        localStorage.setItem(this.storageID, this.selectedOption);
                    }
                }
                else if((event.keyCode >= 65 && event.keyCode <= 90) || (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode === 8) {
                    this.$refs.searchField.focus();
                }
            },
            toggleDropdown() {
                this.isOpen = !this.isOpen;
            },
            closeDropdown() {
                this.isOpen = false;
                this.openedWithKeyboard = false;
            }
        };
    }
</script>
