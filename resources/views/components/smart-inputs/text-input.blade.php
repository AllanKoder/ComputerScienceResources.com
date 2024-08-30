@props(['type', 'name', 'inputText'=>'', 'saveToStorage'=>false, 'useQueryParameters'=>false, 'attributes' => []])

@if($type == "textarea")
    <textarea {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="text" name="{{ $name }}" id="text-input-{{ $name }}" 
        x-data="textInputComponent('{{ $name }}', '{{ $saveToStorage ? 'true' : ''}}', '{{ $inputText }}', '{{ $useQueryParameters ? 'true' : ''}}')"
        x-init="initialize()" x-model="inputValue" 
        @clear-inputs-event.window="resetInputs()"></textarea>
@else
    <input {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="{{ $type }}" name="{{ $name }}" id="text-input-{{ $name }}" 
    x-data="textInputComponent('{{ $name }}', '{{ $saveToStorage ? 'true' : ''}}', '{{ $inputText }}', '{{ $useQueryParameters ? 'true' : ''}}')"
    x-init="initialize()" x-model="inputValue" 
    @clear-inputs-event.window="resetInputs()" />
@endif

<script>
    function textInputComponent(name, saveToStorage, inputText, useQueryParameters) {
        return {
            inputValue: '',
            get storageID() { return `${Alpine.store('getURL')()}-text-input-${name}` },
            initialize() {
                if (saveToStorage)
                {
                    // Watch for changes to inputValue and update local storage
                    this.$watch('inputValue', (value) => {
                        localStorage.setItem(this.storageID, value);
                    });
                }

               if (useQueryParameters)
               {
                   this.inputValue = Alpine.store('getQueryParameter')(name);
                   return;
               }

               this.resetInputs();
            },
            resetInputs() {
                this.inputValue = '';
                localStorage.removeItem(this.storageID);
                // Load the value from local storage if it exists
                const storedValue = localStorage.getItem(this.storageID);
                if (storedValue && saveToStorage) {
                    this.inputValue = storedValue;
                }
                else {
                    this.inputValue = inputText;
                }
           },
        }
    }
</script>
