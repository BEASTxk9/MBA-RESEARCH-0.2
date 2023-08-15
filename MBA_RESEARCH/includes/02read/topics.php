<?php
// shortcode function
function display_topics_table(){
// ____________________________________________________________________________
// connect to database.
  global $wpdb;
// check connection
  if (!$wpdb) {
    $wpdb->show_errors();
  }

  // ____________________________________________________________________________
  // Set table name that is being called
  $table_name = $wpdb->prefix . 'topics';

  // SQL query to retrieve data from the table
  $data = $wpdb->get_results("SELECT * FROM $table_name");

  // ____________________________________________________________________________
// HTML DISPLAY

// external links
$output = '
<!-- bootstrap css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
';

?>
<style scoped>

</style>
<?php

    $output.= '
    <a href="' . site_url() . '/add_topic/" class="btn button-add my-3">Add A Topic</a>
    ';
 if($data){

    $output .= '
    <table class="table table-striped table-bordered table-responsive table-hover">
    <thead>
    <tr>
    <th>Topic</th>
    <th>Research Field</th>
    <th>Work Title</th>
    <th>Sub Topics</th>
    </tr>
    </thead>
    <tbody>
    ';

   // for each data item in the table
  foreach ($data as $i) {
    $output .= '<tr>';
    $output .= '<td>' . $i->id . '</td>';
    $output .= '<td>' . $i->research_field . '</td>';
    $output .= '<td>' . $i->work_title . '</td>';
    $output .= '<td>' . $i->sub_topics . '</td>';
    $output .= '</tr>';
  }

  $output .='
  </tbody>
  </table>
  ';
  
 } else{
    $output .= '
    <p>There are no topics avalible</p>
    ';
 }


  // ____________________________________________________________________________
  // Return the table html
  return $output;
}


// Register the shortcode
add_shortcode('topics_table', 'display_topics_table');
?>