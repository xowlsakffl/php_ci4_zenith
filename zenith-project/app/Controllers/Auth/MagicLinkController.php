<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Controllers\MagicLinkController as ShieldMagicLinkController;
use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Config\Auth;
use CodeIgniter\Shield\Models\UserIdentityModel;

class MagicLinkController extends ShieldMagicLinkController
{
    public function verify(): RedirectResponse
    {
        $token = $this->request->getGet('token');

        /** @var UserIdentityModel $identityModel */
        $identityModel = model(UserIdentityModel::class);

        $identity = $identityModel->getIdentityBySecret(Session::ID_TYPE_MAGIC_LINK, $token);
        
        $identifier = $token ?? '';

        // No token found?
        if ($identity === null) {
            $this->recordLoginAttempt($identifier, false);

            $credentials = ['magicLinkToken' => $token];
            Events::trigger('failedLogin', $credentials);

            return redirect()->route('magic-link')->with('error', lang('Auth.magicTokenNotFound'));
        }

        // Delete the db entry so it cannot be used again.
        $identityModel->delete($identity->id);

        // Token expired?
        if (Time::now()->isAfter($identity->expires)) {
            $this->recordLoginAttempt($identifier, false);

            $credentials = ['magicLinkToken' => $token];
            Events::trigger('failedLogin', $credentials);

            return redirect()->route('magic-link')->with('error', lang('Auth.magicLinkExpired'));
        }

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // If an action has been defined
        if ($authenticator->hasAction($identity->user_id)) {
            return redirect()->route('auth-action-show')->with('error', lang('Auth.needActivate'));
        }

        // Log the user in
        $authenticator->loginById($identity->user_id);

        $user = $authenticator->getUser();
        
        $this->recordLoginAttempt($identifier, true, $user->id);
        dd($identifier);
        // Give the developer a way to know the user
        // logged in via a magic link.
        session()->setTempdata('magicLogin', true);

        Events::trigger('magicLogin');

        // Get our login redirect url
        return redirect()->route('set_password');
    }

    private function recordLoginAttempt(
        string $identifier,
        bool $success,
        $userId = null
    ): void {
        /** @var LoginModel $loginModel */
        $loginModel = model(LoginModel::class);

        $loginModel->recordLoginAttempt(
            Session::ID_TYPE_MAGIC_LINK,
            $identifier,
            $success,
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent(),
            $userId
        );
    }
}
