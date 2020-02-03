<?php

class sessions {

    protected $table_name = "sessions";

    function getTable_name() {
        return $this->table_name;
    }

    /**
     * 
     */
    function get_all_session_name_without_ducplicat() {
        $SQL = "SELECT DISTINCT  * FROM " . $this->getTable_name();
        return DB::feachSQLObject(DB::SQLquery($SQL));
    }

    /**
     * 
     * @param type $long
     * @param type $starts
     * @param type $ends
     * @param type $name
     * @param type $notes
     * @param type $completed
     */
    function insert_new_session($long, $starts, $ends, $name, $notes, $completed) {
        $notes = DB::mysql_escape_mimic($notes);
        $name = DB::mysql_escape_mimic($name);
        
        $columns = "(`long`, `starts`, `ends` , `set_dat`, `name`, `notes`, `completed`)";
        $values = "($long, $starts, $ends, NOW(), '$name', '$notes', $completed)";
        $SQL = "INSERT INTO " . $this->getTable_name() . " $columns VALUES $values";
        return DB::EXESQLquery($SQL);
    }

    /**
     * 
     * @return type
     */
    function get_columns() {
        $SQL = "SHOW COLUMNS FROM " . $this->getTable_name();
        return DB::feachSQLObject(DB::SQLquery($SQL));
    }

    /**
     * 
     * @param type $id
     */
    function remove_by_id($id) {
        $SQL = "DELETE FROM " . $this->getTable_name() . " WHERE id='$id' ;";
        return DB::SQLquery($SQL);
    }

    /**
     * 
     */
    function get_all_inserted_rows() {
        $SQL = "SELECT * FROM " . $this->getTable_name() . " ; ";
        return DB::feachSQLObject(DB::SQLquery($SQL));
    }

}
