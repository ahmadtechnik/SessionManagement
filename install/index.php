<?php

include_once './../bootstrap.php';
include_once ROOT . '/objects/DB.php';
$DB_structre = json_decode(file_get_contents(ROOT . "/install/DBimmigration.json"), true);
$installed = $DB_structre["installed"];
$DB = new DB();

if (isset($_GET["put_data"])) {
    /**
     * in this section it will create new structure of the data base
     * in case I am using the phpMyAdmin to create the tables
     * and mage them from there,
     * this step will help to create structure easy and quickly
     */
    $exsits_tables = $DB->feachSQLObject($DB->SQLquery("SHOW TABLES FROM " . $DB_structre["DB_name"]));
    // feach db tables
    $structure = $DB_structre["tables"];

    foreach ($exsits_tables as $value) {

        $single_table = $value["Tables_in_" . $DB_structre["DB_name"]];
        $SQL = "SELECT * FROM information_schema.columns WHERE table_name='$single_table'; ";
        $single_table_schema = $DB->feachSQLObject($DB->SQLquery($SQL));
        $table_hash = substr(md5(json_encode($single_table_schema)), -10);

        if (!isset($structure[$table_hash])) {

            $structure[$table_hash] = [
                "table_name" => $single_table,
                "tables_columns" => [],
            ];

            // feach tables rows
            foreach ($single_table_schema as $key => $value) {
                $columnName = $value["COLUMN_NAME"];
                foreach ($value as $key => $value_) {
                    $value[$key] = strtoupper($value_);
                }
                $columnDataType = $value["DATA_TYPE"];
                $columnType = $value["COLUMN_TYPE"];
                $columnLengh = $value["CHARACTER_MAXIMUM_LENGTH"];
                $columnNullAble = $value["IS_NULLABLE"] === "NO" ? "NOT NULL" : "NULL";
                $columnExtra = $value["EXTRA"];
                $is_primery = $value["COLUMN_KEY"] === "PRI" ? "PRIMARY KEY" : "";

                $single_row_schema = "$columnName $columnType $columnNullAble $columnExtra $is_primery ";
                array_push($structure[$table_hash]["tables_columns"], $single_row_schema);
            }
            echo "TABLE '$single_table' added to structure file.<br>";
        } else {
            echo "TABLE '$single_table' already exist in the structure file.<br>";
            // check if the coulns are the same count in the DBimmigration as in 
            // in DB server
            $exist_t_columns = $structure[$table_hash]["tables_columns"];
            $ex_t_cou_count = count($exist_t_columns);
            if ($ex_t_cou_count < count($single_table_schema)) {
                echo "Table : $single_table hasn't coulmn ";
            }
        }
    }


    $DB_structre["tables"] = $structure;
    $DB_structre["installed"] = false;
    $encoded_data_srtucture = json_encode($DB_structre);

    $myfile = fopen(ROOT . "/install/DBimmigration.json", "w") or die("Unable to open file!");
    fwrite($myfile, $encoded_data_srtucture);
    fclose($myfile);
} else {
// OBJECTS 
    if (!$installed) {
        $tables_to_create = $DB_structre["tables"];
        // foreach all needed tables.
        foreach ($tables_to_create as $hash => $table_row) {
            $table_name = preg_replace('/\s+/', '_', $table_row["table_name"]);
            $table_columns = $table_row["tables_columns"];
            // foreach all needed columns
            $table_count = count($DB::feachSQLObject($DB::SQLquery("SHOW TABLES LIKE '$table_name'")));
            // table not exist
            if ($table_count < 1) {
                $columns = join(", ", $table_columns);
                $SQL = "CREATE TABLE $table_name ( $columns ) ";

                $result = $DB::SQLquery($SQL);
                if ($result) {
                    echo "TABLE : $table_name WAS CREATED ..<br>";
                }
            } else {
                echo "TABLE : $table_name IS EXIST<br>";
            }
        }
        $DB_structre["installed"] = true;
        $myfile = fopen(ROOT . "/install/DBimmigration.json", "w") or die("Unable to open file!");
        $encoded_data_srtucture = json_encode($DB_structre);
        fwrite($myfile, $encoded_data_srtucture);
        fclose($myfile);
    } else {
        echo "THE TABLES WAS INSTALLED ....";
    }
}

/**

{
    "DB_host": "localhost",
    "DB_name": "customerreminder",
    "DB_user": "root",
    "DB_pass": "",
    "installed": false,
    
    "tables": [{
            "table_name": "test",
            "tables_columns": [
                "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY"
            ]
        }]
}


 */