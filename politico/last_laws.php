<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/include/funciones.php");
include($_SERVER['DOCUMENT_ROOT'] . "/politico/launch_law.php");

//De momento:
check_laws();
?><h3><? echo getString('open_polls'); ?></h3><?
$time = time();

$sql = sql2("SELECT id_votacion, tipo_votacion FROM votaciones WHERE solved = 0 AND is_cargo = 0 AND fin > " . $time . " AND id_pais = " . $id_pais);

foreach ($sql as $votacion) {
    if ($votacion['tipo_votacion'] >= 100) {//Votaciones de leyes
        $si = sql("SELECT votos FROM candidatos_elecciones WHERE id_candidato =-1 AND id_votacion = " . $votacion['id_votacion']);
        $no = sql("SELECT votos FROM candidatos_elecciones WHERE id_candidato =-2 AND id_votacion = " . $votacion['id_votacion']);
        echo proposal_text($votacion['tipo_votacion'], $votacion['id_votacion']);
        if (puedo_votar($objeto_usuario->id_usuario, $votacion['tipo_votacion'], $votacion['id_votacion'])) {
            echo "<br><a href=/politico/votar.php?idv=" . $votacion['id_votacion'] . "&vot=-1>" . getString('si') . ": </a>" . $si;
            echo " <a href=/politico/votar.php?idv=" . $votacion['id_votacion'] . "&vot=-2>" . getString('no') . ": </a>" . $no;
        } else {//No puede votar pero que vea los resultados
            echo "<br>" . getString('si') . ": " . $si;
            echo " " . getString('no') . ": " . $no . "<br>";
        }
    } else {//Leyes especiales que debamos ver
        echo proposal_text($votacion['tipo_votacion'], $votacion['id_votacion']) . "<br>";
        echo "<a href='../../" . $_GET['lang'] . "/candidatos/" . $votacion['id_votacion'] . "'>" . getString('vote') . "</a><br>";
    }
}
?><h3><? echo getString('closed_polls'); ?></h3><?
$sql = sql2("SELECT id_votacion, tipo_votacion FROM votaciones WHERE solved = 1 AND is_cargo = 0 AND id_pais = " . $id_pais . " ORDER BY id_votacion DESC LIMIT 10");
foreach ($sql as $votacion) {

    if ($votacion['tipo_votacion'] >= 100) {//Votaciones de si/no
        $si = sql("SELECT votos FROM candidatos_elecciones WHERE id_candidato =-1 AND id_votacion = " . $votacion['id_votacion']);
        $no = sql("SELECT votos FROM candidatos_elecciones WHERE id_candidato =-2 AND id_votacion = " . $votacion['id_votacion']);
        echo proposal_text($votacion['tipo_votacion'], $votacion['id_votacion']);

        echo "<br>" . getString('si') . ": " . $si;
        echo " " . getString('no') . ": " . $no . "<br>";
    } else {//Votaciones de ir a la pagina a votar
        echo proposal_text($votacion['tipo_votacion'], $votacion['id_votacion']) . "<br>";
        echo "<a href='../../" . $_GET['lang'] . "/candidatos/" . $votacion['id_votacion'] . "'>" . getString('see_results') . "</a><br>";
    }
}

function proposal_text($tipo, $id) {
    $sql = sql("SELECT id_pais, param1 FROM votaciones WHERE id_votacion = " . $id);
    $p = explode(".", $sql['param1']);

    switch ($tipo):
        case 2:
            $txt = getString('1prop_2');
            break;
        case 100:
            $txt = getString('1prop_100') . " " . $p[0] . " " . getString('2prop_100');
            break;
        case 105:
            $txt = getString('1prop_105') . " <img src='" . $sql['param1'] . "'>";
            break;
        case 106:
            $txt = getString('1prop_106') . ": " . $p[0];
            break;
        case 200:
            $txt = getString('1prop_200') . $p[0] . " " . moneda_pais($sql['id_pais']);
            break;
        case 201:
            $txt = getString('1prop_201') . $p[0] . " " . strtoupper($p[1]) . getString('2prop_201') . id2nick($p[2]);
            break;
        case 300:
            $sql = sql("SELECT name FROM country WHERE idcountry = ".$p[0]);
            $txt = getString('1prop_300') . $sql;
            break;
        case 301:
            $txt = getString('1prop_301') . region2name($p[0]) . getString('2prop_301') . region2name($p[1]);
            break;
    endswitch;
    return $txt;
}
?>