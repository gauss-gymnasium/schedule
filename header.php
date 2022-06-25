<?php
if (!defined('INCLUDE_ROOT')) {
	exit('Kein direkter Zugriff erlaubt.');
}

include_once 'settings.php';
include_once 'functions.php';

session_start();


$loginstatus = 'not_tried';
if (isset($_POST['login'])) {
	$login_error = login();
	if ($login_error) {
		$loginstatus = 'success';
	} else {
		$loginstatus = 'failed';
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<!-- META ETC... -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="charset" content="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $title ?> | Gauss-Gymnasium</title>

	<link rel="shortcut icon" href="/sites/default/files/gaussv3_favicon.png" type="image/x-icon">
	<!-- END OF META -->

	<!-- STYLES -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="/themes/gaussv3/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="/themes/gaussv3/css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/general.css" />
	<link rel="stylesheet" type="text/css" href="css/scheduleAccordion.css" />
	<link rel="stylesheet" type="text/css" href="css/messages.css" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!-- END OF STYLES -->

	<!-- SCRIPTS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="tinymce/tinymce.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<!-- END OF SCRIPTS -->
</head>

<body>
	<div id="wrapper">
		<div id="head">
			<img src="/themes/gaussv3/css/header-Gymnasium-Carl-Friedrich-Gauss.jpg" id="heading" alt="Städtisches Gymnasium Carl-Friedrich-Gauß - Frankfurt (Oder)">
			<img src="/themes/gaussv3/css/header-mobile.jpg" id="heading-mobile" alt="Städtisches Gymnasium Carl-Friedrich-Gauß - Frankfurt (Oder)">
			<div class="clear"></div>
		</div>
		<div id="main">
			<a class="menu_button" href="#footer_nav" onclick="document.getElementById('leftcolumn').classList.toggle('expanded');">☰ MENU</a>
			<div id="maincontent">
				<div id="leftcolumn">
					<!-- Die gleiche Struktur wie auf der Gauß-Hauptseite -->
					<div class="navigation">
						<div id="sidebar-left">
							<div class="block block-menu" id="block-menu-menu-schule">
								<h2 class="title">schule</h2>
								<div class="content">
									<ul class="menu">
										<li class="expanded active-trail first">
											<a class="active">Navigation</a>
											<ul class="menu">
												<li class="leaf"><a href="/">Gauß-Gymnasium Seite</a></li>
												<?php
												if (is_logged_in()) {
												?>
													<li class="leaf"><a href="index.php">Plan für heute</a></li>
													<?php
													if (is_admin()) { ?>
														<li class="leaf adminmenu"><a href="edit.php">Plan hinzufügen</a></li>
														<li class="leaf last adminmenu"><a href="list.php">Pläne bearbeiten/löschen</a></li>
													<?php
													} else { ?>
														<li class="leaf last"><a href="list.php">Liste der Pläne</a></li>
												<?php }
												}
												?>
											</ul>
										</li>
										<li class="expanded">
											<?php
											if (is_logged_in()) {
												echo '<a class="active">Hallo ' . htmlentities($_SESSION['user']->get_display_name()) . '!</a>'; ?>
												<ul class="menu">
													<li class="leaf first last"><a href="logout.php">Abmelden</a></li>
												</ul>
											<?php } else { ?>
												<a class="active">Anmeldung</a>
												<form action="index.php" method="post" id="loginform">
													<ul class="menu">
														<li class="leaf first">
															<label for="user">Name</label>
															<input type="text" name="user" placeholder="Benutzername" autofocus />
														</li class="leaf">
														<li class="leaf">
															<label for="pw">Passwort</label>
															<input type="password" placeholder="Passwort" name="pw" />
														</li>
														<li class="leaf">
															<input type="submit" name="login" value="Einloggen" />
														</li>
													</ul>
												</form>
											<?php }
											?>
										</li>
									</ul> <!-- menu -->
								</div> <!-- content -->
							</div> <!-- block -->
						</div> <!-- sidebar-left -->
					</div> <!-- navigation -->
				</div> <!-- leftcolumn -->
				<div id="rightcolumn">
					<h1 class="title"><?php echo (isset($heading) ? $heading : "") ?></h1>
					<?php
					if ($loginstatus == "success") {
						success_msg("Eingeloggt.");
					} else if ($loginstatus == "failed") {
						error_msg("Falsche Zugangsdaten!", true);
					}
					if (!is_logged_in()) {
						echo '<p>Zum Ansehen des Vertretungsplans muss man eingeloggt sein.</p>';
						include_once "footer.php";
						die();
					}
