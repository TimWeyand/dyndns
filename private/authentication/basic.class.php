<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns\authentication;
use website\weyand\dyndns;

/**
 * Description of basic
 *
 * @author tim
 */
class basic {
    
    public static function authenticate() {
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            if ($_SERVER['PHP_AUTH_USER']=== dyndns\config::basic_auth_username && $_SERVER['PHP_AUTH_PW']=== dyndns\config::basic_auth_password) {
                return true;
            }
        } 
        
        header('WWW-Authenticate: Basic realm="DynDNS Service"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
}
