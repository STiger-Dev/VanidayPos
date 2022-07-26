<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class XeroAuthController extends Controller
{
    private $provider;

    public function __construct()
    {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => 'E91D49A4140E4FF18600E09414DC693D',   
            'clientSecret'            => 'Te0AErn9FX-dVoBso7aG-R6tZA5GSKP_0EBAnf7-bfx2qGyI',
            'redirectUri'             => route('xero.callback'),
            'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken'          => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
        ]);
    }

    /**
     * Update shipping.
     *
     * @param  Request $request, $event
     * @return \Illuminate\Http\Response
     */
    protected function auth(Request $request, $event)
    {
        // Scope defines the data your app has permission to access.
        // Learn more about scopes at https://developer.xero.com/documentation/oauth2/scopes
        $options = [
        'scope' => ['openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.journals.read accounting.reports.read accounting.attachments']
        ];
    
        // This returns the authorizeUrl with necessary parameters applied (e.g. state).
        $authorizationUrl = $this->provider->getAuthorizationUrl($options);
    
        // Save the state generated for you and store it to the session.
        // For security, on callback we compare the saved state with the one returned to ensure they match.
        $_SESSION['oauth2state'] = $this->provider->getState();
        $request->session()->put('xero-event', $event);
    
        return redirect($authorizationUrl);
    }

    protected function callback(Request $request) {
        $code = $request->only(['code']);
        $accessTokenInfo = $this->provider->getAccessToken('authorization_code', $code);
        $accessToken = $accessTokenInfo->getToken();

        $event = $request->session()->get('xero-event');
        switch ($event) {
            case 'invoice':
                return redirect()->route('sells.sent-invoice-xero', ['accessToken' => $accessToken]);
            break;
        }
    }
}
