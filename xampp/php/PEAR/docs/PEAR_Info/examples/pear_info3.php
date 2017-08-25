<?php
/**
 * Generate phpinfo() style PEAR information,
 * embedded into user-defined html template
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id: pear_info3.php,v 1.2 2007/12/15 16:27:23 farell Exp $
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

require_once 'PEAR/Info.php';

/**
 * @ignore
 */
class PEAR_Info3 extends PEAR_Info
{
    function PEAR_Info3($pear_dir = '', $user_file = '', $system_file = '')
    {
        $this->__construct($pear_dir, $user_file, $system_file);
    }

    function toHtml()
    {
        $styles = basename($this->getStyleSheet(false));

        $body = $this->info;

        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Laurent Laville" />
<title>My PEAR_Info()</title>
<link rel="stylesheet" type="text/css" href="$styles" />
</head>
<body>

<div id="header">
<h1>Laurent-Laville.org</h1>
</div>

<div id="footer">
</div>

<div id="contents">
$body
</div>

</body>
</html>
HTML;
        return $html;
    }
}

// Create PEAR_Info object
$info = new PEAR_Info3();

// set your own styles, rather than use the default stylesheet
$info->setStyleSheet(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pearinfo3.css');

// Display PEAR_Info output
$info->display();
?>