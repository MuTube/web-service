<?php

class FileManager {
    public static function getLastDbArchive() {
        $lastArchive = '';
        foreach(scandir('config/database/archive') as $archive) {
            if(!in_array($archive, ['.', '..']) && ($lastArchive == '' || explode('_', $archive)[0] > explode('_', $lastArchive)[0])) {
                $lastArchive = $archive;
            }
        }
        return $lastArchive;
    }

    public static function processUserImage($file, $oldFileName, $newFileName = false) {
        self::validateFile($file, ['png', 'jpg', 'jpeg', 'gif']);

        self::deleteFile('files/user_image/' . $oldFileName);

        $newPath = 'files/user_image/' . $newFileName;
        if(self::fileExistWithPath($newPath)) self::deleteFile($newPath);
        self::uploadFile($file, $newPath);
    }

    public static function processCurlDownloadedImageToDestination($imageContent, $destination) {
        if(self::fileExistWithPath($destination)) self::deleteFile($destination);
        self::generateFileWithContent($destination, $imageContent);
    }

    public static function savePngImageAtPath($image, $path) {
        if(self::fileExistWithPath($path)) throw new Exception('file already exists');
        imagepng($image, $path, 8);
    }

    public static function deleteFile($path) {
        if(!self::fileExistWithPath($path)) {
            throw new SoftException("No file found at path '" . $path . "'");
        }

        unlink($path);
    }

    protected static function replaceFile($basePath, $oldName, $newFile) {
        if(self::fileExistWithPath($basePath . $oldName)) {
            self::deleteFile($basePath . $oldName);
        }

        self::uploadFile($newFile, $basePath);
    }

    protected static function generateFileWithContent($fileName, $content) {
        if(self::fileExistWithPath($fileName)) throw new Exception("File already exist...");
        clearstatcache();

        $file = fopen($fileName, "w");
        fwrite($file, $content);
        fclose($file);
    }

    protected static function uploadFile($file, $path) {
        move_uploaded_file($file['tmp_name'], $path);
    }

    protected static function fileExistWithPath($path) {
        return file_exists($path);
    }

    protected static function directoryExists($dir) {
        return is_dir($dir);
    }

    protected static function validateFile($file, $types = []) {
        $validFileType = 'notFound';

        foreach($types as $type) {
            if(pathinfo($file['name'], PATHINFO_EXTENSION) == $type) {
                $validFileType = $type;
            }
        }

        if($validFileType == 'notFound') throw new SoftException('Only this types are allowed for file : ' . implode(', ', $types));
    }
}