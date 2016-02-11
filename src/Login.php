<?php
namespace Gatekeeper;

class Login
{
    public static function ajax()
    {
        $result = [];

        $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
        if ($nonce && wp_verify_nonce($nonce, 'gatekeeper')) {
            if (is_user_logged_in()) {
                wp_logout();
            }

            $user = \get_user_by('email', $_REQUEST['email']);
            if (!$user) {
                $user = \get_user_by('login', $_REQUEST['email']);
            }

            if (!$user) {
                $result['status'] = 'error';
                $result['message'] = __('Sorry! Given credentials are not correct.', 'gatekeeper');
            } else {
                $creds = [];
                $creds['user_login'] = $user->user_login;
                $creds['user_password'] = $_REQUEST['password'];
                $creds['remember'] = isset($_REQUEST['remember']) ? $_REQUEST['remember'] : false;
                $user = wp_signon($creds, false);
                if (is_wp_error($user)) {
                    $result['status'] = 'error';
                    $result['message'] = __('Sorry! Given credentials are not correct.', 'gatekeeper');
                } else {
                    wp_clear_auth_cookie();
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID);
                    $result['status'] = 'success';
                    $result['message'] = __('Welcome! We will now start redirecting you...', 'gatekeeper');
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = __("Security error! Try reload the page and try again! (nonce=$nonce)", 'gatekeeper');
        }

        echo json_encode($result);

        exit(0);
    }
}
