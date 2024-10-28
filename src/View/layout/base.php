<!DOCTYPE html>
<html lang="fr">
<head>
	<?php
	$config = require __DIR__ . '/../../../config/config.php';
	$baseUrl = $config['base_url'];
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title ?? 'TomTroc' ?></title>
	<link rel="stylesheet" href="<?php echo $baseUrl ?>/assets/css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<main>
	<?php
	if (isset($content)) {
		include $content;
	}
	?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
