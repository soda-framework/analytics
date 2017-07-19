<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;
    use Soda\Analytics\Components\GoogleAPI;

    $config = GoogleConfig::get();
    $logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();

?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li>Analytics</li>
        <li class="active">Events</li>
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
        {{--{!! $filter !!}--}}
        <a href="{{ Request::url() }}">Clear</a>
    </div>

    <div class="content-block">
        {!!  $grid  !!}
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

