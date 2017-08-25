--TEST--
PEAR_Info using default configuration
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
 * default class constructor without parameter
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testConfigFilesExistInSysConfDir';

$pearInfo = new PEAR_Info();

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'System PEAR configuration files does not exist';

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
testConfigFilesExistInSysConfDir : OK