<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;


class basicClass {
   
    /**
     * @var basicClass 
     */
    protected static $_instance = array();
    
  
    /**
     * Singleton Class Constructor
     * 
     * @return website\weyand\dyndns\basicClass
     */
    public static function init() {
        $classname = get_called_class();
        if (!isset(self::$_instance[$classname]) || self::$_instance[$classname] === null) {
           self::$_instance[$classname] = new $classname(); 
        }
        return self::$_instance[$classname];
    }
}