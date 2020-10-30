<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();

	if ( empty($_SESSION['activo']))
	{
		header('location: .');
	}

	include_once "../conexion.php"; 
	
	// Búsqueda por fecha
	$buscar_fecha = '';
	$fecha_de = '';
	$fecha_a = '';
	$and_fecha = "";

	if ( !empty($_REQUEST['fecha_de']) &&  !empty($_REQUEST['fecha_a']) )
	{
		$fecha_de = $_REQUEST['fecha_de'];
		$fecha_a = $_REQUEST['fecha_a'];

		if ( $fecha_de > $fecha_a )
		{
			$f1 = $fecha_a;
			$f2 = $fecha_de;
			$f_de = $f1.' 00:00:00';
			$f_a = $f2.' 23:59:59';
			$and_fecha = " AND fecha BETWEEN '$f_de' AND '$f_a'";
			$buscar_fecha = "fecha_de=$fecha_de&fecha_a=$fecha_a";	
			$fecha_de = $f1;		
			$fecha_a = $f2;		
			// header("location: ventas.php");
		}
		else if ( $fecha_de == $fecha_a ) 
			{
				$and_fecha = " AND fecha like '$fecha_de%'";
				$buscar_fecha = "fecha_de=$fecha_de&fecha_a=$fecha_a";			
			}
			else
				{
					$f_de = $fecha_de.' 00:00:00';
					$f_a = $fecha_a.' 23:59:59';
					$and_fecha = " AND fecha BETWEEN '$f_de' AND '$f_a'";
					$buscar_fecha = "fecha_de=$fecha_de&fecha_a=$fecha_a";	
				}
	}


	// Fin Búsqueda por fecha
	$proveedor_busq = empty($_REQUEST['proveedor']) 
				  	? empty($_REQUEST['busq_prov']) 
				  		? "" 
			  			: strtolower($_REQUEST['busq_prov']) 
			  		: strtolower($_REQUEST['proveedor']);
	$busqueda = empty($_REQUEST['busqueda']) ? "" : strtolower($_REQUEST['busqueda']);
	$seleccion = empty($_REQUEST['cuales']) ? "activos" : $_REQUEST['cuales'];

	// Lista de proveedores
	$sql = "SELECT * FROM proveedor ORDER BY proveedor ASC";
	$query_prov = mysqli_query($conexion, $sql );
	$resultado_prov = mysqli_num_rows($query_prov);
	$opciones_prov = '';
	
	if ( $resultado_prov > 0 )
	{
		if ( $proveedor_busq == 0 )
		{
			$opciones_prov .= '<option value="" selected> Proveedores</option>';
		} else
		{
			$opciones_prov .= '<option value=""> Proveedores</option>';
		}

		while ( $prov = mysqli_fetch_array($query_prov) )
		{
			if ( $proveedor_busq == $prov['codproveedor'] )
			{
				$opciones_prov .= '<option value="'.$prov['codproveedor'].'" selected>' . $prov['proveedor'] . '</option>';
			} else
			{
				$opciones_prov .= '<option value="'.$prov['codproveedor'].'">' . $prov['proveedor'] . '</option>';
			}
		}
	}	
	// Fin lista de proveedores
	// 	

	$sql_registros = "SELECT sum(case when estatus = 1 then 1 else 0 end ) as total_registros_pagado
				        , sum(case when estatus = 2 then 1 else 0 end ) as total_registros_anulado 
				        , sum(case when estatus = 10 then 1 else 0 end ) as total_registros_nose 
				        , count(*) as total_registros 
				   FROM ( SELECT f.nofactura
						     , f.fecha
						     , f.totalfactura
						     , f.codcliente
						     , f.estatus
						     , u.nombre as vendedor
						     , cl.nombre as cliente
				          FROM factura f 
				          LEFT OUTER JOIN usuario u 
				             ON ( f.usuario = u.idusuario ) 
				          LEFT OUTER JOIN cliente cl 
				             ON ( f.codcliente = cl.idcliente )
						WHERE LOWER(CONCAT('F:', f.nofactura, f.fecha, f.totalfactura, f.codcliente, f.estatus, u.nombre, cl.nombre )) LIKE '%$busqueda%' ";
	$sql_registros .= $and_fecha;
	$sql_registros .= ( $proveedor_busq == "" ? "" : "		  AND 1 = 1 ");
	$sql_registros .= "		        ) A ";

	// echo $sql_registros . '<br>';
	$query_registros = mysqli_query($conexion, $sql_registros);
	$res_registros = mysqli_fetch_array($query_registros);

	$total_registros_pagado = $res_registros['total_registros_pagado'];
	$total_registros_anulado = $res_registros['total_registros_anulado'];
	$total_registros_nose = $res_registros['total_registros_nose'];
	$registros_total = $res_registros['total_registros'];
	$total_registros = ($seleccion == "inactivos" ? $total_registros_anulado : $total_registros_pagado ); 

	// echo 'total_registros_pagado: ' . $total_registros_pagado  . '<br>';
	// echo 'total_registros_anulado: ' . $total_registros_anulado  . '<br>';
	// echo 'total_registros_nose: ' . $total_registros_nose  . '<br>';
	// echo 'registros_total: ' . $registros_total  . '<br>';
	// echo 'total_registros: ' . $total_registros  . '<br>';

	$reg_por_pagina = 10;

	// Botón de selección de activos/inactivos
	$eleccion_tg  = "";
	// if ( $total_registros_anulado )
	// {
		$eleccion_tg  = '	<div class="form_select">';
		$eleccion_tg .= '		<a href="?cuales='.($seleccion == "inactivos" ? "activos" : "inactivos" );
		$eleccion_tg .= '&busqueda=' . $busqueda;
		$eleccion_tg .= '&busq_prov=' . $proveedor_busq;
		$eleccion_tg .= '&' . $buscar_fecha .'"';
		$eleccion_tg .= '		   class="btn_select ' . ( $total_registros_anulado ? "" : "noBloque" );
		$eleccion_tg .= '        ">';
		$eleccion_tg .=  		($seleccion == "inactivos" ? "Ver pagadas" : "Ver anuladas");
		$eleccion_tg .= '		</a>';
		$eleccion_tg .= '	</div>';		
	// }

	$seleccion_pag = ($seleccion == "inactivos" ? "&cuales=inactivos" : "&cuales=activos" );

	if ( empty($_REQUEST['pagina']))
	{
		$pagina = 1;
	} else
	{
		$pagina = $_REQUEST['pagina'];
	}

	$total_paginas = ceil($total_registros / $reg_por_pagina);
	if ( $pagina < 1 ) $pagina = 1;
	if ( $pagina > $total_paginas ) $pagina = $total_paginas;
	$desde = ( $pagina <= 1 ? 0 : ($pagina - 1) * $reg_por_pagina );
	// echo 'desde: ' . $desde . '<br>';
	// echo 'total_registros: ' . $total_registros . '<br>';
	// echo 'total_paginas: ' . $total_paginas . '<br>';
	$paginadores = "";
	if ( $total_paginas > 1 )
	{
		for ($i=1; $i <= $total_paginas; $i++) 
		{ 
			if ( $i == $pagina )
			{
				$paginadores .= '<li class="pageSelected">'.$i.'</li>';
			} else
			{
				$paginadores .= '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq . '&' . $buscar_fecha .'">'.$i.'</a></li>';
			}					 	
		}						
	}

	$sql = "SELECT f.nofactura
		        , f.fecha
		        , f.totalfactura
		        , f.codcliente
		        , f.estatus
		        , u.nombre as vendedor
		        , cl.nombre as cliente
             FROM factura f 
             LEFT OUTER JOIN usuario u 
                ON ( f.usuario = u.idusuario ) 
             LEFT OUTER JOIN cliente cl 
                ON ( f.codcliente = cl.idcliente )";
     $sql .= ($seleccion == "inactivos" 
     	 ? " WHERE f.estatus = 2 " 
     	 : " WHERE f.estatus = 1 " );
     $sql .= $and_fecha;
	$sql .= ( $proveedor_busq == "" 
		 ? "" 
		 : "	AND 1 = 1 ");
	$sql .= " AND LOWER(CONCAT('F:', f.nofactura, f.fecha, f.totalfactura, f.codcliente, f.estatus, u.nombre, cl.nombre )) LIKE '%$busqueda%'
	        ORDER BY f.fecha DESC
	        LIMIT $desde, $reg_por_pagina";					           
	// echo $sql . '<br>';
	$query = mysqli_query($conexion, $sql);
	$resultado = 0;
	if ( $query )
	{
		$resultado = mysqli_num_rows($query);
	}

	$tabla = "";
	if ( $resultado > 0 )
	{
		while ( $datos = mysqli_fetch_array($query) )
		{
			$formato = 'Y-m-d H:i:s';
			// echo 'fecha: ' . $datos['fecha'] . '<br>';
			$fecha = DateTime::createFromFormat($formato, $datos['fecha']);
			// print_r( $fecha);
			// 
			// estado de la factura 1 -> pagada, 2 -> anulada
			if ( $datos['estatus'] == 1)
			{
				$estado  = '		<span class="pagada">Pagada</span>';
			} else
			{
				$estado  = '		<span class="anulada">Anulada</span>';
			}

			$fila  = '<tr id="fila_'.$datos['nofactura'].'">';
			$fila .= '	<td style="text-align: center;">'.$datos['nofactura'].'</td>';
			$fila .= '	<td>'.$fecha->format("d-m-Y") . ' / ' .$fecha->format("H:i:s"). '</td>';
			$fila .= '	<td>'.$datos['cliente'].'</td>';
			$fila .= '	<td>'.$datos['vendedor'].'</td>';
			$fila .= '	<td>'.$estado.'</td>';
			$fila .= '	<td class="textright totalfactura"><span>Q.</span>'.$datos['totalfactura'].'</td>';
			$fila .= '	<td class="acciones_ventas">';
			$fila .= '		<div class="div_acciones">';
			$fila .= '			<button class="btn_view ver_factura" type="button" cl="'. $datos['codcliente'] .'" f="'. $datos['nofactura'] .'"><i class="fas fa-eye"></i></button>';
			if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 )
			{
				$fila .= '			<div class="div_factura">';
				if ( $datos['estatus'] == 1)
				{
					$fila .= '				<button class="btn_anular anular_factura" type="button" fac="'. $datos['nofactura'] .'"><i class="fas fa-ban"></i></button>';
				}
				else
				{
					$fila .= '				<button class="btn_anular anular_factura inactivo" type="button" fac="'. $datos['nofactura'] .'"><i class="fas fa-ban"></i></button>';
				}
				$fila .= '			</div>';

			}
			$fila .= '		</div>';
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
	<title>Lista de Ventas</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">

		<div class="barraCabecera">

			<div class="cabeceraSuperior">
				<div class="divTitulo">
					<h1><i class="far fa-newspaper"></i> Lista de Ventas</h1>
					<a href="nueva_venta.php" class="btn_new"><i class="fas fa-user-plus"></i> Nueva Venta</a>
				</div>

				<div class="divBusquedas">

					<?php echo $eleccion_tg; ?>

					<form action="ventas.php" method="get" class="form_search">
						<input type="hidden" id="busq_prov" name="busq_prov" value="<?php echo $proveedor_busq; ?>"> 
						<input type="hidden" id="cuales"    name="cuales"    value="<?php echo $seleccion; ?>"> 
						<input type="hidden" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
						<input type="hidden" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
						<input type="text"  id="busqueda"  name="busqueda"  value="<?php echo $busqueda; ?>" placeholder="Buscar"> 
						<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>			
					</form>

				</div>

			</div>
			
			<div class="clearB"></div> 

			<div class="cabeceraBuscarFechas">
				<div class="divBuscarPorFechas">
					<h5>Buscar por Fechas</h5>
					<form action="ventas.php" method="get" class="form_busca_fechas">
						<input  type="hidden" id="busq_prov" name="busq_prov" value="<?php echo $proveedor_busq; ?>"> 
						<input  type="hidden" id="cuales"    name="cuales"    value="<?php echo $seleccion; ?>"> 
						<input  type="hidden"   id="busqueda"  name="busqueda"  value="<?php echo $busqueda; ?>"> 
						<label for="fecha_de">De: </label>
						<input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
						<label for="fecha_a"> A </label>
						<input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
						<button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
					</form>
				</div>	
			</div>			

		</div>

		<div class="contenedorTabla">
			<table>
			<!-- <caption>table title and/or explanatory text</caption> -->
			<thead>
				<tr>
					<th>No.</th>
					<th>Fecha/Hora</th>
					<th>Cliente</th>
					<th>Vendedor</th>
					<th>Estado</th>
					<th class="textright">Total factura</th>
					<th class="textright">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $tabla; ?>
				</tbody>
			</table>
		</div>

		<div class="paginador">
			<ul>
				<?php echo ( $pagina <= 1 ? '' : '<li><a href="?pagina=1&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq. '&' . $buscar_fecha .'"><i class="fas fa-step-backward"></i></a></li>' .
				                                 '<li><a href="?pagina='.($pagina - 1).'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq. '&' . $buscar_fecha .'"><i class="fas fa-caret-left fa-lg"></i></a></li>'); ?>
				<?php echo $paginadores; ?>
				<?php echo ( $pagina  >= $total_paginas ? '' : '<li><a href="?pagina='.($pagina + 1).'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq. '&' . $buscar_fecha .'"><i class="fas fa-caret-right fa-lg"></i></a></li>' .
				                                               '<li><a href="?pagina='. $total_paginas . '&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq. '&' . $buscar_fecha .'"><i class="fas fa-step-forward"></i></a></li>'); ?>
			</ul>
		</div>

	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>