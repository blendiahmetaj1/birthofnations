<?

session_start();
include_once("config.php");

/*

Script con varias funciones:


online(): Muestra si el servidor mysql esta disponible o no
sql($sql): Ejecuta la sentencia sql, si da error muestra mensaje de error con die()
 *         devuelve un array con los valores obtenidos si hay mas de 1,
 *			o el valor sin array si solo devuelve 1 valor
 enviar_mail($destino, $nick, $id)
 *        Envia un correo para activar la cuenta --EN DESARROLLO--
 anadir_foro($usuario, $password, $email)
 *         Añade un usuario al foro smf

*/

function online()
{
 
$ip = "localhost";  
$puerto = 3306; 
 
if ($fp=@fsockopen($ip,$puerto,$ERROR_NO,$ERROR_STR,(float)0.5))   
    {   
    fclose($fp);   
            echo "<font color='Green'>Online</font>";  
        } else {         
            echo "<font color='Red'>OFFLINE</font>";   
        }   


}

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
	
select_lang();

function enviar_mail($destino, $nick)
{

global $mail_activation;
//var_dump($mail_activation);
// subject
$titulo = $mail_activation['activacion_titulo'];

// message
$mensaje = $mail_activation['activacion_mensaje_titulo'].$mail_activation['activacion_mensaje_cuerpo'];

// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Cabeceras adicionales
$cabeceras .= 'From: BirthofNations <admin@birthofnations.com>' . "\r\n";

// Mail it
mail($destino, $titulo, $mensaje, $cabeceras);
	
	
	
	
}




function mail_referido($nick_padrino, $destino, $code)
{
    global $txt;

// subject
$titulo = $txt['referer_title'];

// message
$mensaje = $txt['referer_mail1'].$nick_padrino.$txt['referer_mail2'].$code;

// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Cabeceras adicionales
$cabeceras .= 'From: BirthofNations <admin@birthofnations.com>' . "\r\n";

// Mail it
mail($destino, $titulo, $mensaje, $cabeceras);
    
    
    
}

function mail_bienvenida($nick, $destino)
{
    global $txt;
    
    $titulo = $txt['mail_bienvenida_title'];

    // message
    $mensaje = $txt['mail_bienvenida1'].$nick.$txt['mail_bienvenida2'].$code;

    // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Cabeceras adicionales
    $cabeceras .= 'From: BirthofNations <admin@birthofnations.com>' . "\r\n";

    // Mail it
    mail($destino, $titulo, $mensaje, $cabeceras);
    
}

function anadir_foro($usuario, $password, $email)
{
    
$server= $GLOBALS["server"];
$forouser=$GLOBALS["forouser"];; /* Usuario DB foro smf */
$foropass=$GLOBALS["foropass"];; /* Password DB foro smf */
$forodb=$GLOBALS["forodb"];; /* DB foro smf */
    
$link2=mysql_connect($server, $forouser, $foropass);

mysql_select_db($forodb, $link2);


$salt = substr(md5(mt_rand()), 0, 4);
$contrasena = sha1(strtolower($usuario) . $password);
$hora = time();

mysql_query("INSERT INTO smf_members (member_name, date_registered, real_name, passwd, email_address, password_salt) VALUES ('$usuario', '$hora', '$usuario', '$contrasena', '$email', '$salt')");

}


function anadir_bugs($usuario, $password, $email)
{
    
$server= $GLOBALS["server"];
$forouser=$GLOBALS["bugsuser"];; /* Usuario DB foro smf */
$foropass=$GLOBALS["bugspass"];; /* Password DB foro smf */
$forodb=$GLOBALS["bugsdb"];; /* DB foro smf */
    
$link3=mysql_connect($server, $forouser, $foropass);

mysql_select_db($forodb, $link3);

$hora = time();
$contrasena = md5($password);
$rand = rand();
mysql_query("INSERT INTO mantis_user_table (username, email, password, cookie_string, date_created, access_level) VALUES ('$usuario', '$email', '$contrasena','$rand', '$hora', '25')");

}

function checkban ($id){
    
    include("config.php");
    
    $sql = sql("SELECT * FROM bans WHERE ( is_perm = 1 OR fecha_fin > ".time()." ) AND id_usuario = " . $id);
    // (es permanente O no ha caducado ) Y esta asignado a su id -> Sigue ban
    if ($sql != false){ //Si ha devuelto algo es que esta ban
        return true;
    } else {
        return false;
    }
    
}


function select_lang ()
{
    global $mail_activation;
    global $signup_form;
    global $login_form;
    global $txt;
    //Cualquier metodo que vaya aqui para elegir el idioma Y cargar el archivo. De momento solo hay español
      
    include_once($_SERVER['DOCUMENT_ROOT']."birth/i18n/es_ES.php");
    
}

?>

