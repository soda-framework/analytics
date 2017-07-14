<?php
    use Illuminate\Http\Request;
    use \Soda\Cms\Database\Models\ApplicationUrl;
?>

<article>
    <h3>
        Getting your Client Credentials
    </h3>
    <p>
        Click "Credentials" in the sidebar. You’ll want to create an "OAuth client ID".</p>
    <p>
        <img style="max-width: 100%;" src="http://files.madeinkatana.com.s3.amazonaws.com/soda-framework/analytics/client-instructions-1.jpg">
    </p>
    <p>
        On the next screen you'll want to set the Application Type to <code>Web application</code>.
        <br/>
        Name the client whatever you want.
        <br/>
        Add these url's to the <code>Authorized redirect URIs</code> field.
        <br/>
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
    </p>
    <p>
        <img style="max-width: 100%;" src="http://files.madeinkatana.com.s3.amazonaws.com/soda-framework/analytics/client-instructions-2.jpg">
    </p>
    <p>
        Enter your Client ID and Client Secret in the fields below.
    </p>
</article>

