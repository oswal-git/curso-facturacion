<?php
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 

	// echo $_GET['cuales'] . '<br>';
	$seleccion = empty($_GET['cuales']) ? "activos" : $_GET['cuales'];
	// echo $seleccion . '<br>';

	// WHERE estatus != 0
	$sql_registros = "SELECT sum(case when estatus = 0 then 0 else 1 end ) as total_registros_ok
	                       , sum(case when estatus = 0 then 1 else 0 end ) as total_registros_ko 
	                  FROM proveedor  ";
     // echo $sql_registros . '<br>';
	$query_registros = mysqli_query($conexion, $sql_registros);
	$res_registros = mysqli_fetch_array($query_registros);
	$total_registros_ok = $res_registros['total_registros_ok'];
	$total_registros_ko = $res_registros['total_registros_ko'];
	$total_registros = ($seleccion == "inactivos" ? $total_registros_ko : $total_registros_ok );

	$reg_por_pagina = 10;

	$eleccion_tg  = "";
	if ( $total_registros_ko )
	{
		$eleccion_tg  = '	<div class="form_select">';
		$eleccion_tg .= '<a href="?cuales='.($seleccion == "inactivos" ? "activos" : "inactivos" ).'" class="btn_select">'.($seleccion == "inactivos" ? "Ver activos" : "Ver inactivos").'</a>';
		$eleccion_tg .= '	</div>';	
	}
	
	$seleccion_pag = ($seleccion == "inactivos" ? "&cuales=inactivos" : "" );
	// echo htmlspecialchars($eleccion_tg, ENT_QUOTES); 
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
	for ($i=1; $i <= $total_paginas; $i++) { 
		if ( $i == $pagina )
		{
			$paginadores .= '<li class="pageSelected">'.$i.'</li>';
		} else
		{
			$paginadores .= '<li><a href="?pagina='.$i.$seleccion_pag.'">'.$i.'</a></li>';
		}					 	
	}

	$sql = "SELECT p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, p.fechaalta, p.estatus, u.usuario 
	        FROM proveedor p 
	        INNER JOIN usuario u 
	           ON p.usuario_id = u.idusuario";
     $sql .= ($seleccion == "inactivos" ? " WHERE p.estatus = 0 " : " WHERE p.estatus != 0 " );
	$sql .="ORDER BY proveedor ASC
	        LIMIT $desde, $reg_por_pagina";
     // echo $sql . '<br>';
	$query = mysqli_query($conexion, $sql);
	$resultado = 0;
	if ( $query )
	{
		$resultado = mysqli_num_rows($query);
	}

	if ( $resultado > 0 )
	{
		$tabla = "";
		while ( $datos = mysqli_fetch_array($query) )
		{
			$formato = 'Y-m-d H:i:s';
			$fecha = DateTime::createFromFormat($formato, $datos['fechaalta']);

			if ( $datos['estatus'] )
			{
				$estado  = '		<i class="fas fa-check-circle fa-lg" style="color: green;"></i></a>';
				$estado_accion  = '';
			} else
			{
				$estado  = '		<i class="fas fa-minus-circle fa-lg" style="color: red;"></a>';
				$estado_accion = '		<a href="editar_proveedor.php?id='.$datos['codproveedor'].'&estado=activar" class="link_activa"><i class="fas fa-check-circle" style="color: green;"></i> Activar</a>';
			}

			$fila  = '<tr>';
			$fila .= '	<td style="text-align: center;">'.$datos['codproveedor'].'</td>';
			$fila .= '	<td>'.$datos['proveedor'].'</td>';
			$fila .= '	<td>'.$datos['contacto'].'</td>';
			$fila .= '	<td style="text-align: center;">'.$datos['telefono'].'</td>';
			$fila .= '	<td>'.$datos['direccion'].'</td>';
			$fila .= '	<td style="text-align: center;">'.$fecha->format("d-m-Y").'</td>';
			$fila .= '	<td style="text-align: center;">'.$estado.'</td>';
			$fila .= '	<td>'.$datos['usuario'].'</td>';
			$fila .= '	<td>';
			if ( $estado_accion == "" )
			{
				$fila .= '		<a href="editar_proveedor.php?id='.$datos['codproveedor'].'" class="link_edit"><i class="far fa-edit"></i> Editar</a>';
				if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 )
				{
					$fila .= '		|';
					$fila .= '		<a href="eliminar_confirmar_proveedor.php?id='.$datos['codproveedor'].'" class="link_del"><i class="far fa-trash-alt"></i> Eliminar</a>';
				}
			} else 
			{
				if ( $_SESSION['rol'] == 1 ) 
				{
					$fila .= $estado_accion;
				}
			}
			$fila .= '	</td>';
			$fila .= '</tr>';
			$tabla .= $fila;
		}
	}
	mysqli_close($conexion);
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
		
		<h1><i class="far fa-building fa-2x"></i> Lista de Proveedores</h1>
		<a href="registro_proveedor.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear proveedor</a>

		<form action="buscar_proveedor.php" method="get" class="form_search">
			<input type="text" id="busqueda" name="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>	
		</form>

		<?php echo $eleccion_tg; ?>

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
						<th>Fecha de Alta</th>
						<th>Estado</th>
						<th>Usuario</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $tabla; ?>
				</tbody>
			</table>
		</div>
		<div class="paginador">
			<ul>
				<?php echo ( $pagina <= 1 ? '' : '<li><a href="?pagina=1'.$seleccion_pag.'"><i class="fas fa-step-backward"></i></a></li>' .
				                                 '<li><a href="?pagina='.($pagina - 1).$seleccion_pag.'"><i class="fas fa-backward"></i></a></li>') ?>
				<?php echo ( $total_paginas == 1 ? '' : $paginadores); ?>
				<?php echo ( $pagina  >= $total_paginas ? '' : '<li><a href="?pagina='.($pagina + 1).$seleccion_pag.'"><i class="fas fa-forward"></i></a></li>' .
				                                               '<li><a href="?pagina='. $total_paginas . $seleccion_pag . '"><i class="fas fa-step-forward"></i></a></li>') ?>
			</ul>
		</div>

	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>