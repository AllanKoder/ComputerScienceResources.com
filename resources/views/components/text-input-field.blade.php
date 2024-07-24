@props(['type', 'name', 'inputText'=>'', 'saveToStorage'=>false, 'attributes' => []])

@if($type == "textarea")
    <textarea {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="text" name="{{ $name }}" id="text-input-{{ $name }}" 
        x-data="textInputComponent('{{ $name }}', '{{ $saveToStorage  ? 'true' : 'false' }}', '{{ $inputText }}')" x-init="initialize()" x-model="inputValue" 
        @clear-inputs-event.window="resetInputs()"></textarea>
@else
    <input {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="{{ $type }}" name="{{ $name }}" id="text-input-{{ $name }}" 
    x-data="textInputComponent('{{ $name }}', '{{ $saveToStorage ? 'true' : 'false'}}', '{{ $inputText }}')" x-init="initialize()" x-model="inputValue" 
    @clear-inputs-event.window="resetInputs()" />
@endif

<script>
    function textInputComponent(name, saveToStorage, inputText) {
        console.log(saveToStorage);
        return {
            inputValue: '',
            get storageID() { return `${Alpine.store('getURL')()}-text-input-${name}` },
            initialize() {
                // Load the value from local storage if it exists
                const storedValue = localStorage.getItem(this.storageID);
                if (storedValue && saveToStorage) {
                    this.inputValue = storedValue;
                }
                else {
                    this.inputValue = inputText;
                }
                
                if (saveToStorage)
                {
                    // Watch for changes to inputValue and update local storage
                    this.$watch('inputValue', (value) => {
                        localStorage.setItem(this.storageID, value);
                    });
                }
            },
            resetInputs() {
                this.inputValue = '';
                localStorage.removeItem(this.storageID);
                this.initialize();
            },
        }
    }
</script>
