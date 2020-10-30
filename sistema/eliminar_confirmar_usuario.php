<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();
	if ( $_SESSION['rol'] != 1) 
	{
		header("location: ./");
	} 
	
	include_once "../conexion.php"; 

	if ( !empty($_POST))
	{
		$alerta ="";

		$idusuario = $_POST['idusuario'];

		if ( $idusuario == 1 )
		{
			header('Location: lista_usuarios.php');
			exit;
		} 

		// $sql = "DELETE FROM usuario WHERE idusuario = $idusuario";
		$sql = "UPDATE usuario SET estatus = 10  WHERE idusuario = $idusuario";
		// echo $sql . "<br>";
		// exit;
		$query_delete = mysqli_query($conexion, $sql);
		// echo "query_delete: " . $query_delete;
		// print_r($query_delete);
		if ( $query_delete )
		{
			header('Location: lista_usuarios.php');
		} else
		{
			echo "Error al eliminar el usuario.";
		}
	}	
	// Recuperar datos
	// 
	if ( empty($_REQUEST['id']) or $_REQUEST['id'] == 1 )
	{
		header('Location: lista_usuarios.php');
	} else
	{
		$iduser = $_REQUEST['id'];

		$sql = "SELECT u.*, r.rol as nom_rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE idusuario = '$iduser'";
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
	<title>Eliminar Usuario</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="data_delete"><i class="fas fa-user-times fa-7x" style="color: #e66262; margin-bottom: 20px;"></i>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre: <span><?php echo $datos['nombre']; ?></span></p>
			<p>Usuario: <span><?php echo $datos['usuario']; ?></span></p>
			<p>Tipo Usuario: <span><?php echo $datos['nom_rol']; ?></span></p>

			<form action="" method="post">
				<input type="hidden" name="idusuario" value="<?php echo $datos['idusuario']; ?>">
				<a href="lista_usuarios.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
			</form>

		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>