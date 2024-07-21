<form hx-get="{{ url()->current() }}" 
    hx-select="#resources-results" hx-target="#resources-results" hx-swap="outerHTML"
    hx-trigger="submit" hx-push-url="true" 
    hx-indicator="#spinner"
    class="flex flex-wrap items-center space-x-4">        
    <!-- Search Bar, for name and description -->
    <div class="mb-4 w-1/3">
        <label for="query" class="block text-gray-700 text-sm font-bold mb-2 w-full">Resource Name or Description:</label>
        <x-text-input-field type="text" name="query" placeholder="Search..." class="w-full h-8 rounded border-gray-400" />
    </div>
    
    <!-- Resource Formats -->
    <div class="mb-4">
        <label for="formats" class="block text-gray-700 text-sm font-bold mb-2">Resource Format:</label>
        @php
            $formatOptions = collect(config("formats"))->map(function ($value, $key) {
                return ['value' => $key, 'label' => $value];
            })->values()->toArray();
        @endphp
        <x-multi-select-input name="formats">
            @foreach(config("formats") as $key => $value)
                <option value="{{ $key }}" id="{{$key}}">{{ $value }}</option>
            @endforeach
        </x-multi-select-input>             
        <x-multi-select-input 
        :options="$formatOptions"
        :saveToStorage=true
        name="formatOptions"
        />           
    </div>

    <!-- Pricing Model Input -->
    <div class="mb-4 min-w-36">
        <label for="pricing" class="block text-gray-700 text-sm font-bold mb-2">Pricing Model:</label>
        @php
            $pricingOptions = collect(config('pricings'))->map(function ($value, $key) {
                return ['value' => $key, 'label' => $value];
            })->values()->toArray();
        @endphp
        <x-multi-select-input 
        :options="$pricingOptions"
        :saveToStorage=true
        name="pricing"
        />        
        {{ json_encode($pricingOptions) }}
    </div>

    <!-- Topics Input (Dynamic Array of Inputs) -->
    <div class="mb-4 min-w-36">
        <label for="topics" class="block text-gray-700 text-sm font-bold mb-2">Computer Science Topics:</label>
        <x-multi-tag-input name="topics" class="w-full"></x-multi-text-input>
    </div>
        
    <!-- Difficulty Input -->
    <div class="mb-4 min-w-36">
        <label for="difficulty" class="block text-gray-700 text-sm font-bold mb-2">Difficulty:</label>
        
        <x-multi-select-input 
        :options="[
            ['value' => 'beginner', 'label' => 'Beginner'],
            ['value' => 'industry', 'label' => 'Industry'],
            ['value' => 'academic', 'label' => 'Academic'],
        ]"
        :saveToStorage=true
        name="difficulty">        
        </x-multi-select-input>
    </div>

    <!-- Tags Input -->
    <div class="mb-4 min-w-36">
        <label for="tags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
        <x-multi-tag-input name="tags" class="w-full"></x-multi-text-input>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-between">
        <button id="search-resources-button" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2">
            Search
        </button>
    </div>   
</form>

<div class="flex items-center justify-between">
    <button id="reset-filter-button" class="bg-red-500 hover:bg-red-600 text-white font-bold mx-5 py-1 px-2">
        Reset Filter
    </button>
</div>   
<div x-data>
    <button @click="$dispatch('clear-inputs-event')">.sadsaasdsa asas.</button>
</div>
<script>
    $('#reset-filter-button').on('click', function() {
        // Trigger the custom event and pass the name of the select to clear
        $(document).trigger('clearInputs');
    });
</script>
