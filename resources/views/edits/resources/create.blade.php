<x-app-layout>
    <h1>Create Resource Edit</h1>
    <div class="py-10">
        <form action="{{ route('resource_edits.store', ['resource' => $resourceID]) }}" method="POST">
            @csrf
            @php
                $pricingOptions = \App\Helpers\ConfigHelper::getConfigOptions("pricings");
                $formatOptions = \App\Helpers\ConfigHelper::getConfigOptions("formats");
                $difficultyOptions = \App\Helpers\ConfigHelper::getConfigOptions("difficulties");
            @endphp

            <div>
                <label for="title">Resource Title:</label>
                <x-smart-inputs.text-input 
                :inputText='@($resource->title)'
                :saveToStorage=true
                type="text" id="title" name="title" required/>
            </div>
            <div>
                <label for="description">Resource Description:</label>
                <x-smart-inputs.text-input
                class="w-1/2"
                :inputText="@($resource->description)"
                :saveToStorage=true
                type="textarea" id="description" name="description" required/>
            </div>

            <!-- Image URL Input -->
            <div class="mb-4">
                <label for="image_url" class="block text-gray-700 text-sm font-bold mb-2">Image URL:</label>
                <x-smart-inputs.text-input type="url" 
                :inputText="@($resource->image_url)"
                :saveToStorage=false
                name="image_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>
            
            <!-- Resource Formats -->
            <div class="mb-4">
                <label for="formats" class="block text-gray-700 text-sm font-bold mb-2">Resource Format:</label>
                <x-smart-inputs.multi-select-input 
                name="formats"
                :options="$formatOptions"
                :selectedOptions="@($resource->formats)"
                :saveToStorage=true
                required/>                
            </div>

            <!-- Features Input -->
            <div class="mb-4">
                <label for="features" class="block text-gray-700 text-sm font-bold mb-2">Features:</label>
                <x-smart-inputs.multi-text-input 
                :inputTexts="@($resource->features)"
                :saveToStorage=true
                name="features" placeholder="a feature of the Resource"></x-smart-inputs.multi-text-input>
            </div>

            <!-- Limitations Input -->
            <div class="mb-4">
                <label for="limitations" class="block text-gray-700 text-sm font-bold mb-2">Limitations:</label>
                <x-smart-inputs.multi-text-input 
                :inputTexts="@($resource->limitations)"
                :saveToStorage=true
                name="limitations" placeholder="A limitation of the Resource"></x-smart-inputs.multi-text-input>
            </div>

            <!-- Resource URL Input -->
            <div class="mb-4">
                <label for="resource_url" class="block text-gray-700 text-sm font-bold mb-2">Resource URL:</label>
                <x-smart-inputs.text-input type="url" 
                :inputText="@($resource->resource_url)"
                :saveToStorage=true
                name="resource_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required></x-smart-inputs.text-input>
            </div>

            <!-- Pricing Input -->
            <div class="mb-4">
                <label for="cost" class="block text-gray-700 text-sm font-bold mb-2">Pricing Model:</label>    
                {{ $resource->pricing }}
                <x-smart-inputs.select-input 
                name="pricing" 
                :selectedOption="@($resource->pricing)"
                :options="$pricingOptions"
                :saveToStorage=true
                required/>       
            </div>

            <!-- Topics Input (Dynamic Array of Inputs) -->
            <div class="mb-4">
                <label for="topics" class="block text-gray-700 text-sm font-bold mb-2">Computer Science Topics:</label>
                <x-smart-inputs.multi-tag-input class="w-full" 
                :saveToStorage=true
                :selectedOptions="@($resource->topics)"
                name="topics" required/>
            </div>
                
            <!-- Difficulty Input -->
            <div class="mb-6">
                <label for="difficulty" class="block text-gray-700 text-sm font-bold mb-2">Difficulty:</label>
                <x-smart-inputs.select-input 
                name="difficulty" 
                :options="$difficultyOptions"
                :saveToStorage=true
                :selectedOption="@($resource->difficulty)"
                required/>
            </div>

            <!-- Tags Input -->
            <div class="mb-4">
                <label for="tag_names" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
                <x-smart-inputs.multi-tag-input class="w-full" 
                :selectedOptions="@($resource->tag_names)"
                :saveToStorage=true
                name="tag_names"/>
            </div>
            
            <div class=" border-blue-400 border-2 p-5"> 
                <div>
                    <label for="edit_title">Edit Title:</label>
                    <x-smart-inputs.text-input
                    :saveToStorage=true
                    type="text" id="edit_title" name="edit_title" required/>
                </div>
                <div>
                    <label for="edit_description">Edit Description:</label>
                    <x-smart-inputs.text-input
                    class="w-1/2"
                    :saveToStorage=true
                    type="textarea" id="edit_description" name="edit_description" required/>
                </div>
            </div>

            <button type="submit">Create</button>
        </form>
        <div x-data>
            <button @click="$dispatch('clear-inputs-event');">Clear Input</button>
        </div>
    </div>
</x-app-layout>
