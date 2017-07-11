<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */

namespace website\weyand\dyndns\connector;
use website\weyand\dyndns;

class plesk {
    
    protected $_database = null;
 
    public function __destruct() {
        //close database connection, if it is still open
        if ($this->_database!==null) {
            $this->_database->close();
        }
    }
    
    /**
     * Establish an singleton database connection
     * 
     * @return \mysqli
     */
    protected function _db() {
        if ($this->_database===null) {
            //suppress warning, if we can not connect to database
            $this->_database = @new \mysqli(dyndns\config::mysqlServer, dyndns\config::mysqlUsername, dyndns\config::mysqlPassword, dyndns\config::mysqlDatabase, dyndns\config::mysqlPort);
   
            if ($this->_database->connect_error) {
                status::sendErrorMessage('Error: Database Connection Error', true, 500);
            }
        }
        return $this->_database;
    } 
    
    public function getDNSEntry($hostname=null, $record="A") {
        if ($hostname===null) {
            $hostname= dyndns\worker::getHostname();
        }
        //append an "." for dns convention
        $dns_hostname = $hostname.'.';
        
        $stmt = $this->_db()->prepare("SELECT val FROM dns_recs WHERE type = ? AND host = ?");
        $stmt->bind_param("ss", $record,$dns_hostname);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        
        return $result;
    }
    
    public function setDNSEntry($hostname, $ipAdress, $record='A') {
        $current_result = $this->getDNSEntry($hostname, $record);
        if ($current_result!==null && $current_result!=$ipAdress) {
            //append an "." for dns convention
            $dns_hostname = $hostname.'.';
            
            // Update IP
            $stmt = $this->_db()->prepare('UPDATE dns_recs SET val = ? , displayVal = ? WHERE type = ? AND host = ?');
            $stmt->bind_param("ssss", $ipAdress, $ipAdress, $record, $dns_hostname);
            $stmt->execute();
            
        } else {
            if ($current_result===null) {
                dyndns\status::sendErrorMessage('Error: Please Setup domainname in Plesk first', true, 500);     
            }
        }
        
        return true;
    }
    
    /**
     * Plesk specific - check if:
     *  * The DNS Zone is DynDNS compatible
     *  * The Domain exists on the Server
     *  * Restrict it to allowed Domains (if set)
     */
    public function checkDomainnameDynDNSCompability() {
        $sql = "SELECT domains.name"
         // for debug purposes
         //  . ", dns_zone.ttl, dns_zone.ttl_unit, dns_zone.refresh, dns_zone.refresh_unit, dns_zone.retry, dns_zone.expire, dns_zone.expire_unit, dns_zone.minimum "
             . "  FROM domains "
             . "  INNER JOIN dns_zone "
             . "  ON domains.dns_zone_id = dns_zone.id"
             . "  WHERE dns_zone.expire<=300";
     
        
        if (dyndns\config::allowedDynDNSDomains!==false && count(dyndns\config::allowedDynDNSDomains)) {
            //not really safe , but it is your config - you control it.
            $sql.= ' AND domains.name IN ("'.implode('","', \website\weyand\dyndns\config::allowedDynDNSDomains).'")';
        }
        
        $result = $this->_db()->query($sql);
        
        if ($result) {
           $dns_names = $result->fetch_all(MYSQLI_ASSOC);
           $everything_ok = false;
           
           if (count($dns_names)) {
               foreach ($dns_names as $key => $value) {
                   if (strpos(dyndns\worker::getHostname(), $value['name'])!==false) {
                       $everything_ok = true;
                       break;
                   }
               }
           }
           
           if ($everything_ok===false) {
               dyndns\status::sendErrorMessage('Error: The Requested Hostname is not on a DynDNS Domain on the Server', true, 500);   
           }
        } else {
           dyndns\status::sendErrorMessage('Error: No DynDNS Domain Found (or Database Credential Problems?)', true, 500);  
        }
    }
}