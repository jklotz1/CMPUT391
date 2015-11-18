<?php
$dbHost="gwynne.cs.ualberta.ca:1521:crs"; 
$dbUsername="sjpartri";
$dbPassword="letmein22";

if (!isset($_FILES['file'])) {
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" 
   enctype="multipart/form-data">
Image filename: <input type="file" name="file">
<input type="submit" value="Upload">
</form>
<?php
}
else {
   $target_file = ($_FILES['file']['name']);
   $ext = pathinfo($target_file, PATHINFO_EXTENSION);
   
   $conn = oci_connect('sjpartri', 'letmein22');
        if (!$conn) {
            $m = oci_error();
            exit('Connect Error' . $m['message']);

        }
   
   if ($ext == 'jpg') {

       
    $myblobid = 3;
    $mysensorid = 123;
    $mydescription = 'desc';
    $mydate = '05/11/2015 00:00:00';


    $query = 'DELETE FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';
    $stmt = oci_parse ($conn, $query);
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    $e = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    if (!$e) {
        die;
    }
    oci_free_statement($stmt);
  

    // Insert the BLOB from PHP's tempory upload area
    $lob = oci_new_descriptor($conn, OCI_D_LOB);
    $stmt = oci_parse($conn, "INSERT INTO IMAGES (IMAGE_ID, SENSOR_ID, DATE_CREATED, DESCRIPTION, RECOREDED_DATA) "
         ."VALUES(:MYBLOBID, :MYSENSORID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECOREDED_DATA INTO :RECOREDED_DATA");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_bind_by_name($stmt, ':MYSENSORID', $mysensorid);
    oci_bind_by_name($stmt, ":MYDATE", $mydate);
    oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
    oci_bind_by_name($stmt, ':RECOREDED_DATA', $lob, -1, OCI_B_BLOB);
    oci_execute($stmt, OCI_DEFAULT);

    if ($lob->savefile($_FILES['file']['tmp_name'])) {
        oci_commit($conn);
    }
    else {
        echo "Couldn't upload Blob\n";
    }
    $lob->free();
    oci_free_statement($stmt);
  
   
    $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

    $stmt = oci_parse ($conn, $query);
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

    header('Content-type: image/jpeg');
    ob_start();
    imagejpeg($new, null, 100);
    $mythumbnail = ob_get_contents();
    ob_clean();
    
    $lob = oci_new_descriptor($conn, OCI_D_LOB);
    $stmt = oci_parse($conn, "UPDATE IMAGES SET THUMBNAIL = EMPTY_BLOB() WHERE IMAGE_ID = :MYBLOBID RETURNING THUMBNAIL INTO :MYTHUMBNAIL");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_bind_by_name($stmt, ':MYTHUMBNAIL', $lob, -1, OCI_B_BLOB);
    oci_execute($stmt, OCI_DEFAULT);
    if ($lob->save($mythumbnail)) {
        oci_commit($conn);
    }
    else {
        echo "Couldn't upload Blob\n";
    }
    $lob->free();
    oci_free_statement($stmt);
    
 
    // Now query the uploaded BLOB and display it

    $query = 'SELECT THUMBNAIL FROM IMAGES WHERE SENSOR_ID = :MYBLOBID';

    $stmt = oci_parse ($conn, $query);
    oci_bind_by_name($stmt, ':MYBLOBID', $mysensorid);
    oci_execute($stmt, OCI_DEFAULT);
    $arr = oci_fetch_assoc($stmt);
    $result = $arr['THUMBNAIL']->load();
  
    header("Content-type: image/JPEG");
    echo $result;
    oci_free_statement($stmt);
  
  

    oci_close($conn); // log off    

   }
   elseif ($ext == 'wav') {
       
    $myblobid = 1;
    $mysensorid = 123;
    $mydescription = 'desc';
    $mydate = '05/11/2015 00:00:00';


    $query = 'DELETE FROM AUDIO_RECORDINGS WHERE RECORDING_ID = :MYBLOBID';
    $stmt = oci_parse ($conn, $query);
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    $e = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    if (!$e) {
        die;
    }
    oci_free_statement($stmt);
  

    // Insert the BLOB from PHP's tempory upload area
    $lob = oci_new_descriptor($conn, OCI_D_LOB);
    $stmt = oci_parse($conn, "INSERT INTO AUDIO_RECORDINGS (RECORDING_ID, SENSOR_ID, DATE_CREATED, DESCRIPTION, RECORDED_DATA) "
         ."VALUES(:MYBLOBID, :MYSENSORID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECORDED_DATA INTO :RECORDED_DATA");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_bind_by_name($stmt, ':MYSENSORID', $mysensorid);
    oci_bind_by_name($stmt, ":MYDATE", $mydate);
    oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
    oci_bind_by_name($stmt, ':RECORDED_DATA', $lob, -1, OCI_B_BLOB);
    oci_execute($stmt, OCI_DEFAULT);

    if ($lob->savefile($_FILES['file']['tmp_name'])) {
        oci_commit($conn);
    }
    else {
        echo "Couldn't upload Blob\n";
    }
    $lob->free();
    oci_free_statement($stmt);

    oci_close($conn); // log off
   }
   elseif ($ext == 'csv') {
    //$myscalarid = 5;
    //$mydate = date('d/M/Y H:i:s');
       
       
    $filename = $_FILES['file']['tmp_name'];
    $file = fopen($filename, "r");
    while(! feof($file)) {
        $line = fgetcsv($file);
        if ($line[0] !== null) {
            $stmt = oci_parse($conn, "INSERT INTO SCALAR_DATA (ID, DATE_CREATED, VALUE) "
                    ."VALUES(:MYSCALARID, to_date(:MYDATE, 'dd/mm/yyyy hh24:mi:ss'), :MYVALUE)");
            oci_bind_by_name($stmt, ':MYSCALARID', $line[0]);
            oci_bind_by_name($stmt, ':MYDATE', $line[1]);
            oci_bind_by_name($stmt, ':MYVALUE', $line[2]);
            oci_execute($stmt, OCI_DEFAULT);
            oci_commit($conn);
            oci_free_statement($stmt);
        }
       }
       fclose($file);
       oci_close($conn); // log off
   }
}
?>



