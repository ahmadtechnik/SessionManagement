<?php
include_once './bootstrap.php';
$sessions_object = new sessions();
$get = $sessions_object->get_all_session_name_without_ducplicat();
$autoComplete = [];
foreach ($get as $key => $row) {
    $autoComplete[$row["name"]] = ucfirst($row["name"]);
}
?>
<div class="h-100">
    <div class="form-group">
        <label>Long</label>
        <input data-role="timepicker" id="selected_long" data-value="00:00" data-seconds="false" />
    </div>
    <div class="form-group">
        <label>Starts</label>
        <input data-role="datepicker" id="selected_date" />
        <input data-role="timepicker" id="selected_time" data-seconds="false" />
    </div>
    <div class="form-group">
        <label>Name</label>
        <input data-role="input" id="entered_name"  data-autocomplete="<?php if ($autoComplete) echo join(", ", $autoComplete) ?>" />
    </div>
    <div class="form-group">
        <label>Notes</label>
        <textarea data-role="textarea" id="entered_note" ></textarea>
    </div>
</div>
