<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title ?? 'TomTroc' ?></title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,400..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
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
		echo $content;
	}
	?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
