@props(['type','name', 'attributes' => []])

@if($type == "textarea")
    <textarea {{ $attributes->merge(['class' => 'form-control']) }} type="text" name="{{ $name }}" id="text-input-{{ $name }}" /></textarea>
@else
    <input {{ $attributes->merge(['class' => 'form-control']) }} type="{{ $type }}" name="{{ $name }}" id="text-input-{{ $name }}" />
@endif
<script>
    // Function to handle local storage for a text input
    function handleLocalStorageForInput(inputElement) {
        let inputName = inputElement.attr('name');
        let baseUrl = @json($getURL());
        let id = `text-input-${inputName}-${baseUrl}`;

        // Function to update local storage with the input value
        function updateLocalStorage() {
            var inputValue = inputElement.val();
            localStorage.setItem(id, inputValue);
        }

        // Set the value of the input if there is a stored value
        if (localStorage.getItem(id)) {
            var storedValue = localStorage.getItem(id);
            inputElement.val(storedValue);
        }

        // Update local storage upon input change
        inputElement.on('input', function() {
            updateLocalStorage();
        });
    }

    // Initialize local storage handling for all text inputs on the page
    $(document).ready(function() {
        $('.form-control').each(function() {
            handleLocalStorageForInput($(this));
        });

        // Listen for a custom global event to clear the input
        $(document).on('clearInputs', function(event, inputName) {
            if (inputName) {
                $(`#text-input-${inputName}`).val('').trigger('input');
            } else {
                // Clear all inputs if no specific name is provided
                $('.form-control').val('').trigger('input');
            }
        });
    });
</script>