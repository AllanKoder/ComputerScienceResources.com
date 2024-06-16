@props(['name', 'attributes' => []])

<select {{ $attributes->merge(['class' => 'form-control-multi-tags']) }} name="{{$name}}[]" multiple="multiple" id="multi-tags-{{$name}}">
    {{$slot}}
</select>

<script>
    // Function to initialize Select2 and set up local storage handling
    function initializeSelect2(selectElement) {
        let selectName = selectElement.attr('name');
        let baseUrl = @json($getURL());
        let id = `multi-tag-${selectName}-${baseUrl}`;

        // Function to update local storage with the selected values
        function updateSelections() {
            var selectedValues = selectElement.val();
            localStorage.setItem(id, JSON.stringify(selectedValues));
        }

        // Function to create options from local storage
        function createOptionsFromLocalStorage() {
            var selectedValues = JSON.parse(localStorage.getItem(id)) || [];
            selectedValues.forEach(function(value) {
                // Create a new option if it doesn't exist
                if (!selectElement.find(`option[value="${value}"]`).length) {
                    var newOption = new Option(value, value, false, false);
                    selectElement.append(newOption).trigger('change');
                }
            });
        }

        // Set the value of Select2 if there are stored selections
        if (localStorage.getItem(id)) {
            createOptionsFromLocalStorage();
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
        $('.form-control-multi-tags').each(function() {
            initializeSelect2($(this));
        });

        // Initialize Select2 with tags
        $('.form-control-multi-tags').select2({
            tags: true
        });
        
        // Listen for a custom global event to clear the select
        $(document).on('clearInputs', function(event, selectName) {
            if (selectName) {
                $(`#multi-tags-${selectName}`).val(null).trigger('change');
            } else {
                // Clear all selects if no specific name is provided
                $('.form-control-multi-tags').val(null).trigger('change');
            }
        });
    });
</script>
