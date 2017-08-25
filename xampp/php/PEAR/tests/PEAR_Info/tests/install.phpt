--TEST--
PEAR_Info check if packages installed
--FILE--
<?php
$ds         = DIRECTORY_SEPARATOR;
$dir        = dirname(__FILE__);
$sysconfdir = $dir . $ds . 'sysconf_dir';
$peardir    = $dir . $ds . 'pear_dir';

putenv("PHP_PEAR_SYSCONF_DIR=" . $sysconfdir);
chdir($dir);

// we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
include_once 'PEAR/Info.php';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $conf_file = $sysconfdir . $ds . 'pearsys.ini';
} else {
    $conf_file = $sysconfdir . $ds . 'pear.conf';
}

if (!file_exists($conf_file)) {
    // write once PEAR system-wide config file for simulation
    $config =& PEAR_Config::singleton();
    $config->set('php_dir', $peardir);
    $config->writeConfigFile($conf_file);
}

/**
 * TestCase 1:
 * check if a package named is installed, under pear.php.net channel.
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testPackageNameInstall';

$GLOBALS['_PEAR_Config_instance'] = null;

$available = PEAR_Info::packageInstalled('Console_Getopt');

$result = ($available)
    ? 'OK' : 'Package Console_Getopt is not yet installed';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 2:
 * check if a package is installed with a minimal version,
 * under pear.php.net channel.
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testPackageNameVersionInstall';

$GLOBALS['_PEAR_Config_instance'] = null;

$available = PEAR_Info::packageInstalled('Console_Getopt', '1.2.2');

$result = ($available)
    ? 'OK' : 'Package Console_Getopt is not installed,' .
             ' or version is less than 1.2.2';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 3:
 * check if a channel/package named is installed.
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testPackageNameChannelInstall';

$GLOBALS['_PEAR_Config_instance'] = null;

$available = PEAR_Info::packageInstalled('PHPUnit', null, 'pear.phpunit.de');

$result = ($available)
    ? 'OK' : 'Package PHPUnit is not yet installed';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 4:
 * check if a channel/package with a minimal version, is installed.
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testPackageNameVersionChannelInstall';

$GLOBALS['_PEAR_Config_instance'] = null;

$available = PEAR_Info::packageInstalled('PHPUnit', '3.0.0', 'phpunit');

$result = ($available)
    ? 'OK' : 'Package phpunit/PHPUnit is not installed,' .
             ' or version is less than 3.0.0';

echo $testCase . ' : ' . $result;
?>
--CLEAN--
<?php
$ds         = DIRECTORY_SEPARATOR;
$dir        = dirname(__FILE__);
$sysconfdir = $dir . $ds . 'sysconf_dir';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $conf_file = $sysconfdir . $ds . 'pearsys.ini';
} else {
    $conf_file = $sysconfdir . $ds . 'pear.conf';
}

unlink ($conf_file);
?>
--EXPECT--
testPackageNameInstall : OK
testPackageNameVersionInstall : OK
testPackageNameChannelInstall : OK
testPackageNameVersionChannelInstall : OK