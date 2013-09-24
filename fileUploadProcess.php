<?php

if( isset($_FILES['gpsImage']) ) {

	$permType = array( 'image/gif', 'image/jpeg', 'image/pjpeg' );
	$file = $_FILES['gpsImage'];

	if( in_array( $file['type'], $permType ) && $file['size'] < 5000000 ) {
		if ($file["error"] > 0) {
			echo "Return Code: " . $file["error"] . "<br />";
		} else {
			echo "Upload: " . $file["name"] . "<br />";
			echo "Type: " . $file["type"] . "<br />";
			echo "Size: " . ($file["size"] / 1024) . " Kb<br />";
			echo "Temp file: " . $file["tmp_name"] . "<br />";

			if (file_exists("imgUpload/" . $file["name"])) {
				echo $file["name"] . " already exists. ";
			} else {
				move_uploaded_file($file["tmp_name"],
					"imgUpload/" . $file["name"]);
				echo "Stored in: " . "imgUpload/" . $file["name"];
			}
			header("location: index.php");
		}
	} else {
		echo "Invalid file";
	}
}
?>
