<?php

function deleteDir($dirPath) {
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dirPath. DIRECTORY_SEPARATOR .$object) && !is_link($dirPath."/".$object))
                    deleteDir($dirPath. DIRECTORY_SEPARATOR .$object);
                else
                    unlink($dirPath. DIRECTORY_SEPARATOR .$object);
            }
        }
        rmdir($dirPath);
    }
}

$runtimePath = __DIR__ . '/Runtime';
deleteDir($runtimePath);
mkdir($runtimePath, 0777, true);
echo "Cache cleared successfully.";