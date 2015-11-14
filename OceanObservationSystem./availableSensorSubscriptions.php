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
           <h1 align="left" style="font-size: 150%">List of All Non-Subscription(s) : </h1> 
    </head>
    <body>

        
        <form name="NonSubscriptionSensorList" method="post">      
              <input type="submit" value="Back" style="font-size:100%; width:200px; margin:10 " name="back"> 
        <?php if (isset($_POST['back'])) { header('Location: mainSubscriptionScreen.php'); }?> 


        
            <?php
                  $sensorTable1 = OceanDB::getInstance()->sensor_table_results($user);
                  $objResult = oci_fetch_all($sensorTable1, $output);
                 
                  $nonSubscription_Results = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($output, $objResult);

                   $cnt = 0;
                
                
                   
                    //$sensorInfo = OceanDB::getInstance()->get_non_subcribed_sensor_table_results($sensorResultTable);
                    
                    
                   if(sizeof($objResult1) > 1){
                       ?>
                       <table  align="center" width="1000" border="1">
                    <tr>

                        <th width="63"> <div align="center">Sensor Type </div></th>
                
                        <th width="63"> <div align="center">Location </div></th>

                        <th width="63"> <div align="center">Description </div></th>
                
                        <th width="63"> <div align="center">Subscription </div></th>

                    </tr> 
                    <?php
                    while ($objResult1 = oci_fetch_array($nonSubscription_Results, OCI_BOTH)) {
                        
                     ?>

                <tr>
                    <td><div align="center"><?php echo $objResult1["SENSOR_TYPE"]; ?></div></td>

                    <td><div align="center"><?php echo $objResult1["LOCATION"]; ?><div></td>

                    <td><div align="center"><?php echo $objResult1["DESCRIPTION"]; ?></div></td>
                    
                   <?php
                   echo "<td><div align='center'><input type='submit' name='Subscribe$cnt' value='Subscribe' style='horizontal-align: middle;'/></div></td>";
                     
                            if (isset($_POST["Subscribe$cnt"])) {
                             
                                OceanDB::getInstance()->add_subscription($objResult1["SENSOR_ID"], $user);
                                header("Refresh:0");
                                }
                                $cnt++;
                   ?>
                    
                    
                </tr>
                 
            <?php
             
                }
                }else{
                    echo "<p style='color:red;'>CURRENTLY SUBSCRIBED TO EVERY SENSOR<p>";
                }
               
            
                
            ?>
        
       
                </table>
</form>
    </body>
</html>
