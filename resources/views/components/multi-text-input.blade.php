@props(['name', 'placeholder'])

<!-- name Input -->
<div class="dynamic-table">
    <input type="text" oninput="updateInput(this)" name="{{ $name }}[0]" placeholder="{{ $placeholder }}" class="form-control w-10/12" />
    <button type="button" class="add btn btn-success p-1 border-black border-2">Add More</button>
</div>

<script>
    $(document).ready(function() {
        // Function to add a new input field
        function addNewInput(container, index, name, placeholder) {
            var newInput = `
                <div class="input-group">
                    <input type="text" name="${name}[${index}]" placeholder="${placeholder}" class="form-control w-10/12" />
                    <button type="button" class="remove btn btn-danger p-1 border-black border-2">Remove</button>
                </div>
            `;
            container.append(newInput);
        }

        // Event delegation for add button click handler
        $(document).off('click', '.add').on('click', '.add', function(){
            var container = $(this).closest('.dynamic-table');
            var name = container.find('input.form-control').attr('name').split('[')[0];
            var placeholder = container.find('input.form-control').attr('placeholder');
            var index = container.find('.input-group').length; // Use the number of existing input groups to determine the new index
            addNewInput(container, index, name, placeholder);
        });

        // Event delegation for remove button click handler
        $(document).off('click', '.remove').on('click', '.remove', function(){  
            $(this).closest('.input-group').remove();
        });

        // Before form submission, remove the name attribute from any empty input fields
        $('form').on('submit', function() {
            $('.dynamic-table input[type="text"]').each(function() {
                if ($(this).val() === '') {
                    $(this).removeAttr('name');
                }
            });
        });
    });
</script>
