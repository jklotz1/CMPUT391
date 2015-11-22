<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Upload Module</title>
    </head>
    <body>
         <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help">
                   <?php 
                   if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
                   ?>
        </form>
        <h1 align="left" style="font-size: 175%">Upload Center</h1>
        <?php  
            require_once 'Includes/db.php';
        ?>
        <form name="upload" method="post">
            <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:200px; margin:10" name="home"> Return to Home Screen
            <br><br>
            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
        </form>
        <?php if (!isset($_FILES['file'])) { ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="upload" method="post" enctype="multipart/form-data">
            <input class="uploadButton" type="file" value="Choose File" style="font-size:100%; width:200px; margin:10" name="file"> Choose File to Upload<br>
            <table>
                <tr><td>Description (For image or audio recording only):
                        <input name="description" type="text" maxlength="128" id="description" value="<?php echo $_POST["description"]; ?>"></td></tr>
                <tr><td>Sensor_Id (For image or audio recording only):&nbsp;
                <?php
                $sensor_ids = OceanDB::getInstance()->get_sensor_ids();
                ?>
                <select name="sensorId" id="sensorId">
                <?php
                while ($row = oci_fetch_array($sensor_ids, OCI_BOTH)) {
                    echo '<option>' . htmlspecialchars($row['SENSOR_ID']) . '</option>';
                }
                ?>
                </select></td></tr>
                </table>
                <input class="logoutButton" type="submit" value="Upload" style="font-size:100%; width:200px; margin:10 " name="upload"> Upload File<br> 
                    
        </form>
        <?php
        }
        else {
            $target_file = ($_FILES['file']['name']);
            $ext = pathinfo($target_file, PATHINFO_EXTENSION);
        
            if ($ext == 'jpg') {
                $result = OceanDB::getInstance()->upload_image($_FILES, $_POST["description"], $_POST["sensorId"]);
                $imageResult = oci_fetch_assoc($result);
                $image = $imageResult['RECOREDED_DATA']->load();
                ?>
                <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                <p><img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" /></p><br>
                <form name="uploadAnother" method="post">
                    <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                    <!--return to upload screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) { header('Location: uploadScreen.php'); }?>
                </form>
            <?php
            }
            elseif ($ext == 'wav') {
                $result = OceanDB::getInstance()->upload_audio($_FILES, $_POST["description"], $_POST["sensorId"]);
                $audioResult = oci_fetch_assoc($result);
                $audio = $audioResult['RECORDED_DATA']->load();
                ?>
                <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                <p><audio controls>
                    <source src='<?= $audio?>' type="audio/wav">
                    Your browser does not support the audio element.
                    </audio></p>
                <form name="uploadAnother" method="post">
                    <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                    <!--return to upload screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) { header('Location: uploadScreen.php'); }?>
                </form>
            <?php
            }
            elseif ($ext == 'csv') {
                $result = OceanDB::getInstance()->upload_csv($_FILES);
                ?>
                <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                <p>Scalar Data Uploaded:</p>
                <?php foreach($result as $key => $value) {
                    echo "Sensor_Id: ". $value[0] .", ";
                    echo "Date_Created: ". $value[1] .", ";
                    echo "Value: ". $value[2];?><br>
                <?php
                }               
                ?>
                <form name="uploadAnother" method="post">
                    <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                    <!--return to upload screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) { header('Location: uploadScreen.php'); }?>
                </form>
            <?php
            }
            else {
                echo "No file chosen or improper file type\n";
                ?>
                <form name="uploadAnother" method="post">
                    <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                    <!--return to upload screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) { header('Location: uploadScreen.php'); }?>
                </form>
            <?php
            }
        }
        ?>
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>