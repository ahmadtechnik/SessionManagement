<?php

include_once './bootstrap.php';
switch ($_POST["order"]) {
    case "set_new_session":set_new_session();
        break;
    case "get_session_table" : get_session_table();
        break;
    case "remove_row_from_db" : remove_row_from_db();
        break;
}

/**
 * 
 */
function set_new_session() {
    $session_object = new sessions();

    $session_name = filter_input(0, "session_name");
    $session_notes = filter_input(0, "session_notes");
    $long_in_millsc = filter_input(0, "long_in_millsc");
    $time_starts = filter_input(0, "time_starts");
    $time_end = filter_input(0, "time_end");
    echo date("Y/m/d H:i", $time_starts) . "  #  " . $time_end;

    $query = $session_object->insert_new_session($long_in_millsc, $time_starts, $time_end, $session_name, $session_notes, 0);
    if ($query) {
       
    }
     print_r($query);
}

/**
 * 
 */
function get_session_table() {
    include_once './sessions_table.php';
}

function remove_row_from_db() {
    $id = filter_input(0, "row_id");
    $session_object = new sessions();
    $session_object->remove_by_id($id);
}
?>

