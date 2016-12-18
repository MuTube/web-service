<?php

class FileManager {
    // USER IMAGES

    public static function processUserImage($file, $oldFileName, $newFileName = false) {
        self::validateFile($file, ['png', 'jpg', 'jpeg', 'gif']);

        if(self::fileExistWithPath('files/user_image/' . $oldFileName)) {
            self::deleteFile('files/user_image/' . $oldFileName);
        }

        $newPath = 'files/user_image/' . $newFileName;
        if(self::fileExistWithPath($newPath)) self::deleteFile($newPath);
        self::uploadFile($file, $newPath);
    }


    // DB ARCHIVE

    public static function getLastDbArchive() {
        $lastArchive = '';
        foreach(scandir('config/database/archive') as $archive) {
            if(!in_array($archive, ['.', '..']) && ($lastArchive == '' || explode('_', $archive)[0] > explode('_', $lastArchive)[0])) {
                $lastArchive = $archive;
            }
        }
        return $lastArchive;
    }

    public static function fileExistWithPath($path) {
        return file_exists($path);
    }


    // BASE FILE SYSTEM METHODS

    public static function deleteFile($path) {
        if(self::fileExistWithPath($path)) {
            unlink($path);
        }
    }

    protected static function uploadFile($file, $path) {
        move_uploaded_file($file['tmp_name'], $path);
    }

    protected static function validateFile($file, $types = []) {
        $validFileType = 'notFound';

        foreach($types as $type) {
            if(pathinfo($file['name'], PATHINFO_EXTENSION) == $type) {
                $validFileType = $type;
            }
        }

        if($validFileType == 'notFound') {
            throw new Exception('Only this types are allowed for file : ' . implode(', ', $types));
        }
    }
}