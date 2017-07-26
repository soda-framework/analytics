<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;

    $config = GoogleConfig::get();
    $frequencies = config('soda.analytics.scheduler.frequencies');
    $frequency = $config->schedule_frequency ? $frequencies[$config->schedule_frequency] : array_first($frequencies);
    $cron = $config->schedule_frequency ? $config->schedule_frequency : array_first(array_keys($frequencies));

    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();
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
    <div id="schedules" class="content-top">
        <form id="schedule_frequency" method="POST" action="{{ route('soda.analytics.scheduler.config-update') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}

            {!! app('soda.form')->dropdown([
                "name"        => "Schedule Frequency",
                "field_name"  => "schedule_frequency",
                "description"  => "How frequent do you want emails to send to each of your schedules?",
                "field_params" => [
                    "options"=>$frequencies,
                ]
            ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
        </form>

        <h3>Cron Command:</h3>
        To start the scheduler, add the following <b>cron job</b>:
        <br/>
        &nbsp;&nbsp;&nbsp;<code>{!! $cron !!} php /path/to/artisan soda-analytics:schedules 1>> /dev/null 2>&1</code>
        <br/>
        to your server (using the <code>crontab -e</code> command),
        which will execute <code>php /path/to/artisan soda-analytics:schedules</code> on a <b>{{ $frequency }}</b> basis.
    </div>

    <div class="content-block">
        <h3>Schedules:</h3>

        {!! $grid !!}

        <a class="btn btn-primary" href="{{route('soda.analytics.scheduler.create')}}">
            Create
        </a>
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

