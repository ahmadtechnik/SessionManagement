<?php

define("ROOT", __DIR__);

/**
 * 
 */
define("TERMS", [
    "head_id" => "",
    "rowindex" => "",
    "customerNumber" => "Kundennummer",
    "deliveryDate" => "Laufzeit",
    "contractPeriod" => "Lieferdatum",
    "customerEmail" => "e-mail",
    "notes" => "Hinweise",
    "alert" => "Beendete",
    "warning" => "Bald",
    "careful" => "Vorsichtig",
    "good" => "In Ordnung"
]);

/**
 * 
 * @param type $needed
 * @return boolean | STRING
 */
function __term($needed) {

    $terms = TERMS;
    $needed = strtolower($needed);
    $terms = array_change_key_case($terms, CASE_LOWER);

    if (isset($terms[$needed])) {
        return $terms[$needed];
    } else {
        return false;
    }
}

include_once ROOT . '/objects/DB.php';
include_once ROOT . '/objects/sessions.php';
$DB = new DB();

/**
 * 
 * @param type $file
 * @param type $delimiter
 * @return type
 */
function csvtojson($file, $delimiter) {
    if (($handle = fopen($file, "r")) === false) {
        die("can't open the file.");
    }

    $csv_headers = fgetcsv($handle, 4000, $delimiter);
    $csv_json = array();

    while ($row = fgetcsv($handle, 4000, $delimiter)) {

        foreach ($row as $key => $value) {
            $row[$key] = DB::mysql_escape_mimic($value);
        }
        $csv_json[] = array_combine($csv_headers, $row);
    }

    fclose($handle);
    return $csv_json;
}

function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function formatMilliseconds($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%02u:%02u:%02u ';
    $time = sprintf($format, $hours, $minutes, $seconds);
    return rtrim($time, '0');
}