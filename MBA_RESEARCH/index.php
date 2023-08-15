<?php
/**
 * MBA RESEARCH
 *
 * @package   MBA RESEARCH
 * @author    Shane Stevens.
 * @copyright Stellenbosch Business School | @2023
 *
 * @wordpress-plugin 
 * Plugin Name: MBA RESEARCH
 * Description: Project starting date, 10 August 2023 01:46am. Why am I awake...
 * Version: 1.0
 * Author: Shane Stevens.
 * License: Free
 */

// _________________________________________
// IMPORT ALL FILES HERE !IMPORTANT HAS TO BE ONTOP OF THE PAGE BEFORE ANY OTHER CODE IS ADDED
// eg.  require_once plugin_dir_path(__FILE__) . './file.php';

// 1CREATE
require_once plugin_dir_path(__FILE__) . './includes/01create/add_topic.php';


// 2READ
require_once plugin_dir_path(__FILE__) . './includes/02read/students.php';
require_once plugin_dir_path(__FILE__) . './includes/02read/supervisor.php';
require_once plugin_dir_path(__FILE__) . './includes/02read/topics.php';


// 3UPDATE
require_once plugin_dir_path(__FILE__) . './includes/03update/select_topic.php';

// 4DELETE
require_once plugin_dir_path(__FILE__) . './includes/04delete/delete.php';



// _________________________________________
// CREATE DATABASE TABLES ON ACTIVATING PLUGIN
function create_table_on_activate()
{
    // connect to WordPress database
    global $wpdb;

    // set table name
    $admin = $wpdb->prefix . 'admin';
    $topics = $wpdb->prefix . 'topics';

    $charset_collate = $wpdb->get_charset_collate(); // Define the character set and collation

    // Define SQL for the admin table
    $sql = "CREATE TABLE $admin (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        username varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        supervisor varchar(255) NOT NULL DEFAULT 'No Supervisor',
        research_field varchar(255) NOT NULL DEFAULT 'Select',
        work_title VARCHAR(255) NOT NULL,
        sub_topics VARCHAR(255) NOT NULL DEFAULT 'no sub_topic',
        students varchar(255) NOT NULL,
        registration_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) $charset_collate;";

    // Define SQL for the topics table
    $sql .= "CREATE TABLE $topics (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        research_field VARCHAR(255) NOT NULL,
        work_title VARCHAR(255) NOT NULL,
        sub_topics VARCHAR(255) NOT NULL DEFAULT 'no sub_topic',
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Execute dbDelta for both tables separately
    $result_admin = dbDelta($sql);

    if (is_wp_error($result_admin) || is_wp_error($result_topics)) {
        echo 'There was an error creating the tables';
        return;
    }
}

register_activation_hook(__FILE__, 'create_table_on_activate');
// Fetch registered users and insert them into the students table




// _________________________________________
// (!IMPORTANT DO NOT TOUCH)  CREATE PAGE FUNCTION  (!IMPORTANT DO NOT TOUCH)
function create_page($title_of_the_page, $content, $parent_id = NULL)
{
	$objPage = get_page_by_title($title_of_the_page, 'OBJECT', 'page');
	if (!empty($objPage)) {
		echo "Page already exists:" . $title_of_the_page . "<br/>";
		return $objPage->ID;
	}
	$page_id = wp_insert_post(
		array(
			'comment_status' => 'close',
			'ping_status' => 'close',
			'post_author' => 1,
			'post_title' => ucwords($title_of_the_page),
			'post_name' => strtolower(str_replace(' ', '-', trim($title_of_the_page))),
			'post_status' => 'publish',
			'post_content' => $content,
			'post_type' => 'page',
			'post_parent' => $parent_id //'id_of_the_parent_page_if_it_available'
		)
	);
	echo "Created page_id=" . $page_id . " for page '" . $title_of_the_page . "'<br/>";
	return $page_id;
}




// _________________________________________
// ACTIVATE PLUGIN
function on_activating_your_plugin()
{
    // _________________________________________
	//  CREATE WP PAGES AUTOMATICALLY ANLONG WITH SHORT CODE TO DISPLAY THE CONTENT
	// eg.  create_page('page-name', '[short-code]');
    // _________________________________________
    
    // 1CREATE
    create_page('add_topic', '[add_topic]');


    // 2READ
    create_page('students_table', '[students_table]');
    create_page('supervisors_table', '[supervisors_table]');
    create_page('topics_table', '[topics_table]');

    // 3UPDATE
    create_page('select_topic', '[select_topic]');

}
register_activation_hook(__FILE__, 'on_activating_your_plugin');




// _________________________________________
// DEACTIVATE PLUGIN
function on_deactivating_your_plugin()
{
    // _________________________________________
	//  DELETE WP PAGES AUTOMATICALLY ANLONG WITH SHORT CODE TO DISPLAY THE CONTENT
	// eg. 	
    // $page_name = get_page_by_path('page_name');
	// wp_delete_post($page_name->ID, true);
    // _________________________________________

    // 1CREATE
    $add_topic = get_page_by_path('add_topic');
	wp_delete_post($add_topic->ID, true);                         // add_topic

    // 2READ
    $students_table = get_page_by_path('students_table');
	wp_delete_post($students_table->ID, true);                    // students
    $supervisors_table = get_page_by_path('supervisors_table');
	wp_delete_post($supervisors_table->ID, true);                 // supervisors
    $topics_table = get_page_by_path('topics_table');
	wp_delete_post($topics_table->ID, true);                      // topics

    // 3UPDATE
    $select_topic = get_page_by_path('select_topic');
	wp_delete_post($select_topic->ID, true);                      // select_topic

}
register_deactivation_hook(__FILE__, 'on_deactivating_your_plugin');

?>