<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<title><?php echo htmlspecialchars($this->title); ?></title>
	</head>
	<body>
<?php
	if ($bootstrap->env == 'development') {
		var_dump($this);
	}

	$message = $this->message;
	$pos = strpos($message, ' in');
	$message = substr($message, 0, $pos);
?>
		<h1><?php echo htmlspecialchars($this->title); ?></h1>
		<p><?php echo htmlspecialchars($message); ?></p>
	</body>
</html>
