<?php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Database\Models\Audience;

    $config = GoogleConfig::get();
    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();

    $last_updated = Audience::first();
    $last_updated = $last_updated ? (new Carbon($last_updated->created_at))->format('d-m-Y') : 'never';
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li><a href="{{ route('soda.analytics.configure') }}">Analytics</a></li>
        <li class="active"><a href="{{ route('soda.analytics.audience') }}">Audience</a></li>
    </ol>
@stop

@section('head.title')
    <title>Analytics | Audience</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics | Audience',
])

@section('content')
    <div class="content-top">
        <form method="POST" action="{{ route('soda.analytics.audience.update') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}

            @include('soda-analytics::cms.partials.inputs.dates')

            <br/>

            <a href="{{route('soda.analytics.audience.update')}}">
                <button class="btn btn-primary">
                    Update
                </button>
            </a>
            Last Updated: {{ $last_updated }}
        </form>
    </div>

    <div class="content-block">
        <h3>Audience:</h3>

        {!! $grid !!}

        <a href="{{route('soda.analytics.audience.export',request()->getQueryString())}}" target="_blank">
            <div class="btn btn-primary">
                Export to CSV
            </div>
        </a>
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

