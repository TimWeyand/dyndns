<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.0
 */
namespace website\weyand\dyndns;

class config {
    
    const cacheFile = __DIR__.'/data/dyndns.data';
    
    const mysqlServer = 'localhost';
    
    const mysqlPort = '3306';
    
    const mysqlUsername = 'dyndns';
    
    const mysqlPassword = 'YouShouldChangeThis-Aas4vxKA;AB2349ysR';
    
    const mysqlDatabase = 'psa';
    
    //you can restrict the usage of this module - but you do not have to.
    const allowedDynDNSDomains = array();
    //const allowedDynDNSDomains = array('dyndns.weyand.biz');
    
    const applicationHash = 'YouShouldChangeThis-kasdj93qosB:DUS';
    
    //possible values [false,'basic','db']
    const authentication = 'basic';
    
    //Basic Authentication
    const basic_auth_username = 'dynuser';
    
    const basic_auth_password = 'password';
}
