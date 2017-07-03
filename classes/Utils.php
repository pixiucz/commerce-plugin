<?php namespace Pixiu\Commerce\Classes;

/**
 * Created by PhpStorm.
 * User: marti
 * Date: 6/29/2017
 * Time: 9:55 AM
 */
class Utils
{

    /**
     * @param String $systemFilesPath
     * @return string
     *
     * From given file-name generates absolute path to
     * file and returns it as a string.
     *
     */
    public static function createAbsolutePathToImages(String $systemFilesPath):String
    {
        return url(
            'storage/app/uploads/public/'
            . substr($systemFilesPath, 0, 3) . '/'
            . substr($systemFilesPath, 3, 3) . '/'
            . substr($systemFilesPath, 6, 3) . '/'
            . $systemFilesPath
        );
    }

}