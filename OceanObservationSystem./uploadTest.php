<HTML>
<HEAD><TITLE>Store binary data into SQL Database</TITLE></HEAD>
<BODY>
<?php
        if ($submit) {
                $data = (fread(fopen($form_data, "r"), filesize($form_data)));
                        //You have to pass Appropriate username, Password,Serveice name
                        $c = oci_connect('sjpartri', 'letmein22');
                        
                        $myblobid = 123;
                        
                        $stmt = oci_parse($c,'insert into upTest (id, image) values(:myblobid, :blobdata)');
                        $lob = oci_new_descriptor($c, OCI_D_LOB);
                        oci_bind_by_name($stmt,':MYBLOBID',$myblobid);
                        oci_bind_by_name($stmt,":BLOBDATA",$lob,-1,OCI_B_BLOB);
                        $lob->writeTemporary($data);
                        oci_execute($stmt, OCI_DEFAULT);
                        oci_commit($c);
                        $lob->close();
        } else {
?>
    <form method="post" action="uploadTest.php" enctype="multipart/form-data">
    File Description:<br>
    <input type="text" name="id"  size="40">
    <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000">
    <br>File to upload/store in database:<br>
    <input type="file" name="form_data"  size="40">
    <p><input type="submit" name="submit" value="submit">
    </form>
<?php
}
?>
</BODY>
</HTML>



