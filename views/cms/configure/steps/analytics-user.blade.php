<div v-show="completed_steps_up_to({{ $step }})" :class="['step', completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Add a read-only user to your Analytics Account</h3>
    <div class="step-content">
        <ul>
            <li>
                So Soda can access your analytics without you needing to log in.
            </li>
        </ul>
        <a href="{{ route('soda.analytics.configure.add-analytics-user') }}">
            <button class="btn btn-primary">Add user permission to analytics account</button>
        </a>
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
