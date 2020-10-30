<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();

	include_once "../conexion.php"; 

	if ( !empty($_POST))
	{
		$alerta ="";

		$idcliente = $_POST['idcliente'];

		$sql = "UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente";
		// echo $sql . "<br>";
		// exit;
		$query_delete = mysqli_query($conexion, $sql);
		if ( $query_delete )
		{
			header('Location: lista_clientes.php');
		} else
		{
			echo "Error al eliminar el cliente.";
		}
	}	
	// Recuperar datos
	// 
	if ( empty($_REQUEST['id']) or $_REQUEST['id'] == 1 )
	{
		header('Location: lista_clientes.php');
	} else
	{
		$idcliente = $_REQUEST['id'];

		$sql = "SELECT c.* FROM cliente c WHERE idcliente = '$idcliente'";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: lista_usuarios.php');
		}
		$datos = mysqli_fetch_array($query);
		// print_r($datos);
		// echo "<br>";
	}

	mysqli_close($conexion);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Eliminar Cliente</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="data_delete"><i class="fas fa-user-times fa-7x" style="color: #e66262; margin-bottom: 20px;"></i>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>NIF: <span><?php echo $datos['nit']; ?></span></p>
			<p>Nombre: <span><?php echo $datos['nombre']; ?></span></p>
			<p>Telefono: <span><?php echo $datos['telefono']; ?></span></p>
			<p>Dirección: <span><?php echo $datos['direccion']; ?></span></p>

			<form action="" method="post">
				<input type="hidden" name="idcliente" value="<?php echo $datos['idcliente']; ?>">
				<a href="lista_clientes.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
			</form>

		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>