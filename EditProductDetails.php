<?php
include './dbhandle.php';

if (isset($_REQUEST['product'])) {
    if ($_REQUEST['product'] == 'update') {
        mysqli_autocommit($dbhandle, false);
        $product_id = $_REQUEST['product_id'];
       

        $search_product = "Select * from Products_table where Product_Id = ?";
        $search_product = $dbhandle->prepare($search_product);
        $search_product->bind_param("i", $product_id);
       $search_product->execute();
        $result_set=$search_product ->get_result();
        $allresult = $result_set->fetch_assoc();
        if ($result_set->num_rows > 0) {

            $folder = "./uploads/products/";
            $directory = 'products';
            $mainDirectory = "./uploads/products/";
            $tempName = $_FILES['image']['tmp_name'];
            $originalImgName = $_FILES['image']['name'];
            if (!file_exists($mainDirectory)) {
                mkdir('./uploads/products/', 0777, true);
            }

            // checking file extension 
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if ($fileExtension != 'jpg' && $fileExtension != 'jpeg' && $fileExtension != 'png') {
                http_response_code(200);
                $data = array(
                    "status"    =>   "500",
                    "type"      =>   "failure",
                    "message"   =>   "Please Select (jpg, jpeg, png) Image Only Your Extension is " . $fileExtension . ""
                );
                echo json_encode($data, JSON_PRETTY_PRINT);
                die;
            } else {
                if (move_uploaded_file($tempName, $folder . $originalImgName)) {
                    $server_link = "http://$_SERVER[HTTP_HOST]";
                    $old = './uploads/products/' . $originalImgName;
                    $file_name = md5(uniqid() . date('His')) . '.' . $fileExtension;
                    $new = './uploads/products/' . $file_name;
                    $content = file_get_contents($old);

                    unlink($old);
                    $file = fopen($new, "w");
                    fwrite($file, $content);
                    fclose($file);
                    $Product_Name = $_REQUEST['Product_Name'];
                    $Price = $_REQUEST['Price'];
                    $Available_Size = $_REQUEST['Available_Size'];
                    $Category = $_REQUEST['Category'];
                    $sqldesc = "UPDATE Products_table SET Product_Name = ? , Price = ? , Available_Size=?, Category = ? ,Product_Image = ?  WHERE Product_Id = ?";
                    $stmt = $dbhandle->prepare($sqldesc);
                    $stmt->bind_param("sisssi", $Product_Name, $Price, $Available_Size, $Category, $new, $product_id);
                    $exe = $stmt->execute();
                    if (!$exe) {
                        $data = array(
                            "status" => "500",
                            "message" => "Failure,Not able to Update product."
                        );
                    } else {
                        mysqli_commit($dbhandle);
                        $data = array(
                            "status" => "200",
                            "message" => "Update Success",
                            "Product" => array(
                                "Product Name" => $Product_Name,
                                "Price" => $Price,
                                "Available Size" => $Available_Size,
                                "Category" => $Category,
                                "Product Image" => $file_name
                            )
                        );
                    }

                    header('Content-type: text/javascript');
                    echo json_encode($data, JSON_PRETTY_PRINT);
                }
            }

           
        } else {
            $data = array(
                "status" => "500",
                "message" => "NO Record Found for the Given Product Id."
            );
        }


       
    }
}
