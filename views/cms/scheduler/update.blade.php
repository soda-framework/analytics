<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Controllers\ScheduleController;
    use Soda\Analytics\Database\Models\Schedule;

    $config = GoogleConfig::get();
    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li>Analytics</li>
        <li class="active">Schedules</li>
    </ol>
@stop

@section('head.title')
    <title>Analytics | Schedules</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics | Schedule',
])

@section('content')
    <div class="content-top">
        <h3>Create Schedule</h3>
    </div>

    <div class="content-block">
        <form method="POST" action="{{ route('soda.analytics.scheduler.update.post',$schedule->id) }}" enctype="multipart/form-data">
            {!! csrf_field() !!}

            @include('soda-analytics::cms.partials.inputs.dates')

            {!! app('soda.form')->dropdown([
                "name"        => "Type",
                "field_name"  => "type",
                "field_params" => ["options"=>[
                    ScheduleController::EVENTS => ScheduleController::EVENTS,
                    ScheduleController::AUDIENCE => ScheduleController::AUDIENCE,
                ]]
            ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            {!! app('soda.form')->tags([
                "name"        => "Recipient Emails",
                "field_name"  => "emails",
            ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            <button class="btn btn-primary">
                Save
            </button>
        </form>
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

