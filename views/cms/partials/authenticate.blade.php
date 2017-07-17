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
