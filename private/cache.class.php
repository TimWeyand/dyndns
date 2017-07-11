<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

class cache extends basicClass {
    
    private $_currentDomain = null;
    
    private $_dyndnsData = null;
    
    /**
     * Check if the IP-Adress has been changed, before we connect to the database
     * 
     * @return website\weyand\dyndns\worker
     */
    public function check() {
        //check cache file
        if (!file_exists(config::cacheFile) || !is_writable(config::cacheFile)) {
            //suppress warning, if we can not touch file
            if (!@touch(config::cacheFile)) {
               status::sendErrorMessage('Error: Disk Permission Rights', true, 500);
            }    
        }
        
        $this->_dyndnsData = file_get_contents(config::cacheFile);
        
        //if file is empty, initialize array
        if (!trim($this->_dyndnsData)) {
            $this->_dyndnsData = array();
        } else {
            $this->_dyndnsData = unserialize($this->_dyndnsData);
        }
        
        //get cached data for the dyndns domain
        if (isset($this->_dyndnsData[worker::getHostname()])) {
           $this->_currentDomain = $this->_dyndnsData[worker::getHostname()];
        } else {
           $this->_currentDomain = array('ip' => null, 'ip6' => null);
        }
        
        //check if the ip has changed, in our cache file
        $recordChanged=false;
        if ($this->_currentDomain['ip'] !== worker::getIPv4()) {
            $recordChanged=true;
        }
        
        if ($this->_currentDomain['ip6'] !== worker::getIPv6()) {
            $recordChanged=true;
        }
        
        if ($recordChanged===false) {
           status::sendErrorMessage('IP did not changed', true, 200); 
        }
        
        return $recordChanged;
    }
    
    public function setCache() {
        static::init()->_dyndnsData[worker::getHostname()]['ip'] = worker::getIPv4();
        static::init()->_dyndnsData[worker::getHostname()]['ip6'] =worker::getIPv6();
        var_dump(static::init()->_dyndnsData);
        file_put_contents(config::cacheFile, serialize(static::init()->_dyndnsData));
    }
}

