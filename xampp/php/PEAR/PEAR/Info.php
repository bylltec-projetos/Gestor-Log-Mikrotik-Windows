<?php
/**
 * This package generate phpinfo() style PEAR information.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Davey Shafik <davey@pixelated-dreams.com>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: Info.php,v 1.60 2009/02/18 17:29:43 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @since    File available since Release 1.0.1
 */

require_once 'PEAR/Config.php';

/**
 * PEAR_INFO_* is a bit-field. Or each number up to get desired information.
 *
 * Examples:
 * <code>
 * <?php
 * require_once 'PEAR/Info.php';
 * // will display for each channel (list displayed),
 * // a quick package list with only its name and version
 * $options = array('resume' => PEAR_INFO_CHANNELS | PEAR_INFO_PACKAGES_VERSION);
 * $info = new PEAR_Info('', 'c:\wamp\php\pear.ini', '', $options);
 * $info->display();
 * ?>
 * </code>
 *
 * - Show all informations, except for credits
 *
 *   $options = array('resume' => PEAR_INFO_ALL & ~PEAR_INFO_CREDITS);
 *
 * - Show only credits and configuration
 *
 *   $options = array('resume' => PEAR_INFO_CONFIGURATION | PEAR_INFO_CREDITS);
 */
/**
 * The configuration line, pear.ini | .pearrc location, and more.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_GENERAL', 1);
/**
 * PEAR Credits
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_CREDITS', 2);
/**
 * All PEAR settings.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_CONFIGURATION', 4);
/**
 * Information on PEAR channels.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_CHANNELS', 8);
/**#@+
 * Information on PEAR packages.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_PACKAGES', 4080);
define('PEAR_INFO_PACKAGES_CHANNEL', 2048);
define('PEAR_INFO_PACKAGES_SUMMARY', 1024);
define('PEAR_INFO_PACKAGES_VERSION', 512);
define('PEAR_INFO_PACKAGES_LICENSE', 256);
define('PEAR_INFO_PACKAGES_DESCRIPTION', 128);
define('PEAR_INFO_PACKAGES_DEPENDENCIES', 64);
define('PEAR_INFO_PACKAGES_XML', 32);
define('PEAR_INFO_PACKAGES_UPDATE', 16);
/**#@-*/
/**
 * Shows all of the above. This is the default value.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define('PEAR_INFO_ALL', 4095);
/**#@+
 * Information on PEAR credits.
 *
 * @var        integer
 * @since      1.7.0RC3
 */
define('PEAR_INFO_CREDITS_GROUP', 4096);
define('PEAR_INFO_CREDITS_DOCS', 8192);
define('PEAR_INFO_CREDITS_WEBSITE', 16384);
define('PEAR_INFO_CREDITS_PACKAGES', 32768);
define('PEAR_INFO_CREDITS_ALL', 61440);
/**#@-*/
/**
 * Indicates that a complete stand-alone HTML page needs to be printed
 * including the information indicated by the other flags.
 *
 * @var        integer
 * @since      1.7.0RC3
 */
define('PEAR_INFO_FULLPAGE', 65536);

/**
 * The PEAR_Info class generate phpinfo() style PEAR information.
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Davey Shafik <davey@pixelated-dreams.com>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.9.2
 * @link     http://pear.php.net/package/PEAR_Info
 * @since    Class available since Release 1.0.1
 */

class PEAR_Info
{
    /**
     * Html code for phpinfo() style PEAR information
     *
     * @var    string
     * @access public
     * @since  1.0.1
     */
    var $info;

    /**
     * Style sheet for the custom layout
     *
     * @var    string
     * @access public
     * @since  1.7.0RC1
     */
    var $css;

    /**
     * instance of PEAR_config
     *
     * @var    object
     * @access public
     * @since  1.0.1
     */
    var $config;

    /**
     * Alternative config files
     *
     * @var    array
     * @access public
     * @since  1.9.0
     */
    var $cfg;

    /**
     * instance of PEAR_Registry
     *
     * @var    object
     * @access public
     * @since  1.0.1
     */
    var $reg;

    /**
     * PHP 4 style constructor (ZE1)
     *
     * @param string $pear_dir    (optional) The PEAR base install directory
     * @param string $user_file   (optional) file to read PEAR user-defined
     *                            options from
     * @param string $system_file (optional) file to read PEAR system-wide
     *                            defaults from
     * @param array  $options     (optional) configure PEAR information output
     *
     * @return void
     * @access public
     * @since  version 1.0.1 (2003-04-24)
     */
    function PEAR_Info($pear_dir = '', $user_file = '', $system_file = '',
        $options = null)
    {
        $this->__construct($pear_dir, $user_file, $system_file, $options);
    }

    /**
     * PHP 5 style constructor (ZE2)
     *
     * @param string $pear_dir    (optional) The PEAR base install directory
     * @param string $user_file   (optional) file to read PEAR user-defined
     *                            options from
     * @param string $system_file (optional) file to read PEAR system-wide
     *                            defaults from
     * @param array  $options     (optional) configure PEAR information output
     *
     * @return void
     * @access private
     * @since  version 1.7.0RC1 (2007-07-01)
     */
    function __construct($pear_dir = '', $user_file = '', $system_file = '',
        $options = null)
    {
        // options defined at run-time (default)
        $this->options = array('channels' => array('pear.php.net'),
            'resume' => PEAR_INFO_ALL | PEAR_INFO_FULLPAGE);
        if (isset($options)) {
            // overwrite one to all defaults
            $this->options = array_merge($this->options, $options);
        }

        // to keep compatibility with version less or equal than 1.6.1
        if (!empty($pear_dir) && empty($user_file) && empty($system_file)) {
            $p_available           = (is_dir($pear_dir));
            $this->cfg['pear_dir'] = array($pear_dir, $p_available);

            if (!$p_available) {
                $e = '<p class="error">No valid PEAR directory</p>';

                $this->info = $e;
                return;
            }

            // try to find a PEAR user-defined config file into $pear_dir
            $user_file = $pear_dir . DIRECTORY_SEPARATOR;
            if (OS_WINDOWS) {
                $user_file .= 'pear.ini';
            } else {
                $user_file .= '.pearrc';
            }
            $u_available            = file_exists($user_file);
            $this->cfg['user_file'] = array($user_file, $u_available);

            // try to find a PEAR system-wide config file into $pear_dir
            $system_file = $pear_dir . DIRECTORY_SEPARATOR;
            if (OS_WINDOWS) {
                $system_file .= 'pearsys.ini';
            } else {
                $system_file .= 'pear.conf';
            }
            $s_available              = file_exists($system_file);
            $this->cfg['system_file'] = array($system_file, $s_available);

            if ($u_available) {
                if (!$s_available) {
                    $system_file = '';
                }
            } else {
                if (!$s_available) {
                    $e = '<p class="error">No PEAR configuration files ('
                        . basename($user_file) . ' or ' . basename($system_file)
                        . ") found into '$pear_dir' directory</p>";

                    $this->info = $e;
                    return;
                }
                $user_file = '';
            }
        }

        $this->config =& PEAR_Config::singleton($user_file, $system_file);

        // look for default PEAR installation
        if (empty($pear_dir)) {
            $php_dir               = $this->config->get('php_dir');
            $p_available           = (is_dir($php_dir));
            $this->cfg['pear_dir'] = array($php_dir, $p_available);

            $pear_user_file         = $this->config->getConfFile('user');
            $u_available            = file_exists($pear_user_file);
            $this->cfg['user_file'] = array($pear_user_file, $u_available);

            $pear_system_file         = $this->config->getConfFile('system');
            $s_available              = file_exists($pear_system_file);
            $this->cfg['system_file'] = array($pear_system_file, $s_available);
        }

        // to keep compatibility with version less or equal than 1.6.1
        if (defined('PEAR_INFO_PROXY')) {
            $this->config->set('http_proxy', PEAR_INFO_PROXY);
        }

        if (empty($user_file) || !file_exists($user_file)) {
            if (empty($system_file) || !file_exists($system_file)) {
                $user_file = $this->config->getConfFile('user');
                if (file_exists($user_file)) {
                    $layer = 'user';
                } else {
                    $system_file = $this->config->getConfFile('system');
                    $layer       = 'system';
                }
            } else {
                $layer = 'system';
            }
        } else {
            $layer = 'user';
        }
        // prevent unexpected result if PEAR config file does not exist
        if (!file_exists($user_file) && !file_exists($system_file)) {
            $e = '<p class="error">PEAR configuration files "'
                . $user_file . '", "' . $system_file . '" does not exist</p>';

            $this->info = $e;
            return;
        }
        // Get the config's registry object.
        $this->reg = &$this->config->getRegistry();

        // Get list of all channels in your PEAR install,
        // when 'channels' option is empty
        if (isset($this->options['channels'])
            && empty($this->options['channels'])) {
            $channels = $this->reg->listChannels();
            if (PEAR::isError($channels)) {
                $this->options['channels'] = array('pear.php.net');
            } else {
                $this->options['channels'] = $channels;
            }
        }

        // show general informations such as PEAR version, PEAR logo,
        // and config file used
        if ($this->options['resume'] & PEAR_INFO_GENERAL) {
            $pear         = $this->reg->getPackage("PEAR");
            $pear_version = $pear->getVersion();
            $this->info   = '
<table>
<tr class="h">
    <td>
        <a href="http://pear.php.net/">
            <img src="{phpself}?pear_image=true" alt="PEAR Logo" />
        </a>
        <h1 class="p">PEAR {pearversion}</h1>
    </td>
</tr>
</table>
';
            $this->info   = str_replace(array('{phpself}', '{pearversion}'),
                array(htmlentities($_SERVER['PHP_SELF']), $pear_version),
                $this->info);

            // Loaded configuration file
            $this->info .= '
<table>
<tr class="v">
    <td class="e">Loaded Configuration File</td>
    <td>{value}</td>
</tr>
<tr class="v">
    <td class="e">Alternative Configuration Files</td>
    <td>{alt}</td>
</tr>
</table>

';

            $alt = '<dl>';

            $found = $this->cfg['user_file'][1];
            if ($found) {
                $class = 'cfg_found';
            } else {
                $class = 'cfg_notfound';
            }
            $alt .= '<dt>USER file </dt>';
            $alt .= '<dd class="' . $class . '">'
                 . $this->cfg['user_file'][0] .'</dd>';

            $found = $this->cfg['system_file'][1];
            if ($found) {
                $class = 'cfg_found';
            } else {
                $class = 'cfg_notfound';
            }

            $alt .= '<dt>SYSTEM file </dt>';
            $alt .= '<dd class="' . $class . '">'
                 . $this->cfg['system_file'][0] .'</dd>';

            $alt .= '</dl>';

            $this->info = str_replace(array('{value}','{alt}'),
                array($this->config->getConfFile($layer), $alt),
                $this->info);
        }

        if (($this->options['resume'] & PEAR_INFO_CREDITS_ALL) ||
            isset($_GET['credits'])) {
            $this->info .= $this->getCredits();
        } else {
            if ($this->options['resume'] & PEAR_INFO_CREDITS) {
                $this->info .= '
<h1><a href="{phpself}?credits=true">PEAR Credits</a></h1>
';
                $this->info  = str_replace('{phpself}',
                    htmlentities($_SERVER['PHP_SELF']),
                    $this->info);
            }
            if ($this->options['resume'] & PEAR_INFO_CONFIGURATION) {
                $this->info .= $this->getConfig();
            }
            if ($this->options['resume'] & PEAR_INFO_CHANNELS) {
                $this->info .= $this->getChannels();
            }
            if ($this->options['resume'] & PEAR_INFO_PACKAGES) {
                $this->info .= $this->getPackages();
            }
        }
    }

    /**
     * Sets PEAR HTTP Proxy Server Address
     *
     * Sets http_proxy config setting at runtime
     *
     * @param string $proxy PEAR HTTP Proxy Server Address
     *
     * @static
     * @return bool
     * @access public
     * @since  version 1.0.6 (2003-05-11)
     */
    function setProxy($proxy)
    {
        $res = define('PEAR_INFO_PROXY', $proxy);
        return $res;
    }

    /**
     * Returns the custom style sheet to use for presentation
     *
     * Default behavior is to return css string contents.
     * Sets $content parameter to false will return css filename reference
     * (defined by setStyleSheet function).
     * Easy for a <link rel="stylesheet" type="text/css" href="" />
     * html tag integration (see example pear_info3.php).
     *
     * @param bool $content (optional) Either return css filename or string contents
     *
     * @return string
     * @access public
     * @since  version 1.7.0RC1 (2007-07-01)
     */
    function getStyleSheet($content = true)
    {
        if ($content) {
            $styles = file_get_contents($this->css);
        } else {
            $styles = $this->css;
        }
        return $styles;
    }

    /**
     * Sets the custom style sheet to use your own styles
     *
     * Sets the custom style sheet (colors, sizes) to applied to PEAR_Info output.
     * If you don't give any parameter, you'll then apply again the default style.
     *
     * @param string $css (optional) File to read user-defined styles from
     *
     * @return bool    True if custom styles, false if default styles applied
     * @access public
     * @since  version 1.7.0RC1 (2007-07-01)
     */
    function setStyleSheet($css = null)
    {
        // default stylesheet is into package data directory
        if (!isset($css)) {
            $this->css = '\xampp\php\PEAR\data' . DIRECTORY_SEPARATOR
                 . 'PEAR_Info' . DIRECTORY_SEPARATOR
                 . 'pearinfo.css';
        }

        $res = isset($css) && file_exists($css);
        if ($res) {
            $this->css = $css;
        }
        return $res;
    }

    /**
     * Retrieve and format PEAR Packages info
     *
     * @return string
     * @access private
     * @since  version 1.0.1 (2003-04-24)
     */
    function getPackages()
    {
        $available = $this->reg->listAllPackages();
        if (PEAR::isError($available)) {
            $e = '<p class="error">An Error occured while fetching the package list.'
               . ' Please try again.</p>';
            return $e;
        }
        if (!is_array($available)) {
            $e = '<p class="error">The package list could not be fetched'
               . ' from the remote server. Please try again.</p>';
            return $e;
        }

        // list of channels to scan
        $channel_allowed = $this->options['channels'];

        // check if there are new versions available for packages installed
        if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {

            $latest = array();
            foreach ($channel_allowed as $channel) {
                // Get a channel object.
                $chan =& $this->reg->getChannel($channel);
                if (PEAR::isError($chan)) {
                    $e = '<p class="error">An error has occured. '
                       . $chan->getMessage()
                       . ' Please try again.</p>';
                    return $e;
                }

                if ($chan->supportsREST($channel) &&
                    $base = $chan->getBaseURL('REST1.0', $channel)) {

                    $rest =& $this->config->getREST('1.0', array());
                    if (is_object($rest)) {
                        $pref_state = $this->config->get('preferred_state');
                        $installed  = array_flip($available[$channel]);

                        $l = $rest->listLatestUpgrades($base, $pref_state,
                                 $installed, $channel, $this->reg);
                    } else {
                        $l = false;
                    }
                } else {
                    $r =& $this->config->getRemote();
                    $l = @$r->call('package.listLatestReleases');
                }
                if (is_array($l)) {
                    $latest = array_merge($latest, $l);
                }
            }
        } else {
            $latest = false;
        }

        if ((PEAR::isError($latest)) || (!is_array($latest))) {
            $latest = false;
        }

        $s             = '';
        $anchor_suffix = 0;  // make page XHTML compliant
        foreach ($available as $channel => $pkg) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
            // sort package by alphabetic order
            sort($pkg);
            //
            $packages = '';
            $index    = array();
            foreach ($pkg as $name) {
                // show general package informations
                $info = &$this->reg->getPackage($name, $channel);
                if (!is_object($info)) {
                    continue; // should never arrive, if package is really installed
                }
                $__info               = $info->getArray();
                $installed['package'] = $info->getPackage();
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_CHANNEL) {
                    $installed['channel'] = $channel;
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_SUMMARY) {
                    $installed['summary'] = $info->getSummary();
                }
                $installed['version'] = $info->getVersion();
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_VERSION) {
                    $installed['current_release'] = $installed['version']
                        . ' (' . $info->getState() . ') was released on '
                        . $info->getDate();
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                    $installed['license'] = $info->getLicense();
                }
                if ($info->getPackagexmlVersion() == '1.0' ) {
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
                        $installed['lastmodified']
                            = $info->packageInfo('_lastmodified');
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                        $installed['packagexml'] = $info->getPackagexmlVersion();
                        if (isset($__info['packagerversion'])) {
                            $installed['packagerversion']
                                = $__info['packagerversion'];
                        }
                    }
                } else {
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                        $uri = $info->getLicenseLocation();
                        if ($uri) {
                            if (isset($uri['uri'])) {
                                $installed['license'] = '<a href="'
                                    . $uri['uri'] . '">'
                                    . $info->getLicense() . '</a>';
                            }
                        }
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
                        $installed['lastmodified'] = $info->getLastModified();
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                        $installed['packagexml'] = $info->getPackagexmlVersion();
                        $installed['packagerversion']
                            = $__info['attribs']['packagerversion'];
                    }
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_DESCRIPTION) {
                    $installed['description'] = $info->getDescription();
                }

                // show dependency list
                $dependencies = '';
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_DEPENDENCIES) {
                    $deps = $info->getDeps();
                    if (is_array($deps)) {
                        static $_deps_rel_trans = array(
                                     'lt' => '<',
                                     'le' => '<=',
                                     'eq' => '=',
                                     'ne' => '!=',
                                     'gt' => '>',
                                     'ge' => '>=',
                                     'has' => 'has',
                                     'not' => 'not'
                                     );
                        static $_deps_type_trans = array(
                                     'pkg' => 'Package',
                                     'ext' => 'Extension',
                                     'php' => 'PHP',
                                     'prog'=> 'Prog',
                                     'os'  => 'OS',
                                     'sapi'=> 'SAPI',
                                     'zend'=> 'Zend'
                                     );

                        $ptpl = '
<tr class="w">
    <td>
        {dep_required}
    </td>
    <td>
        {dep_type}
    </td>
    <td>
        {dep_name}
    </td>
    <td>
        {dep_rel}
    </td>
    <td>
        {dep_version}
    </td>
</tr>
';
                        foreach ($deps as $dep) {
                            if (!isset($dep['optional'])) {
                                $dep['optional'] = '';
                            }
                            if (isset($dep['name'])) {
                                if (isset($dep['channel'])) {
                                    $dep_name = '<a href="http://'
                                              . $dep['channel'] . '/' . $dep['name']
                                              . '">' . $dep['name'] . '</a>';
                                } else {
                                    $dep_name = $dep['name'];
                                }
                            } else {
                                $dep_name = '';
                            }
                            $dependencies .= str_replace(array('{dep_required}',
                                    '{dep_type}',
                                    '{dep_name}',
                                    '{dep_rel}',
                                    '{dep_version}',
                                    ),
                                array(($dep['optional'] == 'no') ? 'Yes' : 'No',
                                    $_deps_type_trans[$dep['type']],
                                    $dep_name,
                                    $_deps_rel_trans[$dep['rel']],
                                    isset($dep['version']) ? $dep['version'] : ''
                                    ),
                                $ptpl);
                        }
                        $ptpl = '
<tr class="w">
    <td class="f">
        Required
    </td>
    <td class="f">
        Type
    </td>
    <td class="f">
        Name
    </td>
    <td class="f">
        Relation
    </td>
    <td class="f">
        Version
    </td>
</tr>
';

                        $dependencies = $ptpl . $dependencies;
                    }
                } // end deps-list

                if (!isset($old_index)) {
                    $old_index = '';
                }
                $current_index = $name{0};
                if (strtolower($current_index) != strtolower($old_index)) {
                    $packages .= '<a id="' . $current_index . $anchor_suffix
                              . '"></a>';
                    $old_index = $current_index;
                    $index[]   = $current_index;
                }

                // prepare package informations template
                $ptpl = '
<h2><a id="pkg_{package_name}"></a><a href="http://{channel}/{package}">{package_name}</a></h2>
<table>
';

                $packages .= str_replace(array('{package_name}',
                                               '{package}','{channel}'),
                    array(trim($installed['package']), $name, $channel),
                    $ptpl);

                if ($this->options['resume'] & PEAR_INFO_PACKAGES_CHANNEL) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Channel
    </td>
    <td>
        {channel}
    </td>
</tr>
';

                    $packages .= str_replace('{channel}',
                        trim($installed['channel']),
                        $ptpl);
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_SUMMARY) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Summary
    </td>
    <td>
        {summary}
    </td>
</tr>
';

                    $packages .= str_replace('{summary}',
                        nl2br(htmlentities(trim($installed['summary']))),
                        $ptpl);
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_VERSION) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Version
    </td>
    <td>
        {version}
    </td>
</tr>
';

                    $packages .= str_replace('{version}',
                        trim($installed['current_release']),
                        $ptpl);
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        License
    </td>
    <td>
        {license}
    </td>
</tr>
';

                    $packages .= str_replace('{license}',
                        trim($installed['license']),
                        $ptpl);
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_DESCRIPTION) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Description
    </td>
    <td>
        {description}
    </td>
</tr>
';

                    $packages .= str_replace('{description}',
                        nl2br(htmlentities(trim($installed['description']))),
                        $ptpl);
                }
                if (!empty($dependencies)) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Dependencies
    </td>
    <td>
        <table class="d">
        {dependencies}
        </table>
    </td>
</tr>';

                    $packages .= str_replace('{dependencies}',
                        $dependencies,
                        $ptpl);
                }

                if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
                    if ($latest != false) {
                        if (isset($latest[$installed['package']])) {
                            $latestInstalledPkg = $latest[$installed['package']];
                            if (version_compare($latestInstalledPkg['version'],
                                $installed['version'], '>')) {
                                $ptpl = '
<tr class="v">
    <td class="e">
        Latest Version
    </td>
    <td>
        <a href="http://{channel}/get/{package}">{latest_version}</a>({latest_state})
    </td>
</tr>';

                                $packages .= str_replace(array('{package}',
                                        '{latest_version}',
                                        '{latest_state}',
                                        '{channel}'
                                        ),
                                    array(trim($installed['package']),
                                        $latestInstalledPkg['version'],
                                        $latestInstalledPkg['state'],
                                        $channel
                                        ),
                                    $ptpl);
                            }
                        }
                    }

                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                        $ptpl = '
<tr class="v">
    <td class="e">
        Package XML version
    </td>
    <td>
        {packagexml}
    </td>
</tr>';

                        $packagexml = $installed['packagexml'];
                        if (isset($installed['packagerversion'])) {
                            $packagexml .= ' packaged with PEAR version '
                                . $installed['packagerversion'];
                        }
                        $packages .= str_replace('{packagexml}',
                            $packagexml,
                            $ptpl);
                    }
                    $ptpl = '
<tr class="v">
    <td class="e">
        Last Modified
    </td>
    <td>
        {lastmodified}
    </td>
</tr>';

                    $packages .= str_replace('{lastmodified}',
                        date('Y-m-d', $installed['lastmodified']),
                        $ptpl);

                }

                $packages .= '
<tr>
    <td colspan="2" class="v"><a href="#{top}">Top</a></td>
</tr>
</table>
';
                $packages  = str_replace('{top}', 'top'.$anchor_suffix, $packages);
            }

            $index_header = '
<h2><a id="{top}">{count} Installed Packages, Channel {channel}</a></h2>
';

            if (count($pkg) > 0) {
                // improve render and display index only when there are packages
                $index_header .= '
<table>
<tr>
    <td class="e">
        Index
    </td>
</tr>
<tr>
    <td class ="v" style="text-align: center">
';
            }
            $index_header = str_replace(array('{channel}', '{top}', '{count}'),
                array($channel, 'top'.$anchor_suffix, count($pkg)), $index_header);
            foreach ($index as $i) {
                $index_header .= ' | <a href="#'.$i.$anchor_suffix.'">'
                              . strtoupper($i) . '</a>';
            }
            if (count($pkg) > 0) {
                // improve render and display index only when there are packages
                $index_header .= ' |
    </td>
</tr>
</table>

';
            }
            $s .= $index_header . $packages;
            $anchor_suffix++;
        }
        return $s;
    }

    /**
     * Retrieves and formats the PEAR Config data
     *
     * @return string
     * @access private
     * @since  version 1.0.1 (2003-04-24)
     */
    function getConfig()
    {
        $keys = $this->config->getKeys();
        sort($keys);

        $html_pear_config = '
<h2>PEAR Configuration</h2>
<table>';
        foreach ($keys as $key) {
            if (   ($key != 'password')
                && ($key != 'username')
                && ($key != 'sig_keyid')
                && ($key != 'http_proxy')) {
                $html_config = '
<tr class="v">
    <td class="e">{key}</td>
    <td>{value}</td>
</tr>';

                $html_config = str_replace(array('{key}', '{value}'),
                    array($key, $this->config->get($key)),
                    $html_config);

                $html_pear_config .= $html_config;
            }
        }
        $html_pear_config .= '
</table>

';
        return $html_pear_config;
    }

    /**
     * Retrieves and formats the PEAR Channel data
     *
     * @return string
     * @access private
     * @since  version 1.7.0RC1 (2007-07-01)
     */
    function getChannels()
    {
        $channels = $this->reg->listChannels();
        if (PEAR::isError($channels)) {
            $e = '<p class="error">An Error occured while fetching the channel list.'
               . ' Please try again.</p>';
            return $e;
        }
        $channel_allowed = $this->options['channels'];

        $html_pear_channel = '
<h2>PEAR Channels</h2>';

        $anchor_suffix = 0;
        foreach ($channels as $channel) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
            $html_pear_channel .= '
<table>';

            $info = $this->reg->channelInfo($channel);
            if (PEAR::isError($info) || is_null($info)) {
                $e = '<p class="error">An Error occured while fetching '
                   . $channel . ' channel data.'
                   . ' Please try again.</p>';
                return $e;
            }

            $data = array('name' => $info['name']);
            if (isset($info['suggestedalias'])) {
                $data['alias'] = $info['suggestedalias'];
            }
            $data['summary'] = $info['summary'];

            foreach ($data as $key => $value) {
                $html_channel = '
<tr class="v">
    <td class="e">{key}</td>
    <td>{value}</td>
</tr>';
                if ($key == 'name') {
                    $value = '<a href="#top' . $anchor_suffix . '">'
                        . $value . '</a>';
                }
                $html_channel = str_replace(array('{key}', '{value}'),
                    array(ucfirst($key), $value),
                    $html_channel);

                $html_pear_channel .= $html_channel;
            }
            $html_pear_channel .= '
</table>
<br />

';
            $anchor_suffix++;
        }

        return $html_pear_channel;
    }

    /**
     * Retrieves and formats the PEAR Credits
     *
     * @return string
     * @access private
     * @since  version 1.0.1 (2003-04-24)
     */
    function getCredits()
    {
        $html_pear_credits = '<h1>PEAR Credits</h1>';

        $teams = PEAR_Info::getMembers();

        if (($this->options['resume'] & PEAR_INFO_CREDITS_GROUP) ||
            isset($_GET['credits'])) {
            $html_pear_credits .= '
<table>
    <tr class="hc">
        <td colspan="2">
            PEAR Group
        </td>
    </tr>
    <tr class="v">
        <td class="e">
            President
        </td>
        <td>
            {president}
        </td>
    </tr>
    <tr class="v">
        <td colspan="2">
';
            foreach ($teams['president'] as $handle => $name) {
                $html_member
                    = '<a href="http://pear.php.net/account-info.php?handle='
                    . $handle .'">'. $name .'</a>,';

                $html_pear_credits = str_replace('{president}',
                    $html_member, $html_pear_credits);
            }

            foreach ($teams['group'] as $handle => $name) {
                $html_member
                    = '<a href="http://pear.php.net/account-info.php?handle='
                    . $handle .'">'. $name .'</a>,';

                $html_pear_credits .= $html_member;
            }

            $html_pear_credits .= '
        </td>
    </tr>
</table>
<br />
';
        }

        if (($this->options['resume'] & PEAR_INFO_CREDITS_DOCS) ||
            isset($_GET['credits'])) {
            if (count($teams['docs']) > 0) {
                $html_pear_credits .= '
<table>
    <tr class="hc">
        <td>
            PEAR Documentation Team
        </td>
    </tr>
    <tr class="v">
        <td>
';
                foreach ($teams['docs'] as $handle => $name) {
                    $html_member
                        = '<a href="http://pear.php.net/account-info.php?handle='
                        . $handle .'">'. $name .'</a>,';

                    $html_pear_credits .= $html_member;
                }

                $html_pear_credits .= '
        </td>
    </tr>
</table>
<br />
';
            }
        }

        if (($this->options['resume'] & PEAR_INFO_CREDITS_WEBSITE) ||
            isset($_GET['credits'])) {
            if (count($teams['website']) > 0) {
                $html_pear_credits .= '
<table>
    <tr class="hc">
        <td>
            PEAR Website Team
        </td>
    </tr>
    <tr class="v">
        <td>
';
                foreach ($teams['website'] as $handle => $name) {
                    $html_member
                        = '<a href="http://pear.php.net/account-info.php?handle='
                        . $handle .'">'. $name .'</a>,';

                    $html_pear_credits .= $html_member;
                }

                $html_pear_credits .= '
        </td>
    </tr>
</table>
<br />
';
            }
        }

        if (!($this->options['resume'] & PEAR_INFO_CREDITS_PACKAGES) &&
            !isset($_GET['credits'])) {
            return $html_pear_credits;
        }

        // Credits authors of packages group by channels
        $channel_allowed = $this->options['channels'];

        $available = $this->reg->listAllPackages();
        if (PEAR::isError($available)) {
            $e = '<p class="error">An Error occured while fetching the credits'
               . ' from the remote server. Please try again.</p>';
            return $e;
        }
        if (!is_array($available)) {
            $e = '<p class="error">The credits could not be fetched'
               . ' from the remote server. Please try again.</p>';
            return $e;
        }

        foreach ($available as $channel => $pkg) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
            if (count($pkg) == 0) {
                // improve render and did not display channel without package
                continue;
            }
            $html_pear_credits .= '
<br />
<table border="0" cellpadding="3" width="600">
<tr class="hc"><td colspan="2">Channel {channel}</td></tr>
<tr class="h"><td>Package</td><td>Maintainers</td></tr>';

            $html_pear_credits = str_replace('{channel}', $channel,
                                     $html_pear_credits);

            // sort package by alphabetic order
            sort($pkg);
            //
            foreach ($pkg as $name) {
                $info = &$this->reg->getPackage($name, $channel);
                if (is_object($info)) {
                    $installed['package']     = $info->getPackage();
                    $installed['maintainers'] = $info->getMaintainers();
                } else {
                    $installed = $info;
                }

                $ptpl = '
<tr>
    <td class="e">
        <a href="http://{channel}/{packageURI}">{package}</a>
    </td>
    <td class="v">
        {maintainers}
    </td>
</tr>';

                $maintainers = array();
                foreach ($installed['maintainers'] as $i) {
                    $maintainers[]
                        = '<a href="http://pear.php.net/account-info.php?handle='
                        . $i['handle']. '">'
                        . htmlentities(html_entity_decode(utf8_decode($i['name'])))
                        . '</a>'
                        .' (' . $i['role']
                        . (isset($i['active']) && $i['active'] === 'no'
                            ? ', inactive' : '')
                        . ')';
                }
                $maintainers = implode(', ', $maintainers);

                $html_pear_credits .= str_replace(array('{packageURI}',
                        '{package}',
                        '{channel}',
                        '{maintainers}'
                        ),
                    array(trim(strtolower($installed['package'])),
                        trim($installed['package']),
                        $channel,
                        $maintainers
                        ),
                    $ptpl);
            }
            $html_pear_credits .= '
</table>
';
        }
        return $html_pear_credits;
    }

    /**
     * Display the PEAR logo
     *
     * Display the PEAR logo (gif image) on browser output
     *
     * @return void
     * @access public
     * @since  version 1.0.1 (2003-04-24)
     */
    function pearImage()
    {
        $pear_image
            = 'R0lGODlhaAAyAMT/AMDAwP3+/TWaAvD47Pj89vz++zebBDmcBj6fDEek'
            . 'FluvKmu3PvX68ujz4XvBS8LgrNXqxeHw1ZnPaa/dgvv9+cLqj8LmltD2msnuls'
            . '3xmszwmf7+/f///wAAAAAAAAAAACH5BAEAAAAALAAAAABoADIAQAX/IC'
            . 'COZGmeaKqubOtWWjwJphLLgH1XUu//C1Jisfj9YLEKQnSY3GaixWQqQTkYHM4'
            . 'AMulNLJFC9pEwIW/odKU8cqTfsWoTTtcomU4ZjbR4ZP+AgYKCG0EiZ1A'
            . 'uiossEhwEXRMEg5SVWQ6MmZqKWD0QlqCUEHubpaYlExwRPRZioZZVp7KzKQoS'
            . 'DxANDLsNXA5simd2FcQYb4YAc2jEU80TmAAIztPCMcjKdg4OEsZJmwIW'
            . 'WQPQI4ikIwtoVQnddgrv8PFlCWgYCwkI+fp5dkvJ/IlUKMCy6tYrDhNIIKLFE'
            . 'AWCTxse+ABD4SClWA0zovAjcUJFi6EwahxZwoGqHhFA/4IqoICkyxQSK'
            . 'kbo0gDkuBXV4FRAJkRCnTgi2P28IcEfk5xpWppykFJVuScmEvDTEETAVJ6bEp'
            . 'ypcADPkz3pvKVAICHChkC7siQ08zVqu4Q6hgIFEFZuEn/KMgRUkaBmAQ'
            . 's+cEHgIiHVH5EAFpIgW4+NT6LnaqhDwe/Ov7YOmWZp4MkiAWBIl0kAVsJWuzc'
            . 'YpdiNgddc0E8cKBAu/FElBwagMb88ZZKDRAkWJtkWhHh3wwUbKHQJN3w'
            . 'QAaXGR2LpArv5oFHRR34C7Mf6oLXZNfqBgNI7oOLhj1f8PaGpygHQ0xtP8MDV'
            . 'KwYTSKcgxr9/hS6/pCCAAg5M4B9/sWh1YP9/XSgQWRML/idBfKUc4IBE'
            . 'T9lFjggKhDYZAELZJYEBI2BDB3ouNBEABwE8gAwiCcSYgAKqPdEVAG7scM8BP'
            . 'PZ4AIlM+OgjAgpMhRE24OVoBwsIFEGFA7ZkQQBWienWxmRa7XDjKZXhB'
            . 'dAeSmKQwgLuUVLICa6VEKIGcK2mQWoVZHCBXJblJUFkY06yAXlGsPIHBEYdYi'
            . 'WHb+WQBgaIJqqoHFNpgMGB7dT5ZQuG/WbBAIAUEEFNfwxAWpokTIXJAW'
            . 'dgoJ9kRFG2g5eDRpXSBpEIF0oEQFaZhDbaSFANRgqcJoEDRARLREtxOQpsPO9'
            . '06ZUeJgjQB6dZUPBAdwcF8KLXXRVQaKFcsRRLJ6vMiiCNKxRE8ECZKgU'
            . 'A3Va4arOAAqdGRWO7uMZH5AL05gvsjQbg6y4NCjQ1kw8TVGcbdoKGKx8j3bGH'
            . '7nARBArqwi0gkFJBrZiXBQRbHoIgnhSjcEBKfD7c3HMhz+JIQSY3t8GG'
            . 'KW+SUhfUajxGzKd0IoHBNkNQK86ZYEqdzYA8AHQpqXRUm80oHs1CAgMoBxzRq'
            . 'vzs9CIKECC1JBp7enUpfXHApwVYNAfo16c4IrYPLVdSAJVob7IAtCBFQ'
            . 'GHcs/RRdiUDPHA33oADEAIAOw==';
        header('content-type: image/gif');
        echo base64_decode($pear_image);
    }

    /**
     * Returns a members list depending of its category (group, docs, website)
     *
     * Retrieve the members list of PEAR group, PEAR doc team, or PEAR website team
     *
     * @param string $group (optional) Member list category.
     *                      Either president, group, docs or website
     * @param bool   $sort  (optional) Return a member list sorted
     *                      in alphabetic order
     *
     * @static
     * @return array
     * @access public
     * @since  version 1.7.0RC3 (2007-07-10)
     */
    function getMembers($group = 'all', $sort = true)
    {
        $members = array(
            'president' => array('davidc' => 'David Coallier'),
            'group'     => array(
                'jeichorn' => 'Joshua Eichorn',
                'dufuz' => 'Helgi &thorn;ormar',
                'jstump' => 'Joe Stump',
                'cweiske' => 'Christian Weiske',
                'ashnazg' => 'Chuck Burgess',
                'tswicegood' => 'Travis Swicegood',
                'saltybeagle' => 'Brett Bieber',
                ),
            'docs'      => array(
                ),
            'website'   => array(
                )
            );

        if ($group === 'all') {
            $list = $members;
            if ($sort === true) {
                asort($list['group']);
                asort($list['docs']);
                asort($list['website']);
            }
        } elseif (in_array($group, array_keys($members))) {
            $list = $members[$group];
            if ($sort === true) {
                asort($list);
            }
        } else {
            $list = false;
        }
        return $list;
    }

    /**
     * Shows PEAR_Info output
     *
     * Displays PEAR_Info output depending of style applied (style sheet).
     *
     * @return void
     * @access public
     * @since  version 1.0.1 (2003-04-24)
     * @see    setStyleSheet()
     * @deprecated  use display() instead
     */
    function show()
    {
         $this->display();
    }

    /**
     * Displays PEAR_Info output
     *
     * Displays PEAR_Info output depending of style applied (style sheet).
     *
     * @return void
     * @access public
     * @since  version 1.7.0RC1 (2007-07-01)
     * @see    setStyleSheet()
     */
    function display()
    {
         echo $this->toHtml();
    }

    /**
     * Returns PEAR_Info output (html code)
     *
     * Returns html code. This code is XHTML 1.1 compliant since version 1.7.0
     * A stand-alone HTML page will be printed only if PEAR_INFO_FULLPAGE
     * resume options is set.
     *
     * @return string
     * @access public
     * @since  version 1.7.0RC1 (2007-07-01)
     * @see    setStyleSheet(), getStyleSheet()
     */
    function toHtml()
    {
        $body = $this->info;

        if (!($this->options['resume'] & PEAR_INFO_FULLPAGE)) {
            return $body;
        }

        if (!isset($this->css)) {
            // when no user-styles defined, used the default values
            $this->setStyleSheet();
        }
        $styles = $this->getStyleSheet();

        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>PEAR :: PEAR_Info()</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
<!--
$styles
 -->
</style>
</head>
<body>
<div>
$body
</div>
</body>
</html>
HTML;
        return $html;
    }

    /**
     * Check if a package is installed
     *
     * Simple function to check if a package is installed under user
     * or system PEAR installation. Minimal version and channel info are supported.
     *
     * @param string $name        Package name
     * @param string $version     (optional) The minimal version
     *                            that should be installed
     * @param string $channel     (optional) The package channel distribution
     * @param string $user_file   (optional) file to read PEAR user-defined
     *                            options from
     * @param string $system_file (optional) file to read PEAR system-wide
     *                            defaults from
     *
     * @static
     * @return bool
     * @access public
     * @since  version 1.6.0 (2005-01-03)
     */
    function packageInstalled($name, $version = null, $channel = null,
        $user_file = '', $system_file = '')
    {
        $config =& PEAR_Config::singleton($user_file, $system_file);
        $reg    =& $config->getRegistry();

        if (is_null($version)) {
            return $reg->packageExists($name, $channel);
        } else {
            $info = &$reg->getPackage($name, $channel);
            if (is_object($info)) {
                $installed['version'] = $info->getVersion();
            } else {
                $installed = $info;
            }
            return version_compare($version, $installed['version'], '<=');
        }
    }
}

if (isset($_GET['pear_image'])) {
    PEAR_Info::pearImage();
    exit();
}
?>