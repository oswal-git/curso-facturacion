<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();

	if ( empty($_SESSION['activo']))
	{
		header('location: ../');
	}

	// print_r($_SESSION);
?>
	<header>
		<div class="header">
			<a href="#" class="btnMenu"><i class="fas fa-bars"></i></a>
			<h1>Sistema Facturación</h1>
			<div class="optionsBar">
				<p><lugar>Riba-roja del Túria, </lugar><fecha><?= fechaC();  ?></fecha></p>
				<span>|</span>
				<span class="user"><?= $_SESSION['usuario']; ?> <rol>( <?= ucfirst($_SESSION['nom_rol']).' - '.$_SESSION['rol'];  ?>)</rol></span>
				<img class="photouser" src="img/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include_once("nav.php") ?>
	</header>

	<div class="modal">
		<div class="bodyModal">
		</div>
	</div>