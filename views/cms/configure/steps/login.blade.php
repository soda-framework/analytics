<div v-show="completed_steps_up_to({{ $step }})" :class="['step', {{ $logged_in ? 'true' : 'false' }} && completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Authorize Soda Access to Google</h3>
    <div class="step-content">
        @if( Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() )
            Currently logged in as: {{ Auth::guard('soda-analytics')->user()->name }}.
        @endif
        <br/>
        <a href="{{ route('soda.analytics.auth') }}">
            <div class="btn btn-info btn-lg">
                <i class="fa fa-google"></i>
                <span>LOG IN WITH {{ Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() ? 'ANOTHER GOOGLE ACCOUNT' : 'GOOGLE' }}</span>
            </div>
        </a>

    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
