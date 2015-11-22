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
    
    public function get_scalar_data_values($sensorId, $startDate, $endDate, $startTime, $endTime){

      
        $sql = "SELECT S1.VALUE, S1.DATE_CREATED "
                . "FROM sjpartri.SCALAR_DATA S1  "
               . "WHERE S1.SENSOR_ID LIKE '$sensorId' "
               . "AND S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')";
                
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    
    
    public function get_search_results($keyword,$location, $sensorType, $sensorId, $startDate, $endDate, $startTime, $endTime){
      
            
     
        
        $sql = "SELECT distinct S.SENSOR_ID, S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE "
                ."FROM sjpartri.SENSORS S, sjpartri.SCALAR_DATA S1, sjpartri.IMAGES I, sjpartri.AUDIO_RECORDINGS AR " 
                . "WHERE S.SENSOR_ID LIKE '$sensorId' "
                . "AND (S1.SENSOR_ID LIKE '$sensorId' "
                . "OR I.SENSOR_ID  LIKE '$sensorId' "
                . "OR AR.SENSOR_ID = '$sensorId' )"
                . "AND( S1.DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss') "
                . "OR I.DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss') "
                . "OR AR.DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')) ";
        
       
        
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
             $sensor_keys = explode(" ",$sensorType);
        foreach($sensor_keys as $k2){
             $sql.= "AND lower(S.SENSOR_TYPE) LIKE lower('%$k2%')";
        }
       }
        
        
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
        
    }
    
    public function get_thumbnail($sensorId,$startDate,$endDate, $startTime, $endTime){
        
       $query = "SELECT THUMBNAIL, DATE_CREATED, DESCRIPTION "
               . "FROM IMAGES WHERE SENSOR_ID = '$sensorId' "
               . "AND DATE_CREATED "
               . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')";

       $stmt = oci_parse ($this->con, $query);
       
       oci_execute($stmt, OCI_DEFAULT);
    
       return $stmt;
    }
    
    public function get_audioInfo($sensorId,$startDate,$endDate, $startTime, $endTime){
        $query = "SELECT DATE_CREATED, DESCRIPTION, RECORDING_ID "
                . "FROM AUDIO_RECORDINGS WHERE SENSOR_ID='$sensorId'"
                . "AND DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')";
        
        $objParse = oci_parse ($this->con, $query);
        
        oci_execute($objParse, OCI_DEFAULT);
        
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
    
    public function upload_image($_FILES, $description, $sensorId) {
        $mysensorid = $sensorId;
        $mydescription = $description;
        $mydate = date('d/M/Y H:i:s');
        
        $query = "SELECT MAX(IMAGE_ID) AS MAXIMUM FROM IMAGES";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;
            
        //$query = 'DELETE FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';
        //$stmt = oci_parse ($this->con, $query);
        //oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        //$e = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
        //if (!$e) {
            //die;
        //}
        //oci_free_statement($stmt);
  

        // Insert the BLOB from PHP's tempory upload area
        $lob = oci_new_descriptor($this->con, OCI_D_LOB);
        $stmt = oci_parse($this->con, "INSERT INTO IMAGES (IMAGE_ID, SENSOR_ID, DATE_CREATED, DESCRIPTION, RECOREDED_DATA) "
            ."VALUES(:MYBLOBID, :MYSENSORID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECOREDED_DATA INTO :RECOREDED_DATA");
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_bind_by_name($stmt, ':MYSENSORID', $mysensorid);
        oci_bind_by_name($stmt, ":MYDATE", $mydate);
        oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
        oci_bind_by_name($stmt, ':RECOREDED_DATA', $lob, -1, OCI_B_BLOB);
        oci_execute($stmt, OCI_DEFAULT);

        if ($lob->savefile($_FILES['file']['tmp_name'])) {
            oci_commit($this->con);
        }
        else {
            echo "Couldn't upload image\n";
        }
        $lob->free();
        oci_free_statement($stmt);

        $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

        $stmt = oci_parse ($this->con, $query);
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_execute($stmt, OCI_DEFAULT);
        $arr = oci_fetch_assoc($stmt);
        $result = $arr['RECOREDED_DATA']->load();
    
        $desired_width = 50;
        $desired_height = 50;
        $im = imagecreatefromstring($result);
        $new = imagecreatetruecolor($desired_width, $desired_height);
        $x = imagesx($im);
        $y = imagesy($im);
        imagecopyresampled($new, $im, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);
        imagedestroy($im);
        oci_free_statement($stmt);

        //header('Content-type: image/jpeg');
        ob_start();
        imagejpeg($new, null, 100);
        $mythumbnail = ob_get_contents();
        ob_clean();
    
        $lob = oci_new_descriptor($this->con, OCI_D_LOB);
        $stmt = oci_parse($this->con, "UPDATE IMAGES SET THUMBNAIL = EMPTY_BLOB() WHERE IMAGE_ID = :MYBLOBID RETURNING THUMBNAIL INTO :MYTHUMBNAIL");
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_bind_by_name($stmt, ':MYTHUMBNAIL', $lob, -1, OCI_B_BLOB);
        oci_execute($stmt, OCI_DEFAULT);
        if ($lob->save($mythumbnail)) {
            oci_commit($this->con);
        }
        else {
            echo "Couldn't upload image\n";
        }
        $lob->free();
        oci_free_statement($stmt);

        $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

        $stmt = oci_parse ($this->con, $query);
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_execute($stmt, OCI_DEFAULT);
  
        return $stmt;

    }
    
    public function upload_audio($_FILES, $description, $sensorId) {
        $mysensorid = $sensorId;
        $mydescription = $description;
        $mydate = date('d/M/Y H:i:s');
        
        $query = "SELECT MAX(RECORDING_ID) AS MAXIMUM FROM AUDIO_RECORDINGS";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;


        $filename = $_FILES['file']['tmp_name'];
        $file = fopen($filename, "r");
        
        $size_in_bytes = filesize($filename);
        fseek($file, 20);
        $rawheader = fread($file, 16);
        $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits', $rawheader);
        $sec = ceil($size_in_bytes/$header['bytespersec']);
  

        // Insert the BLOB from PHP's tempory upload area
        $lob = oci_new_descriptor($this->con, OCI_D_LOB);
        $stmt = oci_parse($this->con, "INSERT INTO AUDIO_RECORDINGS (RECORDING_ID, SENSOR_ID, DATE_CREATED, LENGTH, DESCRIPTION, RECORDED_DATA) "
            ."VALUES(:MYBLOBID, :MYSENSORID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYLENGTH, :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECORDED_DATA INTO :RECORDED_DATA");
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_bind_by_name($stmt, ':MYSENSORID', $mysensorid);
        oci_bind_by_name($stmt, ":MYDATE", $mydate);
        oci_bind_by_name($stmt, ":MYLENGTH", $sec);
        oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
        oci_bind_by_name($stmt, ':RECORDED_DATA', $lob, -1, OCI_B_BLOB);
        oci_execute($stmt, OCI_DEFAULT);

        if ($lob->savefile($_FILES['file']['tmp_name'])) {
            oci_commit($this->con);
        }
        else {
            echo "Couldn't upload audio recording\n";
        }
        $lob->free();
        oci_free_statement($stmt);
        
        $query = 'SELECT RECORDED_DATA FROM AUDIO_RECORDINGS WHERE RECORDING_ID = :MYBLOBID';

        $stmt = oci_parse ($this->con, $query);
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_execute($stmt, OCI_DEFAULT);
  
        return $stmt;
        }
        
    public function upload_csv($_FILES) {
        $filename = $_FILES['file']['tmp_name'];
        $file = fopen($filename, "r");
        
        $query = "SELECT MAX(ID) AS MAXIMUM FROM SCALAR_DATA";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;
        
        $data = array();
        while(! feof($file)) {
            $line = fgetcsv($file);
            if ($line[0] !== null) {
                $stmt = oci_parse($this->con, "INSERT INTO SCALAR_DATA (ID, SENSOR_ID, DATE_CREATED, VALUE) "
                        ."VALUES(:MYBLOBID, :MYSCALARID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYVALUE)");
                oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
                oci_bind_by_name($stmt, ':MYSCALARID', $line[0]);
                oci_bind_by_name($stmt, ':MYDATE', $line[1]);
                oci_bind_by_name($stmt, ':MYVALUE', $line[2]);
                oci_execute($stmt, OCI_DEFAULT);
                oci_commit($this->con);
                oci_free_statement($stmt);
                array_push($data, $line);
                $myblobid = $myblobid + 1;
            }
       }
       fclose($file);
       return $data;
   }
   
   public function get_sensor_ids() {
       $query = 'SELECT SENSOR_ID FROM SENSORS';
       $stmt = oci_parse ($this->con, $query);
       oci_execute($stmt, OCI_DEFAULT);
       return $stmt;
   }
   
   public function get_image($_FILES) {
       $query = 'SELECT RECOREDED_DATA FROM IMAGES';

       $stmt = oci_parse ($this->con, $query);
       oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
       oci_execute($stmt, OCI_DEFAULT);
  
       return $stmt;
   }
   
   public function get_subscribed_sensors($user){
        $query = "SELECT S.* 
                    FROM SJPARTRI.USERS U
                    INNER JOIN SJPARTRI.SUBSCRIPTIONS SB ON U.PERSON_ID = SB.PERSON_ID
                    INNER JOIN SJPARTRI.SENSORS S ON SB.SENSOR_ID = S.SENSOR_ID
                    WHERE U.USER_NAME = '$user'
                    ORDER BY S.SENSOR_ID";
        $sensors = oci_parse ($this->con, $query);
        oci_execute ($sensors);
        return $sensors;
    }
    
    public function get_data_to_display($sensor_id,$time){
        //create view with relevant data
        $query = "CREATE VIEW vw_data AS SELECT D.YEAR as YEAR, D.MONTH as MONTH, D.QUARTER as QUARTER, D.WEEKOFYEAR as WEEK, D.DAY as DAY, D.DAYOFWEEK as dayofweek, DATE_CREATED as DATE_CREATED, D.VALUE as VALUE
                        FROM (SELECT DISTINCT SD.ID, extract(year from DATE_CREATED) as YEAR, extract(month from DATE_CREATED) as MONTH, 
                                    extract(day from DATE_CREATED) as DAY, TO_CHAR(DATE_CREATED, 'Q') AS QUARTER, TO_CHAR(DATE_CREATED, 'D') AS DAYOFWEEK,
                                    TO_CHAR(DATE_CREATED+1, 'IW') AS WEEKOFYEAR, SD.VALUE as VALUE, DATE_CREATED
                                FROM SJPARTRI.USERS U
                                JOIN SJPARTRI.PERSONS P ON U.PERSON_ID=P.PERSON_ID
                                JOIN SJPARTRI.SUBSCRIPTIONS SB ON SB.PERSON_ID=P.PERSON_ID
                                JOIN SJPARTRI.SENSORS S ON S.SENSOR_ID=SB.SENSOR_ID
                                JOIN SJPARTRI.SCALAR_DATA SD ON S.SENSOR_ID=SD.SENSOR_ID
                                WHERE S.SENSOR_ID = $sensor_id) D
                        ORDER BY D.YEAR, D.MONTH, D.DAY, D.VALUE";
        $view = oci_parse ($this->con, $query);
        oci_execute ($view);
        
        //retrieve data for years
        if ($time == 'yearly'){
            $query = "SELECT YEAR, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                        FROM VW_DATA
                        GROUP BY YEAR
                        ORDER BY YEAR";
            $data = oci_parse ($this->con, $query);
            oci_execute ($data);
        }
        
        //retrieve data for quarters
        if ($time == 'quarterly'){
            $query = "SELECT YEAR, QUARTER, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                FROM vw_data
                GROUP BY YEAR, QUARTER
                ORDER BY YEAR, QUARTER";
            $data = oci_parse ($this->con, $query);
            oci_execute ($data);
        }
        
        //retrieve data for months
        if ($time == 'monthly'){
            $query = "SELECT YEAR, MONTH, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                        FROM vw_data
                        GROUP BY YEAR, MONTH
                        ORDER BY YEAR, MONTH";
            $data = oci_parse ($this->con, $query);
            oci_execute ($data);
        }
        
        //retrieve data for weeks
        if ($time == 'weekly'){
            $query = "SELECT YEAR, MONTH, DAY, WEEK, DAYOFWEEK, DATE_CREATED, VALUE
                        FROM ( SELECT YEAR, MONTH, (CASE WHEN WEEK=53 AND MONTH=1 THEN TO_CHAR(00) ELSE WEEK END) WEEK, DAY, DAYOFWEEK, DATE_CREATED, VALUE
                                FROM VW_DATA )
                        ORDER BY YEAR, WEEK";
            $data = oci_parse ($this->con, $query);
            oci_execute ($data);
        }

        //retrieve data for days
        if ($time == 'daily'){
            $query = "SELECT YEAR, MONTH, DAY, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                        FROM vw_data
                        GROUP BY YEAR, MONTH, DAY
                        ORDER BY YEAR, MONTH, DAY";
            $data = oci_parse ($this->con, $query);
            oci_execute ($data);
        }
        
        //drop view from database
        $query = "DROP VIEW VW_DATA";
        $viewDLT = oci_parse ($this->con, $query);
        oci_execute ($viewDLT);
        
        return $data;
    }
    
    public function get_sensor_by_ID($sensor_id){
        $query = "SELECT * 
            FROM SJPARTRI.SENSORS S
            WHERE S.SENSOR_ID = $sensor_id";
        $sensors = oci_parse ($this->con, $query);
        oci_execute ($sensors);
        $sensorInfo = null;
        while (($row = oci_fetch_array($sensors,OCI_BOTH)) != false) {
            $sensorInfo = $row;
         }
        return $sensorInfo;
    }
    
}

?>

