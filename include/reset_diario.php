<?

//Archivo de reset diario

include_once("config.php");
function sql_error($sql)
{
	
	$result = mysql_query($sql);

    if ($result == false)
    {
        //error_log("SQL error: ".mysql_error()."\n\nOriginal query: $sql\n");
        // Remove following line from production servers 
        die("SQL error: ".mysql_error()."\b<br>\n<br>Original query: $sql \n<br>\n<br>");
    }
	return $result;
	
}



function sql_data($result)
{
	
	//$result = sql_error($sql);
	
	if($lst = mysql_fetch_assoc($result))
    {
        mysql_free_result($result);
        return $lst;
    }
	    return false;
	
}

function sql($sql)
{
	
	$result = sql_error($sql);
	
        //Si no devuelve nada sale de la funcion
        if($result==1)
            return true;

	if (mysql_num_rows($result) == 1)	
	{
		if (mysql_num_fields($result) == 1)
			{
				
			$dato = mysql_fetch_row($result);
			return $dato[0];
			
			}
		else
	$table = sql_data($result);
	}
	else
	{
    
    $table = array();
	 if (mysql_num_rows($result) > 0)
    {
        $i = 0;
        while($table[$i] = mysql_fetch_assoc($result)) 
            $i++;
        unset($table[$i]);                                                                                  
    }                                                                                                                                     
    mysql_free_result($result);
	}
    return $table;
	}


        
        function sql2($sql)
{
	
	$result = sql_error($sql);
	
        //Si no devuelve nada sale de la funcion
        if($result==1)
            return true;

	if (mysql_num_rows($result) == 1)	
	{
		if (mysql_num_fields($result) == 1)
			{
				
			$dato = mysql_fetch_row($result);
			return $dato[0];
			
			}
		else{
                    $table = array();
                    $table[0] = sql_data($result);
                }
	
	}
	else
	{
    
    $table = array();
	 if (mysql_num_rows($result) > 0)
    {
        $i = 0;
        while($table[$i] = mysql_fetch_assoc($result)) 
            $i++;
        unset($table[$i]);                                                                                  
    }                                                                                                                                     
    mysql_free_result($result);
	}
    return $table;
	}
//Seguridad       
if(!isset($_SERVER['argv']))
    die("Error");

$pin = $_SERVER['argv'][1];
$pin2 = sql("SELECT pin FROM settings");

if($pin != $pin2)
    die("Error");

//Economico
sql("UPDATE diario SET work = 0");

//Actualizacion del dia
sql("UPDATE settings SET day=day+1");

//Politico
sql("UPDATE usuarios SET ant_partido = ant_partido + 1");
$DA = sql("SELECT day FROM settings");
$sql = sql2("SELECT id_partido, frec_elecciones, dia_elecciones FROM partidos");

foreach ($sql as $party) {
    $mod = $DA % $party['frec_elecciones']; //Modulo del dia
    if($mod < 0){$mod += $party['frec_elecciones'];} // Si es negativo lo subimos
    if ($mod == $party['dia_elecciones']-2) {//2 dias antes de las elecciones, abrimos la votacion
        {
        $time = time();
        $time2 = $time +86400 - 100; //quitamos unos cuantos, por si da problemas al 
        sql("INSERT INTO votaciones(tipo_votacion,fin,comienzo,param1) VALUES ('1','" . $time2 . "',' " . $time . "','" . $party['id_partido'] . "')");
        }
        
    }
}


$time = time();
//Cerramos las votaciones que no hayan sido resueltas, que ya hayan terminado y sean de presidente de partido
$sql = sql2("SELECT * FROM votaciones WHERE solved = 0 AND fin < " . $time . " AND tipo_votacion = 1");

foreach ($sql as $votacion) {
    //Ver la lista de candidatos
    $candidatos = sql2("SELECT * FROM candidatos_elecciones WHERE id_votacion = " . $votacion['id_votacion']);
    if ($candidatos != false) {//Si hay candidatos
        $winner = array('id' => 0, 'votos' => -1);
        foreach ($candidatos as $cand) {//Ver quien es el ganador
            if ($cand['votos'] > $winner['votos']) {//Comparamos los votos con los del ganador
                $winner['id'] = $cand['id_candidato'];
                $winner['votos'] = $cand['votos'];
            } elseif ($cand['votos'] == $winner['votos']) {//Si empatan utilizamos los criterios de desempate (que no tenemos)
            }
        }

        //Cambiar las casilla de lider
        sql("UPDATE partidos SET id_lider = " . $winner['id'] . " WHERE id_partido = " . $votacion['param1']);
        sql("UPDATE votaciones SET solved = 1 WHERE id_votacion = " . $votacion['id_votacion']);
    } else {//Si no hay candidatos
        //Todo sigue igual
    }
}

echo "Cron realizado correctamente";
?>

