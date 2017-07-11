@section("field")
    <select name="{{ $prefixed_field_name }}" class="form-control field_{{ $field_name }}" id="{{ $field_id }}" v-model="{!! $field_parameters['v-model'] !!}">
        <option v-for="(option, index) in {!! $field_parameters['v-options'] !!}" :value="index"> @{{ option }} </option>
    </select>
@overwrite
