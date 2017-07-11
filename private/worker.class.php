<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

/**
 * Check and Process the DynDNS Request for:
 *   * validity of input
 *   * minimal imput
 *   * against our config
 *   * against our database
 * 
 * A valid DynDNS Request is: [scriptname]?hostname=<DOMAIN>&ip=<ipaddr>&ip6=<ip6addr>
 *
 * @author tim.weyand
 */
class worker extends basicClass {
    
    private $_hostname = null;
    
    private $_ipv4 = null;
    
    private $_ipv6 = null;
    
    protected $_connector = null;
    
    /**
     * actual Class Constructor, which should not be called manually
     */
    protected function __construct() {
        if (!file_exists(__DIR__.DIRECTORY_SEPARATOR.'config.class.php')) {
            status::sendErrorMessage('Configfile config.class.php missing - Please edit config.class.example.php and rename it'); 
        }
        if (config::authentication!==false) {
            $classname = 'website\\weyand\\dyndns\\authentication\\'.config::authentication;
            $classname::authenticate();
        }
        $this->_connector = new connector\plesk;
    }
    
    public static function getConnector() {
        return static::init()->_connector;
    }
    
    public static function getHostname() {
        return static::init()->_hostname;
    }
    
    public static function getIPv4() {
        return static::init()->_ipv4;
    }
    
    public static function getIPv6() {
        return static::init()->_ipv6;
        var_dump(static::init()->_ipv6 );
    }
    
    public static function setHostname($hostname) {
        static::init()->_hostname = filter::checkDomainnameValidation($hostname);
    }    
    
    public static function setIPv4($ip) {
        static::init()->_ipv4 = filter::checkIPvalidation($ip);
    }
    
    public static function setIPv6($ipv6) {
        static::init()->_ipv6 = filter::checkIPvalidation($ipv6,'ipv6');
    }
    
    public function setRecords() {
        filter::checkGETParameters($_GET);
        if (cache::init()->check() && filter::checkDomainnameValidation($this->_hostname, $fail_on_error)) {
            $recordsChanged=false;
            if ($this->_ipv4!==null && $this->_ipv4 != $this->_connector->getDNSEntry()) {
                $this->_connector->setDNSEntry($this->_hostname, $this->_ipv4);
                $recordsChanged=true;
            }
            
            if ($this->_ipv6!==null && $this->_ipv6 != $this->_connector->getDNSEntry(null,'AAAA')) {
                $this->_connector->setDNSEntry($this->_hostname, $this->_ipv6,'AAAA');
                $recordsChanged=true;
            }
            
            if ($recordsChanged===true) {
                status::sendErrorMessage('IP changed', false, 200); 
                cache::setCache();
            } else {
                status::sendErrorMessage('IP did not change', true, 200); 
            }
        }
    }
}