<?php
ini_set('error_log', 'error.log');
?>
<html>
<head>
	<title>Spis błędów</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
	<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
	<p><input type="submit" value="Odśwież"><input type="submit" name="Czysc" value="Wyczyść logi"></p>
	</form>
	<hr width="100%">
<?php
	$Plik = "error.log";
	if (isset($_POST['Czysc'])) {
		if ($el = fopen($Plik, 'w')) {
			fclose($el);
		}
	}
	echo(nl2br(file_get_contents($Plik)));
?>
	<hr width="100%">
	<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
	<p><input type="submit" value="Odśwież"><input type="submit" name="Czysc" value="Wyczyść logi"></p>
	</form>
</body>
</html>