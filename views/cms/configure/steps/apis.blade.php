<div v-show="completed_steps_up_to({{ $step }})" :class="['step', completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Enable API's for @{{ config ? config.project_name : 'your project' }} in Google Console</h3>
    <div class="step-content">
        <ul>
            @foreach(config('soda.analytics.apis') as $api)
                <li>{{ $api }}</li>
            @endforeach
        </ul>
        <a href="{{ route('soda.analytics.configure.enable-apis') }}">
            <button class="btn btn-primary">Enable API's</button>
        </a>
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
