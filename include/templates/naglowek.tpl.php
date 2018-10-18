<!DOCTYPE html>
<html>
<head>
	<title>PANELplus</title>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Expires" content="-1">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="author" content="ARTplus">
	<meta name="robots" content="nofollow, noindex">
	<link href="css/envelope.css" rel="stylesheet" media="screen">
        <link href="css/style.css" rel="stylesheet" media="screen">
        <link href="css/form.css" rel="stylesheet" media="screen">
        <link href="css/chosen.css" rel="stylesheet" media="screen">
        <link href="css/ui.notify.css" rel="stylesheet" media="screen">
        <link href="css/jquery-datepicker-light.css" rel="stylesheet" media="screen">
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" media="screen">
        <link href="css/jquery.qtip.css" rel="stylesheet" media="screen">
	<!--[if IE]>
		<link href="css/style-ie.css" rel="stylesheet" media="screen">
	<![endif]-->
        <script language="javascript" type="text/javascript" src='js/ajax.js'></script>
        <script language="javascript" type="text/javascript" src='js/jquery-1.10.2.js'></script>
        <script language="javascript" type="text/javascript" src='js/jquery-ui-1.10.3.custom.js'></script>
        <script language="javascript" type='text/javascript' src='js/plugins/datapicker-lang/jquery.ui.datepicker-pl.js'></script>
        <script language="javascript" type="text/javascript" src='js/plugins/jquery.notify.js'></script>
        <script language="javascript" type="text/javascript" src="js/plugins/jquery.qtip.js"></script>
        <script language="javascript" type="text/javascript" src='js/jsfunction.js'></script>
        <?php if(isset($_GET['modul']) && isset($_GET['akcja']) && $_GET['modul'] == 'zlecenia' && $_GET['akcja'] == 'szczegoly'){ ?>
	  <script language="javascript" type="text/javascript" src='js/items.js'></script>
        <?php  } ?>
</head>
<body>
<div id="container" style="display:none">
    <div id="default">
        <a class="ui-notify-cross ui-notify-close" href="#">x</a>
        <h1>#{title}</h1>
        <p>#{text}</p>
    </div>
</div>