<?php 
	include_once "../conexion.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Lista de Proveedores</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<?php 
			if ( empty($_REQUEST['busqueda']) )
			{
				header('Location: lista_proveedores.php');
			}
			$busqueda = strtolower($_REQUEST['busqueda']);	
		 ?>
		
		<h1><i class="fas fa-users fa-2x"></i>  Lista de Proveedores</h1>
		<a href="registro_proveedor.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear proveedor</a>

		<form action="buscar_proveedor.php" method="get" class="form_search">
			<input type="text" id="busqueda" name="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>"> 
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>			
		</form>

		<div class="contenedorTabla tablaProveedor">
			<table>
				<!-- <caption>table title and/or explanatory text</caption> -->
				<thead>
					<tr>
						<th>ID</th>
						<th>Proveedor</th>
						<th>Contacto</th>
						<th>Teléfono</th>
						<th>Dirección</th>
						<th>Usuario</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql_registros = "SELECT count(*) as total_registros 
									   FROM (
									   		SELECT p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, u.usuario 
									          FROM proveedor p 
									          INNER JOIN usuario u 
									             ON p.usuario_id = u.idusuario
								               WHERE p.estatus != 0
						        				  AND LOWER(CONCAT(p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, u.usuario )) LIKE '%$busqueda%') A";
						// echo $sql_registros . '<br>';
						$query_registros = mysqli_query($conexion, $sql_registros);
						$res_registros = mysqli_fetch_array($query_registros);
						$total_registros = $res_registros['total_registros'];

						$reg_por_pagina = 5;

						if ( empty($_GET['pagina']))
						{
							$pagina = 1;
						} else
						{
							$pagina = $_GET['pagina'];
						}
	 
						$total_paginas = ceil($total_registros / $reg_por_pagina);
						if ( $pagina < 1 ) $pagina = 1;
						if ( $pagina > $total_paginas ) $pagina = $total_paginas;
						$desde = ($pagina - 1) * $reg_por_pagina;
						// echo $desde . '<br>';
						// echo $total_registros . '<br>';
						// echo $total_paginas . '<br>';
						$paginadores = "";
						if ( $total_paginas > 1 )
						{
							for ($i=1; $i <= $total_paginas; $i++) { 
								if ( $i == $pagina )
								{
									$paginadores .= '<li class="pageSelected">'.$i.'</li>';
								} else
								{
									$paginadores .= '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
								}					 	
							}						
						}

						$sql = "SELECT p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, u.usuario 
						        FROM proveedor p 
						        INNER JOIN usuario u 
						           ON p.usuario_id = u.idusuario
					             WHERE p.estatus != 0
		        				     AND LOWER(CONCAT(p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, u.usuario )) LIKE '%$busqueda%'
						        ORDER BY proveedor ASC
						        LIMIT $desde, $reg_por_pagina";
						// echo $sql . '<br>';
						$query = mysqli_query($conexion, $sql);
						$resultado = mysqli_num_rows($query);
						if ( $resultado > 0 )
						{
							while ( $datos = mysqli_fetch_array($query) )
							{
								$fila  = '<tr>';
								$fila .= '	<td>'.$datos['codproveedor'].'</td>';
								$fila .= '	<td>'.$datos['proveedor'].'</td>';
								$fila .= '	<td>'.$datos['contacto'].'</td>';
								$fila .= '	<td>'.$datos['telefono'].'</td>';
								$fila .= '	<td>'.$datos['direccion'].'</td>';
								$fila .= '	<td>'.$datos['usuario'].'</td>';
								$fila .= '	<td>';
								$fila .= '		<a href="editar_proveedor.php?id='.$datos['codproveedor'].'" class="link_edit"><i class="far fa-edit"></i> Editar</a>';
								if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 )
								{
									$fila .= '		|';
									$fila .= '		<a href="eliminar_confirmar_proveedor.php?id='.$datos['codproveedor'].'" class="link_del"><i class="far fa-trash-alt"></i> Eliminar</a>';
								}
								$fila .= '	</td>';
								$fila .= '</tr>';
								echo "$fila";
							}
						}

						mysqli_close($conexion);
					?>
				</tbody>
			</table>
		</div>
		<div class="paginador">
			<ul>
				<?php echo ( $pagina <= 1 ? '' : '<li><a href="?pagina=1&busqueda='.$busqueda.'"><i class="fas fa-step-backward"></i></a></li>' .
				                                 '<li><a href="?pagina='.($pagina - 1).'&busqueda='.$busqueda.'"><i class="fas fa-caret-left fa-lg"></i></a></li>'); ?>
				<?php echo $paginadores; ?>
				<?php echo ( $pagina  >= $total_paginas ? '' : '<li><a href="?pagina='.($pagina + 1).'&busqueda='.$busqueda.'"><i class="fas fa-caret-right fa-lg"></i></a></li>' .
				                                               '<li><a href="?pagina='. $total_paginas . '"><i class="fas fa-step-forward"></i></a></li>'); ?>
			</ul>
		</div>

	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>