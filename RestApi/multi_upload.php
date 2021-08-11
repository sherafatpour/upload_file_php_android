<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST"); // here is define the request method

include 'dbconfig.php'; // include database connection file

$data = json_decode(file_get_contents("php://input"), true); // collect input parameters and convert into readable format

// Count total files
$countfiles = count($_FILES['file']['name']);
$file = $_FILES['file']['name'][0];

if(empty($file))
{
	$errorMSG = json_encode(array("message" => "please select image", "status" => false));	
	echo $errorMSG;
}
else
{

$upload_path = 'upload/';
$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid image extensions

// Looping all files
for($i=0;$i<$countfiles;$i++){
    $fileName = $_FILES['file']['name'][$i];
    $tempPath = $_FILES['file']['tmp_name'][$i];
    $fileSize  =  $_FILES['file']['size'][$i];

    $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
    // Upload file
    if(in_array($fileExt, $valid_extensions))
	{				
		//check file not exist our upload folder path
		if(!file_exists($upload_path . $fileName))
		{
			// check file size '5MB'
			if($fileSize < 5000000){
				move_uploaded_file($tempPath, $upload_path . $fileName); // move file from system temporary path to our upload folder path 
                
                //insert into database table
                $query =  mysqli_query($conn,'INSERT into tbl_image (name) VALUES("'.$fileName.'")');
                
            }
			else{		
				$errorMSG = json_encode(array("message" => "Sorry, your file is too large, please upload 5 MB size", "status" => false));	
				echo $errorMSG;
			}
		}
		else
		{		
			$errorMSG = json_encode(array("message" => "Sorry, file already exists check upload folder", "status" => false));	
			echo $errorMSG;
		}
	}
	else
	{		
		$errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed", "status" => false));	
		echo $errorMSG;		
	}
   
   }
}

if(!isset($errorMSG))
{	
	echo json_encode(array("message" => "Image Uploaded Successfully", "status" => true));	
}

?>