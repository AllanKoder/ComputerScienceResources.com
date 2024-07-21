@props(['type', 'name', 'attributes' => []])

@if($type == "textarea")
    <textarea {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="text" name="{{ $name }}" id="text-input-{{ $name }}" 
        x-data="textInputComponent('{{ $name }}')" x-init="initialize()" x-model="inputValue" @clear-inputs-event.window="clearInput"></textarea>
@else
    <input {{ $attributes->merge(['class' => 'form-control-text-input']) }} type="{{ $type }}" name="{{ $name }}" id="text-input-{{ $name }}" 
    x-data="textInputComponent('{{ $name }}')" x-init="initialize()" x-model="inputValue" @clear-inputs-event.window="clearInput" />
@endif

<script>
    function textInputComponent(name) {
        return {
            inputValue: '',
            get storageID() { return `${Alpine.store('getURL')()}-text-input-${name}` },
            initialize() {
                // Load the value from local storage if it exists
                const storedValue = localStorage.getItem(this.storageID);
                if (storedValue) {
                    this.inputValue = storedValue;
                }

                // Watch for changes to inputValue and update local storage
                this.$watch('inputValue', (value) => {
                    localStorage.setItem(this.storageID, value);
                });
            },
            clearInput(event) {
                this.inputValue = '';
            }
        }
    }
</script>
