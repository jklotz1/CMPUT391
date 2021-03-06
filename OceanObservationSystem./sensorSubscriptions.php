<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
CMPUT 391 Project
Code Belongs to: Sean Partridge, Jessica Klotz, Brennan Stang
-->
<html>
    <?php
    require_once("Includes/db.php");
    session_start();
    $user = $_SESSION['user']
    ?> 
    <head>
        <meta charset="UTF-8">
        <title>Subscribe To Sensors</title>
    <h1 align="left" style="font-size: 150%">Current Subscription(s) Of User <span style='color:red;'><?php echo $user; ?></span> : </h1> 
</head>
<body>
    <form name="SubscriptionSensorList" method="post">     
        <input class="logoutbutton" type="submit" value="Back" style="font-size:100%; width:100px; margin:10 " name="back"> 
        <br>
        <input class="logoutbutton" type="submit" value="Home" style="font-size:100%; width:100px; margin:10 " name="home">

        <!-- Get a list of all the subscriptions of user -->
        <?php
        if (isset($_POST['back'])) {
            header('Location: mainSubscriptionScreen.php');
        }
        if (isset($_POST['home'])) {
            header('Location: homeScreen.php');
        }


        $data = array();
        $sensorTable = OceanDB::getInstance()->sensor_table_results($user);
        while ($res = oci_fetch_array($sensorTable, OCI_BOTH)) {
            $data[] = $res;
        }
        $cnt = 0;

        $num = count($data);

        if ($num > 0) {
            ?> 

            <!-- Populate the table -->
            <table  class="searchResult">
                <tr>

                    <td> <div align="center">Sensor Type </div></td>

                    <td> <div align="center">Location </div></td>

                    <td> <div align="center">Description </div></td>

                    <td> <div align="center">Subscription </div></td>

                </tr> 
                <?php
                $sensorTable = OceanDB::getInstance()->sensor_table_results($user);
                $cnt = 0;
                while ($objResult = oci_fetch_array($sensorTable, OCI_BOTH)) {
                    $sensorInfo = OceanDB::getInstance()->get_subscription_details($objResult["SENSOR_ID"]);



                    while ($objResult1 = oci_fetch_array($sensorInfo, OCI_BOTH)) {
                        ?>

                        <tr>
                            <td><div align="center"><?php echo $objResult1["SENSOR_TYPE"]; ?></div></td>

                            <td><div align="center"><?php echo $objResult1["LOCATION"]; ?><div></td>

                                        <td><div align="center"><?php echo $objResult1["DESCRIPTION"]; ?></div></td>

                                        <?php
                                        echo "<td><div align='center'><input class='downloadbutton' type='submit' name='UnSubscribe$cnt' value='UnSubscribe' style='horizontal-align: middle;'/></div></td>";
                                        if (isset($_POST["UnSubscribe$cnt"])) {

                                            OceanDB::getInstance()->delete_subscription($objResult1["SENSOR_ID"], $user);
                                            header("Refresh:0");
                                        }
                                        $cnt++;
                                        ?>

                                        </tr>

                                        <!-- Display message if the user has no subscriptions -->
                                        <?php
                                    }
                                }
                            } else {
                                echo "<p style='color:red;'>CURENTLY NOT SUBSCRIBED TO ANY SENSOR<p>";
                            }
                            ?>
                            </table>
                            </form>
                                    <?php require_once("Includes/css.php"); ?>
                            </html>
