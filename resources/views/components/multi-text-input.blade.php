@props(['name', 'placeholder'])

<!-- name Input -->
<div class="dynamic-table">
    <p>{{ $name }}</p>
    <input type="text" oninput="updateInput(this)" name="{{ $name }}[0]" placeholder="{{ $placeholder}}" class="form-control" />
    <button type="button" class="add btn btn-success">Add More</button>
</div>

<script>
    // Function to add a new row
    function addNewRow(table, index, name, placeholder) {
        var newRow = `
        <tr>
            <td>
                <input type="text" name="${name}[${index}]" placeholder="${placeholder}" class="form-control" />
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-tr">Remove</button>
            </td>
        </tr>`;
        table.append(newRow);
    }

    // Event delegation for add button click handler
    $(document).off('click', '.add').on('click', '.add', function(){
        var table = $(this).closest('.dynamic-table');
        var name = table.find('input.form-control').attr('name').split('[')[0];
        var placeholder = table.find('input.form-control').attr('placeholder').split('[')[0];
        var index = table.find('tr').length; // Use the number of existing rows to determine the new index
        addNewRow(table, index, name, placeholder);
    });

    // Event delegation for remove button click handler
    $(document).off('click', '.remove-tr').on('click', '.remove-tr', function(){  
        $(this).closest('tr').remove();
    });
</script>

