<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/include/funciones.php");

if (!isset($_GET['id_partido']))
    die("Error: id no valido"); //Substituir por error 404

$party = sql("SELECT * FROM partidos WHERE id_partido = " . $_GET['id_partido']);
$leader = sql("SELECT nick, avatar FROM usuarios WHERE id_usuario = " . $party['id_lider']);
$user = sql("SELECT id_partido, id_nacionalidad FROM usuarios WHERE id_usuario = " . $_SESSION['id_usuario']);
$dia_actual = sql("SELECT day FROM settings");


echo "<h1>" . '<img src="' . $party['url_bandera'] . '">';
echo $party['nombre_partido'] . "</h1>";

if ($user['id_nacionalidad'] == $party['id_pais'] && $user['id_partido'] == 0) {//Condiciones para poder afiliarse
    echo '<a href="/politico/afiliar.php?af=' . $party['id_partido'] . '">'.getString('affiliate').'</a>';
} elseif ($user['id_partido'] == $_GET['id_partido']) {//Condiciones para desafiliarse
    echo '<a href="/politico/desafiliar.php">'.getString('unaffiliate').'</a>';
}

echo "Jefe actual: " . $leader['nick'] . '<img src="' . $leader['avatar'] . '">';
if ($dia_actual % $party['frec_elecciones'] == $party['dia_elecciones'] && $user['id_partido'] == $party['id_partido']) { //Dia de elecciones + Estoy afiliado
    //Mensaje de que hay elecciones
    echo "<br>".getString('vote_day')."<br>";
    //Sacar id de la votacion
    $time = time();
    $sql = sql("SELECT id_votacion FROM votaciones WHERE tipo_votacion = 1 AND param1 = " . $party['id_partido'] . " AND fin > " . $time);
    echo '<a href="/politico/lista_candidatos.php?id=' . $sql . '">'.getSTring('vote').'</a>';
} elseif (($dia_actual % $party['frec_elecciones'] == $party['dia_elecciones'] - 1 || $dia_actual % $party['frec_elecciones'] == $party['dia_elecciones'] - 2) && $user['id_partido'] == $party['id_partido']) {
    //2 dias anteriores a las elecciones + Estoy afiliado
    // Fecha + Postulacion
    echo getString('next_elections') . next_elecciones($dia_actual, $party['dia_elecciones'], $party['frec_elecciones']);
    $time = time();
    $vot = sql("SELECT id_votacion FROM votaciones WHERE param1 = " . $party['id_partido'] . " AND tipo_votacion = 1 AND fin > " . $time);
    $sql = sql("SELECT * from candidatos_elecciones WHERE id_candidato = " . $_SESSION['id_usuario'] . " AND id_votacion = " . $vot);

    if ($sql == false) {//Si aun no esta postulado
        echo "[<a href='/politico/postular.php?v=" . $vot . "'>".getString('postulate')."</a>]";
    } else {//Si ya esta postulado
        echo "[<a href='/politico/despostular.php?v=" . $vot . "'>".getString('unpostulate')."</a>]";
    }
} else {
    //Calculamos la siguiente fecha
    echo getString('next_elections') . next_elecciones($dia_actual, $party['dia_elecciones'], $party['frec_elecciones']);
}
$sql = sql("SELECT COUNT(*) FROM usuarios WHERE id_partido = " . $_GET['id_partido']);

echo "<h2> ".getString('member_list')." (" . $sql . ")</h2>";

$sql = sql2("SELECT id_usuario, nick FROM usuarios WHERE id_partido = " . $_GET['id_partido']);

foreach ($sql as $miembro) {
    echo '<a href="../perfil/' . $miembro['id_usuario'] . '">' . $miembro['nick'] . '</a><br>';
}
?>