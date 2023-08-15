<?php
function select_topic(){
    global $wpdb;
    $current_user = wp_get_current_user();

    if (!$wpdb) {
        $wpdb->show_errors();
    }

    // Fetch data
    $table_name = $wpdb->prefix . 'topics';
    $data = $wpdb->get_results("SELECT * FROM $table_name");

    // Process form submission
    if (isset($_POST['submit'])) {
        // Sanitize input data
        $research_field = sanitize_text_field($_POST['research_field']);
        $work_title = sanitize_text_field($_POST['work_title']);
        $sub_topics = sanitize_text_field($_POST['sub_topics']);

        // Update or insert data for current user
        $admin_table = $wpdb->prefix . 'admin';
        $existing_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $admin_table WHERE user_id = %d", $current_user->ID));

        if ($existing_data) {
            // Update existing data
            $update_data = array(
                'research_field' => $research_field,
                'work_title' => $work_title,
                'sub_topics' => $sub_topics,
            );

            $update_format = array('%s', '%s', '%s');
            $update_result = $wpdb->update($admin_table, $update_data, array('user_id' => $current_user->ID), $update_format);

            if ($update_result === false) {
                wp_die($wpdb->last_error);
            }
        } else {
            // Insert new data
            $insert_data = array(
                'user_id' => $current_user->ID,
                'research_field' => $research_field,
                'work_title' => $work_title,
                'sub_topics' => $sub_topics,
            );

            $insert_format = array('%d', '%s', '%s', '%s');
            $insert_result = $wpdb->insert($admin_table, $insert_data, $insert_format);

            if ($insert_result === false) {
                wp_die($wpdb->last_error);
            }
        }

        // Redirect after successful update or insertion
        $redirect_url = site_url('/students_table/');
        wp_redirect($redirect_url);
        exit;
    }

    $output = '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style scoped>
    </style>
    ';

    $uniqueResearchFields = array(); // Array to store unique research fields

    if($data){
        $output .= '
    <div class="container">
        <form id="topicForm" method="post" action="">
            <div class="row">
                <div class="col-md-4">
                    <select name="research_field" id="researchFieldSelect">
                        <option value="">Select Research Field</option>';
        foreach ($data as $i) {   
            if (!in_array($i->research_field, $uniqueResearchFields)) {
                $uniqueResearchFields[] = $i->research_field; // Add to unique research fields array
                $output .= '<option value="' . esc_attr($i->research_field) . '">' . esc_html($i->research_field) . '</option>';
            }
        }
        $output .= '
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="work_title" id="work_title_select" style="display: none;">
                        <option value="">Select Work Title</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="sub_topics" id="sub_topic_select" style="display: none;">
                        <option value="">Select Sub Topic</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <button type="submit" name="submit" id="submitBtn" class="btn btn-primary" style="display: none;">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let form = document.getElementById("topicForm");
        let work_title_select = document.getElementById("work_title_select");
        let sub_topic_select = document.getElementById("sub_topic_select");
        let researchFieldSelect = document.getElementById("researchFieldSelect");
        let submitBtn = document.getElementById("submitBtn");
        
        researchFieldSelect.addEventListener("change", function() {
            let selectedResearchField = researchFieldSelect.value;
            work_title_select.innerHTML = "<option value=\'\'>Select Work Title</option>";
            sub_topic_select.innerHTML = "<option value=\'\'>Select Sub Topic</option>";
            
            if (selectedResearchField !== "") {
                let workTopics = ' . json_encode($data, JSON_HEX_TAG) . ';
                let workTopicOptions = workTopics.filter(topic => topic.research_field === selectedResearchField);
                
                workTopicOptions.forEach(topic => {
                    let option = document.createElement("option");
                    option.value = topic.work_title;
                    option.text = topic.work_title;
                    work_title_select.appendChild(option);
                });
                
                work_title_select.style.display = "block"; // Display work_title select
                work_title_select.dispatchEvent(new Event("change")); // Trigger work_title change event
            } else {
                work_title_select.style.display = "none"; // Hide work_title select if no research field is selected
                sub_topic_select.style.display = "none"; // Hide sub_topic select as well
                submitBtn.style.display = "none"; // Hide submit button
            }
        });

        work_title_select.addEventListener("change", function() {
            let selectedWorkTitle = work_title_select.value;
            sub_topic_select.innerHTML = "<option value=\'\'>Select Sub Topic</option>";
            
            if (selectedWorkTitle !== "") {
                let subTopics = ' . json_encode($data, JSON_HEX_TAG) . ';
                let subTopicOptions = subTopics.filter(topic => topic.work_title === selectedWorkTitle);
                
                if (subTopicOptions.length > 0) {
                    sub_topic_select.style.display = "block"; // Show sub_topic select
                    subTopicOptions.forEach(topic => {
                        let option = document.createElement("option");
                        option.value = topic.sub_topics;
                        option.text = topic.sub_topics;
                        sub_topic_select.appendChild(option);
                    });
                    submitBtn.style.display = "block"; // Show submit button
                } else {
                    sub_topic_select.style.display = "none"; // Hide sub_topic select
                    submitBtn.style.display = "none"; // Hide submit button
                }
            } else {
                sub_topic_select.style.display = "none"; // Hide sub_topic select if no work title is selected
                submitBtn.style.display = "none"; // Hide submit button
            }
        });

        // Trigger the initial change event to populate the work_title and sub_topic selects
        researchFieldSelect.dispatchEvent(new Event("change"));
    });
    </script>
    ';
    } else {
        $output .= '<p>There are no topics available</p>';
    }

    return $output;
}

add_shortcode('select_topic', 'select_topic');

?>
