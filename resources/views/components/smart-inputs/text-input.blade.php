@props(['type', 'name', 'inputText'=>'', 'saveToStorage'=>false, 'useQueryParameters'=>false, 'attributes' => []])

@if($type == "textarea")
    <textarea {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="text" name="{{ $name }}" id="text-input-{{ $name }}" 
        x-data="textInputComponent('{{ $name }}', {{ $saveToStorage ? 'true' : 'false'}}, '{{ $inputText }}', '{{ $useQueryParameters ? 'true' : 'false'}}')"
        x-init="initialize()" x-model="inputValue" 
        @clear-inputs-event.window="resetInputs()"></textarea>
@else
    <input {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="{{ $type }}" name="{{ $name }}" id="text-input-{{ $name }}" 
    x-data="textInputComponent('{{ $name }}', {{ $saveToStorage ? 'true' : 'false'}}, '{{ $inputText }}', {{ $useQueryParameters ? 'true' : 'false'}})"
    x-init="initialize()" x-model="inputValue" 
    @clear-inputs-event.window="resetInputs()" />
@endif

<script>
    function textInputComponent(name, saveToStorage, inputText, useQueryParameters) {
        return {
            inputValue: '',
            get storageID() { return `${Alpine.store('getURL')()}-text-input-${name}` },
            initialize() {
                // Load the value from local storage if it exists
                const storedValue = localStorage.getItem(this.storageID);
                if (useQueryParameters == true) {
                    this.inputValue = Alpine.store('getQueryParameter')(name) ?? '';
                } else if (storedValue && saveToStorage) {
                    this.inputValue = storedValue;
                } else {
                    this.inputValue = inputText;
                }

                // Now set up the watcher after initializing inputValue
                if (saveToStorage) {
                    this.$watch('inputValue', (value) => {
                        localStorage.setItem(this.storageID, value);
                    });
                }
            },
            resetInputs() {
                this.inputValue = inputText;
            },
        }
    }
</script>
