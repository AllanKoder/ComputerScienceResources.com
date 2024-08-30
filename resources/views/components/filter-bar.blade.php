<!-- TODO: make the initial value from the url -->
<form hx-get="{{ url()->current() }}" 
    hx-select="#resources-results" hx-target="#resources-results" hx-swap="outerHTML"
    hx-trigger="submit" hx-push-url="true" 
    hx-indicator="#spinner"
    class="flex flex-wrap items-center space-x-4"
    >

    <!-- TODO: make the initial value from the url -->

    @php
        $pricingOptions = \App\Helpers\ConfigHelper::getConfigOptions("pricings");
        $formatOptions = \App\Helpers\ConfigHelper::getConfigOptions("formats");
        $difficultyOptions = \App\Helpers\ConfigHelper::getConfigOptions("difficulties");
    @endphp
    <!-- Search Bar, for name and description -->
    <div class="mb-4 w-1/3">
        <label for="title" class="block text-gray-700 text-sm font-bold mb-2 w-full">Resource Name:</label>
        <x-smart-inputs.text-input type="text" name="title" 
        inputText="" 
        :useQueryParameters=true
        placeholder="Search..." class="w-full h-8 rounded border-gray-400"/>
    </div>
    
    <div class="mb-4 w-1/3">
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2 w-full">Resource Description:</label>
        <x-smart-inputs.text-input type="text" name="description"
        placeholder="Search..."
        :useQueryParameters=true
        class="w-full h-8 rounded border-gray-400"/>
    </div>
   
    <!-- Resource Formats -->
    <div class="mb-4">
        <label for="formats" class="block text-gray-700 text-sm font-bold mb-2">Resource Format:</label>
        <x-smart-inputs.multi-select-input 
        :options="$formatOptions"
        :selectedOptions="[]"
        :useQueryParameters=true
        name="formats"
        />           
    </div>

    <!-- Pricing Model Input -->
    <div class="mb-4 min-w-36">
        <label for="pricing" class="block text-gray-700 text-sm font-bold mb-2">Pricing Model:</label>
        <x-smart-inputs.multi-select-input 
        :options="$pricingOptions"
        :selectedOptions="[]"
        :useQueryParameters=true
        name="pricing"
        />        
    </div>

    <!-- Topics Input (Dynamic Array of Inputs) -->
    <div class="mb-4 min-w-36">
        <label for="topics" class="block text-gray-700 text-sm font-bold mb-2">Computer Science Topics:</label>
        <x-smart-inputs.multi-tag-input 
        :selectedOptions="[]"
        :useQueryParameters=true
        name="topics"/>
    </div>
        
    <!-- Difficulty Input -->
    <div class="mb-4 min-w-36">
        <label for="difficulty" class="block text-gray-700 text-sm font-bold mb-2">Difficulty:</label>
        <x-smart-inputs.multi-select-input 
        :options="$difficultyOptions"
        :selectedOptions="[]"
        :useQueryParameters=true
        name="difficulty">        
        </x-smart-inputs.multi-select-input>
    </div>

    <!-- Tags Input -->
    <div class="mb-4 min-w-36">
        <label for="tags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
        <x-smart-inputs.multi-tag-input 
        name="tags"
        :selectedOptions="[]"
        :options="[]"
        :useQueryParameters=true
        class="w-full"/>
    </div>

    <!-- Review Ratings -->
    <div class="mb-4 min-w-36">
        <label for="community_size" class="block text-gray-700 text-sm font-bold mb-2">Community Size</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="community_size"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="teaching_clarity" class="block text-gray-700 text-sm font-bold mb-2">Teaching Clarity</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="teaching_clarity"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="technical_dept" class="block text-gray-700 text-sm font-bold mb-2">Technical Depth</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="technical_depth"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="practicality_to_industry" class="block text-gray-700 text-sm font-bold mb-2">Practicality to Industry</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="practicality_to_industry"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="user_friendliness" class="block text-gray-700 text-sm font-bold mb-2">User Friendliness</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="user_friendliness"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="updates_and_maintenance" class="block text-gray-700 text-sm font-bold mb-2">Updates and Maintenance </label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="updates_and_maintenance"
        />
    </div>

    <div class="mb-4 min-w-36">
        <label for="average_score" class="block text-gray-700 text-sm font-bold mb-2">Average Score</label>
        <x-smart-inputs.rating-filter
        :useQueryParameters=true
        name="average_score"
        />
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
    <button @click="$dispatch('clear-inputs-event')">Clear Input</button>
</div>

<!-- TODO: add a sort by dropdown -->