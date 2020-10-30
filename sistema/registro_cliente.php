<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 
	include_once("includes/funciones.php");

	if ( !empty($_POST))
	{
		$alerta ="";
		if ( empty($_POST['nif']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$nif = $_POST['nif'];
			$nombre = $_POST['nombre'];
			$telefono = $_POST['telefono'];
			$direccion = $_POST['direccion'];
			$usuario_id = $_SESSION['idUsuario'];

			if ( validateNif($nif) || validateNie($nif) || validateCif ($nif) )
			{
				$sql = "SELECT * FROM cliente WHERE nit = '$nif'";
				$query = mysqli_query($conexion, $sql );
				$resultado = mysqli_fetch_array($query);
				
				if ( $resultado > 0 )
				{
					$alerta = '<p class="msg_error">El NIF ya existe para un cliente.</p>';
				} else
				{
					$sql = "INSERT INTO cliente (nit, nombre, telefono, direccion, usuario_id) VALUES ('$nif', '$nombre', '$telefono', '$direccion', '$usuario_id')";
					// echo $sql . "<br>";
					$query_insert = mysqli_query($conexion, $sql);
					if ( $query_insert )
					{
						$alerta = '<p class="msg_ok">Cliente almacenado correctamente.</p>';
					} else
					{
						$alerta = '<p class="msg_error">Error al guardar el usuario.</p>';
					}
				}
			} else
			{
				$alerta = '<p class="msg_error">El NIF es erróneo.</p>';
			}
		}
	}

	mysqli_close($conexion);

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Registro Cliente</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="fas fa-user-plus"></i> Registro cliente</h1>
			<hr>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<label for="nif">NIF</label>
				<input type="text" name="nif" id="nif" placeholder="NIF">
				<label for="nombre">Nombre</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
				<button type="submit" class="btn_save"><i class="far fa-save"></i> Guardar cliente</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>