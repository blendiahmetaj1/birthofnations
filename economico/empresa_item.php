<?

include_once($_SERVER['DOCUMENT_ROOT']."/include/funciones.php");
include_once($_SERVER['DOCUMENT_ROOT']."/include/config_variables.php");

if (isset($_GET['emp']) && $_GET['emp'] != "" && strlen($_GET['emp'])>0 && isset($_GET['item']) && $_GET['item'] != "" && strlen($_GET['item'])>0 ){
    
    //Comprobar que tiene
    
    $nombre = nombre_item($_GET['item']);
    
    $cantidad = sql("SELECT ". $nombre ." FROM inventario_empresas WHERE id_empresa = " . $_GET['emp']);
    
    if($cantidad <= 0){
        
        die(getString('company_no_tienes'));
        
    }else{
        //Quitar uno
        sql("UPDATE inventario_empresas SET ". $nombre . " = " . $nombre ." - 1 WHERE id_empresa = ". $_GET['emp']);

        switch($_GET['item'])://Segun el objeto se realiza una accion
                //Aqui van los usos para las empresas
        endswitch;
    }
    
}

?>