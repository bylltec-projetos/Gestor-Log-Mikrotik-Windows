<?php
/**
 * Check packages installed
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: pear_info4.php,v 1.2 2007/12/15 16:27:23 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

$res = PEAR_Info::packageInstalled('Role_Web', '1.1.0', 'pearified');
var_dump($res);

$res = PEAR_Info::packageInstalled('PEAR_PackageFileManager', '1.6.0');
var_dump($res);

?>