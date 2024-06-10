@props(['name', 'attributes' => []])

<select {{ $attributes->merge(['class' => 'form-control-multi-select']) }} name="{{$name}}[]" multiple="multiple" id="multi-select-{{$name}}">
    {{$slot}}
</select>

<script>
    // Function to initialize Select2 and set up local storage handling
    function initializeSelect2(selectElement) {
        let selectName = selectElement.attr('name');
        let baseUrl = @json($getURL());
        let id = `multi-select-${selectName}-${baseUrl}`;
        
        // Initialize Select2
        selectElement.select2();

        // Function to update local storage with the selected values
        function updateSelections() {
            var selectedValues = selectElement.val();
            localStorage.setItem(id, JSON.stringify(selectedValues));
        }

        // Set the value of Select2 if there are stored selections
        if (localStorage.getItem(id)) {
            var selectedValues = JSON.parse(localStorage.getItem(id));
            selectElement.val(selectedValues).trigger('change');
        }

        // Update local storage upon selection
        selectElement.on('change', function() {
            updateSelections();
        });
    }

    // Initialize all Select2 elements on the page
    $(document).ready(function() {
        $('.form-control-multi-select').each(function() {
            initializeSelect2($(this));
        });
   
         // Listen for a custom global event to clear the select
        $(document).on('clearInputs', function(event, selectName) {
            if (selectName) {
                $(`#multi-select-${selectName}`).val(null).trigger('change');
            } else {
                // Clear all selects if no specific name is provided
                $('.form-control-multi-select').val(null).trigger('change');
            }
        });
   });
</script>
