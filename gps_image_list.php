<?php
if ($handle = opendir('./imgUpload')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
        	echo '<div class="img_container">';
        	echo '<img src="' . $entry . '" class="img" onclick="javascript:console.log(\'This is for '. $entry .' \');" exif="true" />';
        	echo '</div>';
        }
    }
    closedir($handle);
}
?>
