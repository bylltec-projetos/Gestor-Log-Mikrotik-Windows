<?php
/**
 * Generate default phpinfo() style PEAR information.
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: pear_info.php,v 1.3 2007/12/15 16:27:23 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

/* May be required on slower (dial-up) connections
ini_set('default_socket_timeout', 600);
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);
*/

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// If you need to set a http_proxy uncomment the line below
// PEAR_Info::setProxy('your.proxy.here');

// Create PEAR_Info object
$info = new PEAR_Info();

// Display PEAR_Info output
$info->display();
?>