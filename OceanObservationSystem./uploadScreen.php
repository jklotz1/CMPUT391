<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Upload Module</title>
    </head>
    <body>
          <?php require_once 'Includes/db.php';
            session_start();
            $user = $_SESSION['user'];
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        <form name="logon" method="post">
            <!--Help button for access to user documentation -->
            <input class="logoutButton" type="submit" value="Help" name="Help">
            <?php
            if (isset($_POST['Help'])) {
                header('Location: userDocumentation.php');
            }
            ?>
            <br><br>
            <input class="logoutButton" type="submit" value="Home"  name="home"> Return to Home Screen
            <br><br>
            <!--return to home screen when "home" button is pressed-->
        <?php if (isset($_POST['home'])) {
            header('Location: homeScreen.php');
        } ?> 
        </form>
        <?php if ($role == 'd') { ?>


            <h1 align="left" style="font-size: 175%">Upload Center</h1>
         

    <?php if (!isset($_FILES['file'])) { ?>
                <!-- For uploading files -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="upload" method="post" enctype="multipart/form-data">
                    <!--Button for choosing file to upload -->
                    Choose File to Upload:<br>
                    <input id="file" type="file" value="Choose File" style="font-size:100%; width:200px; margin:10" name="file"><br>
                    <table>
                        <!-- Description of image/audio file -->
                        <tr><td>Description (For image or audio recording only):</td>
                            <td><input name="description" type="text" maxlength="128" id="description" value="<?php echo $_POST["description"]; ?>"></td></tr>
                        <tr><td>Date Created (For image or audio recording only):</td>
                            <td><input name="date" type="date" id="date" value="<?php echo $_POST["date"]; ?>"></td>
                            <td><input name="time" type="time" id="time" value="<?php echo $_POST["time"]; ?>"></td></tr>
                        <!-- Drop down menu of sensor ids for image/audio file -->
                        <tr><td>Sensor_Id (For image or audio recording only):</td>
                            <td>
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
                    <!-- Button that uploads the file -->
                    <input class="logoutButton" type="submit" value="Upload" style="font-size:100%; width:200px; margin:10 " name="upload"> Upload File<br> 

                </form>
                <?php
            } else {
                //File to be uploaded
                $target_file = ($_FILES['file']['name']);
                //Extension of file to determine what to do with eahc type
                $ext = pathinfo($target_file, PATHINFO_EXTENSION);
                //Jpg image file
                if ($ext == 'jpg') {
                    //Call to funciton for uploading image, image is inserted in this funtion 
                    $result = OceanDB::getInstance()->upload_image($_FILES, $_POST["description"], $_POST["sensorId"], $_POST["date"], $_POST["time"]);
                    //Fetch image data and load to display uploaded image
                    $imageResult = oci_fetch_assoc($result);
                    $image = $imageResult['RECOREDED_DATA']->load();
                    ?>
                    <!-- Image is uploaded -->
                    <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                    <!-- Display uploaded image -->
                    <p><img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" /></p><br>
                    <form name="uploadAnother" method="post">
                        <!--Upload another file -->
                        <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                        <!--return to home screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) {
                        header('Location: uploadScreen.php');
                    } ?>
                    </form>
                    <?php
                }
                //Wav audio file
                elseif ($ext == 'wav') {
                    //Call to funciton for uploading audio, audio file is inserted in this funtion 
                    $result = OceanDB::getInstance()->upload_audio($_FILES, $_POST["description"], $_POST["sensorId"], $_POST["date"], $_POST["time"]);
                    $audioResult = oci_fetch_assoc($result);
                    $audio = $audioResult['RECORDED_DATA']->load();
                    ?>
                    <!-- Audio file is uploaded -->
                    <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                    <p>
                        <!--Upload another file -->
                    <form name="uploadAnother" method="post">
                        <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                        <!--return to home screen when "home" button is pressed-->
                    <?php if (isset($_POST['uploadAnother'])) {
                        header('Location: uploadScreen.php');
                    } ?>
                    </form>
            <?php
        }
        //Scalar data in csv file
        elseif ($ext == 'csv') {
            //Call to funciton for uploading scalar data in batches form csv file, scalar data is inserted in this funtion 
            $result = OceanDB::getInstance()->upload_csv($_FILES);
            ?>
                    <!-- Scalar data is uploaded -->
                    <h1 style="font-size: 115%; color:green">Upload Successful</h1>
                    <!--Display uploaded data -->
                    <p>Scalar Data Uploaded:</p>
            <?php
            foreach ($result as $key => $value) {
                echo "Sensor_Id: " . $value[0] . ", ";
                echo "Date_Created: " . $value[1] . ", ";
                echo "Value: " . $value[2];
                ?><br>
                        <?php
                    }
                    ?>
                    <!--Upload another file -->
                    <form name="uploadAnother" method="post">
                        <input class="logoutButton" type="submit" value="Upload Another File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                        <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['uploadAnother'])) {
                header('Location: uploadScreen.php');
            } ?>
                    </form>
                    <?php
                }
                //If no file was chosen alert user
                elseif ($ext == NULL) {
                    echo "<p style='color:red; font-size: 20px;'>No file chosen </p>";
                    ?>
                    <!--Upload file -->
                    <form name="uploadAnother" method="post">
                        <input class="logoutButton" type="submit" value="Upload File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                        <!--return to home screen when "home" button is pressed-->
                        <?php if (isset($_POST['uploadAnother'])) {
                            header('Location: uploadScreen.php');
                        } ?>
                    </form>
                    <?php
                }
                //If file was the wrong type alert user
                else {
                    echo "<p style='color:red; font-size: 20px;'>Improper file type </p>";
                    ?>
                    <!--Upload proper file -->
                    <form name="uploadAnother" method="post">
                        <input class="logoutButton" type="submit" value="Upload File" style="font-size:100%; width:200px; margin:10" name="uploadAnother">
                        <!--return to home screen when "home" button is pressed-->
                <?php if (isset($_POST['uploadAnother'])) {
                    header('Location: uploadScreen.php');
                } ?>
                    </form>
            <?php
        }
    }
    ?>
<?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied: Not a Data Curator</h1> 
<?php } ?>


    </body>

<?php require_once("Includes/css.php"); ?>
</html>