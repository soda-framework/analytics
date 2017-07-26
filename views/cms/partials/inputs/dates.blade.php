<?php
    $model = isset($model) ? $model : GoogleConfig::get();
?>

<div class="dates row">
    <div class="col-xs-12">
        <h3>Dates:</h3>
    </div>
    <div class="col-xs-12 col-sm-6">
        {!! app('soda.form')->datetime([
            "name"        => "From",
            "field_name"  => 'analytics_from',
            "field_params" => ["options"=>[
                "format" => "DD/MM/YYYY"
            ]]
        ])->setModel($model)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
    </div>
    <div class="col-xs-12 col-sm-6">
        {!! app('soda.form')->datetime([
            "name"        => "To",
            "field_name"  => 'analytics_to',
            "field_params" => ["options"=>[
                "format" => "DD/MM/YYYY",
                "showClear" => true
            ]],
            "description" => "Leaving blank will use the current time whenever this schedule is run."
        ])->setModel($model)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
    </div>
</div>
