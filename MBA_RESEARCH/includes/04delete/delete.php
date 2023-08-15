<?php
function delete_student($id)
{
    global $wpdb;
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    $table_name = $wpdb->prefix . 'students';
    $wpdb->delete($table_name, array('id' => $id));
}

function delete_supervisor($id)
{
    global $wpdb;
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    $table_name = $wpdb->prefix . 'users';
    $wpdb->delete($table_name, array('ID' => $id));
}

// Delete student action
if (isset($_GET['action']) && $_GET['action'] == 'delete(student)' && isset($_GET['id'])) {
    delete_student($_GET['id']);
    header('location:' . site_url() . '/students_table/');
    exit;
}

// Delete supervisor action
if (isset($_GET['action']) && $_GET['action'] == 'delete(supervisor)' && isset($_GET['id'])) {
    delete_supervisor($_GET['id']);
    header('location:' . site_url() . '/supervisors_table/');
    exit;
}

?>