@props(['name', 'placeholder', 'maxSize'=>10, 'inputTexts'=>[], 'saveToStorage'=>false])

<!-- name Input -->
<div x-data="dynamicTableComponent('{{ $name }}', '{{ $placeholder }}', {{ $maxSize }}, {{ json_encode($inputTexts) }}, {{ $saveToStorage ? 'true' : 'false' }})" 
    x-init="initialize"
    class="dynamic-table border-width: 0"
    @clear-inputs-event.window="resetInputs()">
    <button type="button" class="add btn btn-success p-1 border-black border-2" @click="addInput">Add More</button>
    <div class="inputs-container">
        <template x-for="(input, index) in inputs" :key="index">
            <div class="input-group">
                <input type="text" :name="`${name}[${index}]`" :placeholder="placeholder" x-model="inputs[index]" @input="updateStorage" class="form-control w-10/12" />
                <button type="button" class="remove btn btn-danger p-1 border-black border-2" @click="removeInput(index)">Remove</button>
            </div>
        </template>
    </div>
</div>

<script>
    function dynamicTableComponent(name, placeholder, maxSize, inputTexts, saveToStorage) {
        return {
            inputs: inputTexts.length ? inputTexts : [''],
            name: name,
            placeholder: placeholder,
            maxSize: maxSize,
            get storageID() { return `${Alpine.store('getURL')()}-stored-${name}` },
            initialize() {
                const savedInputs = JSON.parse(localStorage.getItem(this.storageID)) || [];
                if (saveToStorage && savedInputs.length) {
                    this.inputs = savedInputs;
                } else {
                    this.inputs = inputTexts.length ? inputTexts : [''];
                }
            },
            addInput() {
                if (this.inputs.length >= this.maxSize) {
                    window.dispatchEvent(new CustomEvent('open-warning-modal', {
                        detail: {
                            title: 'Too many Inputs',
                            description: `Max ${this.maxSize} inputs for ${this.name}`,
                        }
                    }));
                    return;
                }
                this.inputs.push('');
                this.updateStorage();
            },
            removeInput(index) {
                successCallback = () => {
                    this.inputs.splice(index, 1);
                    this.updateStorage();
                }
                window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                    detail: {
                        title: 'Confirm Deletion',
                        description: 'This will be deleted and unrecoverable',
                        onSuccess: () => { successCallback(); },
                        onFailure: () => { console.log('Failure!'); }
                    }
                }));
            },
            updateStorage() {
                if (saveToStorage) {
                    localStorage.setItem(this.storageID, JSON.stringify(this.inputs));
                }
            },
            resetInputs() {
                this.inputs = [];
                localStorage.removeItem(this.storageID);
                this.initialize();
            },
        };
    }
</script>
