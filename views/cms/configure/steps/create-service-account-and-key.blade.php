<div v-show="completed_steps_up_to({{ $step }})" :class="['step', completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Create a Service Account and Access Key</h3>
    <div class="step-content">
        <a href="{{ route('soda.analytics.configure.create-service-account-and-key') }}">
            <button class="btn btn-primary">Create Service Account & Access Key</button>
        </a>
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
