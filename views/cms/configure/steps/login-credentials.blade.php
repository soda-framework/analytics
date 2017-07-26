<?php
    use \Soda\Cms\Database\Models\ApplicationUrl;
?>

<div :class="['step', config && config.project_name && completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Create Client Credentials for Google Authentication</h3>
    <div v-if="config" class="step-content">
        <ul>
            <li>
                Go to the <a :href="'https://console.developers.google.com/apis/credentials?project='+ config.project_name"
                   target="_blank">Credentials Tab</a> for your project on Google Console.
            </li>
            <li>
                Click <i>Create credentials</i>, then <i>OAuth client ID</i>.
            </li>
            <li>
                Set the Application Type to <i>Web application</i>.
            </li>
            <li>
                Give the client your desired name.
            </li>
            <li>
                Add these url's to the <i>Authorized redirect URIs</i> field (use https if required).
                <ul>
                    @foreach( ApplicationUrl::all() as $url )
                        <li>
                            http{{ request()->isSecure() ? 's' : '' }}://{{ $url->domain }}{{ URL::route('soda.analytics.auth', [], false) }}
                        </li>
                        <li>
                            http{{ request()->isSecure() ? 's' : '' }}://{{ $url->domain }}{{ URL::route('soda.analytics.auth.callback', [], false) }}
                        </li>
                    @endforeach
                </ul>
            </li>
            <li>
                Enter the created Client ID and Client Secret in the fields below.
            </li>
        </ul>

        <label for="client_id">Client ID</label>
        <input id="client_id" name="client_id" type="text" v-model="config.client_id" placeholder="Client ID" class="form-control">

        <label for="client_secret">Client Secret</label>
        <input id="client_secret" name="client_secret" type="text" v-model="config.client_secret" placeholder="Client Secret" class="form-control">

        <div v-on:click="set_login_credentials(config.client_id, config.client_secret)" class="btn btn-primary">SAVE</div>
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
