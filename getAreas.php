<?php
/**
 * Created by IntelliJ IDEA.
 * User: op49265
 * Date: 17.03.2016
 * Time: 11:46
 */

$folder = "areas";
$data = array();
if ($handle = opendir($folder)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $path = $folder."/".$entry;
            $data[] = file_get_contents($path);

        }
    }
    closedir($handle);
}
echo "[";
echo implode(",",$data);
echo "]";