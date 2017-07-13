<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\AnalyticsAccount;

    $config = Analytics::config();
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
        @if( Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() )
            Currently logged in as: {{ Auth::guard('soda-analytics')->user()->name }}.
            <br/>
            <br/>
        @endif

        <a href="{{ route('soda.analytics.auth') }}">
            <div class="btn btn-info btn-lg">
                <i class="fa fa-google"></i>
                <span>LOG IN WITH {{ Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() ? 'ANOTHER GOOGLE ACCOUNT' : 'GOOGLE' }}</span>
            </div>
        </a>

        <br/>
        <br/>
    </div>
    @if( Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() )
        <div id="analytics-index" class="content-block">
            <div v-if="account != 0 && account != 1">
                @{{ account_name }} (@{{ account }}): <a v-on:click="post_get_accounts()">CHANGE</a>
            </div>
            <div v-else>
                {{--List Accounts--}}
                {!! app('soda.form')->dropdown_vue([
                    'name'        => 'Accounts',
                    'field_name'  => 'accounts',
                    'description'  => 'Your google analytics accounts.',
                    'field_params' => [
                        'v-model' => 'account',
                        'v-options' => 'accounts'
                    ]
                ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked-group')) !!}
                <div v-if="account == 1" class="" style="padding-left: 20px;">
                    <hr/>
                    <form method="POST" action="{{ route('soda.analytics.create-account') }}" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        {!! app('soda.form')->text([
                            "name"        => "New Account",
                            "field_name"  => "account_name",
                            "description" => "Create a New Account"
                        ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

                        <button class="btn btn-primary">Create New Account</button>
                    </form>
                    <hr/>
                </div>
            </div>

            <div v-if="property != 0 && property != 1">
                @{{ property_name }} (@{{ property }}): <a v-on:click="post_get_web_properties()">CHANGE</a>
            </div>
            <div v-else>
                {{--List Account Properties--}}
                {!! app('soda.form')->dropdown_vue([
                    'name'        => 'Properties',
                    'field_name'  => 'properties',
                    'description'  => 'Your properties for your selected account.',
                    'field_params' => [
                        'v-model' => 'property',
                        'v-options' => 'properties'
                    ]
                ])
                ->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked-group')) !!}
                <div v-if="property == 1" class="" style="padding-left: 20px;">
                    <hr/>
                    <form method="POST" action="{{ route('soda.analytics.create-property') }}"
                          enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="account_id" :value="account"/>
                        {!! app('soda.form')->text([
                            "name"        => "New Property",
                            "field_name"  => "property_name",
                            "description" => "Create a New Property"
                        ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

                        <button class="btn btn-primary">Create New Property</button>
                    </form>
                    <hr/>
                </div>
            </div>

            <br/>
            <br/>

            <form v-if="account != 0 && account != 1 && property != 0 && property != 1" method="POST" action="{{ route('soda.analytics.submit-account-property') }}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="hidden" name="account_id" :value="account"/>
                <input type="hidden" name="account_name" :value="account_name"/>
                <input type="hidden" name="property_id" :value="property"/>
                <input type="hidden" name="property_name" :value="property_name"/>

                <button class="btn btn-primary">Save</button>
            </form>

        </div>
    @endif
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
@endsection
