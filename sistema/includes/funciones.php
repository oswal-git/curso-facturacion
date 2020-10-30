<?php 
	date_default_timezone_set('America/Guatemala'); 
	// date_default_timezone_set('Europe/Madrid'); 
	
	function fechaC(){
		$mes = array("","Enero", 
					  "Febrero", 
					  "Marzo", 
					  "Abril", 
					  "Mayo", 
					  "Junio", 
					  "Julio", 
					  "Agosto", 
					  "Septiembre", 
					  "Octubre", 
					  "Noviembre", 
					  "Diciembre");
		return date('d')." de ". $mes[date('n')] . " de " . date('Y');
	}

	function validateNif($nif) 
	{
		$nif = strtoupper(substr(str_repeat("0", 10) . $nif, -9));
		$nif_codes = 'TRWAGMYFPDXBNJZSQVHLCKE';
		echo '0 nif ->'. $nif.'<br>';

		$sum = (string) getCifSum ($nif);
		$n = 10 - substr($sum, -1);

		if (preg_match ('/^[0-9]{8}[A-Z]{1}$/', $nif)) {
		// DNIs
			$num = substr($nif, 0, 8);
			echo '1 ->'. $nif_codes[$num % 23].'<br>';
			return ($nif[8] == $nif_codes[$num % 23]);
		} elseif (preg_match ('/^[XYZ][0-9]{7}[A-Z]{1}$/', $nif)) {
		// NIEs normales
			$tmp = substr ($nif, 1, 7);
			$tmp = strtr(substr ($nif, 0, 1), 'XYZ', '012') . $tmp;

			return ($nif[8] == $nif_codes[$tmp % 23]);
		} elseif (preg_match ('/^[KLM]{1}/', $nif)) {
		// NIFs especiales
			return ($nif[8] == chr($n + 64));
		} elseif (preg_match ('/^[T]{1}[A-Z0-9]{8}$/', $nif)) {
		// NIE extraño
			return true;
		}
		return false;
	}

	function validateNie($nif)
	{
		$nif = substr(str_repeat("0", 10) . $nif, -9);
		if (preg_match('/^[XYZT][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/', $nif)) 
		{
			for ($i = 0; $i < 9; $i ++)
			{
				$num[$i] = substr($nif, $i, 1);
			}
			echo  '2 ->'.substr(‘TRWAGMYFPDXBNJZSQVHLCKE’, substr(str_replace(array(‘X’,’Y’,’Z’), array(‘0′,’1′,’2’), $nif), 0, 8) % 23, 1).'<br>';
			if ($num[8] == substr(‘TRWAGMYFPDXBNJZSQVHLCKE’, substr(str_replace(array(‘X’,’Y’,’Z’), array(‘0′,’1′,’2’), $nif), 0, 8) % 23, 1)) 
			{
				return true;
			} else 
			{
				return false;
			}
		}
	}

	function validateCif ($cif) 
	{
		$cif = substr(str_repeat("0", 10) . $cif, -9);
		$cif_codes = 'JABCDEFGHI';

		$sum = (string) getCifSum ($cif);
		$n = (10 - substr ($sum, -1)) % 10;

		if (preg_match ('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)) 
		{
			if (in_array ($cif[0], array ('A', 'B', 'E', 'H'))) 
			{
				// Numerico
				echo  '3 ->'.$n.'<br>';
				return ($cif[8] == $n);
			} elseif (in_array ($cif[0], array ('K', 'P', 'Q', 'S'))) 
			{
				// Letras
				echo  '4 ->'.$cif_codes[$n].'<br>';
				return ($cif[8] == $cif_codes[$n]);
			} else 
			{
				// Alfanumérico
				if (is_numeric ($cif[8])) 
				{
					echo  '5 ->'.$n.'<br>';
					return ($cif[8] == $n);
				} else 
				{
					echo  '6 ->'.$cif_codes[$n].'<br>';
					return ($cif[8] == $cif_codes[$n]);
				}
			}
		}

		echo  '7 ->'.$n.'<br>';
		return false;
	}

	function getCifSum($cif) 
	{
		$sum = $cif[2] + $cif[4] + $cif[6];

		for ($i = 1; $i<8; $i += 2) 
		{
			$tmp = (string) (2 * $cif[$i]);

			$tmp = $tmp[0] + ((strlen ($tmp) == 2) ?  $tmp[1] : 0);

			$sum += $tmp;
		}

		return $sum;
	}	
 ?>