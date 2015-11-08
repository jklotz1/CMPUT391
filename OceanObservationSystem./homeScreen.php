<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php session_start(); $user = $_SESSION['user'] ?> 
        Hello <?php echo $user; ?>
        <form name="OceanSearch" method="get" action="<?php echo $_SERVER['SCRIPT_NAME'];?>">
              <table width="599" border="1">  
                  <tr>  
                  <th>Search by KeyWords:
                  <input name="txtKeyword" type="text" id="txtKeyword" value="<?php echo $_GET["txtKeyword"];?>">
                  <input type="submit" value="Search"></th>
                  </tr>  
               </table> 
        </form>
                <?php
                    require_once("Includes/db.php");
                 
                    if($_GET["txtKeyword"] != "")
                    {
                        $objParse = OceanDB::getInstance()->get_keyword_search_results($_GET['txtKeyword']);
                    }
                ?>
                <table width="600" border="1">
                    
                    <tr>

                    <th width="91"> <div align="center">Sensor ID </div></th>

                    <th width="98"> <div align="center">Location </div></th>

                    <th width="198"> <div align="center">Sensor Type </div></th>

                    <th width="97"> <div align="center">Description </div></th>
                    
                    <th width="97"> <div align="center">Media Data </div></th>
                    
                    <th width="97"> <div align="center">Scalar Data </div></th>
                    
                    </tr>
        
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
