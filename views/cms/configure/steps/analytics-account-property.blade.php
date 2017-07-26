<div v-show="completed_steps_up_to({{ $step }})" :class="['step', completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Choose your Analytics Account & Property</h3>
    <div class="step-content">
        @include('soda-analytics::cms.partials.accounts-properties')
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
