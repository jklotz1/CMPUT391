<?php
$dbHost="gwynne.cs.ualberta.ca:1521:crs"; 
$dbUsername="sjpartri";
$dbPassword="letmein22";

if (!isset($_FILES['lob_upload'])) {
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" 
   enctype="multipart/form-data">
Image filename: <input type="file" name="lob_upload">
<input type="submit" value="Upload">
</form>


<?php
}
else {
   $target_file = ($_FILES['lob_upload']['name']);
   $ext = pathinfo($target_file, PATHINFO_EXTENSION);
   
   if ($ext == 'jpg') {

       
    $myblobid = 4;
    $mydescription = 'desc';
    $mydate = date('d M Y H:i:s');
    $conn = oci_connect('sjpartri', 'letmein22');
        if (!$conn) {
            $m = oci_error();
            exit('Connect Error' . $m['message']);

        }


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
    $stmt = oci_parse($conn, "INSERT INTO IMAGES (IMAGE_ID, DATE_CREATED, DESCRIPTION, RECOREDED_DATA) "
         ."VALUES(:MYBLOBID, to_date(:MYDATE, 'dd Mon yyyy hh24:mi:ss'), :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECOREDED_DATA INTO :RECOREDED_DATA");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_bind_by_name($stmt, ":MYDATE", $mydate);
    oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
    oci_bind_by_name($stmt, ':RECOREDED_DATA', $lob, -1, OCI_B_BLOB);
    oci_execute($stmt, OCI_DEFAULT);

    if ($lob->savefile($_FILES['lob_upload']['tmp_name'])) {
        oci_commit($conn);
    }
    else {
        echo "Couldn't upload Blob\n";
    }
    $lob->free();
    oci_free_statement($stmt);
  
    $stmt = oci_parse($conn, "UPDATE IMAGES SET THUMBNAIL = RECOREDED_DATA WHERE IMAGE_ID = :MYBLOBID");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_execute($stmt);
    oci_free_statement($stmt);
  
  
  
 
    // Now query the uploaded BLOB and display it

    $query = 'SELECT THUMBNAIL FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

    $stmt = oci_parse ($conn, $query);
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_execute($stmt, OCI_DEFAULT);
    $arr = oci_fetch_assoc($stmt);
    $result = $arr['THUMBNAIL']->load();
  
    header("Content-type: image/JPEG");
    echo $result;
  
    oci_free_statement($stmt);
  
  

    oci_close($conn); // log off
   }
   elseif ($ext == 'wav') {
       
    $myblobid = 4;
    $mydescription = 'desc';
    $mydate = date('d M Y H:i:s');
       
    $conn = oci_connect('sjpartri', 'letmein22');
    if (!$conn) {
        $m = oci_error();
        exit('Connect Error' . $m['message']);

        }


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
    $stmt = oci_parse($conn, "INSERT INTO AUDIO_RECORDINGS (RECORDING_ID, DATE_CREATED, DESCRIPTION, RECOREDED_DATA) "
         ."VALUES(:MYBLOBID, to_date(:MYDATE, 'dd Mon yyyy hh24:mi:ss'), :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECOREDED_DATA INTO :RECOREDED_DATA");
    oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
    oci_bind_by_name($stmt, ":MYDATE", $mydate);
    oci_bind_by_name($stmt, ':MYDESCRIPTION', $mydescription);
    oci_bind_by_name($stmt, ':RECOREDED_DATA', $lob, -1, OCI_B_BLOB);
    oci_execute($stmt, OCI_DEFAULT);

    if ($lob->savefile($_FILES['lob_upload']['tmp_name'])) {
        oci_commit($conn);
    }
    else {
        echo "Couldn't upload Blob\n";
    }
    $lob->free();
    oci_free_statement($stmt);

    oci_close($conn); // log off
   }
}
?>



