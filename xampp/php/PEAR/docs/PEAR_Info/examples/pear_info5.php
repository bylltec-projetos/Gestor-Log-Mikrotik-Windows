<?php
/**
 * Outputs lots of PEAR information
 *
 * The output may be customized by passing one or more of the following constants
 * bitwise values summed together in the optional 'resume' options parameter.
 * One can also combine the respective constants or bitwise values together
 * with the or operator.
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: pear_info5.php,v 1.5 2008/10/12 16:18:31 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

require_once 'PEAR/Info.php';

$options = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_CHANNELS |
                PEAR_INFO_PACKAGES_VERSION | PEAR_INFO_FULLPAGE,
                'channels' => array('pear.php.net', '__uri'));

$info = new PEAR_Info('', 'c:\wamp\bin\php\php5.2.6\pear.ini', '', $options);
$info->display();
?>