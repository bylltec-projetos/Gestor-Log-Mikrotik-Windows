<?php
/**
 * CLI Script to generate text phpinfo() style PEAR information
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
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: Cli.php,v 1.4 2009/01/07 21:52:44 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @since    File available since Release 1.8.0
 */

require_once 'PEAR/Info.php';
require_once 'Console/Getargs.php';

/**
 * CLI Script to display information about your PEAR installation
 *
 * <code>
 * <?php
 *     require_once 'PEAR/Info/Cli.php';
 *     $cli = new PEAR_Info_Cli();
 *     $cli->run();
 * ?>
 * </code>
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.9.2
 * @link     http://pear.php.net/package/PEAR_Info
 * @since    Class available since Release 1.8.0
 */

class PEAR_Info_Cli extends PEAR_Info
{
    /**
     * @var    array    Current CLI Flags
     * @since  1.8.0
     */
    var $opts = array();

    /**
     * @var    string   error message
     * @since  1.8.0
     */
    var $error;

    /**
     * @var    object   Console_Getargs instance
     * @since  1.8.0
     */
    var $args;


    /**
     * ZE2 Constructor
     *
     * @since  1.8.0
     */
    function __construct()
    {
        $this->opts = array(
            'php-dir' =>
                array('short' => 'pd',
                      'desc'  => 'PEAR directory',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'usr-cfg' =>
                array('short' => 'uc',
                      'desc' => 'User Configuration File',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'sys-cfg' =>
                array('short' => 'sc',
                      'desc' => 'System Configuration File',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'http-proxy' =>
                array('short' => 'hp',
                      'desc' => 'HTTP Proxy Server Address',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'all' =>
                array('short'   => 'a',
                      'desc'    => 'Display informations for installed packages',
                      'default' => PEAR_INFO_ALL,
                      'min'     => 0 , 'max' => 1),
            'allchannels' =>
                array('short' => 'A',
                      'desc'  => 'List packages from all channels, '
                               . 'not just the default one',
                      'max'   => 0),
            'channel' =>
                array('short'   => 'c',
                      'desc'    => 'Specify which channel',
                      'default' => '',
                      'min'     => 0 , 'max' => 1),
            'version' =>
                array('short' => 'V',
                      'desc'  => 'Print version information',
                      'max'   => 0),
            'help' =>
                array('short' => 'h',
                      'desc'  => 'Show this help',
                      'max'   => 0),
        );
        $this->args = & Console_Getargs::factory($this->opts);
        if (PEAR::isError($this->args)) {
            if ($this->args->getCode() === CONSOLE_GETARGS_HELP) {
                $this->error = '';
            } else {
                $this->error = $this->args->getMessage();
            }
            return;
        }

        $options = array('channels' => array('pear.php.net'),
                         'resume' => PEAR_INFO_ALL);

        // php-dir
        if ($this->args->isDefined('pd')) {
            $pear_dir = $this->args->getValue('pd');
        } else {
            $pear_dir = '';
        }

        // usr-cfg
        if ($this->args->isDefined('uc')) {
            $user_file = $this->args->getValue('uc');
            if (!file_exists($user_file)) {
                $this->error = 'Failed opening PEAR user configuration file "'
                     . $user_file
                     . '". Please check your spelling and try again.';
                return;
            }
        } else {
            $user_file = '';
        }

        // sys-cfg
        if ($this->args->isDefined('sc')) {
            $system_file = $this->args->getValue('sc');
            if (!file_exists($system_file)) {
                $this->error = 'Failed opening PEAR system configuration file "'
                     . $system_file
                     . '". Please check your spelling and try again.';
                return;
            }
        } else {
            $system_file = '';
        }

        // http-proxy
        if ($this->args->isDefined('hp')) {
            $proxy = $this->args->getValue('hp');
            $res   = PEAR_Info::setProxy($proxy);
            if ($res === false) {
                $this->error = 'Failed define Proxy Server Address.'
                     . ' Please check your spelling and try again.';
                return;
            }
        }

        // all
        if ($this->args->isDefined('a')) {
            $a = $this->args->getValue('a');
            if (is_numeric($a)) {
                $options['resume'] = intval($a);
            } else {
                $this->error = "No valid 'resume' option for argument all."
                     . ' Please check your spelling and try again.';
                return;
            }
        }

        // allchannels
        $A = $this->args->getValue('A');
        if (isset($A)) {
            $options['channels'] = array();
        }

        // channel
        if ($this->args->isDefined('c')) {
            $chan = $this->args->getValue('c');
            if (is_string($chan)) {
                $options['channels'] = explode(',', $chan);
            } else {
                $this->error = 'No valid channel list provided.'
                     . ' Please check your spelling and try again.';
                return;
            }
        }

        // version
        $V = $this->args->getValue('V');
        if (isset($V)) {
            $this->error = 'PEAR_Info (cli) version 1.9.2'
                         . ' (http://pear.php.net/package/PEAR_Info)';
            return;
        }

        parent::__construct($pear_dir, $user_file, $system_file, $options);
    }

    /**
     * ZE1 PHP4 Compatible Constructor
     *
     * @since  1.8.0
     */
    function PEAR_Info_Cli()
    {
        $this->__construct();
    }

    /**
     * Run the CLI Script
     *
     * @return void
     * @access public
     * @since  1.8.0
     */
    function run()
    {
        if (isset($this->error)) {
            if (strpos($this->error, 'PEAR_Info') === false) {
                $this->_printUsage($this->error);
            } else {
                // when Version asked, do not print help usage
                echo $this->error;
            }
            return;
        }

        $table      = 0;
        $skip_table = 0;
        $tdeps      = false;
        $td         = '';
        $lines      = explode("\n", $this->info);

        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) == 0 && $tdeps === false) {
                // skip blank line

            } elseif ($line == '<br />') {
                // skip

            } elseif (substr($line, 0, 6) == '<a id=') {
                // skip package anchor

            } elseif (substr($line, 0, 4) == '<h1>') {
                // skip PEAR credits link

            } elseif (substr($line, 0, 4) == '<h2>') {
                if (substr($line, 4, 10) == '<a id="top') {
                    // skip anchors
                    $skip_table = 1;
                } else {
                    $td = strip_tags($line);
                    echo PHP_EOL . $td . PHP_EOL;
                }

            } elseif ($skip_table == 0 && $line == '<table>') {
                echo PHP_EOL;
                $table++;

            } elseif ($line == '</table>') {
                $skip_table = 0;
                if ($tdeps) {
                    $td = PHP_EOL . implode(PHP_EOL, $deps);
                }
                $tdeps = false;

            } elseif ($skip_table == 0 && $line == '<tr>') {
                $tr_class = '';

            } elseif ($skip_table == 0 && substr($line, 0, 3) == '<tr') {
                $tr_class = substr($line, 11, 1);
                if ($tr_class == 'w') {
                    $tdeps = true;
                    $tr_w  = array();
                    $w     = 0;
                } elseif ($tr_class == 'h') {
                    $td = '';
                } else {
                    $td = array('', '');
                }

            } elseif ($skip_table == 0 && $line == '</tr>' && $tr_class != '') {
                if ($tr_class == 'h') {
                    $td = explode(' ', $td);
                }
                if (isset($td_v)) {
                    $td = $td_v;
                }
                if ($tdeps) {
                    if ($tr_w[0] != 'Required') {
                        if ($tr_w[1] == 'PHP') {
                            $td = $tr_w[1] .' '. $tr_w[3] .' '. $tr_w[4];
                        } else {
                            if ($tr_w[3] == 'has' || $tr_w[3] == 'not') {
                                $td = $tr_w[3] .' '. $tr_w[2] .' '. $tr_w[1] .' '.
                                      $tr_w[4];
                            } else {
                                $td = strip_tags($tr_w[2]) .' '. $tr_w[1] .' '.
                                      $tr_w[3] .' '. $tr_w[4];
                            }
                            if ($tr_w[0] == 'No') {
                                $td .= ' (optional)';
                            }
                        }
                        $deps[] = ' - ' . $td;
                    }
                    $w = 0;
                } else {
                    if (isset($td_c) && count($td_c) > 0) {
                        echo $td[0] . ' => ' . PHP_EOL;
                        echo $td_c[0] . PHP_EOL;
                        echo $td_c[1] . PHP_EOL;
                    } else {
                        echo $td[0] . ' => ' . $td[1] . PHP_EOL;
                    }
                }

            } elseif ($skip_table == 0 && $line == '</td>') {
                if (isset($td_v)) {
                    if (empty($td_v[0])) {
                        $td_v[0] = $td;
                        $td      = '';
                    } else {
                        $td_v[1] = $td;
                    }
                }

            } elseif ($skip_table == 0 && $line == '<td>') {
                // skip

            } elseif ($skip_table == 0 && substr($line, 0, 3) == '<td') {
                if ($tr_class == 'h') {
                    $td = '';

                } elseif ($tr_class == 'v') {
                    if ($line == '<td class="e">') {
                        $td_v = array('', '');
                        $td   = '';
                    } else {
                        unset($td_v);
                        preg_match('`\<td(.*)\>(.*)\</td\>`', $line, $matches);
                        if (empty($matches[2])) {
                            $td_c = array();
                            if (substr($line, 0, 8) == '<td><dl>') {
                                preg_match('`\<td\>\<dl\>\<dt\>(.*)\</dt\>(.*)\<dt\>(.*)\</dt\>(.*)\</dl\>\</td\>`', $line, $matches);
                                $td_c[0] = ' - '.$matches[1];
                                $td_c[1] = ' - '.$matches[3];
                                $dd_user = $matches[2];
                                $dd_sys  = $matches[4];
                                preg_match('`\<dd class="cfg_(.*)"\>(.*)\</dd\>`', $dd_user, $matches);
                                $td_c[0] .= strtoupper($matches[1]).' => '.$matches[2];
                                preg_match('`\<dd class="cfg_(.*)"\>(.*)\</dd\>`', $dd_sys, $matches);
                                $td_c[1] .= strtoupper($matches[1]).' => '.$matches[2];
                            } else {
                                $td[1] = strip_tags($matches[0]);
                            }
                        } else {
                            if (empty($matches[1])) {
                                $td[1] = strip_tags($matches[2]);
                            } else {
                                $td[0] = $matches[2];
                            }
                        }
                    }
                }

            } else {
                if ($tdeps) {
                    $tr_w[$w] = $line;
                    $w++;
                } else {
                    if ($line == '<table class="d">') {
                        $deps = array();
                    } else {
                        $line = strip_tags($line);
                        $td  .= $line;
                    }
                }
            }
        }
    }

    /**
     * Show full help information
     *
     * @param string $footer (optional) page footer content
     *
     * @return void
     * @access private
     */
    function _printUsage($footer = '')
    {
        $header = 'Usage: '
            . basename($_SERVER['SCRIPT_NAME']) . " [options]\n\n";
        echo Console_Getargs::getHelp($this->opts, $header,
            "\n$footer\n", 78, 2)."\n";
    }
}
?>