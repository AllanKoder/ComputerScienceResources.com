@props(['name'])

<select class="js-example-basic-multiple" name="{{$name}}[]" multiple="multiple">
    {{$slot}}
</select>

<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
</script>