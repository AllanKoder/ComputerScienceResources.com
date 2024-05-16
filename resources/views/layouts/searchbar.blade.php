<div>
    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-4">
        @method('get')
        
        <!-- Search Bar -->
        <input type="text" name="query" placeholder="Search..." class="outline-none" />

        <!-- Dropdowns -->
        <div>
            <select name="category" class="py-2 px-3 rounded-md">
                <option value="">Select Category</option>
                <option value="books">Books</option>
                <option value="electronics">Electronics</option>
                <!-- Add more categories as needed -->
            </select>
        </div>

        <div>
            <select name="price_range" class="py-2 px-3 rounded-md">
                <option value="">Select Price Range</option>
                <option value="0-50">$0 - $50</option>
                <option value="51-100">$51 - $100</option>
                <!-- Add more price ranges as needed -->
            </select>
        </div>

        <div>
            <select name="rating" class="border-2 py-2 px-3 rounded-md">
                <option value="">Select Rating</option>
                <option value="5">★★★★★</option>
                <option value="4">★★★★☆</option>
                <option value="3">★★★☆☆</option>
                <option value="2">★★☆☆☆</option>
                <option value="1">★☆☆☆☆</option>
            </select>
        </div>
    </form>
</div>
