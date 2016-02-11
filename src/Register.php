<?php
namespace Gatekeeper;

class Register
{
    public static function ajax()
    {
        $result = [];

        $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
        if ($nonce && wp_verify_nonce($nonce, 'gatekeeper')) {
            if (is_user_logged_in()) {
                wp_logout();
            }

            $email = esc_attr($_REQUEST['email']);

            if (!is_email($email)) {
                $result['status'] = 'error';
                $result['message'] = __('Email is not acceptable!', 'gatekeeper');
                echo json_encode($result);
                exit(0);
            }

            $display_name = esc_attr($_REQUEST['display_name']);

            if (!$display_name ||
                strlen($display_name) < 3 ||
                !strpos($display_name, ' ')) {
                $result['status'] = 'error';
                $result['message'] = __('Please ensure first name and last name!', 'gatekeeper');
                echo json_encode($result);
                exit(0);
            }

            $password = esc_attr($_REQUEST['password']);
            if (strlen($password) < 6) {
                $result['status'] = 'error';
                $result['message'] = __('Password needs to be at least 6 letters or digits long.', 'gatekeeper');
                echo json_encode($result);
                exit(0);
            }

            $user = \get_user_by('email', $_REQUEST['email']);
            if (!$user) {
                $user = \get_user_by('login', $_REQUEST['email']);
            }

            if ($user) {
                $result['status'] = 'error';
                $result['message'] = __('Email address already exists. Use the "Forgot password" to get a login link!', 'gatekeeper');
                echo json_encode($result);
                exit(0);
            } else {
                $user_id = wp_create_user($email, $password, $email);

                $name = explode(' ', $display_name);
                $user_data = [
                    'ID'           => $user_id,
                    'display_name' => $display_name,
                    'first_name'   => isset($name[0]) ? $name[0] : $display_name,
                    'last_name'    => isset($name[1]) ? $name[1] : '',
                ];
                wp_update_user($user_data);

                $meta = $_REQUEST['meta'];
                if(is_array($meta) && $meta) {
                    foreach ($meta as $key => $m) {
                        update_user_meta($user_id, esc_attr($key), esc_attr($m));
                    }
                                    
                }

                if ($user_id) {
                    $message = apply_filters('gatekeeper/messages/new', __("<h2>Welcome!</h2>\n<br/>You can now login to the site with the credentials given."));
                    $result['status'] = 'success';
                    $result['message'] = $message;
                    $mail = mandriller(get_bloginfo('title'), $message, $email, $display_name);
                } else {
                    $result['status'] = 'error';
                    $result['message'] = __('Not possible to create an account with the data given.', 'gatekeeper');
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
