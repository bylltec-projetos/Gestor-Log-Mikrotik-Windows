--TEST--
PEAR_Info using render options
--FILE--
<?php
$ds         = DIRECTORY_SEPARATOR;
$dir        = dirname(__FILE__);
$sysconfdir = $dir . $ds . 'sysconf_dir';
$peardir    = $dir . $ds . 'pear_dir';
$userdir    = $dir . $ds . 'user_dir';
$tpldir     = $dir . $ds . 'templates';

putenv("PHP_PEAR_SYSCONF_DIR=" . $sysconfdir);
chdir($dir);

// we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
include_once 'PEAR/Info.php';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $u_conf_file  = $peardir . $ds . 'pear.ini';
    $conf_file    = $peardir . $ds . 'pearsys.ini';
    $custom_file1 = $peardir . $ds . 'name1.pearsys.ini';
    $custom_file2 = $userdir . $ds . 'name2.pearsys.ini';
} else {
    $u_conf_file  = $peardir . $ds . '.pearrc';
    $conf_file    = $peardir . $ds . 'pear.conf';
    $custom_file1 = $peardir . $ds . 'name1.pear.conf';
    $custom_file2 = $userdir . $ds . 'name2.pear.conf';
}

if (!file_exists($conf_file)) {
    // write once PEAR system-wide config file for simulation
    $config =& PEAR_Config::singleton();
    $config->set('php_dir', $peardir);
    $config->writeConfigFile($conf_file);

    // also writes custom pear system config files
    $config->writeConfigFile($custom_file1);
    $config->writeConfigFile($custom_file2);
}

/**
 * TestCase 1:
 * usage of stylesheet to customize look and feel
 */
$testCase = 'testCustomStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$pearInfo   = new PEAR_Info($peardir);
$css_exists = $pearInfo->setStyleSheet($tpldir . $ds . 'blueskin.css');

$result = ($css_exists)
    ? 'OK' : 'CSS file does not exists';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 2:
 * display main page with default stylesheet
 */
$testCase = 'testDefaultStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$options = array('resume' =>  PEAR_INFO_GENERAL |
                              PEAR_INFO_PACKAGES_VERSION |
                              PEAR_INFO_FULLPAGE,
                 'channels' => array());

$pearInfo = new PEAR_Info($peardir, '', '', $options);
$html = $pearInfo->toHtml();

$packages_tpl = file_get_contents($tpldir . $ds . 'packages.tpl');
$packages_tpl = str_replace(
                    array(
                        '{styles}',
                        '{script_filename}',
                        '{config_file}',
                        '{usr_config_file}',
                        '{sys_config_file}'
                    ),
                    array(
                        $pearInfo->getStyleSheet(),
                        __FILE__,
                        $conf_file,
                        $u_conf_file,
                        $conf_file
                    ),
                    $packages_tpl);

if (OS_WINDOWS) {
    $html = str_replace("\r\n", "\n", $html);
}

$result = (strcasecmp($html, $packages_tpl) == 0)
    ? 'OK' : 'HTML strings are not same';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 3:
 * display credits page with default stylesheet
 */
$testCase = 'testCreditsWithDefaultStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$options = array('resume' =>  PEAR_INFO_GENERAL |
                              PEAR_INFO_CREDITS_ALL |
                              PEAR_INFO_FULLPAGE,
                 'channels' => array());

$pearInfo = new PEAR_Info($peardir, '', '', $options);
ob_start();
$pearInfo->show();
$html = ob_get_contents();
ob_end_clean();

$credits_tpl = file_get_contents($tpldir . $ds . 'credits.tpl');
$credits_tpl = str_replace(
                    array(
                        '{styles}',
                        '{script_filename}',
                        '{config_file}',
                        '{usr_config_file}',
                        '{sys_config_file}'
                    ),
                    array(
                        $pearInfo->getStyleSheet(),
                        __FILE__,
                        $conf_file,
                        $u_conf_file,
                        $conf_file
                    ),
                    $credits_tpl);

if (OS_WINDOWS) {
    $html = str_replace("\r\n", "\n", $html);
}

$result = (strcasecmp($html, $credits_tpl) == 0)
    ? 'OK' : 'HTML strings are not same';

echo $testCase . ' : ' . $result;
?>
--CLEAN--
<?php
$ds         = DIRECTORY_SEPARATOR;
$dir        = dirname(__FILE__);
$sysconfdir = $dir . $ds . 'sysconf_dir';
$peardir    = $dir . $ds . 'pear_dir';
$userdir    = $dir . $ds . 'user_dir';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $conf_file    = $peardir . $ds . 'pearsys.ini';
    $custom_file1 = $peardir . $ds . 'name1.pearsys.ini';
    $custom_file2 = $userdir . $ds . 'name2.pearsys.ini';
} else {
    $conf_file    = $peardir . $ds . 'pear.conf';
    $custom_file1 = $peardir . $ds . 'name1.pear.conf';
    $custom_file2 = $userdir . $ds . 'name2.pear.conf';
}

unlink ($conf_file);
unlink ($custom_file1);
unlink ($custom_file2);
?>
--EXPECT--
testCustomStyleSheet : OK
testDefaultStyleSheet : OK
testCreditsWithDefaultStyleSheet : OK