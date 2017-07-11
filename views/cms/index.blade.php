<?php
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\AnalyticsAccount;
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <ol class="breadcrumb">
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
        @if( Auth::guard('soda-analytics')->check() )
            Currently logged in as: {{ Auth::guard('soda-analytics')->user()->name }}.
        @endif

        <br/>
        <br/>

        <a href="{{ route('analytics.auth') }}">
            <div class="btn btn-info btn-lg">
                <i class="fa fa-google"></i>
                <span>LOG IN WITH {{ Auth::guard('soda-analytics')->check() ? 'ANOTHER GOOGLE ACCOUNT' : 'GOOGLE' }}</span>
            </div>
        </a>

        <br/>
        <br/>
    </div>
    <div id="analytics-index" class="content-block">
        @if( Auth::guard('soda-analytics')->check() )
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
                <form method="POST" action="{{ route('api.analytics.create-account') }}" enctype="multipart/form-data">
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
                <form method="POST" action="{{ route('api.analytics.create-property') }}"
                      enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    {!! app('soda.form')->text([
                        "name"        => "New Property",
                        "field_name"  => "property_name",
                        "description" => "Create a New Property"
                    ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

                    <button class="btn btn-primary">Create New Property</button>
                </form>
                <hr/>
            </div>

            <br/>
            <br/>

            <form v-if="account != 0 && account != 1 && property != 0 && property != 1" method="POST" action="{{ route('api.analytics.submit-account-property') }}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="hidden" name="account_id" :value="account"/>
                <input type="hidden" name="account_name" :value="accounts[account]"/>
                <input type="hidden" name="property_id" :value="property"/>
                <input type="hidden" name="property_name" :value="properties[property]"/>

                <button class="btn btn-primary">Save</button>
            </form>

        @endif
    </div>
@endsection

@section('footer.js')
    @parent
    <script src="/soda/analytics/js/app.js"></script>
    @if( Auth::guard('soda-analytics')->check() )
        <script>
            if ($('#analytics-index').length) {
                var analytics_vm = new Vue({
                    el: '#analytics-index',
                    data: {
                        account: 0,
                        accounts: {0: 'Loading...'},
                        new_account: false,
                        property: 0,
                        properties: {0: 'Loading...'},
                        new_property: false,
                    },
                    created: function () {
                        var me = this;

                        me.post_get_accounts();
                    },
                    watch:{
                        account: function(){
                            var me = this;

                            if (me.account == 1) {
                                me.new_account = true;
                            }
                            else if( me.account != 0 ){
                                me.post_get_account_properties();
                            }
                        },
                        property: function () {
                            var me = this;

                            if (me.property == 1) {
                                me.new_property = true;
                            }
                            else if (me.property != 0) {
                                // ready to hit save
                            }
                        },
                    },
                    methods: {
                        reset: function(){
                            var me = this;

                            me.reset_accounts();
                        },
                        reset_accounts: function(){
                            var me = this;

                            me.account = 0;
                            me.accounts = {0: 'Loading...'};

                            me.reset_properties();
                        },
                        reset_properties: function () {
                            var me = this;

                            me.property = 0;
                            me.properties = {0: 'Loading...'};
                        },


                        // ACCOUNTS
                        post_get_accounts: function () {
                            var me = this;

                            me.reset_accounts();

                            axios.post('/cms/analytics/accounts', {
                                        _token: meta('csrf-token'),
                                    })
                                    .then(function (response) {
                                        if (response.data.success) {
                                            me.accounts = Object.assign({0: 'Choose your account'}, {1: 'Create new account'}, response.data.accounts);
                                            me.account = 0;
                                        }
                                    });
                        },
                        post_new_account: function (account_name) {
                            var me = this;

                            axios.post('/cms/analytics/create-account', {
                                        _token: meta('csrf-token'),
                                        account_name: account_name
                                    })
                                    .then(function (response) {
                                        if (response.data.success) {
                                            me.post_get_accounts();
                                            me.account = response.data.account_id;
                                        }
                                    });
                        },


                        post_get_account_properties: function () {
                            var me = this;

                            me.reset_properties();

                            axios.post('/cms/analytics/account-properties', {
                                        _token: meta('csrf-token'),
                                        account: me.account
                                    })
                                    .then(function (response) {
                                        if (response.data.success) {
                                            me.properties = Object.assign({0: 'Choose your property'}, {1: 'Create new property'}, response.data.properties);
                                            me.property = 0;
                                        }
                                    });
                        },
                        post_new_property: function (property_name) {
                            var me = this;

                            axios.post('/cms/analytics/create-property', {
                                        _token: meta('csrf-token'),
                                        account: me.account,
                                        property_name: property_name
                                    })
                                    .then(function (response) {
                                        if (response.data.success) {
                                            me.post_get_account_properties();
                                            me.account = response.data.property_id;
                                        }
                                    });
                        },
                    },
                });
            }
        </script>
    @endif
@endsection
