<?php
/**
 * Test suite for the PEAR_Info class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2009 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: PEAR_Info_TestSuite_Standard.php,v 1.2 2009/01/05 21:02:49 farell Exp $
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     File available since Release 1.7.1
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestSuite_Standard::main");
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/PhptTestSuite.php';

/**
 * Test suite class to test standard PEAR_Info API.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2009 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.9.2
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     Class available since Release 1.7.1
 */

class PEAR_Info_TestSuite_Standard extends PHPUnit_Extensions_PhptTestSuite
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     * @static
     * @since  1.7.1
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $dir    = dirname(__FILE__);
        $suite  = new PHPUnit_Framework_TestSuite('PEAR_Info API test suite');
        $suite->addTestSuite(new PEAR_Info_TestSuite_Standard($dir));
        PHPUnit_TextUI_TestRunner::run($suite);
    }
}

// Call PEAR_Info_TestSuite_Standard::main()
// if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestSuite_Standard::main") {
    PEAR_Info_TestSuite_Standard::main();
}
?>