<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Controllers\ScheduleController;
    use Soda\Analytics\Database\Models\Schedule;

    $config = GoogleConfig::get();
    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();

    $request = $schedule->request;
    if( $schedule->request ){
        $string = '';
        foreach (json_decode($schedule->request, true) as $key => $filter) {
            $string .= ucwords($key) . ': ' . $filter . '<br/>';
        }
        $schedule->request = $string;
    }
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li><a href="{{ route('soda.analytics.configure') }}">Analytics</a></li>
        <li class="active"><a href="{{ route('soda.analytics.scheduler') }}">Schedules</a></li>
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
            <input type="hidden" name="request" value="{{ $request }}"/>

            @include('soda-analytics::cms.partials.inputs.dates',['model'=>$schedule])

            {!! app('soda.form')->text([
                "name"        => "Name",
                "field_name"  => "name",
            ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            {!! app('soda.form')->dropdown([
                "name"        => "Type",
                "field_name"  => "type",
                "field_params" => ["options"=>[
                    ScheduleController::EVENTS => ScheduleController::EVENTS,
                    ScheduleController::AUDIENCE => ScheduleController::AUDIENCE,
                    ScheduleController::EVENTS_AND_AUDIENCE => ScheduleController::EVENTS_AND_AUDIENCE,
                ]]
            ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            {!! app('soda.form')->tags([
                "name"        => "Recipient Emails",
                "field_name"  => "emails",
            ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            @if( $schedule->request )
                {!! app('soda.form')->static_text([
                    "name"        => "Filters",
                    "field_name"  => "request",
                ])->setModel($schedule)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
            @endif

            <button class="btn btn-primary">
                Save
            </button>
            <a href="{{ route('soda.analytics.scheduler.run',$schedule->id) }}" class="btn btn-warning">
                Run Schedule
            </a>
        </form>
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

