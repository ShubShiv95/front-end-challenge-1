<?php
include_once './dbhandle.php';
if (isset($_REQUEST['product'])) {
    if ($_REQUEST['product'] == 'delete') {
      $Product_Id = $_REQUEST['Product_Id'];
      $sqldesc =  "Delete From Products_Table where Product_Id = ?";
        $stmt=$dbhandle->prepare($sqldesc);
        $stmt->bind_param("i",$Product_Id);     
       $exe= $stmt->execute(); 
        if (!$exe)
        {
            $data = array(
                "status"=>"500",
                "message"=>"Not able To Delete Product."
                );
        }
        else { 
            $data = array(
            "status"=>"200",
            "message"=>"Product Deleted SuccessFully."
            );
       }
       header('Content-type: text/javascript');
       echo json_encode($data,JSON_PRETTY_PRINT);
    }
}
