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
    
    //checks whether the user and password exist in the database
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

        $query = "SELECT PERSON_ID FROM SJPARTRI.USERS WHERE USER_NAME = '$name'";
        $stid = oci_parse($this->con, $query);
        oci_execute($stid);
        return $stid;

    }
    
    public function sensor_table_results($user){
        $person_id = OceanDB::getInstance()->get_person_id_by_name($user);
        $person_id_parse = oci_fetch_array($person_id, OCI_BOTH);
        $person_id = $person_id_parse["PERSON_ID"];
        $query = "SELECT SB.SENSOR_ID FROM SJPARTRI.SUBSCRIPTIONS SB WHERE SB.PERSON_ID = '$person_id'";
        $stid = oci_parse($this->con, $query);
        oci_execute($stid);
        return $stid;
    }
    
    
    
    public function get_keyword_search_results($keyword){
      
        $keys = explode(" ",$_POST["txtKeyword"]);
        $sql = "SELECT  * FROM SJPARTRI.SENSORS WHERE lower(DESCRIPTION) LIKE lower('%".$_POST["txtKeyword"]."%') ";
        
        foreach($keys as $k){
            $sql.= "or lower(DESCRIPTION) LIKE lower('%$k%')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    public function full_search_results($sensorID,$keyword, $sensorType, $location, $startDate, $endDate){
        
        $keyword_keys = explode(" ",$keyword);
        $sensorType_keys = explode(" ",$sensorType);
        $location_keys = explode(" ",$location);
        
        $sql = "Select S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE,AR.RECORDED_DATA,I.THUMBNAIL
                From sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.AUDIO_RECORDINGS AR, sjpartri.IMAGES I, sjpartri.SUBSCRIPTIONS SB
                WHERE S.SENSOR_ID=S1.SENSOR_ID
                AND S.SENSOR_ID=AR.SENSOR_ID
                AND S.SENSOR_ID=I.SENSOR_ID
                AND S.SENSOR_ID = $sensorID
                AND lower(S.DESCRIPTION) LIKE lower('%$keyword%')
                AND lower(S.SENSOR_TYPE) LIKE lower('%$sensorType%')
                AND lower(S.LOCATION) LIKE lower('$location')";
        
        foreach($keyword_keys as $k){
            $sql.= "or lower(S.DESCRIPTION) LIKE lower('%$k%')";
        }
        foreach($sensorType_keys as $k){
            $sql.= "or lower(S.SENSOR_TYPE) LIKE lower('%$k%')";
        }
        foreach($location_keys as $k){
            $sql.= "or lower(S.LOCATION) LIKE lower('$k')";
        }
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
           
    }
    
    public function get_sensor_type_search_results($sensorType){

        $keys = explode(" ",$_POST["txtSensorType"]);
        $sql = "SELECT * FROM SJPARTRI.SENSORS WHERE lower(SENSOR_TYPE) LIKE lower('%".$_POST["txtSensorType"]."%')";
        foreach($keys as $k){
            $sql.= "or lower(SENSOR_TYPE) LIKE lower('%$k%')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
        
    }
    
    public function get_location_results($location){
        
        $keys = explode(" ",$_POST["txtLocation"]);
        $sql = "SELECT * FROM SJPARTRI.SENSORS WHERE lower(LOCATION) LIKE lower('".$_POST["txtLocation"]."')";
        foreach($keys as $k){
            $sql.= "or lower(LOCATION) LIKE lower('$k')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    //gets the users role to allow the correct activity
    public function get_user_role($user){
        $query = "SELECT * FROM SJPARTRI.USERS WHERE user_name = '$user'";
        //Prepare sql using conn and returns the statement identifier
        $results = oci_parse($this->con, $query);
        //Execute a statement returned from oci_parse()
        oci_execute($results);
        while (($row = oci_fetch_array($results, OCI_ASSOC)) != false) {
            $role = $row["ROLE"];
         }
        return $role;          
    }
    
    //get all sensors
    public function get_sensors(){
        $query = "SELECT  * FROM SJPARTRI.SENSORS ORDER BY SENSOR_ID";
        $sensors = oci_parse ($this->con, $query);
        oci_execute ($sensors);
        return $sensors;
    }
    
    //get sensor full name for character abbreviation
    public function get_sensors_type($type){
        if ($type == "a")
            return "a (audio recorder)";
        else if ($type == "i")
            return "i (image recorder)";
        else if($type == "s")
            return "s (scalar value recorder)";
    }
    
    //get users - personal info and user roles
    public function get_user_info(){
        $query = "SELECT * 
            FROM SJPARTRI.PERSONS P
            LEFT JOIN SJPARTRI.USERS U ON P.PERSON_ID = U.PERSON_ID";
        $users = oci_parse ($this->con, $query);
        oci_execute ($users);
        return $users;
    }
    
    //get sensor full name for character abbreviation
    public function get_role($type){
        if ($type == "a")
            return "a (administrator)";
        else if ($type == "d")
            return "d (data curator)";
        else if($type == "s")
            return "s (scientist)";
    }
    
    //when creating a new sensor get the next sensorID - autoincrement
    public function get_next_sensorID()
    {
        $query = "SELECT MAX(SENSOR_ID) FROM SJPARTRI.SENSORS";
        $sensorID = oci_parse ($this->con, $query);
        oci_execute ($sensorID);
        while (($row = oci_fetch_array($sensorID,OCI_BOTH)) != false) {
            $id = $row["MAX(SENSOR_ID)"];
         }
        return $id+1; 
    }
    
    //add new sensor to the database
    public function add_new_sensor($sensorID, $location, $type, $desciption)
    {
        $query = "INSERT INTO SJPARTRI.SENSORS VALUES ($sensorID,$location,$type,$desciption)";
        $sensor = oci_parse ($this->con, $query);
        $success = oci_execute ($sensor);
        return $success;
    }

    
}

?>

