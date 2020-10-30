<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();
	if ( $_SESSION['rol'] != 1) 
	{
		header("location: ./");
	} 
	
	include_once "../conexion.php"; 

	// echo "md5: " . md5($_SESSION['idUsuario']);

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once "includes/scripts.php" ?>
	<title>Nueva Venta</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>

	<section id="contenedor">
			<div class="titulo_pagina">
				<h1><i class="fas fa-cube"></i> Nueva Venta</h1>
			</div>
			<div class="datos_cliente">
				<div class="accion_cliente">
					<h4>Datos del Cliente</h4>
					<a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i> Nuevo cliente</a>
				</div>

				<form name="form_nuevo_cliente_venta" id="form_nuevo_cliente_venta" class="datos">
					<input type="hidden" name="accion" value="addCliente">
					<input type="hidden" id="idcliente" name="idcliente" value="" required>
					<div class="wd30">
						<label for="nif_cliente">NIF</label>
						<input type="text" name="nif_cliente" id="nif_cliente" placeholder="NIF">
					</div>
						
					<div class="wd30">
						<label for="nom_cliente">Nombre</label>
						<input type="text" name="nom_cliente" id="nom_cliente" disabled required placeholder="Nombre completo">
					</div>

					<div class="wd30">
						<label for="tel_cliente">Teléfono</label>
						<input type="number" name="tel_cliente" id="tel_cliente" disabled required placeholder="Teléfono">
					</div>

					<div class="wd100">
						<label for="dir_cliente">Dirección</label>
						<input type="text" name="dir_cliente" id="dir_cliente" disabled required placeholder="Dirección completa">
					</div>
						
					<div id="div_registro_cliente" class="wd100">
						<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar</button>
					</div>
				</form>
			</div>

			<div class="datos_venta">
				<h4>Datos de Venta</h4>
				<div class="datos">
					<div class="wd50">
						<label>Vendedor</label>
						<p><?php echo $_SESSION['nombre']; ?></p>
					</div>
					<div class="wd50">
						<label>Acciones</label>
						<div class="acciones_venta">
							<a href="#" class="btn_cancel textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
							<a href="#" class="btn_new textcenter" id="btn_facturar_venta"><i class="fas fa-edit"></i> Procesar</a>
						</div>
					</div>
				</div>
			</div>

			<div class="contenedorTabla">
				<table class="tbl_venta">
					<thead>
						<tr>
							<th width="100px">Código</th>
							<th>Descripción</th>
							<th>Existencia</th>
							<th width="100px">Cantidad</th>
							<th class="textright">Precio</th>
							<th class="textright">Precio Total</th>
							<th>Acción</th>
						</tr>
						<tr>
							<td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
							<td id="txt_descripcion">-</td>
							<td id="txt_existencia">-</td>
							<td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
							<td id="txt_precio" class="textright">0.00</td>
							<td id="txt_precio_total" class="textright">0.00</td>
							<td><a href="#" class="link_add" id="add_producto_venta"><i class="fas fa-plus"></i> Agregar</a></td>
						</tr>
						<tr>
							<th>Código</th>
							<th colspan="2">Descripción</th>
							<th>Cantidad</th>
							<th class="textright">Precio</th>
							<th class="textright">Precio Total</th>
							<th>Acción</th>
						</tr>
					</thead>
					<tbody id="detalle_venta">
					</tbody>
					<tfoot id="detalle_totales">
					</tfoot>
				</table>
			</div>
	</section>

	<?php include_once("includes/pie.php") ?>
</body>
</html>