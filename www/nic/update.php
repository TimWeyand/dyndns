<?php
/**
 * @author Tim Weyand <https://www.weyand.biz>
 * @copyright 2017 - Tim Weyand
 * @license https://opensource.org/licenses/MIT MIT License
 * @version 1.1 edited by CptNemooo
 */
include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'autoload.class.php';

$result = website\weyand\dyndns\worker::init()->setRecords();
