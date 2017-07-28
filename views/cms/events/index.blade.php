<?php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Database\Models\Event;

    $config = GoogleConfig::get();
    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();

    $last_updated = Event::first();
    $last_updated = $last_updated ? (new Carbon($last_updated->created_at))->format('d-m-Y') : 'never';
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li><a href="{{ route('soda.analytics.configure') }}">Analytics</a></li>
        <li class="active"><a href="{{ route('soda.analytics.events') }}">Events</a></li>
    </ol>
@stop

@section('head.title')
    <title>Analytics | Events</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics | Events',
])

@section('content')
    <div class="content-top">
        <form method="POST" action="{{ route('soda.analytics.events.update') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}

            @include('soda-analytics::cms.partials.inputs.dates')

            <br/>

            <a href="{{route('soda.analytics.events.update')}}">
                <button class="btn btn-primary">
                    Update
                </button>
            </a>
            Last Updated: {{ $last_updated }}
        </form>

        <br/>
        <br/>

        <h3>Filters:</h3>
        {!! $filter !!}
    </div>

    <div class="content-block">
        <h3>Events: {{ $grid->source->query->count() }}</h3>

        {!! $grid !!}

        <a href="{{route('soda.analytics.events.export',request()->getQueryString())}}" target="_blank">
            <div class="btn btn-primary">
                Export to CSV
            </div>
        </a>
        <a href="{{route('soda.analytics.scheduler.create-from-event',request()->query->all())}}">
            <div class="btn btn-primary">
                Create Schedule
            </div>
        </a>
    </div>
@endsection

@section('modals')
    @parent
    @include('soda-analytics::cms.partials.error')
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

