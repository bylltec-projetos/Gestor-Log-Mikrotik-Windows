<?php
/**
 * PEAR_Info no-regression test suite
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
 * @version   CVS: $Id: AllTests.php,v 1.8 2009/01/05 21:02:49 farell Exp $
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     File available since Release 1.7.0
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PEAR_Info_AllTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

chdir(dirname(__FILE__));

require_once 'PEAR_Info_TestSuite_Standard.php';

/**
 * PEAR_Info no-regression test suite
 *
 * Run all tests from the package root directory:
 * #phpunit PEAR_Info_AllTests tests/AllTests.php
 * or
 * #php tests/AllTests.php
 * or for code coverage testing
 * #phpunit --coverage-html tests/coverage PEAR_Info_AllTests tests/AllTests.php
 *
 * After the code coverage test browse the index.html file in tests/coverage.
 * The code coverage is close to 100%.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2009 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.9.2
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     Class available since Release 1.7.0
 */

class PEAR_Info_AllTests
{
    /**
     * Runs the test suite
     *
     * @return void
     * @static
     * @since  1.7.0
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Runs the test suite
     *
     * @return object the PHPUnit_Framework_TestSuite object
     * @static
     * @since  1.7.0
     */
    public static function suite()
    {
        $dir   = dirname(__FILE__);
        $suite = new PHPUnit_Framework_TestSuite('PEAR_Info Test Suite');
        $suite->addTestSuite(new PEAR_Info_TestSuite_Standard($dir));
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'PEAR_Info_AllTests::main') {
    PEAR_Info_AllTests::main();
}
?>