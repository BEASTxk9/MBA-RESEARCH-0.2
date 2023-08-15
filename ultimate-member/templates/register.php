<?php
/**
 * Template for the register page
 *
 * This template can be overridden by copying it to yourtheme/ultimate-member/templates/register.php
 *
 * Page: "Register"
 *
 * @version 2.6.1
 *
 * @var string $mode
 * @var int    $form_id
 * @var array  $args
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    um_reset_user();
}

$registration_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_text_field($_POST['user_login']);
    $email = sanitize_text_field($_POST['user_email']);
    $password = sanitize_text_field($_POST['user_pass']);

    $user_id = wp_insert_user(array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass' => $password,
        'role' => 'subscriber', // Set the default role
    ));

    if (!is_wp_error($user_id)) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'admin'; // Replace 'prefix' with your actual table prefix
        $data = array(
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'registration_date' => current_time('mysql'),
        );

        $wpdb->insert($table_name, $data);

        // Get the user role
        $user_data = get_userdata($user_id);
        $user_role = $user_data->roles[0]; // Get the first user role

        // Log in the user automatically after successful registration
        $user_login = $user_data->user_login;
        $user = wp_signon(array('user_login' => $user_login, 'user_password' => $password, 'remember' => true), false);
        if (!is_wp_error($user)) {
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user_login, $user);
        }

        // Redirect after successful registration and login
        wp_redirect(home_url('/account')); // Change '/account' to the actual URL you want to redirect to
        exit;
    } else {
        $registration_error = $user_id->get_error_message();
    }
}
?>

<div class="um <?php echo esc_attr($this->get_class($mode)); ?> um-<?php echo esc_attr($form_id); ?>">

    <div class="um-form" data-mode="<?php echo esc_attr($mode) ?>">

        <form method="post" action="">

            <!-- Your form fields here -->

            <input type="text" name="user_login" placeholder="Username">
            <input type="email" name="user_email" placeholder="Email">
            <input type="password" name="user_pass" placeholder="Password">

            <?php do_action("um_before_form", $args); ?>

            <!-- Registration Button -->
            <button type="submit" name="um_submit_form">Register</button>

            <!-- Rest of your form code -->

            <?php do_action('um_after_form', $args); ?>

        </form>

    </div>

</div>
