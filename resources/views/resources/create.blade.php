<x-app-layout>
    <div class="flex flex-col items-center justify-center">
        <div x-data='confirmClearInput'>
            <button @click="clearInput($dispatch)">Clear Input</button>
        </div>
        <form id="create-resource-form" method="POST" action="{{ route('resources.store') }}" class="w-full max-w-xl my-14">
            @csrf <!-- CSRF token for security -->
            @php
                $pricingOptions = \App\Helpers\ConfigHelper::getConfigOptions("pricings");
                $formatOptions = \App\Helpers\ConfigHelper::getConfigOptions("formats");
                $difficultyOptions = \App\Helpers\ConfigHelper::getConfigOptions("difficulties");
            @endphp
            <!-- Title Input -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <x-smart-inputs.text-input type="text" 
                :saveToStorage=true
                name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>

            <!-- Description Input -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                <x-smart-inputs.text-input type="textarea" 
                :saveToStorage=true
                name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>
            
            <!-- Image URL Input -->
            <div class="mb-4">
                <label for="image_url" class="block text-gray-700 text-sm font-bold mb-2">Image URL:</label>
                <x-smart-inputs.text-input type="url" 
                :saveToStorage=true
                :inputText="''"
                name="image_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>
            
            <!-- Resource Formats -->
            <div class="mb-4">
                <label for="formats" class="block text-gray-700 text-sm font-bold mb-2">Resource Format:</label>
                <x-smart-inputs.multi-select-input 
                name="formats"
                :options="$formatOptions"
                :saveToStorage=true
                required/>                
            </div>

            <!-- Features Input -->
            <div class="mb-4">
                <label for="features" class="block text-gray-700 text-sm font-bold mb-2">Features:</label>
                <x-smart-inputs.multi-text-input 
                :saveToStorage=true
                name="features" placeholder="a Feature of the Resource"></x-smart-inputs.multi-text-input>
            </div>

            <!-- Limitations Input -->
            <div class="mb-4">
                <label for="limitations" class="block text-gray-700 text-sm font-bold mb-2">Limitations:</label>
                <x-smart-inputs.multi-text-input 
                :saveToStorage=true
                name="limitations" placeholder="A Limitation of the Resource"></x-smart-inputs.multi-text-input>
            </div>

            <!-- Resource URL Input -->
            <div class="mb-4">
                <label for="resource_url" class="block text-gray-700 text-sm font-bold mb-2">Resource URL:</label>
                <x-smart-inputs.text-input type="url" 
                :saveToStorage=true
                name="resource_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>

            <!-- Pricing Input -->
            <div class="mb-4">
                <label for="cost" class="block text-gray-700 text-sm font-bold mb-2">Pricing Model:</label>    
                <x-smart-inputs.multi-select-input 
                name="pricing" 
                :options="$pricingOptions"
                :saveToStorage=true
                required/>       
            </div>

            <!-- Topics Input (Dynamic Array of Inputs) -->
            <div class="mb-4">
                <label for="topics" class="block text-gray-700 text-sm font-bold mb-2">Computer Science Topics:</label>
                <x-smart-inputs.multi-tag-input class="w-full" 
                :saveToStorage=true
                :selectedOptions="[]"
                name="topics" required/>
            </div>
              
            <!-- Difficulty Input -->
            <div class="mb-6">
                <label for="difficulty" class="block text-gray-700 text-sm font-bold mb-2">Difficulty:</label>

                <x-smart-inputs.select-input 
                name="difficulty" 
                :options="$difficultyOptions"
                :saveToStorage=true
                :hasSearch=true
                required/>
            </div>

            <!-- Tags Input -->
            <div class="mb-4">
                <label for="tags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
                <x-smart-inputs.multi-tag-input class="w-full" 
                :saveToStorage=true
                name="tags"/>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Submit
                </button>
            </div>            
        </form>
    </div>
    <script>
        function confirmClearInput() {
            return {
                clearInput(dispatch) {
                    window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                        detail: {
                                title: 'Confirm Resetting the Fields',
                                description: 'This cannot be undone',
                                onSuccess: () => { dispatch('clear-inputs-event') },
                            }
                    }));
                } 
            };
        }         
    </script>
</x-app-layout>
