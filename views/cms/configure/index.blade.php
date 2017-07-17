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
    <meta name="account_id" content="{{ $config->account_id }}">
    <meta name="account_name" content="{{ $config->account_name }}">
    <meta name="property_id" content="{{ $config->property_id }}">
    <meta name="property_name" content="{{ $config->property_name }}">
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics > Auth',
])

@section('content')
    <div class="content-header">
        <h2>Steps</h2>
    </div>

    <hr/>

    <step class="content-block">
        <h3 class="">STEP 1</h3>
        <ul>
            <li>
                <a href="https://console.developers.google.com/projectcreate" target="_blank">Create</a>
                or
                <a href="https://console.developers.google.com/apis/library?project={{ $config->project_name }}" target="_blank">Choose</a>
                a project on Google Console
            </li>
            <li>
                Enable the
                <a href="https://console.developers.google.com/apis/api/servicemanagement.googleapis.com/overview?project={{ $config->project_name }}" target="_blank">Service Management API</a>
            </li>
            <li>
                Enter your project name below and save.
            </li>
        </ul>
        <form method="POST" action="{{ route('soda.analytics.configure.post') }}"
              enctype="multipart/form-data">
            {!! csrf_field() !!}

            {!! app('soda.form')->text([
                "name"        => "Project Name",
                "field_name"  => "project_name",
            ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            <button class="btn btn-primary">SAVE</button>
        </form>
    </step>

    <hr/>

    <step class="content-block {{ $logged_in ? 'completed' : '' }}">
        <h3 class="">STEP 2</h3>
        @include('soda-analytics::cms.partials.authenticate')
    </step>

    <hr/>

    @if( $logged_in )
        <step class="content-block {{ $config->apis_enabled ? 'completed' : '' }}">
            <h3 class="">STEP 3</h3>
            <a href="{{ route('soda.analytics.configure.enable-apis') }}">
                <button class="btn btn-primary">Enable API's</button>
            </a>
        </step>

        <hr/>

        @if( $config->apis_enabled )
            <step class="content-block {{ $config->service_account_credentials_json ? 'completed' : '' }}">
                <h3 class="">STEP 4</h3>
                <a href="{{ route('soda.analytics.configure.create-service-account-and-key') }}">
                    <button class="btn btn-primary">Create Service Account & Access Key</button>
                </a>
            </step>

            <hr/>

            @if( $config->service_account_credentials_json )
                <step class="content-block">
                    <h3 class="">STEP 5</h3>
                    @include('soda-analytics::cms.partials.accounts-properties')
                    <button class="btn btn-primary">Create Analytics Account and Web Property</button>
                </step>

                <hr/>

                @if( $config->account_id && $config->property_id && $config->view_id)
                    <step class="content-block">
                        <h3 class="">STEP 6</h3>
                        <a href="{{ route('soda.analytics.configure.add-analytics-user') }}">
                            <button class="btn btn-primary">Add user permission to analytics account</button>
                        </a>
                    </step>

                    <hr/>

                    @if( $config->analytics_user_added )
                        <h1>YOU'RE DONE</h1>
                        <a href="{{ route('soda.analytics') }}">
                            <button class="btn btn-primary">Start using Google Analytics</button>
                        </a>
                    @endif
                @endif
            @endif
        @endif
    @endif
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection
