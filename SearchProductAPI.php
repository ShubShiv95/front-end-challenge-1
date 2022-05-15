<?php
include './dbhandle.php';
    if ($_REQUEST['product'] == "productsearch") {

        /* http://localhost/product/front-end-challenge-1/SearchProductAPI.php?product=productsearch&search_by=product_name&searchkey=s
           http://localhost/product/front-end-challenge-1/SearchProductAPI.php?product=productsearch&search_by=category&searchkey=g
            */

        $search = "%" . $_REQUEST['searchkey'] . "%";
        if ($_REQUEST['search_by'] == 'product_name') {
            // search by product_name
            $search1 = "SELECT * FROM `products_table` WHERE `Product_Name` LIKE ? ";
            $search1_prep = $dbhandle->prepare($search1);
        } elseif ($_REQUEST['search_by'] == 'category') {
            // search by category
            $search1 = "SELECT * FROM `products_table` WHERE `Category` LIKE ? ";
            $search1_prep = $dbhandle->prepare($search1);
        } 
        $search1_prep->bind_param("s",$search);
        $search1_prep->execute();
        $result_set = $search1_prep->get_result();
        if ($result_set->num_rows > 0) {
            $data = array(
                "type" => "200",
                "status" => "success",
                "message" => "some record found",
                "details" => array()
            );

            while ($row_result = $result_set->fetch_assoc()) {
                $data['details'][]  = array(
                    "Product" => array(
                        "Product Name" => $row_result["Product_Name"],
                        "Price" => $row_result["Price"],
                        "Available Size" => $row_result["Available_Size"],
                        "Category" => $row_result["Category"],
                        "Product Image"=>$row_result["Product_Image"], 
                    )       
                );
            }
        } else {
            $data = array("type" => "500", "status" => "error", "message" => "no record found");
        }
        header('Content-type: text/javascript');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

