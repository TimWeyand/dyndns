<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

/**
 * Autoloader specific for the namespace website\weyand\dyndns
 */
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'website\weyand\dyndns')!==FALSE) {
        $load_class = substr($class_name, 21);
        $file_path_load_class = __DIR__. str_replace('\\',DIRECTORY_SEPARATOR, $load_class).'.class.php';
        if (file_exists($file_path_load_class)) {
            include $file_path_load_class;
        }
    }
});
