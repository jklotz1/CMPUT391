<?php

$myblobid = 1;
$dbHost="gwynne.cs.ualberta.ca:1521:crs"; 
$dbUsername="sjpartri";
$dbPassword="letmein22";

if (!isset($_FILES['lob_upload'])) {
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" 
   enctype="multipart/form-data">
Audio filename: <input type="file" name="lob_upload">
<input type="submit" value="Upload">
</form>


<?php
}
else {

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
  $stmt = oci_parse($conn, 'INSERT INTO AUDIO_RECORDINGS (RECORDING_ID, RECORDED_DATA) '
         .'VALUES(:MYBLOBID, EMPTY_BLOB()) RETURNING RECORDED_DATA INTO :RECORDED_DATA');
  oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
  oci_bind_by_name($stmt, ':RECORDED_DATA', $lob, -1, OCI_B_BLOB);
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
?>

