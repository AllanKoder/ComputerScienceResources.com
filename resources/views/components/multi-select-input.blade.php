@props(['name', 'clear' => false])

<select class="js-example-basic-multiple" name="{{$name}}[]" multiple="multiple" id="multi-select-{{$name}}">
    {{$slot}}
</select>

<script>
    // Function to initialize Select2 and set up local storage handling
    function initializeSelect2(selectElement, shouldClear) {
        let selectName = selectElement.attr('name');
        let baseUrl = @json($getURL());
        let id = `multi-select-${selectName}-${baseUrl}`;
        
        // Initialize Select2
        selectElement.select2();

        // Clear the selection if the clear prop is true
        if (shouldClear) {
            selectElement.val(null).trigger('change');
            localStorage.removeItem(id);
        }

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
        $('.js-example-basic-multiple').each(function() {
            let shouldClear = @json($clear) // This will be true or false based on the passed prop
            initializeSelect2($(this), shouldClear);
        });
    });
</script>
