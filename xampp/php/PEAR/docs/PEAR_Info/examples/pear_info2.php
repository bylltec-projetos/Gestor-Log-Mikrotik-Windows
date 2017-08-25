<?php
/**
 * Generate phpinfo() style PEAR information, with a custom blue skin
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: pear_info2.php,v 1.2 2007/12/15 16:27:23 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// Create PEAR_Info object
$info = new PEAR_Info();

// set your own styles, rather than use the default stylesheet
$info->setStyleSheet(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'blueskin.css');

// Display PEAR_Info output
$info->display();
?>