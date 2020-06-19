<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

class filter {
    
    /**
     * Check if the Domainname is valid
     * 
     * @param string $domainname
     * @param boolean $fail_on_error
     * @return mixed
     */
    public static function checkDomainnameValidation($domainname, $fail_on_error=true) {
       if (!trim($domainname)) return null;
       if ('https://'.$domainname == filter_var('https://'.$domainname, FILTER_VALIDATE_URL)) {
           $destination=$domainname;
       } else {
           $destination=null;
       }
         
       if ($fail_on_error===true && $destination===null) {
           status::sendErrorMessage('Error: Domainname invalid', true, 400);
       }
               
       return $destination;
    }
    
    /**
     * Check if the IP-Adress is valid
     * 
     * @param string $source
     * @param string $type
     * @return mixed
     */
    public static function checkIPvalidation($source, $type='ipv4') {
        if (!trim($source)) return null;
        switch ($type) {
            case 'ipv6' : $destination = filter_var($source, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE); break;
            default     : $destination = filter_var($source, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }
        
        //if filter failed - reset variable
        return ($destination===false ? null : $destination);
    }
    
    /**
     * Validation of GET Parameters
     * 
     * @param array $get
     * @return website\weyand\dyndns\worker
     */
    public static function checkGETParameters($get) {
        //check if variables are set
        if (!count($get) && !isset($get['hostname']) && !(isset($get['ip']) || isset($get['ip6'])) ) {
           status::sendErrorMessage('Error: DynDNS Request invalid', true, 400);
        }
      
        //set local variables
        worker::setHostname($get['hostname']);
        worker::setIPv4(isset($get['myip'])==true ? $get['myip'] : $get['ip']);
        worker::setIPv6($get['ip6']);
        
        if (config::allowedDynDNSDomains!==false && count(config::allowedDynDNSDomains)) {
            $allowed_domain = false;
            foreach(config::allowedDynDNSDomains as $key => $value) {
                if (strpos(worker::getHostname(), $value)!==false) {
                    $allowed_domain = true;
                } 
            }
            
            if ($allowed_domain===false) {
                status::sendErrorMessage('Error: Domain not allowed for DynDNS Service', true, 400);
            }
        }
        
        if (worker::getIPv4()===null && worker::getIPv6()===null) {
           status::sendErrorMessage('Error: Missing an valid IP-Address', true, 503); 
        }

        return true;
    }
    
    public static function checkDomainnameDynDNSCompability() {
        worker::getConnector()->checkDomainnameDynDNSCompability();
    }
}

