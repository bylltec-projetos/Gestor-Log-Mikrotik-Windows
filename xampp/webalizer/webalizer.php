<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="/xampp/xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		<pre>

<?php
    set_time_limit(0);
    while (@ob_end_flush());
	system('webalizer.bat');
?>
		</pre>
		<script type="text/javascript">
	    <!--
	        window.setTimeout("window.location.replace('/webalizer/');", 5000);
		//-->
		</script>
        <noscript>
            <br>
            <a href="/webalizer/">Click here to view the statistics.</a>
        </noscript>

	</body>
</html>
