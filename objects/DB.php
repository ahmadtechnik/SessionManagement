<?php

//
class DB {

    protected $servername = "";
    protected $dbname = "";
    protected $username = "";
    protected $password = "";
    //
    protected $enc_type = "AES-128-ECB";
    protected $enc_pass = "Aa123654";

    function __construct() {
        $DB_structre = json_decode(file_get_contents(ROOT . "/install/DBimmigration.json"));
        $this->servername = $DB_structre->DB_host;
        $this->dbname = $DB_structre->DB_name;
        $this->username = $DB_structre->DB_user;
        $this->password = $DB_structre->DB_pass;
    }

    function getEnc_type() {
        return $this->enc_type;
    }

    function getEnc_pass() {
        return $this->enc_pass;
    }

    public function getServername() {
        return $this->servername;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDbname() {
        return $this->dbname;
    }

    /**
     * @example  Send Query to  MY sql and get Responce
     * * */
    public static function SQLquery($SQL) {
        $mainClass = new DB();
        $mysql_connent = new mysqli($mainClass->getServername(), $mainClass->getUsername(), $mainClass->getPassword(), $mainClass->getDbname());
        $result = $mysql_connent->query($SQL) or die($mysql_connent->error);
        $mysql_connent->close();
        // mysqli_close($mysql_connent);
        return $result;
    }

    /**
     * @fetch mysqlArray
     * 
     * 
     * * */
    public static function feachSQLObject($object) {
        $json_Object = array();
        while ($row = $object->fetch_assoc()) {
            $json_Object[] = $row;
        }

        return $json_Object;
    }

    /**
     * @example  Send Query to  MY sql and get Responce
     * * */
    public static function EXESQLquery($SQL) {
        $mainClass = new DB();
        $mysql_connent = new mysqli($mainClass->getServername(), $mainClass->getUsername(), $mainClass->getPassword(), $mainClass->getDbname());
        $LASTID = "";
        // Check connection
        if ($mysql_connent->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($mysql_connent->query($SQL) === TRUE) {
            $LASTID = $mysql_connent->insert_id;
            $mysql_connent->close();
        } else {
            return false;
        }
        return $LASTID;
    }

    /** CONVERT ARRAY TO COLUMN NAMES * */
    public static function ArrayToColumnNames($array) {
        $columnNames_ = "";
        foreach ($array as $key => $value) {
            $columnNames_ .= $value . ",";
        }
        return "( " . substr($columnNames_, 0, -1) . " )";
    }

    /** GET VALUES OF ARRAY TO MAKE INSERT QUERY * */
    public static function createValueQuery($array) {
        $values = "";
        foreach ($array as $key => $value) {
            $values .= "'" . DB::mysql_escape_mimic($value) . "',";
        }
        return "(" . substr($values, 0, -1) . ")";
    }

    /** CREATE TABLE IF NOT EXIST, AND CHECK IF TABLE EXIST 
     * @param ARRAY $columns [col_Name , type_size , Null Or Not , more Attributes ] 
     * * */
    public static function checkIfExistOrCreate($tableName, $columns) {
        // create coumns Data of this Table
        $tableContains = "";
        foreach ($columns as $columnName => $columnsAttrs) {
            $tableContains .= $columnName . " " . $columnsAttrs["type_size"] . " " . $columnsAttrs["NULL_NOT"] . " " . $columnsAttrs["more"] . ", ";
        }

        $SQL = "CREATE TABLE IF NOT EXISTS `$tableName` ( $tableContains );";
        echo $SQL;
    }

    /*
     * get all tables Names from DB   
     */

    public static function getAllTablesNames() {
        $SQL = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG='" . $this->getDbname() . "' ; ";
        return DB::feachSQLObject(DB::SQLquery($SQL));
    }

    /** write on JSON file  * */
    public static function write_on_json_file($file_name, $new_value_to_add) {
        $get_origin_content = json_decode(file_get_contents($file_name), true);
        if (isset($get_origin_content[key($new_value_to_add)]))
            return false;
        $get_origin_content[key($new_value_to_add)] = $new_value_to_add[key($new_value_to_add)];

        $myfile = fopen($file_name, "w") or die("Unable to open file!");
        $is_written = fwrite($myfile, json_encode($get_origin_content, JSON_PRETTY_PRINT));
        fclose($myfile);
        if ($is_written === false) {
            return false;
        } else {
            return true;
        }
    }

    /** remove object from json file * */
    public static function remove_object_from_json($file_name, $object_name_to_remove) {
        $get_origin_content = json_decode(file_get_contents($file_name), true);
        if (isset($get_origin_content[$object_name_to_remove])) {
            /** step to remove the object * */
            unset($get_origin_content[$object_name_to_remove]);

            $myfile = fopen($file_name, "w") or die("Unable to open file!");
            $is_written = fwrite($myfile, json_encode($get_origin_content, JSON_PRETTY_PRINT));
            fclose($myfile);

            if ($is_written === false) {
                return false;
            } else {
                return true;
            }

            return true;
        } else {
            return false;
        }
    }

    /** echo full Array in one line with key and value  * */
    public static function featchArrayToOnLine($array) {
        $final = "";
        foreach ($array as $key => $value) {
            $final .= strtoupper($key) . " : " . $value . " , ";
        }
        return $final;
    }

    public static function mysql_escape_mimic($inp) {
        if (is_array($inp))
            return array_map(__METHOD__, $inp);
        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }

    /** @param object $object add new object to get * */
    public static function ObjectToColumnNames($object) {
        $columnNames_ = "";
        foreach ($object as $key => $value) {
            $columnNames_ .= "" . $key . "" . ", ";
        }
        return "( " . substr($columnNames_, 0, -2) . " )";
    }

    /**
     * 
     * @param type $string
     * @return boolean
     */
    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 
     * @param type $string
     * @return type
     */
    public static function encrypt_data($string = "") {
        if ($string !== "") {
            $db = new DB();
            return openssl_encrypt($string, $db->getEnc_type(), $db->getEnc_pass());
        }
    }

    /**
     * 
     * @param type $string
     * @return type
     */
    public static function decrypt_data($string = "") {
        if ($string !== "") {
            $db = new DB();
            return openssl_decrypt($string, $db->getEnc_type(), $db->getEnc_pass());
        }
    }

}
