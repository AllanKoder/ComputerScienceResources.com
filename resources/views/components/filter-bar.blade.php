<form hx-get="{{ url()->current() }}" 
    hx-select="#resources-results" hx-target="#resources-results" hx-swap="outerHTML"
    hx-trigger="submit" hx-push-url="true" 
    class="flex flex-wrap items-center space-x-4">        
    <!-- Search Bar, for name and description -->
    <div class="mb-4 w-1/2">
        <label for="query" class="block text-gray-700 text-sm font-bold mb-2 w-full">Resource Name or Description:</label>
        <input type="text" name="query" placeholder="Search..." class="w-full h-8 rounded border-gray-400" />
    </div>
    
    <!-- Resource Formats -->
    <div class="mb-4 min-w-36">
        <label for="formats" class="block text-gray-700 text-sm font-bold mb-2">Resource Format:</label>
        <x-multi-select-input name="formats">
            @foreach(config("formats") as $key => $value)
                <option value="{{ $key }}" id="{{$key}}">{{ $value }}</option>
            @endforeach
        </x-multi-select-input>                
    </div>

    <!-- Pricing Input -->
    <div class="mb-4 min-w-36">
        <label for="pricing" class="block text-gray-700 text-sm font-bold mb-2">Pricing Model:</label>
        <x-multi-select-input name="pricing">        
            <option value="free">Free</option>
            <option value="freemium">Freemium</option>
            <option value="subscription">Subscription Service</option>
            <option value="paid">One Time Payment</option>
        </x-multi-select-input>            
    </div>

    <!-- Topics Input (Dynamic Array of Inputs) -->
    <div class="mb-4 min-w-36">
        <label for="topics" class="block text-gray-700 text-sm font-bold mb-2">Computer Science Topics:</label>
        <x-multi-tag-input name="topics"></x-multi-text-input>
    </div>
        
    <!-- Difficulty Input -->
    <div class="mb-4 min-w-36">
        <label for="difficulty" class="block text-gray-700 text-sm font-bold mb-2">Difficulty:</label>
        <x-multi-tag-input name="difficulty" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight" required>        
            <option value="beginner">Beginner</option>
            <option value="industry">Industry</option>
            <option value="academic">Academic</option>
        </x-multi-tag-input>
    </div>

    <!-- Tags Input -->
    <div class="mb-4 min-w-36">
        <label for="tags" class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
        <x-multi-tag-input name="tags"></x-multi-text-input>
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

<script>
    $('#reset-filter-button').on('click', function() {
    // Trigger the custom event and pass the name of the select to clear
        $(document).trigger('clearInputs');
    });
</script>