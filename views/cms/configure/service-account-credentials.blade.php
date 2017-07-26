<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\GoogleAPI;

    $config = Analytics::config();
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <li><a href="{{ route('soda.home') }}">Home</a></li>
        <li class="active">Configure</li>
    </ol>
@stop

@section('head.title')
    <title>Analytics</title>
@endsection

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-share-alt',
    'title'       => 'Analytics > Configure',
])

@section('content')
    <div class="content-header">
        Configure
    </div>
    <div class="content-block">
        In order to access your Google Analytics account, I need a few things first.

        <br/>
        <br/>

        @include('soda-analytics::cms.configure.instructions.service-account-credentials')

        {!! app('soda.form')->fancy_upload([
            "name"        => "Service Account Credentials",
            "field_name"  => "service_account_credentials_json",
            "description" => "JSON File provided after creating your Service Account Key",
            "field_params" => '{"allowedFileTypes":["text"],"allowedFileExtensions":["json"],"allowedPreviewTypes": ["image", "html", "video", "audio", "flash", "object"]}'
        ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection
