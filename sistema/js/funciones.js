$(document).ready(function() 
{

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// ****  Menu responsive  ********************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('.btnMenu').click(function(e) 
	{
		e.preventDefault();

		if ( $('nav').hasClass('verMenu') ) 
		{
			$('nav').removeClass('verMenu');
		}
		else
			{
				$('nav').addClass('verMenu');
			}
	});

	$('nav ul li').click(function()
	{
		$('nav ul li ul').slideUp();
		$(this).children('ul').slideToggle();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$("#foto").on("change",function() 
	{
	
		var uploadFoto = document.getElementById("foto").value;
		var foto       = document.getElementById("foto").files;
		var nav = window.URL || window.webkitURL;
		var contactAlert = document.getElementById('form_alerta');

		if ( uploadFoto != '' )
		{
			var tipo = foto[0].type;
			var nombre = foto[0].name;

			if ( tipo != 'image/jpeg' && tipo != 'image/jpg' &&  tipo != 'image/png' )
			{
				contactAlert.innerHTML = '<p class=errorArchivo" >El archivo no es válido.</p>';
				$("#img").remove();
				$(".delFoto").addClass('noBloque');
				$("#foto").val('');
				return false;
			} else
				{
					contactAlert.innerHTML = '';
					$("#img").remove();
					$(".delFoto").removeClass('noBloque');
					var objeto_url = nav.createObjectURL(this.files[0]);
					$(".prevFoto").append("<img id='img' src="+objeto_url+">");
					$(".label").remove();
					// $(".upimg label").remove();
				}
		} else
			{
				alert("No seleccionó foto");
				$("#image").remove();
			}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$(".delFoto").click(function() 
	{
			$("#foto").val('');
			$(".delFoto").addClass('noBloque');
			$("#img").remove();

			if ( $("#foto_actual") && $("#foto_remove") )
			{
				$("#foto_remove").val('img_producto.jpg');
			}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Modal form agregar producto
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$(".add_producto").click(function(e) 
	{
		e.preventDefault();
		var producto = $(this).attr("producto");
		var accion = 'infoProducto';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, producto:producto},
		
		success: function(respuesta){
			console.log(respuesta);

			if ( respuesta != 'error' )
			{
				var info = JSON.parse(respuesta);

				$('.bodyModal').html(''+
					'<form action="" method="post" name="form_add_producto" id="form_add_producto" onsubmit="event.preventDefault(); envioDatosProducto();">'+
					'	<h1><i class="fas fa-cubes" style="font-size: 45pt;" ></i> Agregar Producto</h1>'+
					'	<h2 class="nombreProducto">'+info.descripcion+'</h2><br>'+
					'	<input type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad del producto" required><br>'+
					'	<input type="text" name="precio" id="txtPrecio" placeholder="Precio del producto" required>'+
					'	<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
					'	<input type="hidden" name="accion" value="addProducto" required>'+
					'	<div class="alerta alertaProducto"></div>'+
					'	<button type="submit" class="btn_add"><i class="fas fa-plus"></i> Agregar</button>'+
					'	<a href="#" class="btn_cancel cerrarModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+
					'</form>'+
				'');

			}
		},

		error: function(error){
			console.log(error);
		}

		});

		$('.modal').fadeIn();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Modal form eliminar producto
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$(".del_producto").click(function(e) 
	{
		e.preventDefault();
		var producto = $(this).attr("producto");
		var accion = 'infoProducto';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, producto:producto},
		
		success: function(respuesta){
			console.log(respuesta);

			if ( respuesta != 'error' )
			{
				var info = JSON.parse(respuesta);

				$('.bodyModal').html(''+
					'<form action="" method="post" name="form_del_producto" id="form_del_producto" onsubmit="event.preventDefault(); eliminarProducto();">'+
					'	<h1><i class="fas fa-cubes" style="font-size: 45pt;" ></i> Eliminar Producto</h1>'+
					'   <p>¿Está seguro de eliminar el siguiente registro?</p>'+
					'	<h2 class="nombreProducto">'+info.descripcion+'</h2><br>'+
					'	<input type="hidden" name="cantidad" id="txtCantidad" value="'+info.existencia+'" required><br>'+
					'	<input type="hidden" name="precio" id="txtPrecio" value="'+info.precio+'" required><br>'+
					'	<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
					'	<input type="hidden" name="accion" value="delProducto" required>'+
					'	<div class="alerta alertaProducto"></div>'+
					'	<a href="#" class="btn_cancel cerrarModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cancelar</a>'+
					'	<button type="submit" class="btn_ok"><i class="fas fa-trash"></i> Eliminar</button>'+
					'</form>'+
				'');

			}
		},

		error: function(error){
			console.log(error);
		}

		});

		$('.modal').fadeIn();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Modal form agregar producto
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$(".act_producto").click(function(e) 
	{
		e.preventDefault();
		var producto = $(this).attr("producto");
		var accion = 'infoProducto';
		var evento = 'activaProducto';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, producto:producto, evento:evento},
		
		success: function(respuesta){
			console.log(respuesta);

			if ( respuesta != 'error' )
			{
				var info = JSON.parse(respuesta);

				$('.bodyModal').html(''+
					'<form action="" method="post" name="form_act_producto" id="form_act_producto" onsubmit="event.preventDefault(); activaProducto();">'+
					'	<h1><i class="fas fa-cubes" style="font-size: 45pt;" ></i> Activar Producto</h1>'+
					'	<h2 class="nombreProducto">'+info.descripcion+'</h2><br>'+
					'	<input type="hidden" name="cantidad" id="txtCantidad value="'+info.existencia+'" required><br>'+
					'	<input type="hidden" name="precio" id="txtPrecio value="'+info.precio+'" required>'+
					'	<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
					'	<input type="hidden" name="accion" value="actProducto" required>'+
					'	<div class="alerta alertaProducto"></div>'+
					'	<button type="submit" class="btn_act"><i class="fas fa-plus"></i> Activar</button>'+
					'	<a href="#" class="btn_cancel cerrarModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+
					'</form>'+
				'');

			}
		},

		error: function(error){
			console.log(error);
		}

		});

		$('.modal').fadeIn();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$("#buscar_proveedor").change(function(e) 
	{
		e.preventDefault();
		var sistema = obtenerURL();
		var busq = $('#busqueda').val();
		var cual = $('#cuales').val();

		location.href = sistema+"buscar_producto.php?proveedor="+$(this).val()+"&cuales="+cual+"&busqueda="+busq;
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	//Activa campos para registrar cliente
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('.btn_new_cliente').click(function(e)
	{
		e.preventDefault();
		$('#nom_cliente').removeAttr('disabled');
		$('#tel_cliente').removeAttr('disabled');
		$('#dir_cliente').removeAttr('disabled');

		$('#div_registro_cliente').slideDown();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Buscar clientes
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#nif_cliente').keyup(function(e)
	{
		e.preventDefault();
		
		var cl = $(this).val();
		var accion = 'buscarCliente';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, cliente:cl},
		
		success: function(respuesta){
			console.log(respuesta);

			if ( respuesta == 0 )
			{
				$('#idcliente').val('');				
				$('#nom_cliente').val('');				
				$('#tel_cliente').val('');				
				$('#dir_cliente').val('');				

				// mostrar botón Agregar
				$('.btn_new_cliente').slideDown();
			} 
			else
				{
					var datos = JSON.parse(respuesta);

					$('#idcliente').val(datos.idcliente);				
					$('#nom_cliente').val(datos.nombre);				
					$('#tel_cliente').val(datos.telefono);				
					$('#dir_cliente').val(datos.direccion);			
					// ocultar botón Agregar
					$('.btn_new_cliente').slideUp();

					// Bloqueo de campos
					$('#nom_cliente').attr('disabled', 'disabled');				
					$('#tel_cliente').attr('disabled', 'disabled');				
					$('#dir_cliente').attr('disabled', 'disabled');		

					// ocultar botón Guardar
					$('#div_registro_cliente').slideUp();	

				}
		},

		error: function(error){
			console.log(error);
		}

		});		
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Crear cliente
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#form_nuevo_cliente_venta').submit(function(e)
	{
		e.preventDefault();

		var accion = 'buscarCliente';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: $('#form_nuevo_cliente_venta').serialize(),
		
		success: function(respuesta){

            console.log(respuesta);

            var principio_msg = respuesta.lastIndexOf('{');
            var fin_msg = respuesta.lastIndexOf('}');

            var res_txt = respuesta.substring(0, principio_msg);
            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

			// console.log('res_txt: '+res_txt);
			// console.log('res_json: '+res_json);
			var datos = JSON.parse(res_json);

			if (datos.msg != 'error')
			{
				// Agregar id a input hidden
				$('#idcliente').val(datos.msg);		

				// Bloqueo campos
				$('#nom_cliente').attr('disabled', 'disabled');				
				$('#tel_cliente').attr('disabled', 'disabled');				
				$('#dir_cliente').attr('disabled', 'disabled');		
				
				// ocultar botón Agregar
				$('.btn_new_cliente').slideUp();

				// ocultar botón Guardar
				$('#div_registro_cliente').slideUp();			
						
			}
			console.log(datos);

		},

		error: function(error){
			console.log(error);
		}

		});		
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Buscar producto - Ventas
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#txt_cod_producto').keyup(function(e)
	{
		e.preventDefault();

		var producto = $(this).val();
		var accion = 'infoProducto';

		if ( producto != '')
		{
			$.ajax(
			{
				url: 'ajax.php',
				type: 'POST',
				async: true,
				data: {accion:accion, producto:producto},
			
			success: function(respuesta){
				console.log(respuesta);

	            var principio_msg = respuesta.lastIndexOf('{');
	            var fin_msg = respuesta.lastIndexOf('}');

	            var res_txt = respuesta.substring(0, principio_msg);
	            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

				console.log('res_txt: '+res_txt);
				console.log('res_json: '+res_json);

				if (respuesta != 'error')
				{
					var datos = JSON.parse(res_json);	
							
					$('#txt_descripcion').html(datos.descripcion);
					$('#txt_existencia').html(datos.existencia);
					$('#txt_cant_producto').html(datos.existencia);
					$('#txt_precio').html(datos.precio);
					$('#txt_precio_total').html(datos.precio);

					// Activar cantidad
					$('#txt_cant_producto').removeAttr('disabled');

					// Nostrar botón agregar
					$('#add_producto_venta').slideDown();
				}
				else
					{
						$('#txt_descripcion').html('');
						$('#txt_existencia').html('');
						$('#txt_cant_producto').html('0');
						$('#txt_precio').html('0.00');
						$('#txt_precio_total').html('0.00');

						// Activar cantidad
						$('#txt_cant_producto').attr('disabled', 'disabled');

						// Nostrar botón agregar
						$('#add_producto_venta').slideUp();
					}
			},

			error: function(error){
				console.log(error);
			}

			});	

		} 
		else
			{
				$('#txt_descripcion').html('');
				$('#txt_existencia').html('');
				$('#txt_cant_producto').html('0');
				$('#txt_precio').html('0.00');
				$('#txt_precio_total').html('0.00');

				// Activar cantidad
				$('#txt_cant_producto').attr('disabled', 'disabled');

				// Nostrar botón agregar
				$('#add_producto_venta').slideUp();
			}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Actualizar totales - Venta
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#txt_cant_producto').keyup(function(e)
	{
		e.preventDefault();

		var existencia = parseFloat($('#txt_existencia').html());
		// console.log('txt_cant_producto: '+$(this).val());

		if ( parseFloat($(this).val()) > existencia )
		{
			$(this).val(existencia);
			// console.log('txt_cant_producto 2: '+$(this).val());
		}

		var precio_total = $(this).val() * $('#txt_precio').html();
		$('#txt_precio_total').html(precio_total);

		// Ocultar botón agregar si la cantidad es menor qiue 1
		if ( $(this).val() < 1 || isNaN($(this).val()) )
		{
			$('#add_producto_venta').slideUp();
		}
		else
		{
			$('#add_producto_venta').slideDown();
		}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Agregar detalle temporal - Venta
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#add_producto_venta').click(function(e)
	{
		e.preventDefault();

		if ( $('#txt_cant_producto').val() > 0 )
		{
			var codproducto = $('#txt_cod_producto').val();
			var cantidad = $('#txt_cant_producto').val();
			var accion = 'addProductoDetalle';

			$.ajax(
			{
				url: 'ajax.php',
				type: 'POST',
				async: true,
				data: {accion:accion, producto:codproducto, cantidad:cantidad},
			
			success: function(respuesta)
			{
				console.log(respuesta);

	            var principio_msg = respuesta.lastIndexOf('{');
	            var fin_msg = respuesta.lastIndexOf('}');

	            var res_txt = respuesta.substring(0, principio_msg);
	            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

				console.log('res_txt: '+res_txt);
				console.log('res_json: '+res_json);

				var datos = JSON.parse(res_json);	
				
				if (datos.resultado == 'ok')
				{
					console.log(datos.resultado);
							
					$('#detalle_venta').html(datos.detalle);
					$('#detalle_totales').html(datos.totales);

					$('#txt_cod_producto').val('');
					$('#txt_descripcion').val('');
					$('#txt_existencia').val('');
					$('#txt_cant_producto').val('0');
					$('#txt_precio').val('0.00');
					$('#txt_precio_total').val('0.00');

				} else
				{
					console.log(datos.resultado+'; '+datos.error);
				}
				verBotonProcesar();
			},

			error: function(error)
			{
				console.log(error);
			}

			});	

		}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Anular venta - Venta
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#btn_anular_venta').click(function(e)
	{
		e.preventDefault();

		var filas_detalle = $('#detalle_venta tr').length;

		if ( filas_detalle > 0 )
		{
			var accion = 'anularVenta';

			$.ajax(
			{
				url: 'ajax.php',
				type: 'POST',
				async: true,
				data: {accion:accion},
			
			success: function(respuesta)
			{
				console.log(respuesta);

	            var principio_msg = respuesta.lastIndexOf('{');
	            var fin_msg = respuesta.lastIndexOf('}');

	            var res_txt = respuesta.substring(0, principio_msg);
	            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

				console.log('res_txt: '+res_txt);
				console.log('res_json: '+res_json);

				var datos = JSON.parse(res_json);	
				
				if (datos.resultado == 'ok')
				{
					console.log(datos.resultado);
							
					$('#detalle_venta').html('');
					$('#detalle_totales').html('');

				} else
				{
					console.log(datos.resultado+'; '+datos.error);
				}
				verBotonProcesar();
			},

			error: function(error)
			{
				console.log(error);
			}

			});	

		}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Procesar venta - Venta
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#btn_facturar_venta').click(function(e)
	{
		e.preventDefault();

		var filas_detalle = $('#detalle_venta tr').length;

		if ( filas_detalle > 0 )
		{
			var accion = 'procesarVenta';
			var codcliente = $('#idcliente').val();

			$.ajax(
			{
				url: 'ajax.php',
				type: 'POST',
				async: true,
				data: {accion:accion, codcliente:codcliente},
			
			success: function(respuesta)
			{
				console.log(respuesta);

	            var principio_msg = respuesta.indexOf('{');
	            var fin_msg = respuesta.lastIndexOf('}');

	            var res_txt = respuesta.substring(0, principio_msg);
	            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

				console.log('res_txt: '+res_txt);
				console.log('res_json: '+res_json);

				var datos = JSON.parse(res_json);	
				
				if (datos.resultado == 'ok')
				{
					// console.log(datos.resultado);

					// console.log(datos.detalle.codcliente+' - '+datos.detalle.nofactura);
					generarPDF(datos.detalle.codcliente, datos.detalle.nofactura);

					location.reload();
							
					// $('#detalle_venta').html('');
					// $('#detalle_totales').html('');

				} else
				{
					console.log(datos.resultado+'; '+datos.error);
				}
				verBotonProcesar();
			},

			error: function(error)
			{
				console.log(error);
			}

			});	

		}
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Modal form eliminar product anular factura
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$(".anular_factura").click(function(e) 
	{
		e.preventDefault();
		var nofactura = $(this).attr("fac");
		var accion = 'infoFactura';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, nofactura:nofactura},
		
		success: function(respuesta){
			// console.log(respuesta);

			if ( respuesta.resultado != 'error' )
			{
				var info = JSON.parse(respuesta);
				// console.log(info);

				$('.bodyModal').html(''+
					'<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
					'	<h1><i class="fas fa-cubes" style="font-size: 45pt;" ></i> <br>Anular Factura</h1><br>'+
					'   <p>¿Está seguro de anular la siguiente factura?</p>'+
					'   <p><strong>No. '+info.detalle.nofactura+'</strong></p>'+
					'   <p><strong>Importe. '+info.detalle.totalfactura+'</strong></p>'+
					'   <p><strong>Fecha. '+info.detalle.fecha+'</strong></p>'+
					'	<input type="hidden" name="accion" value="anularFactura">'+
					'	<input type="hidden" name="no_factura" id="no_factura" value="'+info.detalle.nofactura+'">'+
					'	<div class="alerta alertaProducto"></div>'+
					'	<button type="submit" class="btn_ok"><i class="fas fa-trash"></i> Anular</button>'+
					'	<a href="#" class="btn_cancel cerrarModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cancelar</a>'+
					'</form>'+
				'');

			}
		},

		error: function(error){
			console.log(error);
		}

		});

		$('.modal').fadeIn();
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Ver factura venta - Venta
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('.ver_factura').click(function(e)
	{
		e.preventDefault();
		var codCliente = $(this).attr("cl");
		var nofactura = $(this).attr("f");
		
		generarPDF(codCliente, nofactura);
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Form actualizar datos empresaa
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#frmEmpresa').submit(function(e) 
	{
		e.preventDefault();

		var strNif = $('#txtNif').val();
		var strNombreEmp = $('#txtNombre').val();
		var strRSocialEmp = $('#txtRSocial').val();
		var intTelEmp = $('#txtTelEmpresa').val();
		var strEmailEmp = $('#txtEmailEmpresa').val();
		var strDirEmp = $('#txtDirEmpresa').val();
		var intIva = $('#txtIva').val();

		if ( strNif == '' || strNombreEmp == '' || intTelEmp == '' || strEmailEmp == '' || strDirEmp == '' || intIva == '' )
		{
			$('.alertaFormEmpresa').html('<p class="alertKo">Todos los campos son obligatorios.<p>');
			$('.alertaFormEmpresa').slideDown();		
			return false;
		}

		$.ajax(
		{
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: $('#frmEmpresa').serialize(),
		
		beforeSend: function()
		{
			$('.alertaFormEmpresa').slideUp();		
			$('.alertaFormEmpresa').html('');
			$('#frmEmpresa input').attr('disabled', 'disabled');
			$('.btnCanvioClave').hide();
				// $('#frmEmpresa input').removeAttr('disabled');
		},

		success: function(respuesta)
		{
			// console.log(respuesta);

            var principio_msg = respuesta.indexOf('{');
            var fin_msg = respuesta.lastIndexOf('}');

            var res_txt = respuesta.substring(0, principio_msg);
            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

			console.log('res_txt: '+res_txt);
			console.log('res_json: '+res_json);

			var datos = JSON.parse(res_json);	
			
			if (datos.resultado == 'ok')
			{
				$('.alertaFormEmpresa').html('<p class="alertOk">Datos actualizados correctamente.<p>');
			} 
			else
				{
					console.log(datos.resultado+'; '+datos.mensaje);
					$('.alertaFormEmpresa').html('<p class="alertKo">'+datos.mensaje+'.<p>');
				}
			
			$('.alertaFormEmpresa').slideDown();
			$('#frmEmpresa input').removeAttr('disabled');
			$('.btnCanvioClave').show();
		},

		error: function(error)
		{
			console.log(error);
		}

		});	

	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Form cambiar contraseña
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('#frmCambioPass').submit(function(e)
	{
		e.preventDefault();
		var claveActual = $('#txtClaveUsuario').val();
		var nuevaClave = $('#txtNuevaClaveUsuario').val();
		var confirmaNuevaClave = $('#txtConfirmacionClave').val();
		var accion = "cambioClave";

		if ( nuevaClave != confirmaNuevaClave )
		{
			$('.alertaCambioClave').html('<p class="alertKo">Las contraseñas no son iguales.<p>');
			$('.alertaCambioClave').slideDown();
			return false;
		}

		if ( nuevaClave.length < 3 )
		{
			$('.alertaCambioClave').html('<p class="alertKo">La nueva contraseña debe de ser de 3 caracteres como mínimo.<p>');
			$('.alertaCambioClave').slideDown();
			return false;
		}

			$.ajax(
			{
				url: 'ajax.php',
				type: 'POST',
				async: true,
				data: {accion:accion, claveActual:claveActual, nuevaClave:nuevaClave},
			
			success: function(respuesta)
			{
				// console.log(respuesta);

	            var principio_msg = respuesta.indexOf('{');
	            var fin_msg = respuesta.lastIndexOf('}');

	            var res_txt = respuesta.substring(0, principio_msg);
	            var res_json = respuesta.substring(principio_msg, fin_msg + 1);

				console.log('res_txt: '+res_txt);
				console.log('res_json: '+res_json);

				var datos = JSON.parse(res_json);	
				
				if (datos.resultado == 'ok')
				{
					$('.alertaCambioClave').html('<p class="alertOk">'+datos.mensaje+'.<p>');
					$('#frmCambioPass')[0].reset();
				} 
				else
					{
						console.log(datos.resultado+'; '+datos.mensaje);
						$('.alertaCambioClave').html('<p class="alertKo">'+datos.mensaje+'.<p>');
					}

				$('.alertaCambioClave').slideDown();
			},

			error: function(error)
			{
				console.log(error);
			}

			});	
	});

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Actualiza lista detalles temp
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	buscaDetalles();

	// *******************************************************************************************************************
	// *******************************************************************************************************************
	// Cambio de clave
	// *******************************************************************************************************************
	// *******************************************************************************************************************
	$('.newPass').keyup(function()
	{
		validaClave();
	});

});	// End ready

// *******************************************************************************************************************
// *******************************************************************************************************************
// ***************************************       FNCIONES       ******************************************************
// *******************************************************************************************************************
// *******************************************************************************************************************

// *******************************************************************************************************************
// *******************************************************************************************************************
function validaClave()
{
	var nuevaClave = $('#txtNuevaClaveUsuario').val();
	var confirmaNuevaClave = $('#txtConfirmacionClave').val();

	if ( nuevaClave != confirmaNuevaClave )
	{
		$('.alertaCambioClave').html('<p class="alertKo">Las contraseñas no son iguales.<p>');
		$('.alertaCambioClave').slideDown();
		return false;
	}

	if ( nuevaClave.length < 3 )
	{
		$('.alertaCambioClave').html('<p class="alertKo">La nueva contraseña debe de ser de 3 caracteres como mínimo.<p>');
		$('.alertaCambioClave').slideDown();
		return false;
	}

	$('.alertaCambioClave').html('');
	$('.alertaCambioClave').slideUp();
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function generarPDF(cliente, factura)
{
	var ancho = 1000;
	var alto = 800;

	// Calcular la posición x, y para centrar la ventana
	var x = parseInt(( window.screen.width/2 ) - (ancho / 2 ));
	var y = parseInt(( window.screen.height/2 ) - (alto / 2 ));

	$url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;

	window.open($url, "Factura", "left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function obtenerURL()
{
	var loc = window.location;
	var url1 = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
	// var nombreDirectorio = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);

	// return loc.href.substring(0, loc.href.length - (( loc.pathname + loc.search + loc.hash ).length - nombreDirectorio.length ));
	return url1;
}

// *******************************************************************************************************************
// Agregar existencias
// *******************************************************************************************************************
function envioDatosProducto()
{
	$(".alertaProducto").html('');

	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		async: true,
		data: $('#form_add_producto').serialize(),
	
		success: function(respuesta){

			if ( respuesta == 'error' )
			{
				$('.alertaProducto').html('<p style="color: red;">Error al agregar el producto,</p>');
			} 
			else
			{
				var info = JSON.parse(respuesta);
				console.log(info);

				$('.fila'+info.producto_id+' .celExistencia').html(info.nueva_existencia);
				$('.fila'+info.producto_id+' .celPrecio').html(info.nuevo_precio);

				// $("#txtCantidad").val('');
				// $("#txtPrecio").val('');

				$('.btn_add').remove();

				$('.alertaProducto').html('<p>Producto agregado correctamente.</p>');
			}
		},

		error: function(error){
			console.log(error);
		}

	});
}

// *******************************************************************************************************************
// eliminar producto
// *******************************************************************************************************************
function eliminarProducto()
{
	$(".alertaProducto").html('');
	var pr = $('#producto_id').val();

	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		async: true,
		data: $('#form_del_producto').serialize(),
	
		success: function(respuesta){

			if ( respuesta == 'error' )
			{
				$('.alertaProducto').html('<p style="color: red;">Error al eliminar el producto,</p>');
			} 
			else
			{
				var info = JSON.parse(respuesta);
				console.log(info);
				$('.fila'+pr).remove();
				$('#form_del_producto .btn_ok').remove();
				$('#form_del_producto .btn_cancel').html('Cerrar');
				$('.btn_select').removeClass('noBloque');

				$('.alertaProducto').html('<p>Producto eliminado correctamente.</p>');
			}
		},

		error: function(error){
			console.log(error);
		}

	});
}

// *******************************************************************************************************************
// Activar producto
// *******************************************************************************************************************
function activaProducto()
{
	$(".alertaProducto").html('');
	var pr = $('#producto_id').val();

	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		async: true,
		data: $('#form_act_producto').serialize(),
	
		success: function(respuesta){

				console.log(respuesta);
			if ( respuesta == 'error' )
			{
				$('.alertaProducto').html('<p style="color: red;">Error al activar el producto,</p>');
			} 
			else
			{
				var info = JSON.parse(respuesta);
				console.log(info);
				$('.fila'+pr).remove();
				$('#form_act_producto .btn_act').remove();
				$('.btn_select').removeClass('noBloque');
				$('.alertaProducto').html('<p>Producto activado correctamente.</p>');
			}
		},

		error: function(error){
			console.log(error);
		}

	});
}

// *******************************************************************************************************************
// Mostrar/ocultar botón procesar
// *******************************************************************************************************************
function verBotonProcesar()
{
	if ( $('#detalle_venta tr').length > 0 )
	{
		$('#btn_facturar_venta').show();
		$('#btn_anular_venta').show();
	}
	else
		{
			$('#btn_facturar_venta').hide();		
			$('#btn_anular_venta').hide();		
		}
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function buscaDetalles(id)
{
	if ( $('#form_nuevo_cliente_venta').length )
	{
		var accion = 'buscaDetalles';
		var user = id;

		$.ajax(
		{
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, user:user},
		
		success: function(respuesta)
		{
			console.log(respuesta);

	        var principio_msg = respuesta.lastIndexOf('{');
	        var fin_msg = respuesta.lastIndexOf('}');

	        var res_txt = respuesta.substring(0, principio_msg);
	        var res_json = respuesta.substring(principio_msg, fin_msg + 1);

			console.log('res_txt: '+res_txt);
			console.log('res_json: '+res_json);

			var datos = JSON.parse(res_json);	
			
			if (datos.resultado == 'ok')
			{
				console.log(datos.resultado);
						
				$('#detalle_venta').html(datos.detalle);
				$('#detalle_totales').html(datos.totales);


			} else
			{
				console.log(datos.resultado+'; '+datos.error);
			}
			verBotonProcesar();
		},

		error: function(error)
		{
			console.log(error);
		}

		});	
	}
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function del_producto_detalle(correlativo)
{
		var accion = 'delProductoDetalle';
		var id_detalle = correlativo;

		$.ajax(
		{
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, id_detalle:id_detalle},
		
		success: function(respuesta)
		{
			console.log(respuesta);

	        var principio_msg = respuesta.lastIndexOf('{');
	        var fin_msg = respuesta.lastIndexOf('}');

	        var res_txt = respuesta.substring(0, principio_msg);
	        var res_json = respuesta.substring(principio_msg, fin_msg + 1);

			console.log('res_txt: '+res_txt);
			console.log('res_json: '+res_json);

			var datos = JSON.parse(res_json);	
			
			if (datos.resultado == 'ok')
			{
				console.log(datos.resultado);
						
				$('#detalle_venta').html(datos.detalle);
				$('#detalle_totales').html(datos.totales);


			} else
			{
				$('#detalle_venta').html('');
				$('#detalle_totales').html('');
				console.log(datos.resultado+'; '+datos.error);
			}
			verBotonProcesar();
		},

		error: function(error)
		{
			console.log(error);
		}

		});	
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function anularFactura()
{
		var nofactura = $('#no_factura').val();
		var accion = 'anularFactura';

		$.ajax(
		{
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {accion:accion, nofactura:nofactura},
		
		success: function(respuesta)
		{
			console.log(respuesta);

	        var principio_salida = respuesta.indexOf('{"salida');
	        // var fin_msg = respuesta.lastIndexOf('}');

	        var res_txt = respuesta.substring(0, principio_salida);
	        var res_json = respuesta.substring(principio_salida);

			console.log('res_txt: '+res_txt);
			console.log('res_json: '+res_json);

			var datos = JSON.parse(res_json);	
			
			if (datos.salida.resultado == 'ok')
			{
				console.log(datos.salida.resultado);
						
				$('#fila_'+nofactura).remove();
				$('#form_anular_factura .btn_ok').remove();
				$('#form_anular_factura .btn_cancel').html('Cerrar');
				$('.alertaProducto').html('<p>Factura anulada.</p>');

			} else
			{				
				console.log(datos.salida.resultado+'; '+datos.salida.error);
				$('.alertaProducto').html('<p style="color:red;">Error al anular la factura.</p>');				
			}
			verBotonProcesar();
		},

		error: function(error)
		{
			console.log(error);
		}

		});		
}

// *******************************************************************************************************************
// *******************************************************************************************************************
function closeModal(accion='')
{
	$(".alertaProducto").html('');
	$("#txtCantidad").val('');
	$("#txtPrecio").val('');


	$('.modal').fadeOut();
}
