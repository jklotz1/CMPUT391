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
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <h1 align="center" style="font-size: 200%">Welcome <?php echo $user; ?> 
 
    </h1>

</head>
<body>

    <!-- Navigation ToolBar -->
    <form name="homeNavigation" method="post">

        <input class="logoutButton" type="submit"  value="Log Out" name="LogOut"/>
        <br></br>
       
        <table class="CSSTableGenerator">  
            <tr>
                <th><input class="navigationbutton" type="submit" value="Search Module" style="margin:10 " name="searchModule"></th>
                <th><input class="navigationbutton" type="submit" value="Management Module" style="margin:10 " name="managementModule"></th>
                <th><input class="navigationbutton" type="submit" value="Upload Module" style="margin:10 " name="uploadModule"></th>
                <th><input class="navigationbutton" type="submit" value="Data Analysis Module" style=" margin:10 " name="dataAnalysisModule"></th>
                <th><input class="navigationbutton" type="submit" value="Subscribe Module" style="margin:10 " name="subscribeModule"></th>
            </tr>
        </table>
        <?php
        
        if (isset($_POST['managementModule'])) {
            header('Location: managementScreen.php');
            
        }

        if (isset($_POST['dataAnalysisModule'])) {
            header('Location: analysisScreen.php');
            
        }

        if (isset($_POST['subscribeModule'])){
            header('Location: mainSubscriptionScreen.php');
              exit();
        }
        if(isset($_POST['LogOut'])){
             unset($_SESSION['user']);
             $_SESSION['user'] = "false";
             header('Location: loginScreen.php');
               exit();
        }
        
        ?> 
    

    </form>

    <!-- Search Module -->
<?php if (isset($_POST['searchModule'])) { ?> 
        <form name="searchCriteria" method="post" >
            <table class="searchTable">


                <th><p>Search By:</p></th>
                <td>
                    <input type="checkbox" name="keywords" value="true" />Keywords<br />
                    <input type="checkbox" name="sensorType" value="true"/>Sensor Type<br />
                    <input type="checkbox" name="location" value="true" />Location<br />
                    <input type="submit" name="formSubmit" value="Submit" style="horizontal-align: middle;"/>
                </td>


            </table>
        </form>
 
    <?php } ?>

    <!-- Error Checking for Search Criteria -->
    <?php
    require_once("Includes/db.php");
    if (isset($_POST['searchSubmit'])) {
        $startDate = strtotime($_POST["startDate"]);
        $endDate = strtotime($_POST["endDate"]);

        //Check for valid start date
        if ($startDate == "") {
            $startDateErr = "Start Date is required";
        } else {
            $startDateErr = "";
        }
        //Check for valid End Date
        if ($endDate == "") {
            $endDateErr = "End Date is required";
        } else {
            $endDateErr = "";
        }
        //Check for valid date range
        if ($startDate != "" && $endDate != "" &&  $endDate < $startDate) {
            $invalidDateError = "Invalid Data Range (Start Date must begin before End Date)";
        }else{
            $invalidDateError = "";
        }
        
    }
    
    ?>
    
     

    <!-- Search Criteria Form -->
    <form name="OceanSearch"  method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" >
        <table class="CSSTableGenerator" width="1000" >  
            <h1 align="center" style="font-size: 200%">Search Criteria</h1>
            <?php
            if (isset($_POST['keywords']) &&
                    $_POST['keywords'] == 'true') {
                ?> 
                <tr>  
                    <th>KeyWords:
                        <input name="txtKeyword" type="text" id="txtKeyword" value="<?php echo isset($_POST['searchSubmit']) ? $_POST["txtKeyword"] : ''; ?>">

                    </th>
                </tr>
            <?php } ?>

            <?php
            if (isset($_POST['sensorType']) &&
                    $_POST['sensorType'] == 'true') {
                ?> 
                <tr>
                    <th>
                        Sensor Type:
                        <input name="txtSensorType" type="text" id="txtSensorType" value="<?php echo $_POST["txtSensorType"]; ?>">
                    </th>
                </tr>
            <?php } ?>

            <?php
            if (isset($_POST['location']) &&
                    $_POST['location'] == 'true') {
                ?> 
                <tr>
                    <th>Location:
                        <input name="txtLocation" type="text" id="txtLocation" value="<?php echo $_POST["txtLocation"]; ?>">

                    </th>
                </tr>
<?php } ?>
            <tr>
                <th>Time Period:
                    <p>Start Date:
                        <input type="date" name="startDate" id="txtStartDate" value="<?php echo $_POST["txtStartDate"]; ?>" />
                        <span class="error"> * <?php echo $startDateErr; ?></span>



                        End Date:<input type="date" name="endDate" value="" />
                        <span class="error"> * <?php echo $endDateErr; ?></span>
                    <p><span class="error">  <?php echo $invalidDateError; ?></span></p>
                    <br>

                   
            </tr>
            <tr>
                <th> Optional Exact Time:
                    <p>Start Time:
                    <input type="time" name="startTime" id="txtStartTime" value="<?php echo $_POST["txtStartTime"]; ?>" />
                 
                End Time:
                    <input type="time" name="endTime" id="txtEndTime" value="<?php echo $_POST["txtEndTime"]; ?>" />
                    <br><br><br>
                    
                     <input class= "searchbutton" type="submit" name="searchSubmit" value="Search" />
                    <input class= "searchbutton" type="submit" name="resetSubmit" value="Reset" />
                
            </tr>
        </table> 
    </form>


      


        <?php
        if (isset($_POST['searchSubmit'])) {
           if($startDate != "" && $endDate != "" &&  $endDate > $startDate ){
            
            $sensorTable = OceanDB::getInstance()->sensor_table_results($user);
            
                   
            
       
          
           
            while ($objResult = oci_fetch_array($sensorTable, OCI_BOTH)) {
     
                $sensorID = $objResult["SENSOR_ID"];
                $sensorID_data[] = $sensorID;
            }
            
            if(count($sensorID_data) == 0){
                 echo "<br><p style='color:red;'>YOU ARE NOT SUBSCRIBED TO ANY SENSORS. PLEASE VISIT THE SUBSCRIBE PAGE.<p>"; 
            }else{
           
               for ($i = 0; $i <= count($sensorID_data); $i++) {
                   $sensorID = $sensorID_data[$i];
                   
                   $thumbnails = OceanDB::getInstance()->get_thumbnail($sensorID, $_POST["startDate"], $_POST["endDate"], $_POST["startTime"],$_POST["endTime"]);
                 
                   $audioDates = OceanDB::getInstance()->get_audioInfo($sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);
                   $scalarData = OceanDB::getInstance()->get_scalar_data_values($sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);
                   $objParse1 = OceanDB::getInstance()->get_search_results($_POST["txtKeyword"],$_POST['txtLocation'],$_POST["txtSensorType"] , $sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);
                   
                   while($result= oci_fetch_array($objParse1, OCI_BOTH)){
                   $sensors_content[] =  $result;
                   }
             
                   while($result = oci_fetch_array($thumbnails, OCI_BOTH)){
                       
                        $thumbnails_content[$i] = $result;
                   }
                   
                   $audioDates_content[$i] = $audioDates;
                   $scalarData_content[$i] = $scalarData;
                   
               }
           
               
               if( count($thumbnails_content) == 0 ){
                    echo "<br><p style='color:red;'>NO SEARCH RESULTS FOUND<p>"; 
               }else{
                   
               ?>
                   <table  class = "searchResult">
                            <tr>

                                <td> <div align="center">Sensor ID </div></td>

                                <td> <div align="center">Date Created </div></td>

                                <td> <div align="center">Description </div></td>

                                <td> <div align="center">Media/Value </div></td>

                            </tr>
                   
                    <?php
                     for ($k = 0; $k < count($sensors_content); $k++) {    
                         //while ($objResult = oci_fetch_array($sensors_content[$k], OCI_BOTH)){
                               //$sensorID = $objResult["SENSOR_ID"];
                         $sensorID=$sensors_content[$k]["SENSOR_ID"];
                    
                         
                //Images Row
                if(count($thumbnails_content) != 0){
                    
                     
                     $thumbnails = OceanDB::getInstance()->get_thumbnail($sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);
            while ($thumbResult = oci_fetch_array($thumbnails, OCI_BOTH)) {
                $result = $thumbResult['THUMBNAIL']->load();
               
                ?>
                            <tr>
                                
                                <td><div align="center"><?php echo $sensors_content[$k]["SENSOR_ID"]; ?></div></td>

                                <td><div align="center"><?php echo $thumbResult['DATE_CREATED'];  ?></div></td>

                                <td><div align="center"><?php echo $thumbResult['DESCRIPTION']; ?></div></td>

                                <td><div align="center">
                                        <p><h3>THUMBNAIL:</h3></p>
                                                <p><img src="data:image/jpeg;base64,<?php echo base64_encode($result); ?>" />
                                                    <br>
                                                    
                                                    <br>
                                                    <input class="downloadbutton" type="button" value="Download" name="Download" />
                                                    <BR>
                                                 
                                                </p>
                          
                <?php }}
                ?>
                                    </div>
                                                </td>
                            </tr>
                                               
                                                
                                
                <?php                                
                 //Audo Row
                if(count($audioDates_content) != 0){
                  
                     
                $audioDates = OceanDB::getInstance()->get_audioInfo($sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);

                                        while ($audioDate = oci_fetch_array($audioDates, OCI_BOTH)) {
                                            ?>
                            <tr>
                                <td><div align="center"><?php echo $sensors_content[$k]["SENSOR_ID"]; ?></div></td>

                                <td><div align="center"><?php echo $audioDate["DATE_CREATED"]; ?></div></td>

                                <td><div align="center"><?php echo $audioDate["DESCRIPTION"]; ?></div></td>

                                <td><div align="center">
                                                 <p><h3>AUDIO RECORDING:</h3></p>
                                                   <medium>
                                                      RECORDING ID:
                                                 <?php echo $audioDate["RECORDING_ID"]; ?>
                                                   </medium>
                                                    
                                                    <br>
                                                    <input class="downloadbutton" type="button" value="Download" name="Download" />
                                                </p>
                          
                <?php }}
                ?>
                                    </div>
                                                </td>   
                            </tr>
                                               
                            <?php    
                           //Scalar Row
                              if(count($scalarData_content) != 0){ 
                                   
                                 $scalarData = OceanDB::getInstance()->get_scalar_data_values($sensorID, $_POST["startDate"], $_POST["endDate"],$_POST["startTime"],$_POST["endTime"]);
                                    while ($sensorResult = oci_fetch_array($scalarData, OCI_BOTH)) {
                                      ?>
                            <tr>
                                        <td><div align="center"><?php echo $sensors_content[$k]["SENSOR_ID"]; ?></div></td>

                                <td><div align="center"><?php echo $sensorResult["DATE_CREATED"];?></div></td>

                                <td><div align="center"></div></td>

                                <td><div align="center">
                                         <p><h3>SCALAR DATA:</h3></p>
                                  
                                            VALUE:
                                     <?php echo $sensorResult["VALUE"];
                                           echo '<br>';
                                    } ?>
                                            
                                                   </div>
                                </td>
                            </tr>
            <?php
                              }
                     
                     }
            }
                     }
           }
        }
        
        ?>
        
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
        <?php
        /*
        if ($data > 0) {
            ?>
   
            <?php
        while ($objResult = oci_fetch_array($objParse1, OCI_BOTH)) {
                                $sensorID = $objResult["SENSOR_ID"];
                                echo ($sensorID);
                                $thumbnails1 = OceanDB::getInstance()->get_thumbnail($sensorID, $_POST["startDate"], $_POST["endDate"]);
                                $audioDates1 = OceanDB::getInstance()->get_audioInfo($sensorID,  $_POST["startDate"], $_POST["endDate"]);
                                $scalarData1 = OceanDB::getInstance()->get_scalar_data_values($sensorID, $_POST["startDate"], $_POST["endDate"]);
                                
                              
                  
                           
                                while ($res = oci_fetch_array($thumbnails1, OCI_BOTH)) {
                                    $data[] = $res;
                                 
                                }
                        
                                $thumbnail_count = count($data);
                                echo($thumbnail_count);
                                
                                  while ($res = oci_fetch_array($audioDates1, OCI_BOTH)) {
                                    $data[] = $res;
                                 
                                }
                                
                                $audioDates_count = count($data);
                                
                                
                            
                               // while ($res = oci_fetch_array($thumbnails, OCI_BOTH)) {
                                  //  $data[] = $res;
                              //  }
                             //   $audio_count = count($data);
                                
                                if($audioDates_count == 0 ){
                                    echo 'hello';
                                }else{

            ?>
            
                            <tr>
                             
                                <td><div align="center"><?php echo $objResult["SENSOR_ID"]; ?></div></td>

                                <td><div align="center"><?php echo $objResult["LOCATION"]; ?></div></td>

                                <td><div align="center"><?php echo $objResult["SENSOR_TYPE"]; ?></div></td>

                                <td><div align="center"><?php echo $objResult["DESCRIPTION"]; ?></div></td>
                                   <td><div align="center">
                    
            <?php
        

            $thumbnails = OceanDB::getInstance()->get_thumbnail($sensorID, $_POST["startDate"], $_POST["endDate"]);
            while ($thumbResult = oci_fetch_array($thumbnails, OCI_BOTH)) {
                $result = $thumbResult['THUMBNAIL']->load();
                ?>
                                      
                                                <p><img src="data:image/jpeg;base64,<?php echo base64_encode($result); ?>" />
                                                    <br>
                                                     <small>
                                                    <?php echo $thumbResult['DATE_CREATED']; ?>
                                                    </small>
                                                    <br>
                                                    <input class="downloadbutton" type="button" value="Download" name="Download" />
                                                    <BR>
                                                 
                                                </p>
                                                
            <?php } ?>
                                </td>
                                <td><div align="center">
                                        <?php
                                     
                                        $audioDates = OceanDB::getInstance()->get_audioInfo($sensorID,  $_POST["startDate"], $_POST["endDate"]);

                                        while ($audioDate = oci_fetch_array($audioDates, OCI_BOTH)) {
                                            ?>
                                                <p> <?php echo $audioDate["DATE_CREATED"]; ?>
                                                    <input class="downloadbutton" type="button" value="Download" name="Download" />
                                                </p>
                                            <?php } ?>
                                    
                                </td>
                                <td>
                                         
                                    <?php
                                   
                                 $scalarData = OceanDB::getInstance()->get_scalar_data_values($sensorID, $_POST["startDate"], $_POST["endDate"]);
                                    while ($sensorResult = oci_fetch_array($scalarData, OCI_BOTH)) {
                                      
                                        
                                        echo $sensorResult["DATE_CREATED"];
                                        ?>
                                            - VALUE:
                                     <?php echo $sensorResult["VALUE"];} ?>
                                
                                </td>
                            </tr>
            <?php
        }
        }
        }
            }
    }
    }
    */

?>
         
        
    </table>

</body>
<?php   require_once("Includes/css.php");  ?>
</html>
