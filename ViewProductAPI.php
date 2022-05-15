<?php
//http://localhost/chall/front-end-challenge-1/ViewProductAPI.php?product=view
include_once './dbhandle.php';
if (isset($_REQUEST['product'])) {
    if ($_REQUEST['product'] == 'view') {    
      $sqldesc =  "SELECT * from Products_Table";
      $result = mysqli_query($dbhandle, $sqldesc);
      $data = array(
        "type" => "200",
        "status" => "success",
        "message" => "some record found",
        "details" => array()
    );
      while ($allresult = mysqli_fetch_assoc($result)) {
        $data['details'][]  = array(
          "Product" => array(
              "Product Name" => $allresult["Product_Name"],
              "Price" => $allresult["Price"],
              "Available Size" => $allresult["Available_Size"],
              "Category" => $allresult["Category"],
              "Product Image"=>$allresult["Product_Image"], 
          )       
      ); 
    }
       header('Content-type: text/javascript');
       echo json_encode($data,JSON_PRETTY_PRINT);
    }
}
