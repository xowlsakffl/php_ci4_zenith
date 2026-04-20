<?php

namespace App\Controllers;

use App\Controllers\AdvertisementManager\AdvFacebookManagerController;
use App\Controllers\AdvertisementManager\AdvGoogleManagerController;
use App\Controllers\AdvertisementManager\AdvKakaoManagerController;
use CodeIgniter\API\ResponseTrait;

class HomeController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $data = [
            'password_check' => false,
        ];

        $user = auth()->user();
        if ($user !== null) {
            $identity       = $user->getEmailIdentity();
            $passwordChange = $identity->password_changed_at ?? null;
            $data['password_check'] = $this->isPasswordChangeRequired($passwordChange);
        }

        return view('pages/home', $data);
    }

    public function getReports()
    {
        if (! $this->request->isAJAX()) {
            return $this->failForbidden('AJAX 요청만 허용됩니다.');
        }

        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->fail('허용되지 않은 요청 메서드입니다.', 405);
        }

        try {
            return $this->respond($this->collectReports());
        } catch (\Throwable $exception) {
            log_message('error', '[HomeController::getReports] {message}', ['message' => $exception->getMessage()]);

            return $this->failServerError('리포트를 불러오지 못했습니다.');
        }
    }

    private function isPasswordChangeRequired(?string $passwordChange): bool
    {
        if ($passwordChange === null || $passwordChange === '') {
            return false;
        }

        $changedAt = strtotime($passwordChange);
        if ($changedAt === false) {
            return false;
        }

        return $changedAt < strtotime('-90 days');
    }

    private function collectReports(): array
    {
        return [
            'facebookReport' => (new AdvFacebookManagerController())->getReport(),
            'googleReport'   => (new AdvGoogleManagerController())->getReport(),
            'kakaoReport'    => (new AdvKakaoManagerController())->getReport(),
        ];
    }
}
