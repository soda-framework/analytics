<div v-if="account_id != 0 && account_id != 1 && account_id">
    <label>Account:</label> @{{ account_name }} (@{{ account_id }}): <a v-on:click="post_get_accounts()">CHANGE</a>
</div>
<div v-else>
    {{--List Accounts--}}
    {!! app('soda.form')->dropdown_vue([
        'name'        => 'Accounts',
        'field_name'  => 'accounts',
        'description'  => 'Your google analytics accounts.',
        'field_params' => [
            'v-model' => 'account_id',
            'v-options' => 'accounts'
        ]
    ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked-group')) !!}
    <div v-if="account_id == 1" class="analytics-create">
        <label for="new_account_name">New Account</label>
        {{--<input id="new_account_name" name="new_account_name" type="text" v-model="new_account_name" placeholder="Account Name" class="form-control">--}}
        {{--<div v-on:click="post_new_account(new_account_name)" class="btn btn-primary">Create New Account</div>--}}

        <br/>
        <br/>

        <i>
            This feature is not yet supported by the Google Management API.
        </i>
    </div>
</div>

<div v-if="property_id != 0 && property_id != 1 && property_id">
    <label>Property:</label> @{{ property_name }} (@{{ property_id }}): <a v-on:click="post_get_properties()">CHANGE</a>
</div>
<div v-else>
    {{--List Account Properties--}}
    {!! app('soda.form')->dropdown_vue([
        'name'        => 'Properties',
        'field_name'  => 'properties',
        'description'  => 'Your properties for your selected account.',
        'field_params' => [
            'v-model' => 'property_id',
            'v-options' => 'properties'
        ]
    ])
    ->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked-group')) !!}
    <div v-if="property_id == 1" class="analytics-create">
        <label for="new_property_name">New Property</label>
        <input id="new_property_name" name="new_property_name" type="text" v-model="new_property_name" placeholder="Property Name" class="form-control">
        <div v-on:click="post_new_property(new_property_name)" class="btn btn-primary">Create New Property</div>

        <br/>
        <br/>

        <i>
            This feature is only available as a developer preview in a limited beta. If you're interested in using this feature,
            <a href="https://docs.google.com/forms/d/1xyjp6ca4YkGjh7TDi1Z3XyA3XHcRHkKzFentxzUrmPY/viewform" target="_blank">request access to the beta.</a>
        </i>
    </div>
</div>
<div v-if="view_id != 0 && view_id != 1 && view_id">
    <label>View:</label> @{{ view_name }} (@{{ view_id }}): <a
            v-on:click="post_get_views()">CHANGE</a>
</div>
<div v-else>
    {{--List Account Properties--}}
    {!! app('soda.form')->dropdown_vue([
        'name'        => 'Views',
        'field_name'  => 'views',
        'description'  => 'The views for your selected account and property.',
        'field_params' => [
            'v-model' => 'view_id',
            'v-options' => 'views'
        ]
    ])
    ->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked-group')) !!}
    <div v-if="view_id == 1" class="analytics-create">
        <label for="new_view_name">New View</label>
        <input id="new_view_name" name="new_view_name" type="text" v-model="new_view_name"
               placeholder="View Name" class="form-control">
        <div v-on:click="post_new_view(new_view_name)" class="btn btn-primary">Create New View</div>

        <br/>
        <br/>

        <i>
            This feature is only available as a developer preview in a limited beta. If you're interested in using this
            feature,
            <a href="https://docs.google.com/forms/d/1xyjp6ca4YkGjh7TDi1Z3XyA3XHcRHkKzFentxzUrmPY/viewform"
               target="_blank">request access to the beta.</a>
        </i>
    </div>
</div>
