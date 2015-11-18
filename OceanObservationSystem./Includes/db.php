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
    
    public function get_subscription_details($sensorID){
        $sql = "SELECT SENSOR_ID, SENSOR_TYPE, LOCATION, DESCRIPTION "
             . "FROM sjpartri.SENSORS "
             . "WHERE SENSOR_ID = '$sensorID'";
        
        $objParse = oci_parse($this->con, $sql);
        oci_execute($objParse);
        return $objParse;
    }
    
    public function get_non_subcribed_sensor_table_results($sensorTable, $count){
        
        $sensorID = $sensorTable["SENSOR_ID"][0];
       
        
        if($sensorID == NULL){
              $sql = "SELECT SENSOR_ID, SENSOR_TYPE, LOCATION, DESCRIPTION "
             . "FROM sjpartri.SENSORS ";
             
        $objParse = oci_parse($this->con, $sql);
        oci_execute($objParse);
        return $objParse;
        
        }else{
       
        $sql = "SELECT SENSOR_ID, SENSOR_TYPE, LOCATION, DESCRIPTION "
             . "FROM sjpartri.SENSORS "
             . "WHERE SENSOR_ID != '$sensorID'";
        
        
       for($i = 1; $i< $count; $i++){
              $sensorID = $sensorTable['SENSOR_ID'][$i];
           
              $sql.= "AND SENSOR_ID != '$sensorID'";
              
        }
        
       $objParse = oci_parse($this->con, $sql);
        oci_execute($objParse);
        return $objParse;
        }
    }
    
    public function add_subscription($sensorID, $user){
   
        $person_id = OceanDB::getInstance()->get_person_id_by_name($user);
        $person_id_parse = oci_fetch_array($person_id, OCI_BOTH);
        $person_id = $person_id_parse["PERSON_ID"];
       
        
        $sql = "INSERT INTO sjpartri.SUBSCRIPTIONS VALUES($sensorID,$person_id)";
        
        $objParse = oci_parse($this->con, $sql);
        oci_execute($objParse);
        return $objParse;
        
    }
    
    public function delete_subscription($sensorID, $user){
        $person_id = OceanDB::getInstance()->get_person_id_by_name($user);
        $person_id_parse = oci_fetch_array($person_id, OCI_BOTH);
        $person_id = $person_id_parse["PERSON_ID"];
       
        
        
        $sql =    "DELETE FROM sjpartri.SUBSCRIPTIONS WHERE "
                . "SENSOR_ID = $sensorID "
                . "AND PERSON_ID = $person_id ";
        
        $objParse = oci_parse($this->con, $sql);
        oci_execute($objParse);
        return $objParse;
    }
    
    public function get_scalar_data_values($sensorId, $startDate, $endDate){

      
        $sql = "SELECT S1.VALUE, S1.DATE_CREATED "
                . "FROM sjpartri.SCALAR_DATA S1  "
               . "WHERE S1.SENSOR_ID LIKE '$sensorId' "
               . "AND S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss')";
                
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    
    public function get_keyword_search_results($keyword, $sensorId, $startDate, $endDate){
      
        $keys = explode(" ",$_POST["txtKeyword"]);
        $sql = "SELECT distinct S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE, S1.DATE_CREATED "
                . "FROM sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.IMAGES I, sjpartri.AUDIO_RECORDINGS AR "
                . "WHERE S.SENSOR_ID LIKE '$sensorId' "
                . "AND( S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR I.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR AR.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss'))";
            
                
        
        foreach($keys as $k){
        
            $sql.= "AND lower(S.DESCRIPTION) LIKE lower('%$k%')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    public function get_location_search_results($location, $sensorId, $startDate, $endDate){
        
        $keys = explode(" ",$location);
        $sql = "SELECT distinct S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE, S1.DATE_CREATED "
                ."FROM sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.IMAGES I, sjpartri.AUDIO_RECORDINGS AR " 
                . "WHERE S.SENSOR_ID LIKE '$sensorId' "
                . "AND( S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR I.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR AR.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss'))";
        
        
        foreach($keys as $k){
            $sql.= "AND lower(S.LOCATION) LIKE lower('$k')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
      public function get_sensor_type_search_results($sensorType, $sensorId, $startDate, $endDate){

        $keys = explode(" ",$sensorType);
        $sql = "SELECT distinct S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE, S1.DATE_CREATED "
                ."FROM sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.IMAGES I, sjpartri.AUDIO_RECORDINGS AR " 
                . "WHERE S.SENSOR_ID LIKE '$sensorId' "
                . "AND( S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR I.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR AR.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss'))";
        
        
        foreach($keys as $k){
            $sql.= "AND lower(SENSOR_TYPE) LIKE lower('%$k%')";
        }
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
        
    }
    
    
    
    public function get_search_results($keyword,$location, $sensorType, $sensorId, $startDate, $endDate){
  
     
        
        $sql = "SELECT distinct S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE, S1.DATE_CREATED "
                ."FROM sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.IMAGES I, sjpartri.AUDIO_RECORDINGS AR " 
                . "WHERE S.SENSOR_ID LIKE '$sensorId' "
                . "AND (S1.SENSOR_ID LIKE '$sensorId' "
                . "OR I.SENSOR_ID  LIKE '$sensorId' "
                . "OR AR.SENSOR_ID = '$sensorId' )"
                . "AND( S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR I.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss') "
                . "OR AR.DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss')) ";
        
       
        
        if($keyword != ""){
       $keyword_keys = explode(" ",$keyword);
        foreach($keyword_keys as $k1){
        
            $sql.= "AND lower(S.DESCRIPTION) LIKE lower('%$k1%')";
        }
        
        }
        
        if($location != ""){
         
        $location_keys = explode(" ",$location);
        foreach($location_keys as $k){
            $sql.= "AND lower(S.LOCATION) LIKE lower('$k')";
        }
        }
        
        if($sensorType != ""){
             $sensor_keys = explode(" ",$location);
        foreach($sensor_keys as $k2){
             $sql.= "AND lower(S.SENSOR_TYPE) LIKE lower('%$k2%')";
        }
       }
        
        
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
        
    }
    
    public function get_thumbnail($sensorId,$startDate,$endDate){
        
       $query = "SELECT THUMBNAIL, DATE_CREATED "
               . "FROM IMAGES WHERE SENSOR_ID = '$sensorId' "
               . "AND DATE_CREATED "
               . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss')";

       $stmt = oci_parse ($this->con, $query);
       
       oci_execute($stmt, OCI_DEFAULT);
    
       return $stmt;
    }
    
    public function get_audioInfo($sensorId,$startDate, $endDate){
        $query = "SELECT DATE_CREATED "
                . "FROM AUDIO_RECORDINGS WHERE SENSOR_ID='$sensorId'"
                . "AND DATE_CREATED "
                . "BETWEEN to_date('$startDate','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate','yyyy-mm-dd hh24:mi:ss')";
        
        $objParse = oci_parse ($this->con, $query);
        
        oci_execute($objParse, OCI_DEFAULT);
        
        return $objParse;
        
    }
    
    public function full_search_results($sensorID,$keyword, $sensorType, $location, $startDate, $endDate){
        
        $keyword_keys = explode(" ",$keyword);
      $sensorType_keys = explode(" ",$sensorType);
        $location_keys = explode(" ",$location);
        
        $sql = "Select distinct S.SENSOR_ID,S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE, S1.VALUE, S1.DATE_CREATED
                From sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.AUDIO_RECORDINGS AR, sjpartri.IMAGES I, sjpartri.SUBSCRIPTIONS SB
                WHERE S.SENSOR_ID=S1.SENSOR_ID
                AND S.SENSOR_ID=AR.SENSOR_ID
                AND S.SENSOR_ID=I.SENSOR_ID
                AND S.SENSOR_ID = $sensorID
                AND lower(S.DESCRIPTION) LIKE lower('%$keyword%')
                AND lower(S.SENSOR_TYPE) LIKE lower('%$sensorType%')
                AND lower(S.LOCATION) LIKE lower('$location')";
        
       foreach($keyword_keys as $k1){
          $sql.= "or lower(S.DESCRIPTION) LIKE lower('%$k1%')";
       }
       foreach($sensorType_keys as $k2){
        $sql.= "or lower(S.SENSOR_TYPE) LIKE lower('%$k2%')";
      }
        foreach($location_keys as $k3){
           $sql.= "and lower(S.LOCATION) LIKE lower('$k3')";
       }
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse, OCI_DEFAULT);
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
            RIGHT JOIN SJPARTRI.USERS U ON P.PERSON_ID = U.PERSON_ID
            ORDER BY P.PERSON_ID, USER_NAME";
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
        $id=0;
        while (($row = oci_fetch_array($sensorID,OCI_BOTH)) != false) {
            $id = $row["MAX(SENSOR_ID)"];
         }
        return $id+1; 
    }
    
    public function get_next_personID()
    {
        $query = "SELECT MAX(PERSON_ID) FROM SJPARTRI.PERSONS";
        $personID = oci_parse ($this->con, $query);
        oci_execute ($personID);
        $id=0;
        while (($row = oci_fetch_array($personID,OCI_BOTH)) != false) {
            $id = $row["MAX(PERSON_ID)"];
         }
        return $id+1; 
    }
    
    //add new sensor to the database
    public function add_new_sensor($sensorID, $location, $type, $desciption)
    {
        $query = "INSERT INTO SJPARTRI.SENSORS VALUES ($sensorID,'$location','$type','$desciption')";
        $sensor = oci_parse ($this->con, $query);
        $success = oci_execute($sensor);
        return $success;
    }
    
    public function delete_sensor($sensorID)
    {
        //must delete all associate data entries with sensor
        //delete audio records
        $query = "DELETE FROM SJPARTRI.AUDIO_RECORDINGS WHERE SENSOR_ID = $sensorID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        //delete image records
        $query = "DELETE FROM SJPARTRI.IMAGES WHERE SENSOR_ID = $sensorID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        //delete scalar records
        $query = "DELETE FROM SJPARTRI.SCALAR_DATA WHERE SENSOR_ID = $sensorID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        //delete subscription
        $query = "DELETE FROM SJPARTRI.SUBSCRIPTIONS WHERE SENSOR_ID = $sensorID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        //now can delete sensor
        $query = "DELETE FROM SJPARTRI.SENSORS WHERE SENSOR_ID = $sensorID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
    }
    
    //delete user
    public function delete_user($username)
    {
        //delete user
        $query = "DELETE FROM SJPARTRI.USERS WHERE USER_NAME = '$username'";
        $sensor = oci_parse ($this->con, $query);
        $success = oci_execute($sensor);
        return $success;
    }
    
    public function add_new_user($user, $passwd, $role, $perID)
    {
        $query = "INSERT INTO SJPARTRI.USERS VALUES ('$user', '$passwd', '$role', $perID, sysdate)";
        $user = oci_parse ($this->con, $query);
        $success = oci_execute($user);
        return $success;
    }
    
    public function add_new_person($peronID,$fname,$lname,$address,$email,$phone)
    {
        $query = "INSERT INTO SJPARTRI.PERSONS VALUES ($peronID,'$fname','$lname','$address','$email','$phone')";
        $person = oci_parse ($this->con, $query);
        $success = oci_execute($person);
        return $success;
    }
    
    //get users - personal info and user roles
    public function get_persons(){
        $query = "SELECT * 
            FROM SJPARTRI.PERSONS P
            ORDER BY P.PERSON_ID";
        $persons = oci_parse ($this->con, $query);
        oci_execute ($persons);
        return $persons;
    }
    
    public function get_person_by_ID($personID){
        $query = "SELECT * 
            FROM SJPARTRI.PERSONS P
            WHERE PERSON_ID = $personID";
        $persons = oci_parse ($this->con, $query);
        oci_execute ($persons);
        $personInfo = null;
        while (($row = oci_fetch_array($persons,OCI_BOTH)) != false) {
            $personInfo = $row;
         }
        return $personInfo;
    }
    
    public function get_personID($user)
    {
        $query = "SELECT PERSON_ID FROM SJPARTRI.USERS WHERE USER_NAME = '$user'";
        $personID = oci_parse ($this->con, $query);
        oci_execute ($personID);
        while (($row = oci_fetch_array($personID,OCI_BOTH)) != false) {
            $id = $row["PERSON_ID"];
         }
        return $id; 
    }
    
    public function delete_person($personID)
    {
        //delete person
        $query = "DELETE FROM SJPARTRI.USERS WHERE PERSON_ID = $personID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        $query = "DELETE FROM SJPARTRI.PERSONS WHERE PERSON_ID = $personID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
    }
    
    public function get_user_info_only($user){
        $query = "SELECT U.* 
            FROM SJPARTRI.USERS U 
            WHERE U.USER_NAME = '$user'";
        $users = oci_parse ($this->con, $query);
        oci_execute ($users);
        $userInfo = null;
        while (($row = oci_fetch_array($users,OCI_BOTH)) != false) {
            $userInfo = $row;
         }
        return $userInfo;
    }

    public function get_personal_info($username){
        $query = "SELECT P.* "
                ."FROM SJPARTRI.PERSONS P "
                ."INNER JOIN SJPARTRI.USERS U ON U.PERSON_ID = P.PERSON_ID "
                ."WHERE U.USER_NAME = '$username'";
        $person = oci_parse ($this->con, $query);
        oci_execute ($person);
        $personInfo = null;
        while (($row = oci_fetch_array($person,OCI_BOTH)) != false) {
            $personInfo = $row;
         }
        return $personInfo;
    }
    
    public function get_all_users_by_user($username){
       $query = "SELECT U.* "
                ."FROM SJPARTRI.USERS U "
                ."WHERE U.PERSON_ID = (SELECT P.PERSON_ID "
                                        ."FROM SJPARTRI.PERSONS P "
                                        ."LEFT JOIN SJPARTRI.USERS U1 ON U1.PERSON_ID=P.PERSON_ID "
                                        ."WHERE U1.USER_NAME = '$username' )" ;
        $users = oci_parse ($this->con, $query);
        oci_execute ($users);
        return $users;
    }
    
    public function password_match($username, $passwordTest){
        $query = "SELECT U.PASSWORD "
                ."FROM SJPARTRI.USERS U "
                ."WHERE U.USER_NAME =  '$username'" ;
        $user = oci_parse ($this->con, $query);
        oci_execute ($user);
        while (($row = oci_fetch_array($user,OCI_BOTH)) != false) {
            $password = $row["PASSWORD"];
            if ($password == $passwordTest) { return TRUE; }
            else { return FALSE; }
         }
        return FALSE;
    }
    
    public function update_user($username, $role, $newUser, $passwd,$personID){
        $query = "UPDATE SJPARTRI.USERS U 
                  SET U.ROLE='$role',
                    U.USER_NAME = '$newUser',
                    U.PASSWORD  = '$passwd',
                    U.PERSON_ID = $personID
                  WHERE U.USER_NAME= '$username'";
        $sensor = oci_parse ($this->con, $query);
        $success = oci_execute($sensor);
        return $success;
    }
    
    public function user_exist($usernameTest){
        $query = "SELECT U.USER_NAME "
                ."FROM SJPARTRI.USERS U "
                ."WHERE U.USER_NAME = '$usernameTest'" ;
        $user = oci_parse ($this->con, $query);
        oci_execute ($user);
        $match = false;
        while (($row = oci_fetch_array($user,OCI_BOTH)) != false) {
            $match = true;
         }
        return $match;
    }
    
    public function update_person($id, $fName, $lName, $address, $email, $phone){
        $query = "UPDATE SJPARTRI.PERSONS P
                SET P.FIRST_NAME = '$fName',
                P.LAST_NAME = '$lName',
                P.ADDRESS = '$address',
                P.EMAIL = '$email',
                P.PHONE = '$phone' 
                WHERE P.PERSON_ID = $id";
        $person = oci_parse ($this->con, $query);
        $success = oci_execute($person);
        return $success;
    }
    
    
    
}

?>

