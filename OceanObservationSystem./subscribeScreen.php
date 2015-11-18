<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <?php 
    require_once("Includes/db.php");
    session_start();
    $user = $_SESSION['user']
    ?> 
    <head>
        <meta charset="UTF-8">
    <h1 align="left" style="font-size: 150%">Current Subscription(s) Of User <span style='color:red;'><?php echo $user; ?></span> : </h1> 
    </head>
    <body>
        <form name="SubscriptionSensorList" action="subscribeScreen.php" method="post">     
       
                <table  align="center" width="1000" border="1">
                        <tr>

                            <th width="63"> <div align="center">Sensor Type </div></th>
                
                            <th width="63"> <div align="center">Location </div></th>

                            <th width="63"> <div align="center">Description </div></th>
                
                            <th width="63"> <div align="center">Subscription </div></th>

                        </tr> 
            <?php
                $sensorTable = OceanDB::getInstance()->sensor_table_results($user);
                      $cnt = 0;
                while ($objResult = oci_fetch_array($sensorTable, OCI_BOTH)) {
                    $sensorInfo = OceanDB::getInstance()->get_subscription_details($objResult["SENSOR_ID"]);
             
                if ($sensorInfo != null){
        
                    while ($objResult1 = oci_fetch_array($sensorInfo, OCI_BOTH)) {
           
                     ?>

                <tr>
                    <td><div align="center"><?php echo $objResult1["SENSOR_TYPE"]; ?></div></td>

                    <td><div align="center"><?php echo $objResult1["LOCATION"]; ?><div></td>

                    <td><div align="center"><?php echo $objResult1["DESCRIPTION"]; ?></div></td>
                   
                    <?php
                    echo "<td><div align='center'><input type='submit' name='UnSubscribe$cnt' value='UnSubscribe' style='horizontal-align: middle;'onClick='window.location.reload()'/></div></td>";
                     if (isset($_POST["UnSubscribe$cnt"])) {
                                echo 'hello';
                                OceanDB::getInstance()->delete_subscription($objResult1["SENSOR_ID"], $user);
                                   
                                }
                                $cnt++;
                    ?>
                    
                </tr>
              
            <?php
                }
                
                }else{
                    echo "<p style='color:red;'>CURRENTLY NOT SUBSCRIBED TO ANY SENSORS!<p>";
                }
                   
                }
            ?>
               </table>
               </form>
        
              <form name="NonSubscriptionSensorList" action="subscribeScreen.php" method="post">      
          
                    
                <h1 align="left" style="font-size: 150%">List of All Non-Subscription(s) : </h1> 
        
        <table  align="center" width="1000" border="1">

            <tr>

                <th width="63"> <div align="center">Sensor Type </div></th>
                
                <th width="63"> <div align="center">Location </div></th>

                <th width="63"> <div align="center">Description </div></th>
                
                <th width="63"> <div align="center">Subscription </div></th>

            </tr> 
            
        <?php
       
                  $sensorTable1 = OceanDB::getInstance()->sensor_table_results($user);
                  $objResult = oci_fetch_all($sensorTable1, $output);
                  var_dump($output);
                  var_dump($objResult);
                  $nonSubscription_Results = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($output, $objResult);
                  var_dump($nonSubscription_Results);
                   $cnt = 0;
                
               
                   
                    //$sensorInfo = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($sensorResultTable);
                    
   
              
                    while ($objResult1 = oci_fetch_array($nonSubscription_Results, OCI_BOTH)) {
           
                     ?>

                <tr>
                    <td><div align="center"><?php echo $objResult1["SENSOR_TYPE"]; ?></div></td>

                    <td><div align="center"><?php echo $objResult1["LOCATION"]; ?><div></td>

                    <td><div align="center"><?php echo $objResult1["DESCRIPTION"]; ?></div></td>
                    
                   <?php
                   echo "<td><div align='center'><input type='submit' name='Subscribe$cnt' value='Subscribe' style='horizontal-align: middle;' onClick='window.location.reload()'/></div></td>";
                     
                            if (isset($_POST["Subscribe$cnt"])) {
                                echo 'hello';
                                OceanDB::getInstance()->add_subscription($objResult1["SENSOR_ID"], $user);
                                   
                                }
                                $cnt++;
                   ?>
                    
                    
                </tr>
                 
            <?php
             
                }
               
            
                
            ?>
          </table>   
</form>
                </body>
</html>
