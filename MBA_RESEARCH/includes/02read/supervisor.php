<?php
function get_contributer_role($user_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'usermeta'; // Replace 'prefix' with your actual table prefix

    $query = $wpdb->prepare(
        "SELECT meta_value FROM $table_name WHERE user_id = %d AND meta_key = %s",
        $user_id,
        'wp_capabilities'
    );

    $user_role = $wpdb->get_var($query);

    if ($user_role) {
        $capabilities = maybe_unserialize($user_role);
        if (is_array($capabilities)) {
            $roles = array_keys($capabilities);
            return $roles[0]; // Return the primary role
        }
    }

    return '';
}

function display_supervisors_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'admin'; // Replace 'prefix' with your actual table prefix

    $role_to_display = 'contributor'; // Change this to the role you want to display

    $query = $wpdb->prepare(
        "SELECT * FROM $table_name
        WHERE user_id IN (SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = 'wp_capabilities' AND meta_value LIKE %s)",
        '%' . $wpdb->esc_like('"' . $role_to_display . '"') . '%'
    );

    $supervisors = $wpdb->get_results($query);

// external links
$output = '
<!-- bootstrap css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
';
?>
<!-- custom css -->
<style scoped>

</style>

<?php

    if ($supervisors) {
        $output .= '
        <table class="table-bordered table-responsive">
        <thead>
        <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Registration Date</th>
        <th>Role</th>
        <th>Students</th>
        </tr>
        </thead>
        <tbody>
        ';

        foreach ($supervisors as $supervisor) {
            $user_role = get_contributer_role($supervisor->user_id);

            $output .= '<tr>';
            $output .= '<td>' . esc_html($supervisor->id) . '</td>';
            $output .= '<td>' . esc_html($supervisor->user_id) . '</td>';
            $output .= '<td>' . esc_html($supervisor->username) . '</td>';
            $output .= '<td>' . esc_html($supervisor->email) . '</td>';
            $output .= '<td>' . esc_html($supervisor->registration_date) . '</td>';
            $output .= '<td>' . esc_html($user_role) . '</td>';
            $output .= '<td>' . esc_html($supervisor->students) . '</td>';
            $output .= '
            <td>
            <a href="' . admin_url('./4delete/delete.php?page=wp_supervisors&action=delete(supervisor)&id=' . $supervisor->id) . '" class="button-delete btn">Delete</a>
            </td>
            ';
            $output .= '</tr>';
        }

        $output .= '
        </tbody>
        </table>
        ';
    } else {
        $output = 'No supervisors found.';
    }

    return $output;
}
add_shortcode('supervisors_table', 'display_supervisors_table');
?>
