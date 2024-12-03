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
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
		  rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $baseUrl ?>/assets/css/base.css">
	<link rel="stylesheet" href="<?php echo $baseUrl ?>/assets/css/header.css">
	<link rel="stylesheet" href="<?php echo $baseUrl ?>/assets/css/footer.css">

	<?php
	if (isset($additionalCss)) {
		foreach ($additionalCss as $cssFile) {
			echo '<link rel="stylesheet" href="' . $baseUrl . '/assets/css/' . $cssFile . '">';
		}
	}
	?>
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
