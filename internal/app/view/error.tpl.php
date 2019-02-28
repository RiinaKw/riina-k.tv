<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<title><?php echo $this->title; ?> - rk. tv</title>
	</head>
	<body>
<?php
	if ($bootstrap->env == 'development') {
		var_dump($this);
	}
?>
		<h1><?php echo $this->title; ?></h1>
		<p><?php echo $this->message; ?></p>
		<pre><?php echo $this->trace; ?></pre>
	</body>
</html>
