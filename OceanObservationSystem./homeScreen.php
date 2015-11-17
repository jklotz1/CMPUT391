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
                    <br></br>

                    <input class= "searchbutton" type="submit" name="searchSubmit" value="Search" />
                    <input class= "searchbutton" type="submit" name="resetSubmit" value="Reset" />
                    <br><br>
        </table> 
    </form>





        <?php
        if (isset($_POST['searchSubmit'])) {
           if($startDate != "" && $endDate != "" &&  $endDate > $startDate ){
            
            $sensorTable = OceanDB::getInstance()->sensor_table_results($user);
            
                   
            
       
          
           
            while ($objResult = oci_fetch_array($sensorTable, OCI_BOTH)) {
     
                $sensorID = $objResult["SENSOR_ID"];
         
           
                $objParse1 = OceanDB::getInstance()->get_search_results($_POST["txtKeyword"],$_POST['txtLocation'],$_POST["txtSensorType"] , $sensorID, $_POST["startDate"], $_POST["endDate"]);
        
              
                
        
        
        
        if ($objParse1 != null) {
        
        while ($objResult = oci_fetch_array($objParse1, OCI_BOTH)) {

            ?>
              
             <table  class = "searchResult">

            <tr>

                <td> <div align="center">Sensor ID </div></td>

                <td> <div align="center">Location </div></td>

                <td> <div align="center">Sensor Type </div></td>

                <td> <div align="center">Description </div></td>

                <td> <div align="center">Thumb Nails </div></td>

                <td> <div align="center">Audio Files </div></td>
                
                <td> <div align="center">Scalar Data Value </div></td>

            </tr>

                            <tr>
                             
                                <td><div align="center"><?php echo $objResult["SENSOR_ID"]; ?></div></td>

                                <td><div align="center"><?php echo $objResult["LOCATION"]; ?></td>

                                <td><div align="center"><?php echo $objResult["SENSOR_TYPE"]; ?></td>

                                <td><div align="center"><?php echo $objResult["DESCRIPTION"]; ?></div></td>
                                
                               <td><div align="center">
            <?php
            $sensorID = $objResult["SENSOR_ID"];
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
                                        $sensorID = $objResult["SENSOR_ID"];
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
    

?>
    </table>

</body>
<?php   require_once("Includes/css.php");  ?>
</html>
