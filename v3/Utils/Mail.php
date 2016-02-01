<?php

namespace VOP\Utils;

use Mandrill;

class Mail {

    public static function create_template($data, $msg) {

        $type = '';
        if (TRUE == array_key_exists('type', $msg)) {
            $type = $msg['type'];
        }

        $header = '<div style="background:#ccc;color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:0;padding:0;text-align:left;width:100%!important" bgcolor="#EFECE4">
    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top" bgcolor="#cccccc" width="100%">
        <tbody>
            <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                <td style="border-collapse:collapse!important;padding:0;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                    <div style="background:#ccc;margin:0 auto;max-width:580px">
                        <table style="border-collapse:collapse;border-spacing:0;height:100%;padding:0;text-align:left;vertical-align:top;width:100%">
                            <tbody>
                                <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                    <td style="border-collapse:collapse!important;padding:0;text-align:center;vertical-align:top;word-break:break-word" valign="top" align="center">
                                    <center style="width:100%">

                                    <table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:560px">
                                        <tbody>
                                            <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                <td style="border-collapse:collapse!important;padding:0;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                    <table style="border-collapse:collapse;border-spacing:0;display:block;padding:0px;text-align:left;vertical-align:top;width:100%">
                                                        <tbody>
                                                            <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                                <td style="border-collapse:collapse!important;padding:0 0px 0px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                    <table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                                        <tbody>
                                                                            <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                                                <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left"><p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 0 10px;text-align:left" align="left"></p>
                                                                                </td>
                                                                                <td style="border-collapse:collapse!important;padding:0;text-align:left;vertical-align:top;width:0px;word-break:break-word" valign="top" align="left"></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table style="background:#fff;border-collapse:collapse;border-spacing:0;border:1px solid #cfcfcb;margin:0 0 20px;padding:0;text-align:inherit;vertical-align:top;width:560px" bgcolor="#fff">
                                        <tbody>
                                            <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                <td style="border-collapse:collapse!important;padding:0 0 25px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">

                                                <table style="border-collapse:collapse;border-spacing:0;display:block;padding:0px;text-align:left;vertical-align:top;width:100%">
                                                    <tbody>
                                                        <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0 0px 0px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                                    <tbody>
                                                                        <tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                                <a href="#" style="color:#00a19a;text-decoration:none!important;" target="_blank">
                                                                                    <img src="https://www.docup.in/assets/images/dms-logo.png" style="border:none;clear:both;display:block;float:left;max-width:100%;outline:none;padding-left:30px!important;padding-top:40px;text-decoration:none;width:200px;" width="125" align="left">
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>';

        $content = '';

        switch ($type) {
            case 'new_user_registration':
                $subject = 'Welcome to Document Management System';
                $activationUrl = $data['activation_url'];
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hello ' . $data['name'] . ',
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    Thank you for signing up with DMS.
                                                                    To confirm your account Email, <a href="' . $activationUrl . '" style="color:#337ab7;text-decoration:none!important" target="_blank"> click here </a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;
            case 'forgot_password':
                $subject = 'Welcome to Document Management Systems- Password Reset';
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hello User,
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    Welcome to Document Management System,
                                                                    Click the link to reset your password.
                                                                    <a href="#" style="color:#337ab7;text-decoration:none!important" target="_blank">' . $data['reset_url'] . '</a>.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;

            case 'share_doc_invitation':
                $subject = 'Welcome to Document Management Systems- Invitation to share documents';
                $user_firstname = $data['user_firstname'];
                $user_lastname = $data['user_lastname'];
                $current_useremail = $data['current_useremail'];
                $access_url = $data['access_url'];
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hi "' . $user_firstname . ' ' . $user_lastname . ' " ,
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    You have been invited to upload and share documents by ("' . $current_useremail . '"). Please click this link to view the same.
                                                                </p>
                                                                <p><a href="' . $access_url . '"> View Documents</a></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;
            case 'user_permission_invitation':
                $subject = 'Welcome to Document Management Systems- Invitation to share documents';
                $user_firstname = $data['user_firstname'];
                $user_lastname = $data['user_lastname'];
                $current_useremail = $data['current_useremail'];
                $access_url = $data['access_url'];
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hi "' . $user_firstname . ' ' . $user_lastname . ' " ,
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    You have been invited to upload and share documents by ("' . $current_useremail . '"). 
                                                                </p>                                                               
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;
            
            case 'new_user_permission_invitation':
                $subject = 'Welcome to Document Management Systems- Invitation to share documents';
                $user_firstname = $data['user_firstname'];
                $user_lastname = $data['user_lastname'];
                $current_useremail = $data['user_email'];
                $password = $data['password'];
                $access_url = $data['access_url'];
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hi "' . $user_firstname . ' ' . $user_lastname . ' " ,
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    You have been invited to upload and share documents by ("' . $current_useremail . '"). 
                                                                </p>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    Use following login details. 
                                                                </p>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    Username : '. $current_useremail . ' 
                                                                </p>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    Password : '.$password.' 
                                                                </p>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                   
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;

            case 'reset_password':
                $subject = 'Welcome to Document Management Systems- Reset password';
                $user_firstname = $data['user_firstname'];
                $user_lastname = $data['user_lastname'];
                $user_email = $data['user_email'];
                $password = $data['password'];
                //$reset_url = $data['reset_url'];
                $content = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <h5 style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:normal;line-height:1.3;margin:10px 0 10px;padding:0 0 0 30px;text-align:left;word-break:normal" align="left">
                                                                    Hi "' . $user_firstname . ' ' . $user_lastname . ' " ,
                                                                </h5>
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:10px 0 0;padding:0 30px 10px;text-align:left" align="left">
                                                                    You requested to reset the password for your DMS account with the e-mail address ("' . $user_email . ' "). Your new temporary password is . ("' . $password . ' "). Please change this password once you login to your account.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>';
                break;

            default :
                $subject = '';
                $content .= '';
        }

        $footer = '<table style="border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:left;vertical-align:top;width:560px;margin-top: 10px;">
                                                    <tbody><tr style="padding:0;text-align:left;vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important;padding:0px 0px 10px;text-align:left;vertical-align:top;word-break:break-word" valign="top" align="left">
                                                                <p style="color:#3d4542;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;line-height:23px;margin:5px 0 0;padding:0 30px 5px;text-align:left" align="left">Thank you for using DMS!<br>The DMS Team</p>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                    </td>
                                                </tr>
                                            </tbody></table>
                                        </center>
                                        </td>
                                        </tr>
                                        </tbody></table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody></table>
                        </div>';

        $message = $header . $content . $footer;

        $msg['html'] = $message;

        if ('' != $subject) {

            return Mail::send_mail($msg);
        }
    }

    public static function send_mail($msg, $data = array()) {

        global $config, $app;

        $app = \Slim\Slim::getInstance();
        $mode = $app->getMode();

        $mandrill = new Mandrill($config[$mode]['mandrill_apikey']);

        if (!is_array($msg)) {
            parse_str($msg, $msg);
        }

        foreach (array('text', 'html') as $t) {

            if (!empty($msg[$t]) && strpos(trim($msg[$t]), 'slim:') === 0) {
                $view = new \Slim\View();
                $view->appendData($data);
                $view->setTemplatesDirectory($app->config('templates.path'));
                $msg[$t] = $view->fetch('email/' . substr(trim($msg[$t]), 5));
            }
        }

//        if (isset($msg['from'])) {
//            $msg['from_email'] = $msg['from'];
//            unset($msg['from']);
//        }

        if (!is_array($msg['to'])) {
            $msg['to'] = array(
                array('email' => $msg['to'])
            );
        }

        return $mandrill->call('/messages/send', array('message' => $msg));
    }

}

?>