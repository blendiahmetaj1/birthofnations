<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once($_SERVER['DOCUMENT_ROOT'] . "/include/funciones.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/politico/objeto_region.php");

if (!isset($_GET['id_region']))
    die("Error: id no valido"); //Substituir por error 404

$region = new region($_GET['id_region']);

echo"<h1>" . $region->nombre . "</h1>";

echo getString('owner') . " <a href='../pais/" . $region->owner_id() . "'>" . $region->owner_name() . "<img alt='bandera' title='" . $region->owner_name() . "' src='" . $region->owner_flag() . "'/>" . "</a>";

echo"<h1>".getString("zonas")."</h1>";

foreach($region->zone_list() as $zona)
{   
    echo "<a href = '../zona/".$zona['id_zona']."'>".$zona['name'] ."</a><br>";
}

echo"<h1>Rutas hacia otros sitios: </h1>";
//echo"<table><tr><td>Destino</td><td>Distancia</td>";

foreach ($region->distance_to_all() as $id => $ruta) {
    //id la id del destino, ruta[0...1...2...] los pasos de la ruta y distance la distancia
    //if ($ruta['distance'] <= 0) {
    if(count($ruta) > 3){
        continue;
    } //Cero es que es la misma region y -1 que no hay conexion
    else {
        $reg = new region($id);
        echo '<tr><td><a href="' . $id . '">' . $reg->nombre . "<img alt='bandera' title='" . $reg->owner_name() . "' src='" . $reg->owner_flag() . "'/>" . '</td><td>' . $ruta['distance'] . '</td><br>';
    }
}

echo '</table>'
?>
