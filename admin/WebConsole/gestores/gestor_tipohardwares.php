<?php
// *************************************************************************************************************************************************
// Aplicación WEB: ogAdmWebCon
// Autor: José Manuel Alonso (E.T.S.I.I.) Universidad de Sevilla
// Fecha Creación: Año 2009-2010
// Fecha Última modificación: Agosto-2010
// Nombre del fichero: gestor_tipohardwares.php
// Descripción :
//		Gestiona el mantenimiento de la tabla de tipohardwares
// *************************************************************************************************************************************************
include_once("../includes/ctrlacc.php");
include_once("../clases/AdoPhp.php");
include_once("../clases/XmlPhp.php");
include_once("../clases/ArbolVistaXML.php");
include_once("../includes/CreaComando.php");
include_once("../includes/constantes.php");
include_once("../includes/opciones.php");
//________________________________________________________________________________________________________
$opcion=0; // Inicializa parametros

$idtipohardware=0; 
$descripcion="";
$urlimg="";
$urlicono="";

if (isset($_POST["opcion"])) $opcion=$_POST["opcion"]; // Recoge parametros

if (isset($_POST["idtipohardware"])) $idtipohardware=$_POST["idtipohardware"];
if (isset($_POST["descripcion"])) $descripcion=$_POST["descripcion"]; 
if (isset($_POST["urlicono"])) $urlicono=$_POST["urlicono"]; 

if(empty($urlicono))
	$urlimg="../images/iconos/confihard.gif";
else
	$urlimg="../images/iconos/".$urlicono;

$tablanodo=""; // Arbol para nodos insertados
$cmd=CreaComando($cadenaconexion); // Crea objeto comando
$resul=false;
if ($cmd){
	$resul=Gestiona();
	$cmd->Conexion->Cerrar();
}
if($opcion!=$op_movida){
	echo '<HTML>';
	echo '<HEAD>';
	echo '	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
	echo '<BODY>';
	echo '<P><SPAN style="visibility:hidden" id="arbol_nodo">'.$tablanodo.'</SPAN></P>';
	echo '	<SCRIPT language="javascript" src="../jscripts/propiedades_tipohardwares.js"></SCRIPT>';
	echo '<SCRIPT language="javascript">'.chr(13);
	if ($resul){
		echo 'var oHTML'.chr(13);
		echo 'var cTBODY=document.getElementsByTagName("TBODY");'.chr(13);
		echo 'o=cTBODY.item(1);'.chr(13);
	}
}
	$literal="";
	switch($opcion){
		case $op_alta :
			$literal="resultado_insertar_tipohardwares";
			break;
		case $op_modificacion:
			$literal="resultado_modificar_tipohardwares";
			break;
		case $op_eliminacion :
			$literal="resultado_eliminar_tipohardwares";
			break;
		default:
			break;
	}
if ($resul){
	if ($opcion==$op_alta )
		echo $literal."(1,'".$cmd->DescripUltimoError()." ',".$idtipohardware.",o.innerHTML);".chr(13);
	else
		echo $literal."(1,'".$cmd->DescripUltimoError()." ','".$descripcion."');".chr(13);
}
else
	echo $literal."(0,'".$cmd->DescripUltimoError()."',".$idtipohardware.")";

if($opcion!=$op_movida){
	echo '	</SCRIPT>';
	echo '</BODY>	';
	echo '</HTML>';	
}
/**************************************************************************************************************************************************
	Inserta, modifica o elimina datos en la tabla tipohardwares
________________________________________________________________________________________________________*/
function Gestiona(){
	global	$cmd;
	global	$opcion;

	global	$idtipohardware;
	global	$descripcion;
	global	$urlimg;
	
	global	$op_alta;
	global	$op_modificacion;
	global	$op_eliminacion;
	global	$tablanodo;

	$cmd->CreaParametro("@idtipohardware",$idtipohardware,1);
	$cmd->CreaParametro("@descripcion",$descripcion,0);
	$cmd->CreaParametro("@urlimg",$urlimg,0);

	switch($opcion){
		case $op_alta :
			$cmd->texto="INSERT INTO tipohardwares(descripcion,urlimg) VALUES (@descripcion,@urlimg)";
			$resul=$cmd->Ejecutar();
			if ($resul){ // Crea una tabla nodo para devolver a la página que llamó ésta
				$idtipohardware=$cmd->Autonumerico();
				$arbolXML=SubarbolXML_tipohardwares($idtipohardware,$descripcion,$urlimg);
				$baseurlimg="../images/signos"; // Url de las imagenes de signo
				$clasedefault="texto_arbol"; // Hoja de estilo (Clase por defecto) del árbol
				$arbol=new ArbolVistaXML($arbolXML,0,$baseurlimg,$clasedefault);
				$tablanodo=$arbol->CreaArbolVistaXML();
			}
			break;
		case $op_modificacion:
			$cmd->texto="UPDATE tipohardwares SET descripcion=@descripcion,urlimg=@urlimg WHERE idtipohardware=@idtipohardware";
			$resul=$cmd->Ejecutar();
			break;
		case $op_eliminacion :
			$cmd->texto="DELETE  FROM tipohardwares WHERE idtipohardware=".$idtipohardware;
			$resul=$cmd->Ejecutar();
			break;
		default:
			break;
	}
	return($resul);
}
/*________________________________________________________________________________________________________
	Crea un arbol XML para el nuevo nodo insertado 
________________________________________________________________________________________________________*/
function SubarbolXML_tipohardwares($idtipohardware,$descripcion,$urlimg){
		global 	$LITAMBITO_TIPOHARDWARES;
		$cadenaXML.='<TIPOHARDWARES';
		// Atributos
		if	($urlimg)
				$cadenaXML.=' imagenodo='.$urlimg;
			else
				$cadenaXML.=' imagenodo="../images/iconos/confihard.gif"';	
		$cadenaXML.=' infonodo="'.$descripcion.'"';
		$cadenaXML.=' clickcontextualnodo="menu_contextual(this,' ."'flo_".$LITAMBITO_TIPOHARDWARES."'" .')"';
		$cadenaXML.=' nodoid='.$LITAMBITO_TIPOHARDWARES.'-'.$idtipohardware;
		$cadenaXML.='>';
		$cadenaXML.='</TIPOHARDWARES>';
		return($cadenaXML);
}

