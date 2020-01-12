<?php
/**
 * Main php file to create a class autoloader
 *
 * @package     photobooth
 * @copyright   2020
 * @license     MIT
 * @link        https://github.com/andreknieriem/photobooth/
 *
 */

/**
 * Class autoloader
 *
 * @param $classname
 * @return bool
 */
function autoload($classname) {
    $file = __DIR__ . '/class/' .str_replace('\\', DIRECTORY_SEPARATOR, $classname). '.php';
    if(file_exists($file)) {
        require_once $file;
        return true;
    }else {
        echo 'Abort. Problem loading class "'.$classname.'".';
        return false;
    }
}
spl_autoload_register('autoload');
