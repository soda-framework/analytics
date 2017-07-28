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

@section('head.meta')
    @parent
    <meta name="logged_id" content="{{ Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() ? 'true' : 'false'}}">
    <meta name="account_id" content="{{ $config->account_id }}">
    <meta name="account_name" content="{{ $config->account_name }}">
    <meta name="property_id" content="{{ $config->property_id }}">
    <meta name="property_name" content="{{ $config->property_name }}">
@endsection

@section('head.css')
    @parent
    <link href="/soda/analytics/css/app.css" rel="stylesheet" type="text/css">
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics > Auth',
])

@section('content')
    <div id="configure">
        <div class="content-header">
            <h2>Setup</h2>
        </div>

        @include('soda-analytics::cms.configure.steps.project-id',['step'=>1])

        @include('soda-analytics::cms.configure.steps.login-credentials',['step'=>2])

        @include('soda-analytics::cms.configure.steps.login',['step'=>3])

        @include('soda-analytics::cms.configure.steps.apis',['step'=>4])

        @include('soda-analytics::cms.configure.steps.create-service-account-and-key',['step'=>5])

        @include('soda-analytics::cms.configure.steps.analytics-account-property',['step'=>6])

        @include('soda-analytics::cms.configure.steps.analytics-user',['step'=>7])

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
