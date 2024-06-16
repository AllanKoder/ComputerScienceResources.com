@props(['name', 'placeholder', 'maxSize'])

<!-- name Input -->
<div class="dynamic-table" name="{{ $name }}" class="border-width: 0">
    <x-text-input-field type="text" name="{{ $name }}[0]" placeholder="{{ $placeholder }}" class="form-control w-10/12"></x-text-input-field>
    <button type="button" class="add btn btn-success p-1 border-black border-2">Add More</button>
    <div class="inputs-container"></div>
</div>

<script>
    $(document).ready(function() {
        $('.dynamic-table').each(function(index, table) {
            let selectName = $(table).attr('name'); // This should be unique for each component
            let id = `multi-text-input-${selectName}-${index}`; // Unique ID for localStorage key
            var savedInputs = JSON.parse(localStorage.getItem(id)) || [];

            // Function to add a new input field with saved value if available
            function addNewInput(container, index, name, placeholder, value = '') {
                var newInput = `
                    <div class="input-group">
                        <input type="text" name="${name}[${index}]" placeholder="${placeholder}" value="${value}" class="form-control w-10/12" />
                        <button type="button" class="remove btn btn-danger p-1 border-black border-2">Remove</button>
                    </div>
                `;
                container.append(newInput);
            }

            // Function to update storage with all input values
            function updateStorage(container){
                let inputs = [];
                container.find('.input-group input').each(function() {
                    inputs.push($(this).val());
                });
                let id = `multi-text-input-${container.attr('name')}-${container.index('.dynamic-table')}`;
                localStorage.setItem(id, JSON.stringify(inputs));
            }

            // Load saved inputs
            if (savedInputs.length > 0) {
                let inputsContainer = $(table).find('.inputs-container');
                inputsContainer.empty(); // Clear existing inputs
                savedInputs.forEach((value, index) => {
                    addNewInput(inputsContainer, index, selectName, 'Placeholder', value);
                });
            }

            // Event delegation for add button click handler
            $(document).off('click', '.add').on('click', '.add', function(){
                let container = $(this).closest('.dynamic-table');
                let name = selectName;
                let placeholder = 'Placeholder'; // Replace with the actual placeholder value
                let index = container.find('.input-group').length;
                addNewInput(container, index, name, placeholder);

                updateStorage(container);
            });

            // Event delegation for remove button click handler
            $(document).off('click', '.remove').on('click', '.remove', function(){  
                if(confirm('Are you sure you want to remove this item?')) {
                    let container = $(this).closest('.dynamic-table');
                    $(this).closest('.input-group').remove();
                    updateStorage(container); // Pass the container to the updateStorage function
                }
            });

            // Attach event listener to input fields for any change
            $(document).on('input', '.input-group input', function(){
                let container = $(this).closest('.dynamic-table');
                updateStorage(container);
            });
        });
    });
</script>
