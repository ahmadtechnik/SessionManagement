<?php

include_once './bootstrap.php';
switch ($_POST["order"]) {
    case "set_new_session":set_new_session();
        break;
    case "get_session_table" : get_session_table();
        break;
    case "remove_row_from_db" : remove_row_from_db();
        break;
    case "inseret_finished_session" : inseret_finished_session();
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

/**
 * 
 */
function remove_row_from_db() {
    $id = filter_input(0, "row_id");
    $session_object = new sessions();
    $session_object->remove_by_id($id);
}

/**
 * 
 */
function inseret_finished_session() {
    $selected_long = filter_input(0, "selected_long"); // long in Millesecound
    $selected_date_start = filter_input(0, "selected_date"); // starts date
    $entered_name = filter_input(0, "entered_name"); // entered name
    $entered_note = filter_input(0, "entered_note"); // entered note
    $session_end = filter_input(0, "session_end"); // date time end of session
    $result = [];
    $sesionManage = new sessions();
    $insert_result = $sesionManage->insert_new_session($selected_long, $selected_date_start, $session_end, $entered_name, $entered_note, 0);
    if (is_int($insert_result)) {
        $result["INSERTED"] = $insert_result;
    } else {
        $result["NOT_INSERTED"] = $insert_result;
    }
    
    echo json_encode($result);
}
?>

