<?php
include './dbhandle.php';
//http://localhost/product/front-end-challenge-1/AddNewProductAPI.php?product=entry&Product_Name=Laptop&Price=10000&Available_Size=1 pc&Category=Electronic&image

if (isset($_REQUEST['product'])) {
    if ($_REQUEST['product'] == 'entry') {
        mysqli_autocommit($dbhandle, false);
        $Product_Name = $_REQUEST['Product_Name'];
        $Price = $_REQUEST['Price'];
        $Available_Size = $_REQUEST['Available_Size'];
        $Category = $_REQUEST['Category'];
        
        $folder="./uploads/products/";   
        $directory = 'products';
        $mainDirectory = "./uploads/products/";    
        $tempName= $_FILES['image']['tmp_name'];
        $originalImgName= $_FILES['image']['name'];

       
        
        if (!file_exists($mainDirectory)) 
        {
            mkdir('./uploads/products/', 0777, true);
        }

        // checking file extension 
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if ($fileExtension !='jpg' && $fileExtension !='jpeg' && $fileExtension !='png') {
            http_response_code(200); 
            $data = array(
                "status"    =>   "500",
                "type"      =>   "failure",
                "message"   =>   "Please Select (jpg, jpeg, png) Image Only Your Extension is ".$fileExtension.""
            );
            echo json_encode($data,JSON_PRETTY_PRINT);
            die;
        }
        else{ 
            if(move_uploaded_file($tempName,$folder.$originalImgName))
            {
                $server_link = "http://$_SERVER[HTTP_HOST]";
                $old = './uploads/products/'.$originalImgName;
                $file_name = md5(uniqid().date('His')).'.'.$fileExtension;
                $new = './uploads/products/'.$file_name;
                $content = file_get_contents($old); 
         
                unlink($old);
                $file = fopen($new, "w");
                fwrite($file,$content);
                fclose($file);
                $sqldesc = "INSERT INTO products_table(`Product_Name`, `Price`, `Available_Size`,  `Category`, `Product_Image`) VALUES (?,?,?,?,?)";
                $stmt_prep = $dbhandle->prepare($sqldesc);
                $stmt_prep->bind_param("sisss", $Product_Name,$Price,$Available_Size,$Category,$new);
                $exe = $stmt_prep->execute();
              
                if (!$exe) {
                                     
                    $data = array(
                        "status" => "500",
                        "message" => "Failure,Not able to save product."
                    );
                   
                } else {
                    mysqli_commit($dbhandle);
                    $data = array(
                        "status" => "200",
                        "message" => "success",
                        "Product" => array(
                            "Product Name" => $Product_Name,
                            "Price" => $Price,
                            "Available Size" =>$Available_Size,
                            "Category" => $Category,
                            "Product Image"=>$new                          
                        )
                    );
                   
                }
              
                header('Content-type: text/javascript');
                echo json_encode($data, JSON_PRETTY_PRINT);

            }
        }
        
   }
}