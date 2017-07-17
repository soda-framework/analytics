<?php
use Illuminate\Support\Facades\Auth;
use Soda\Analytics\Components\GoogleAPI;

$config = GoogleConfig::get();
$logged_in = Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle();
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li class="active">Analytics</li>
    </ol>
@stop

@section('head.title')
    <title>Analytics</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics > Auth',
])

@section('content')
    <div class="content-header">
        <h2>Audience</h2>
    </div>

    <div class="content-block">
        {{ dd($visitors) }}
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection

