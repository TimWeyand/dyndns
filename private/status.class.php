<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

class status {
    
    
    /**
     * Send an appropiate answer to the client
     * 
     * @param string $message
     * @param boolean $exit
     * @param integer $httpCode
     */
    public static function sendErrorMessage($message, $exit=true, $httpCode=500) {
        switch ($httpCode) {
            case 200: header('HTTP/1.0 200 OK'); break;
            case 400: header('HTTP/1.0 400 Bad Request'); break;
            case 500:
            default: header('HTTP/1.0 500 Internal Server Error');
        }
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Content-Type: text/plain');
        header('X-Powered-By: github.com/tweyand/plesk-dyndns');
        echo $message;
        if ($exit===true) exit;
    }
}

