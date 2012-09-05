<?php
if ($handle = opendir('./html5fu/uploads')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
        	echo '<div class="img_container">';
        	echo '<img src="/test_bed/html5fu/uploads/' . $entry . '" class="img" onclick="javascript:console.log(\'This is for '. $entry .' \');" exif="true" />';
        	echo '</div>';
        }
    }
    closedir($handle);
}
?>