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

        <form method="POST" action="{{ route('soda.analytics.configure.post') }}"
              enctype="multipart/form-data">
            {!! csrf_field() !!}

            @include('soda-analytics::cms.configure.instructions.client-credentials')

            {!! app('soda.form')->text([
                "name"        => "Client ID",
                "field_name"  => "client_id",
            ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
            {!! app('soda.form')->text([
                "name"        => "Client Secret",
                "field_name"  => "client_secret",
            ])->setModel($config)->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

            <button class="btn btn-primary">SAVE</button>
        </form>

    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection
