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
            <form method="POST" action="{{ route('soda.analytics.configure.create-account') }}" enctype="multipart/form-data">
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
            <form method="POST" action="{{ route('soda.analytics.configure.create-property') }}"
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

    <form v-if="account != 0 && account != 1 && property != 0 && property != 1" method="POST"
          action="{{ route('soda.analytics.configure.submit-account-property') }}" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <input type="hidden" name="account_id" :value="account"/>
        <input type="hidden" name="account_name" :value="account_name"/>
        <input type="hidden" name="property_id" :value="property"/>
        <input type="hidden" name="property_name" :value="property_name"/>

        <button class="btn btn-primary">Save</button>
    </form>

</div>
