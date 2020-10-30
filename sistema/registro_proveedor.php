<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 
	include_once("includes/funciones.php");

	if ( $_SESSION['rol'] != 1) 
	{
		header("location: ./");
	}

	if ( !empty($_POST))
	{
		$alerta ="";
		if ( empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$proveedor = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono = $_POST['telefono'];
			$direccion = $_POST['direccion'];
			$usuario_id = $_SESSION['idUsuario'];

			$sql = "SELECT * FROM proveedor WHERE proveedor = '$proveedor'";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El proveedor ya existe.</p>';
			} else
			{
				$sql = "INSERT INTO proveedor (proveedor, contacto, telefono, direccion, usuario_id) VALUES ('$proveedor', '$contacto', '$telefono', '$direccion', '$usuario_id')";
				// echo $sql . "<br>";
				$query_insert = mysqli_query($conexion, $sql);
				if ( $query_insert )
				{
					$alerta = '<p class="msg_ok">Proveedor almacenado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al guardar el proveedor.</p>';
				}
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
	<title>Registro Proveedor</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="far fa-building"></i> Registro proveedor</h1>
			<hr>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<label for="proveedor">Proveedor</label>
				<input type="text" name="proveedor" id="proveedor" placeholder="Proveedor">
				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" id="contacto" placeholder="Contacto">
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
				<button type="submit" class="btn_save"><i class="far fa-save"></i> Guardar proveedor</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>