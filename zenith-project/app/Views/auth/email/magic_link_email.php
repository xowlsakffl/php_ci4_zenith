<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">

<head>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= lang('Auth.magicLinkSubject') ?></title>
</head>

<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="background-color: #000; color: #fff; border: 1px solid pink;">
        <tbody>
            <tr>
                <td style="line-height: 24px; font-size: 16px; border-radius: 6px; width: 100%; height: 20px; margin: 0; background-color: #0d6efd; text-align="center">
                    <a href="<?= url_to('verify-magic-link') ?>?token=<?= $token ?>" style=""><?= lang('Auth.login') ?></a>
                </td>
            </tr>
        </tbody>
    </table>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0; text-align: center;">
                    &#160;
                </td>
            </tr>
        </tbody>
    </table>
    <b><?= lang('Auth.emailInfo') ?></b>
    <p><?= lang('Auth.emailIpAddress') ?> <?= esc($ipAddress) ?></p>
    <p><?= lang('Auth.emailDevice') ?> <?= esc($userAgent) ?></p>
    <p><?= lang('Auth.emailDate') ?> <?= esc($date) ?></p>
</body>

</html>
