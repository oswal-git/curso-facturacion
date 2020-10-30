<?php 

	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();
	include_once "../conexion.php"; 

	$sql_empresa = "SELECT * FROM configuracion WHERE id = 1";
	$query_empresa = mysqli_query($conexion, $sql_empresa);
	$filas_empresa = 0;
	$filas_empresa = mysqli_num_rows($query_empresa);
	// echo 'filas_empresa:' . "\n\r";
	// echo 'nº filas_empresa: ' . $filas_empresa . "\n\r";

	if ( $filas_empresa > 0 )
	{
		$datos_empresa = mysqli_fetch_assoc($query_empresa);
	}	
	
	$iduser = $_SESSION['idUsuario'];
	//Ejecutar procedimiento almacenado
	$call = "CALL dataCuadroMando()";
	// echo 'call: ' . $call . "\n\r";
	$query = mysqli_query($conexion, $call);
	$resultado_tmp = 0;
	$resultado_tmp = mysqli_num_rows($query);
	// echo 'resultado_tmp:' . "\n\r";
	// echo 'nº resultado_tmp: ' . $resultado_tmp . "\n\r";

	if ( $resultado_tmp > 0 )
	{
		$datos_cuadro = mysqli_fetch_assoc($query);
	}

	mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Sistema Ventas</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="divContenedor">
			<div>
				<h1 class="tituloPanelControl">Panel de Control</h1>
			</div>
			<div class="escritorio">
				<?php 
					if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 ) 
					{
				 ?>
				<a href="lista_usuarios.php">
					<i class="fas fa-users"></i>
						<p>
							<strong>Usuarios</strong><br>
							<span><?php echo $datos_cuadro['usuarios'] ?></span>
						</p>
					</a>
				<?php 
				} 				
				 ?>
				<a href="lista_clientes.php">
					<i class="fas fa-user"></i>
						<p>
							<strong>Clientes</strong><br>
							<span><?php echo $datos_cuadro['clientes'] ?></span>
						</p>
					</a>
				<a href="lista_proveedores.php">
					<i class="far fa-building"></i>
						<p>
							<strong>Proveedores</strong><br>
							<span><?php echo$datos_cuadro['proveedores'] ?></span>
						</p>
					</a>
				<a href="buscar_producto.php">
					<i class="fas fa-cubes"></i>
						<p>
							<strong>Productos</strong><br>
							<span><?php echo $datos_cuadro['productos'] ?></span>
						</p>
					</a>
				<a href="ventas.php">
					<i class="far fa-file-alt"></i>
						<p>
							<strong>Ventas</strong><br>
							<span><?php echo $datos_cuadro['ventas'] ?></span>
						</p>
					</a>
			</div>
		</div>

		<div class="divInfoSistema">	
			<div>
				<h1 class="tituloPanelControl">Configuración</h1>
			</div>
			<div class="contenedorPerfil">	
				<div class="contenedorDatosUsuario">				
					<div class="logoUsuario">	
						<img src="img/user.png" alt="">
					</div>	
					<div class="divDatosUsuario">
						<h4>	Información personal</h4>
						<div>
							<label>Nombre:</label><span><?php echo $_SESSION['nombre'] ?></span>
						</div>
						<div>
							<label>Correo:</label><span><?php echo $_SESSION['email'] ?></span>
						</div>
						<h4>Datos Usuario</h4>
						<div>
							<label>Rol:</label><span><?php echo $_SESSION['nom_rol'] ?></span>
						</div>
						<div>
							<label>Usuario</label><span><?php echo $_SESSION['usuario'] ?></span>
						</div>
						<h4>Cambiar contraseña</h4>
						<form action="" method="post" name="frmCambioPass" id="frmCambioPass">							
							<div>
								<input type="password" name="txtClaveUsuario" id="txtClaveUsuario" placeholder="Contraseña actual" required>	
							</div>
							<div>
								<input class="newPass" type="password" name="txtNuevaClaveUsuario" id="txtNuevaClaveUsuario" placeholder="Nueva contraseña" required>	
							</div>
							<div>
								<input class="newPass" type="password" name="txtConfirmacionClave" id="txtConfirmacionClave" placeholder="Confirmar contraseña" required>	
							</div>
							<div class="alertaCambioClave" style="display: none";></div>
							<div>	
								<button type="submit" class="btn_save btnCambioClave"><i class="fas fa-key"></i> Cambiar contraseña</button>
							</div>
						</form>
					</div>
				</div>
				<?php 
					if ( $_SESSION['rol'] == 1 ) 
					{
				 ?>				
				<div class="contenedorDatosEmpresa">	
					<div class="logoEmpresa">	
						<img src="img/LOGO_eglos_200.png" alt="">
					</div>	
					<h4>Datos de la empresa</h4>
					<form action="" method="post" name="frmEmpresa" id="frmEmpresa">
						<input type="hidden" name="accion" value="modificarDatosEmpresa">
							<div>
								<label>NIF</label><input type="text" name="txtNif" id="txtNif" placeholder="CIF de la empresa" value="<?php echo $datos_empresa['nif']; ?>">
							</div>
							<div>
								<label>Nombre</label><input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?php echo $datos_empresa['nombre']; ?>">
							</div>
							<div>
								<label>Razón social</label><input type="text" name="txtRSocial" id="txtRSocial" placeholder="Razón social" value="<?php echo $datos_empresa['razon_social']; ?>">
							</div>
							<div>
								<label>Teléfono</label><input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Número de teléfono" value="<?php echo $datos_empresa['telefono']; ?>">
							</div>
							<div>
								<label>Coreo electrónico</label><input type="email" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electrónico" value="<?php echo $datos_empresa['email']; ?>">
							</div>
							<div>
								<label>Dirección</label><input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Dirección de la empresa" value="<?php echo $datos_empresa['direccion']; ?>">
							</div>
							<div>
								<label>IVA (%)</label><input type="text" name="txtIva" id="txtIva" placeholder="Impuesto al valor añadido (IVA)" value="<?php echo $datos_empresa['iva']; ?>">
							</div>
							<div class="alertaFormEmpresa" style="display: none";></div>
							<div>	
								<button type="submit" class="btn_save btnCanvioClave"><i class="far fa-save fa-lg"></i> Guardar datos</button>
							</div>
						</form>
				</div>	
				<?php 
					}
				 ?>
			</div>	
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>