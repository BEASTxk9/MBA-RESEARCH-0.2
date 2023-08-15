<?php
function get_user_role($user_id) {
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

function display_students_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'admin'; // Replace 'prefix' with your actual table prefix

    $role_to_display = 'subscriber'; // Change this to the role you want to display

    $query = $wpdb->prepare(
        "SELECT * FROM $table_name
        WHERE user_id IN (SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = 'wp_capabilities' AND meta_value LIKE %s)",
        '%' . $wpdb->esc_like('"' . $role_to_display . '"') . '%'
    );

    $students = $wpdb->get_results($query);


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

    if ($students) {
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
        <th>supervisor</th>
        <th>Research Field</th>
        <th>Work Title</th>
        <th>Sub Topics</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>
        ';

        foreach ($students as $student) {
            $user_role = get_user_role($student->user_id);

            $output .= '<tr>';
            $output .= '<td>' . esc_html($student->id) . '</td>';
            $output .= '<td>' . esc_html($student->user_id) . '</td>';
            $output .= '<td>' . esc_html($student->username) . '</td>';
            $output .= '<td>' . esc_html($student->email) . '</td>';
            $output .= '<td>' . esc_html($student->registration_date) . '</td>';
            $output .= '<td>' . esc_html($user_role) . '</td>';
            $output .= '<td>' . esc_html($student->supervisor) . '</td>';
            $output .= '<td>' . esc_html($student->research_field) . '</td>';
            $output .= '<td>' . esc_html($student->work_title) . '</td>';
            $output .= '<td>' . esc_html($student->sub_topics) . '</td>';
            $output .= '
            <td>
                <a href="' . admin_url('./04delete/delete.php?page=wp_students&action=delete(student)&id=' . $student->id) . '" class="button-delete btn">Delete</a>
            </td>
            ';
            $output .= '</tr>';
        }

        $output .= '
        </tbody>
        </table>
        ';
    } else {
        $output = 'No students found.';
    }

    return $output;
}
add_shortcode('students_table', 'display_students_table');
?>
