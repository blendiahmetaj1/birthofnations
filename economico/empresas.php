<?

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once($_SERVER['DOCUMENT_ROOT']."/include/funciones.php");



echo"<h1>Mis empresas</h1>";


$sql = sql("SELECT * FROM empresas WHERE id_propietario='".$_SESSION['id_usuario']."'");

foreach ($sql as $empresa) {
    echo "<a href='/".$_GET['lang']."/empresa/".$empresa['id_empresa']."'>".$empresa['nombre_empresa']."</a><br/>";
}
?>

<br/>