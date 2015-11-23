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
        <title>Available Subscriptions</title>
    <h1 align="left" style="font-size: 150%">List of All Non-Subscription(s) : </h1> 
</head>
<body>

    <!-- Navigation Panel-->    
    <form name="NonSubscriptionSensorList" method="post">      
        <input type="submit" class="logoutbutton" value="Back" style="font-size:100%; width:100px; margin:10 " name="back"> 
        <br>
        <input type="submit" class="logoutbutton" value="Home" style="font-size:100%; width:100px; margin:10 " name="home">
        <?php if (isset($_POST['back'])) {
            header('Location: mainSubscriptionScreen.php');
        }
        if (isset($_POST['home'])) {
            header('Location: homeScreen.php');
        }
        ?>


        <!-- Get  an array of non-subscribed sensors -->
        <?php
        $sensorTable1 = OceanDB::getInstance()->sensor_table_results($user);
        $objResult = oci_fetch_all($sensorTable1, $output);
        $data = array();
        $nonSubscription_Results = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($output, $objResult);
        while ($res = oci_fetch_array($nonSubscription_Results, OCI_BOTH)) {
            $data[] = $res;
        }
        $cnt = 0;

        $num = count($data);

        $nonSubscription_Results = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($output, $objResult);

        if ($num > 0) {
            ?>

            <!-- Create and populate the table -->
            <table  class="availableSensorTable">
                <tr>

                    <td> <div align="center">Sensor Type </div></td>

                    <td> <div align="center">Location </div></td>

                    <td> <div align="center">Description </div></td>

                    <td> <div align="center">Subscription </div></td>

                </tr> 

    <?php
    while ($objResult1 = oci_fetch_array($nonSubscription_Results, OCI_BOTH)) {
        ?>


                    <tr>
                        <td><div align="center"><?php echo $objResult1["SENSOR_TYPE"]; ?></div></td>

                        <td><div align="center"><?php echo $objResult1["LOCATION"]; ?><div></td>

                                    <td><div align="center"><?php echo $objResult1["DESCRIPTION"]; ?></div></td>

                                    <?php
                                    echo "<td><div align='center'><input type='submit' class='downloadbutton' name='Subscribe$cnt' value='Subscribe' style='horizontal-align: middle;'/></div></td>";

                                    if (isset($_POST["Subscribe$cnt"])) {

                                        OceanDB::getInstance()->add_subscription($objResult1["SENSOR_ID"], $user);
                                        header("Refresh:0");
                                    }
                                    $cnt++;
                                    ?>


                                    </tr>

                                    <!-- Give message to user if they are subscribed to all the sensors -->
                                    <?php
                                }
                            } else {
                                echo "<p style='color:red;'>CURENTLY SUBSCRIBED TO EVERY SENSOR<p>";
                            }
                            ?>


                            </table>
                            </form>
                            </body>
                            <?php require_once("Includes/css.php"); ?>
                            </html>
