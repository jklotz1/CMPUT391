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
     
      
        $sql = "SELECT VALUE, to_char(DATE_CREATED, 'dd/mm/yyyy hh24:mi:ss') AS DATE_CREATED "
                . "FROM sjpartri.SCALAR_DATA  "
               . "WHERE SENSOR_ID LIKE '$sensorId' "
               . "AND DATE_CREATED "
                . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')";
                
        
        $objParse = oci_parse ($this->con, $sql);
        oci_execute ($objParse);
        return $objParse;
    }
    
    
    
    public function get_search_results($keyword,$location, $sensorType, $sensorId, $startDate, $endDate, $startTime, $endTime){

        $sql = "SELECT distinct S.SENSOR_ID, S.LOCATION,S.DESCRIPTION,S.SENSOR_TYPE "
                ."FROM sjpartri.SENSORS S " 
                . "WHERE S.SENSOR_ID LIKE '$sensorId' ";
         
        
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
        
       $query = "SELECT THUMBNAIL, to_char(DATE_CREATED, 'dd/mm/yyyy hh24:mi:ss') AS DATE_CREATED, DESCRIPTION, IMAGE_ID "
               . "FROM IMAGES WHERE SENSOR_ID = '$sensorId' "
               . "AND DATE_CREATED "
               . "BETWEEN to_date('$startDate $startTime','yyyy-mm-dd hh24:mi:ss') AND to_date('$endDate $endTime','yyyy-mm-dd hh24:mi:ss')";

       $stmt = oci_parse ($this->con, $query);
       
       oci_execute($stmt, OCI_DEFAULT);
    
       return $stmt;
    }
    
    public function get_audioInfo($sensorId,$startDate,$endDate, $startTime, $endTime){
        $query = "SELECT to_char(DATE_CREATED, 'dd/mm/yyyy hh24:mi:ss') AS DATE_CREATED, DESCRIPTION, RECORDING_ID "
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
    
    //get all sensors in the system
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
        //get the largest sensorID in the system
        while (($row = oci_fetch_array($sensorID,OCI_BOTH)) != false) {
            $id = $row["MAX(SENSOR_ID)"];
         }
        //increment the ID and return for creating a new sensor
        return $id+1; 
    }
    
    //when creating a new person get the next personID - autoincrement
    public function get_next_personID()
    {
        $query = "SELECT MAX(PERSON_ID) FROM SJPARTRI.PERSONS";
        $personID = oci_parse ($this->con, $query);
        oci_execute ($personID);
        $id=0;
        //get the largest personID in the system
        while (($row = oci_fetch_array($personID,OCI_BOTH)) != false) {
            $id = $row["MAX(PERSON_ID)"];
         }
         //increment the ID and return for creating a new person
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
    
    //delete sensor from the system and all associated records
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
    
    //delete user from the system when username matches the passed in user
    public function delete_user($username)
    {
        //delete user
        $query = "DELETE FROM SJPARTRI.USERS WHERE USER_NAME = '$username'";
        $sensor = oci_parse ($this->con, $query);
        $success = oci_execute($sensor);
        return $success;
    }
    
    //add new user into the system
    public function add_new_user($user, $passwd, $role, $perID)
    {
        $query = "INSERT INTO SJPARTRI.USERS VALUES ('$user', '$passwd', '$role', $perID, sysdate)";
        $user = oci_parse ($this->con, $query);
        $success = oci_execute($user);
        return $success;
    }
    
    //add new person to the system
    public function add_new_person($peronID,$fname,$lname,$address,$email,$phone)
    {
        $query = "INSERT INTO SJPARTRI.PERSONS VALUES ($peronID,'$fname','$lname','$address','$email','$phone')";
        $person = oci_parse ($this->con, $query);
        $success = oci_execute($person);
        return $success;
    }
    
    //get personal accounts for all persons in the systems
    public function get_persons(){
        $query = "SELECT * 
            FROM SJPARTRI.PERSONS P
            ORDER BY P.PERSON_ID";
        $persons = oci_parse ($this->con, $query);
        oci_execute ($persons);
        return $persons;
    }
    
    //get the personal account information for the personID passed in
    public function get_person_by_ID($personID){
        $query = "SELECT * 
            FROM SJPARTRI.PERSONS P
            WHERE PERSON_ID = $personID";
        $persons = oci_parse ($this->con, $query);
        oci_execute ($persons);
        $personInfo = null;
        //get the row with the information and return that instead of an array
        while (($row = oci_fetch_array($persons,OCI_BOTH)) != false) {
            $personInfo = $row;
         }
        return $personInfo;
    }
    
    //get the the personID for the personal account corresponding to the user passed in
    public function get_personID($user)
    {
        $query = "SELECT PERSON_ID FROM SJPARTRI.USERS WHERE USER_NAME = '$user'";
        $personID = oci_parse ($this->con, $query);
        oci_execute ($personID);
        //get the personID
        while (($row = oci_fetch_array($personID,OCI_BOTH)) != false) {
            $id = $row["PERSON_ID"];
         }
         //return the ID not the entire array
        return $id; 
    }
    
    //delete a person from the system - must delete all associated user
    public function delete_person($personID)
    {
        //delete users associated with personID
        $query = "DELETE FROM SJPARTRI.USERS WHERE PERSON_ID = $personID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);
        
        //delete person
        $query = "DELETE FROM SJPARTRI.PERSONS WHERE PERSON_ID = $personID";
        $sensor = oci_parse ($this->con, $query);
        oci_execute($sensor);        
    }
    
    //retrieve the specific user their information from the system
    public function get_user_info_only($user){
        $query = "SELECT U.* 
            FROM SJPARTRI.USERS U 
            WHERE U.USER_NAME = '$user'";
        $users = oci_parse ($this->con, $query);
        oci_execute ($users);
        $userInfo = null;
        //get the row of user information
        while (($row = oci_fetch_array($users,OCI_BOTH)) != false) {
            $userInfo = $row;
         }
         //return the user information not array
        return $userInfo;
    }

    
    // get the personal information of the personal account associated with the specified user
    public function get_personal_info($username){
        $query = "SELECT P.* "
                ."FROM SJPARTRI.PERSONS P "
                ."INNER JOIN SJPARTRI.USERS U ON U.PERSON_ID = P.PERSON_ID "
                ."WHERE U.USER_NAME = '$username'";
        $person = oci_parse ($this->con, $query);
        oci_execute ($person);
        $personInfo = null;
        //get the row of the personal account info
        while (($row = oci_fetch_array($person,OCI_BOTH)) != false) {
            $personInfo = $row;
         }
        return $personInfo;
    }
    
    //get all the users assocaited with the person account that corresponds to the passed in username
    public function get_all_users_by_user($username){
        //first get the personID of $username then find all users with that personID
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
    
    //check that the password given for a user is correct
    public function password_match($username, $passwordTest){
        //get the password of the user
        $query = "SELECT U.PASSWORD "
                ."FROM SJPARTRI.USERS U "
                ."WHERE U.USER_NAME =  '$username'" ;
        $user = oci_parse ($this->con, $query);
        oci_execute ($user);
        //check if the password given by the user matches the password from the database
        while (($row = oci_fetch_array($user,OCI_BOTH)) != false) {
            $password = $row["PASSWORD"];
            if ($password == $passwordTest) { return TRUE; }
            else { return FALSE; }
         }
        return FALSE;
    }
    
    //update the user information for the specific user
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
    
    //chekc if a username already exists in the system - used for creating new users, cannot have duplicates
    public function user_exist($usernameTest){
        //try to retrieve a user by the given username
        $query = "SELECT U.USER_NAME "
                ."FROM SJPARTRI.USERS U "
                ."WHERE U.USER_NAME = '$usernameTest'" ;
        $user = oci_parse ($this->con, $query);
        oci_execute ($user);
        $match = false;
        //if a user was retrieved then the username already exists in the system
        while (($row = oci_fetch_array($user,OCI_BOTH)) != false) {
            $match = true;
         }
        return $match;
    }
    
    //update the personal information for the given person that matches ID
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
    
    //Upload image file if file was of type jpg
    public function upload_image($_FILES, $description, $sensorId) {
        $mysensorid = $sensorId;
        $mydescription = $description;
        $mydate = date('d/M/Y H:i:s');
        
        //Get new id for image, done by incrementing previous id by 1
        $query = "SELECT MAX(IMAGE_ID) AS MAXIMUM FROM IMAGES";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;
 

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

        //Get uploaded image data for creating thumbnail
        $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

        $stmt = oci_parse ($this->con, $query);
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_execute($stmt, OCI_DEFAULT);
        $arr = oci_fetch_assoc($stmt);
        $result = $arr['RECOREDED_DATA']->load();
        
        //Set desired thumbnail size
        $desired_width = 50;
        $desired_height = 50;
        //Create image from data
        $im = imagecreatefromstring($result);
        //Create new image for thumbnail
        $new = imagecreatetruecolor($desired_width, $desired_height);
        //Get image dimensions
        $x = imagesx($im);
        $y = imagesy($im);
        //Copy image into resized image
        imagecopyresampled($new, $im, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);
        //Destroy unneeded image
        imagedestroy($im);
        oci_free_statement($stmt);

        //Get thumbnail from new image
        ob_start();
        imagejpeg($new, null, 100);
        $mythumbnail = ob_get_contents();
        ob_clean();
        
        //Insert thumbnail into database
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

        //Get uploaded image again for displaying
        $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

        $stmt = oci_parse ($this->con, $query);
        oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
        oci_execute($stmt, OCI_DEFAULT);
  
        return $stmt;

    }
    
    //Upload audio file if file was of type wav
    public function upload_audio($_FILES, $description, $sensorId) {
        $mysensorid = $sensorId;
        $mydescription = $description;
        $mydate = date('d/M/Y H:i:s');
        
        //Get new id for audio file, done by incrementing previous id by 1
        $query = "SELECT MAX(RECORDING_ID) AS MAXIMUM FROM AUDIO_RECORDINGS";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;

        //Get audio file
        $filename = $_FILES['file']['tmp_name'];
        $file = fopen($filename, "r");
        
        //Calculate length of audio file
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
        
     //Upload scalar data in batches from csv file  
    public function upload_csv($_FILES) {
        //Get csv file
        $filename = $_FILES['file']['tmp_name'];
        $file = fopen($filename, "r");
        
        //Get new id for first lone of csv file, done by incrementing previous id by 1
        $query = "SELECT MAX(ID) AS MAXIMUM FROM SCALAR_DATA";
        $stmt = oci_parse ($this->con, $query);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $myblobid = oci_result($stmt, "MAXIMUM");
        oci_free_statement($stmt);
        $myblobid = $myblobid + 1;
        
        //Insert scalar data by each line in csv file
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
                //Get next id
                $myblobid = $myblobid + 1;
            }
       }
       fclose($file);
       return $data;
   }
   
   //Get all sensor ids for dropdown menu
   public function get_sensor_ids() {
       $query = 'SELECT SENSOR_ID FROM SENSORS';
       $stmt = oci_parse ($this->con, $query);
       oci_execute($stmt, OCI_DEFAULT);
       return $stmt;
   }
   
   //Get image for downloading
   public function get_image($imageid) {
       
       $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYIMAGEID';
       $stmt = oci_parse ($this->con, $query);
       oci_bind_by_name($stmt, ':MYIMAGEID', $imageid);
       oci_execute($stmt, OCI_DEFAULT);
       return $stmt;
   }
   
   //Get audio file for downloading
   public function get_audio($audioid) {
       
       $query = 'SELECT RECORDED_DATA FROM AUDIO_RECORDINGS WHERE RECORDING_ID = :MYAUDIOID';
       $stmt = oci_parse ($this->con, $query);
       oci_bind_by_name($stmt, ':MYAUDIOID', $audioid);
       oci_execute($stmt, OCI_DEFAULT);
       return $stmt;
   }
   
   //get all scalar sensors that a certian user is subscribed to
   public function get_subscribed_sensors($user){
        $query = "SELECT S.* 
                    FROM SJPARTRI.USERS U
                    INNER JOIN SJPARTRI.SUBSCRIPTIONS SB ON U.PERSON_ID = SB.PERSON_ID
                    INNER JOIN SJPARTRI.SENSORS S ON SB.SENSOR_ID = S.SENSOR_ID
                    WHERE U.USER_NAME = '$user' and S.SENSOR_TYPE = 's'
                    ORDER BY S.SENSOR_ID";
        $sensors = oci_parse ($this->con, $query);
        oci_execute ($sensors);
        return $sensors;
    }
    
    //create a data view used for the data analysis OLAP report
    //get the values of all the scalar data records
    //seperate the date_created into years, quarters, months, weeks, days to easily query and group later
    public function create_view_data($sensorID){
        $query = "CREATE VIEW vw_data AS SELECT D.YEAR as YEAR, D.MONTH as MONTH, D.QUARTER as QUARTER, D.WEEKOFYEAR as WEEK, D.DAY as DAY, 
                        D.DAYOFWEEK as dayofweek, D.DATE_CREATED as DATE_CREATED, D.VALUE as VALUE
                    FROM (SELECT DISTINCT SD.ID, extract(year from DATE_CREATED) as YEAR, extract(month from DATE_CREATED) as MONTH, 
                                extract(day from DATE_CREATED) as DAY, TO_CHAR(DATE_CREATED, 'Q') AS QUARTER, TO_CHAR(DATE_CREATED, 'D') AS DAYOFWEEK,
                                TO_CHAR(DATE_CREATED+1, 'IW') AS WEEKOFYEAR, SD.VALUE as VALUE, DATE_CREATED as DATE_CREATED
                            FROM SJPARTRI.USERS U
                            JOIN SJPARTRI.PERSONS P ON U.PERSON_ID=P.PERSON_ID
                            JOIN SJPARTRI.SUBSCRIPTIONS SB ON SB.PERSON_ID=P.PERSON_ID
                            JOIN SJPARTRI.SENSORS S ON S.SENSOR_ID=SB.SENSOR_ID
                            JOIN SJPARTRI.SCALAR_DATA SD ON S.SENSOR_ID=SD.SENSOR_ID
                            WHERE S.SENSOR_ID = '$sensorID') D
                    ORDER BY D.YEAR, D.MONTH, D.DAY, D.VALUE";  
        $view = oci_parse ($this->con, $query);
        oci_execute ($view);  
    }
    
    //drop the above data view from the system
    public function drop_view_data(){
        $query = "DROP VIEW VW_DATA";
        $view = oci_parse ($this->con, $query);
        oci_execute ($view);
    }
    
    //retrieve data for each year for data analysis
    public function get_data_to_display_year(){      
        //group by year
        $query = "SELECT YEAR, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                    FROM VW_DATA
                    GROUP BY YEAR
                    ORDER BY YEAR";
        $data = oci_parse ($this->con, $query);
        oci_execute ($data);
        return $data;
    }
    
    //retrieve data for each quarter of the specific year for data analysis
    public function get_data_to_display_quarter($year){
        //group by quarter and year
        $query = "SELECT YEAR, QUARTER, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                    FROM vw_data
                    WHERE YEAR = '$year'
                    GROUP BY YEAR, QUARTER
                    ORDER BY YEAR, QUARTER";
        $data = oci_parse ($this->con, $query);
        oci_execute ($data);
        return $data;
    }
    
    //retrieve data for each month of the specific quarter in the specific year for data analysis
    public function get_data_to_display_months($quarter, $year){
        //group by year and month
        $query = "SELECT YEAR, MONTH, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                    FROM vw_data
                    WHERE QUARTER = '$quarter' AND YEAR = '$year'
                    GROUP BY YEAR, MONTH
                    ORDER BY YEAR, MONTH";
        $data = oci_parse ($this->con, $query);
        oci_execute ($data);
        return $data;
    }
    
    //retrieve data for each week in the spefic month of the specific year for data analysis
    public function get_data_to_display_weeks($month, $year){
        //group by year, month, week
        //also get the first and last day cause not all weeks will start on sunday and end on saturday so need to do checks on these dates
        $query = "SELECT YEAR, MONTH, WEEK, (CASE WHEN (DAYOFWEEK-1)>DAY THEN TO_CHAR(1) ELSE TO_CHAR(DAY-(DAYOFWEEK-1)) END) AS FIRSTDAY, 
                        extract(day from LAST_DAY(DATE_CREATED)) as LASTDAY, 
                        CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                    FROM VW_DATA
                    WHERE MONTH = '$month' AND YEAR = '$year'
                    GROUP BY YEAR, MONTH, WEEK, (CASE WHEN (DAYOFWEEK-1)>DAY THEN TO_CHAR(1) ELSE TO_CHAR(DAY-(DAYOFWEEK-1)) END),extract(day from LAST_DAY(DATE_CREATED))
                    ORDER BY YEAR, MONTH, WEEK, (CASE WHEN (DAYOFWEEK-1)>DAY THEN TO_CHAR(1) ELSE TO_CHAR(DAY-(DAYOFWEEK-1)) END),extract(day from LAST_DAY(DATE_CREATED))";
        $data = oci_parse ($this->con, $query);
        oci_execute ($data);
        return $data;
    }

    //retrieve data for each day in the specific week in the specific year for data analysis
    public function get_data_to_display_days($day, $month, $year){
        //first find the week of the year that the day, month and year belong to
        //get all entries also within this week that fall in the same month
        $query = "SELECT YEAR, MONTH, DAY, WEEK, CAST(AVG(VALUE)AS DECIMAL(16,3)) AS AVERAGE, MIN(VALUE) AS MINIMUM, MAX(VALUE) AS MAXIMUM
                            FROM vw_data
                            WHERE TO_CHAR(TO_DATE('$year-$month-$day','YYYY-MM-DD')+1, 'IW') = WEEK AND MONTH = $month
                            GROUP BY YEAR, MONTH, DAY, WEEK
                            ORDER BY YEAR, MONTH, DAY, WEEK";
        $data = oci_parse ($this->con, $query);
        oci_execute ($data);
        return $data;
    }
    
    //get the sensor information for the specified sensorID
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

