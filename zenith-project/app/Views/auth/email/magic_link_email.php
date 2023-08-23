<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">

<head>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= lang('Auth.magicLinkSubject') ?></title>
</head>

<body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">

    <!-- ... (Rest of the code) ... -->

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="background-color: #f6f6f6; width: 100%;">
      <tr>
        <td>&nbsp;</td>
        <td class="container" style="display: block; margin: 0 auto !important; max-width: 580px; padding: 10px; width: 580px;">

          <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main" style="background: #ffffff; border-radius: 3px; width: 100%;">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <h2 style="color: #000000; font-family: sans-serif; font-weight: 400; line-height: 1.4; margin: 0; margin-bottom: 30px; font-size: 35px; text-align: center; text-transform: capitalize;">비밀번호 재설정 링크</h2>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">아래 버튼을 클릭하여 접속 후 비밀번호를 변경해주시기 바랍니다.</p>
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="box-sizing: border-box; width: 100%;">
                          <tbody>
                            <tr>
                              <td align="left">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                  <tbody>
                                    <tr>
                                      <td> <a href="<?= url_to('verify-magic-link') ?>?token=<?= $token ?>" target="_blank" style="background-color: #ffffff; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; color: #3498db; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize;">비밀번호 재설정</a></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            <!-- ... (Rest of the code) ... -->

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
</body>
</html>