<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <?php session_start(); $user = $_SESSION['user'] ?> 
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <h1 align="center" style="font-size: 200%">Welcome <?php echo $user; ?> <input  type="submit"  value="Log Out" name="Log Out" style="float: right;"/></h1>
        
    </head>
    <body>
        
       <form name="homeNavigation" method="post">
       <table align="center" width="1000" border="2">  
           <tr>
               <th><input type="submit" value="Search Module" style="width:125px; margin:10 " name="searchModule"></th>
               <th><input type="submit" value="Managment Module" style="width:125px; margin:10 " name="managementModule"></th>
               <th><input type="submit" value="Upload Module" style="width:125px; margin:10 " name="uploadModule"></th>
               <th><input type="submit" value="Data Analysis Module" style="width:150px; margin:10 " name="dataAnalysisModule"></th>
           </tr>
       </table>
           <?php if (isset($_POST['managementModule'])) { header('Location: managementScreen.php'); }?> 
           
           
       </form>
     
        <?php if (isset($_POST['searchModule'])) { ?> 
        <form name="searchCriteria" method="post" >
            <table  align="center" width="500" border= "5">
              
                    
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
   
               
        <form name="OceanSearch"  method="get" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" >
              <table width="1000" border="1" align="center" >  
                  <h1 align="center" style="font-size: 200%">Search Criteria</h1>
                  <?php if (isset($_POST['keywords']) && 
                        $_POST['keywords'] == 'true') { ?> 
                    <tr>  
                        <th>KeyWords:
                        <input name="txtKeyword" type="text" id="txtKeyword" value="<?php echo $_GET["txtKeyword"];?>">
                       
                    </th>
                  </tr>
                  <?php } ?>
                  
                  <?php if (isset($_POST['sensorType']) && 
                  $_POST['sensorType'] == 'true') { ?> 
                  <tr>
                  <th>
                   Sensor Type:
                   <input name="txtSensorType" type="text" id="txtSensorType" value="<?php echo $_GET["txtSensorType"];?>">
                  </th>
                  </tr>
                  <?php } ?>
                  
                  <?php if (isset($_POST['location']) && 
                  $_POST['location'] == 'true') { ?> 
                  <tr>
                  <th>Location:
                  <input name="txtLocation" type="text" id="txtLocation" value="<?php echo $_GET["txtLocation"];?>">
                 
                  </th>
                  </tr>
                  <?php } ?>
                  <tr>
                  <th>Time Period:
                  
                  <br></br>
                  
                  <input type="submit" name="searchSubmit" value="Search" />
                  <input type="submit" name="resetSubmit" value="Reset" />
               </table> 
        </form>
                <?php
                    require_once("Includes/db.php");
                        $here = 'here';
                    if($_GET["txtKeyword"] != "")
                    {

                        $objParse = OceanDB::getInstance()->get_keyword_search_results($_GET['txtKeyword']);
                    }
                    
                    if($_GET["txtSensorType"] != "")
                    {

                        $objParse = OceanDB::getInstance()->get_sensor_type_search_results($_GET['txtSensorType']);
                    }
                    
                      if($_GET["txtLocation"] != "")
                    {

                        $objParse = OceanDB::getInstance()->get_location_results($_GET['txtLocation']);
                    }
                    
                ?>
               
                <?php if (isset($_GET['searchSubmit'])) { ?> 
                <table  align="center" width="1000" border="1">
                    
                    <tr>

                    <th width="91"> <div align="center">Sensor ID </div></th>

                    <th width="98"> <div align="center">Location </div></th>

                    <th width="198"> <div align="center">Sensor Type </div></th>

                    <th width="97"> <div align="center">Description </div></th>
                    
                    <th width="97"> <div align="center">Media Data </div></th>
                    
                    <th width="97"> <div align="center">Scalar Data </div></th>
                    
                    </tr>
                <?php } ?>
        
                <?php
                if ($objParse != null)
                while($objResult = oci_fetch_array($objParse,OCI_BOTH) ){
                ?>
                    
                <tr>

                <td><div align="center"><?php echo $objResult["SENSOR_ID"];?></div></td>

                <td><?php echo $objResult["LOCATION"];?></td>

                <td><?php echo $objResult["SENSOR_TYPE"];?></td>

                <td><div align="center"><?php echo $objResult["DESCRIPTION"];?></div></td>

                </tr>
            <?php
                }
            ?>
         </table>
 
    </body>
</html>
