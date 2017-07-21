<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;
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
        <form method="POST" action="{{ route('soda.analytics.audience.update') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}

            {!! app('soda.form')->dropdown([
                "name"        => "Schedule Frequency",
                "field_name"  => "schedule_frequency",
                "description"  => "How frequent do you want emails to send to each of your schedules?",
                "field_params" => ["options"=>config('soda.analytics.schedule.frequencies')]
            ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            <button class="btn btn-primary">
                Update
            </button>
        </form>
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

