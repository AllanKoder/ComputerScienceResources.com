@props(['name'])

<select name ="{{$name}}[]" class="form-control-multi-tags shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" multiple="multiple">
    {{$slot}}
</select>

<script>
    $(".form-control-multi-tags").select2({
        tags: true
    });
</script>