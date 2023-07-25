<?php

declare(strict_types=1);

namespace App\Language\ko;

return [
    // Exceptions
    'unknownAuthenticator'  => '{0} is not a valid authenticator.',
    'unknownUserProvider'   => 'Unable to determine the User Provider to use.',
    'invalidUser'           => 'Unable to locate the specified user.',
    'badAttempt'            => 'Unable to log you in. Please check your credentials.',
    'noPassword'            => 'Cannot validate a user without a password.',
    'invalidPassword'       => '아이디 및 비밀번호를 확인해주세요.',
    'noToken'               => 'Every request must have a bearer token in the {0} header.',
    'badToken'              => 'The access token is invalid.',
    'oldToken'              => 'The access token has expired.',
    'noUserEntity'          => 'User Entity must be provided for password validation.',
    'invalidEmail'          => 'Unable to verify the email address matches the email on record.',
    'unableSendEmailToUser' => '"{0}"로 이메일 전송을 실패하였습니다. ',
    'throttled'             => 'Too many requests made from this IP address. You may try again in {0} seconds.',
    'notEnoughPrivilege'    => 'You do not have the necessary permission to perform the desired operation.',

    'email'           => '이메일 주소',
    'username'        => '아이디',
    'old_password'    => '기존 비밀번호',
    'password'        => '비밀번호',
    'passwordConfirm' => '비밀번호 확인',
    'haveAccount'     => 'Already have an account?',

    // Buttons
    'confirm' => 'Confirm',
    'send'    => '보내기',

    // Registration
    'register'         => '회원가입',
    'registerDisabled' => 'Registration is not currently allowed.',
    'registerSuccess'  => 'Welcome aboard!',

    // Login
    'login'              => '로그인',
    'needAccount'        => 'Need an account?',
    'rememberMe'         => '로그인 유지',
    'forgotPassword'     => '비밀번호 찾기',
    'useMagicLink'       => '로그인 링크',
    'magicLinkSubject'   => '로그인 링크',
    'magicTokenNotFound' => '잘못된 주소입니다.',
    'magicLinkExpired'   => '링크가 만료되었습니다.',
    'checkYourEmail'     => '이메일을 확인하세요!',
    'magicLinkDetails'   => '로그인 링크가 메일로 전송되었습니다. 60분간 유효합니다.',
    'successLogout'      => '로그아웃 되었습니다.',
    
    // Passwords
    'errorPasswordLength'       => '비밀번호는 최소 {0, number}자리여야 합니다.',
    'suggestPasswordLength'     => 'Pass phrases - up to 255 characters long - make more secure passwords that are easy to remember.',
    'errorPasswordCommon'       => '더 강력한 비밀번호를 입력해주세요.',
    'suggestPasswordCommon'     => 'The password was checked against over 65k commonly used passwords or passwords that have been leaked through hacks.',
    'errorPasswordPersonal'     => 'Passwords cannot contain re-hashed personal information.',
    'suggestPasswordPersonal'   => 'Variations on your email address or username should not be used for passwords.',
    'errorPasswordTooSimilar'   => 'Password is too similar to the username.',
    'suggestPasswordTooSimilar' => 'Do not use parts of your username in your password.',
    'errorPasswordPwned'        => 'The password {0} has been exposed due to a data breach and has been seen {1, number} times in {2} of compromised passwords.',
    'suggestPasswordPwned'      => '{0} should never be used as a password. If you are using it anywhere change it immediately.',
    'errorPasswordEmpty'        => 'A Password is required.',
    'passwordChangeSuccess'     => 'Password changed successfully',
    'userDoesNotExist'          => 'Password was not changed. User does not exist',
    'resetTokenExpired'         => 'Sorry. Your reset token has expired.',
    'forcePasswordChange'       => '비밀번호를 변경해주세요.',
    // Email Globals
    'emailInfo'      => 'Some information about the person:',
    'emailIpAddress' => 'IP Address:',
    'emailDevice'    => 'Device:',
    'emailDate'      => 'Date:',

    // 2FA
    'email2FATitle'       => 'Two Factor Authentication',
    'confirmEmailAddress' => 'Confirm your email address.',
    'emailEnterCode'      => 'Confirm your Email',
    'emailConfirmCode'    => 'Enter the 6-digit code we just sent to your email address.',
    'email2FASubject'     => 'Your authentication code',
    'email2FAMailBody'    => 'Your authentication code is:',
    'invalid2FAToken'     => 'The code was incorrect.',
    'need2FA'             => 'You must complete a two-factor verification.',
    'needVerification'    => 'Check your email to complete account activation.',

    // Activate
    'emailActivateTitle'    => 'Email Activation',
    'emailActivateBody'     => 'We just sent an email to you with a code to confirm your email address. Copy that code and paste it below.',
    'emailActivateSubject'  => 'Your activation code',
    'emailActivateMailBody' => 'Please use the code below to activate your account and start using the site.',
    'invalidActivateToken'  => 'The code was incorrect.',
    'needActivate'          => 'You must complete your registration by confirming the code sent to your email address.',

    // Groups
    'unknownGroup' => '{0} is not a valid group.',
    'missingTitle' => 'Groups must have a title.',

    // Permissions
    'unknownPermission' => '{0} is not a valid permission.',
];
