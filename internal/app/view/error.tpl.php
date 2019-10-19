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
?>
		<h1><?php echo htmlspecialchars($this->title); ?></h1>
		<p><?php echo htmlspecialchars($this->message); ?></p>
	</body>
</html>
