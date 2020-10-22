<?php
include('init.php');

$jsonData = array('data' => array());

$tempFilesArray = array();
if ($handle = opendir('./images')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "desktop.ini") {
            $path_parts = pathinfo("./images/$entry");
            $tempFilesArray['isbn-' . $path_parts['filename']] = array('basename' => $entry, 'extension' => $path_parts['extension']);
        }
    }
    closedir($handle);
    $jsonData = array('data' => $tempFilesArray);
}
echo json_encode($jsonData);
