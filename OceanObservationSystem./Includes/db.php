<?php

class OceanDB{
    
// single instance of self shared among all instances
    private static $instance = null;
// db connection config vars
    private $user = "sjpartri";
    private $pass = "letmein22";
    private $dbHost = "gwynne.cs.ualberta.ca:1521:crs";
    private $con = null;
    
    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    // private constructor
    private function __construct() {
        $this->con = oci_connect($this->user, $this->pass);
        if (!$this->con) {
            $m = oci_error();
            exit('Connect Error ' . $m['message']);
        }
    }
    
    public function is_valid_login($user, $password){
        $query = "SELECT * FROM SJPARTRI.USERS WHERE user_name = '$user' AND password = '$password'";
        //Prepare sql using conn and returns the statement identifier
        $stid = oci_parse($this->con, $query);
        //Execute a statement returned from oci_parse()
        oci_execute($stid);
        
        $count = 0;
        while (($row = oci_fetch_array($stid, OCI_ASSOC)) != false) {
            $count++;
         }
         
         if ($count > 0) 
             return true;
         else
             return false;
             
    }
    
    public function get_person_id_by_name($name) {
        $query = "SELECT PERSON_ID FROM SJPARTRI.USERS WHERE USER_NAME = 'jsmith'";
        $stid = oci_parse($this->con, $query);
        //oci_bind_by_name($stid, ':user_bv', $name);
        oci_execute($stid);

//Because user is a unique value I only expect one row
        $row = oci_fetch_array($stid, OCI_ASSOC);
        if ($row)
            return $row['PERSON_ID'];
        else
            return null;
    }
    
    public function get_keyword_search_results($keyword){
        $query = "SELECT  * FROM SJPARTRI.SENSORS WHERE (SENSOR_ID LIKE '%".$_GET["txtKeyword"]."%'
        or LOCATION LIKE '%".$_GET["txtKeyword"]."%' or SENSOR_TYPE LIKE '%".$_GET["txtKeyword"]."%' "
        . "or DESCRIPTION LIKE '%".$_GET["txtKeyword"]."%')  ";
        $objParse = oci_parse ($this->con, $query);
        oci_execute ($objParse);
        return $objParse;
    }
    
}
?>

