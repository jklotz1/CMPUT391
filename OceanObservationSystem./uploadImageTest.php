<?php

$myblobid = 1;
$mydescription = 'desc';
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
  //$mydate = date("Y-m-d");
  $lob = oci_new_descriptor($conn, OCI_D_LOB);
  $stmt = oci_parse($conn, 'INSERT INTO IMAGES (IMAGE_ID, DESCRIPTION, RECOREDED_DATA) '
         .'VALUES(:MYBLOBID, :MYDESCRIPTION, EMPTY_BLOB()) RETURNING RECOREDED_DATA INTO :RECOREDED_DATA');
  oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
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

  // Now query the uploaded BLOB and display it

  $query = 'SELECT RECOREDED_DATA FROM IMAGES WHERE IMAGE_ID = :MYBLOBID';

  $stmt = oci_parse ($conn, $query);
  oci_bind_by_name($stmt, ':MYBLOBID', $myblobid);
  oci_execute($stmt, OCI_DEFAULT);
  $arr = oci_fetch_assoc($stmt);
  $result = $arr['RECOREDED_DATA']->load();

  // If any text (or whitespace!) is printed before this header is sent,
  // the text won't be displayed and the image won't display properly.
  // Comment out this line to see the text and debug such a problem.
  header("Content-type: image/JPEG");
  echo $result;

  oci_free_statement($stmt);

  oci_close($conn); // log off
}
?>



