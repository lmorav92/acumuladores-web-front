<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_Controller extends CI_Controller 
{
  public $data=array();
  public $data_general=array();
  public function __construct() 
  { 
    parent::__construct();     
    #$this->load->model('usuario_m');
   
   
    
  }
  
/* Cambio de horario 1er domingo de mayo a las 12 am se adelanta una hora */
/* Cambio de horario 1er domingo de noviembre a las 2 am se atrasa una hora */
  
public function hoy(){
  date_default_timezone_set('GMT');
  $gtm = time() -$this->Cambio_Horario();  
  $hoy = getdate($gtm);
	return $hoy['year'] . "-" . $hoy['mon'] . "-" . $hoy['mday'] . " " . $hoy['hours'] . ":" . $hoy['minutes'] . ":" . $hoy['seconds'];
}
public function Anno(){
  date_default_timezone_set('GMT');
  $gtm = time() - $this->Cambio_Horario();  
  $hoy = getdate($gtm);
	return "Año ".($hoy['year']-1958) . " de la Revolución.";
}
public function Anno_Actual()
{
  date_default_timezone_set('GMT');
  $gtm = time() - $this->Cambio_Horario();  
  $hoy = getdate($gtm);
  return $hoy['year'];
}
public function Fecha_Larga(){
  date_default_timezone_set('GMT');
  $gtm = time() - $this->Cambio_Horario();  
  $hoy = getdate($gtm);
  switch ($hoy['mon']) {
    case 1:
      $mes="Enero";
      break;
    case 2:
      $mes="Febrero";
      break;
    case 3:
      $mes="Marzo";
      break;
    case 4:
      $mes="Abril";
      break;
    case 5:
      $mes="Mayo";
      break;
    case 6:
      $mes="Junio";
      break;
    case 7:
      $mes="Julio";
      break;
    case 8:
      $mes="Agosto";
      break; 
    case 9:
      $mes="Septiembre";
      break;
    case 10:
      $mes="Octubre";
      break;
    case 11:
      $mes="Noviembre";
      break;
    case 12:
      $mes="Diciembre";
      break;
  }
	return "Bayamo MN, ".$hoy['mday'] . " de " . $mes . " del " . $hoy['year'] . ".";
}
public function TiempoDesconexion(){
  $h = array();
  if(count($this->session->userdata())>2){
  $datos =$this->prestamo_m->List(0,'Activo',$this->hoy(),$this->session->userdata());	
  foreach ($datos as $key => $value) {
    # code...
    if($value->diff != 'Préstamo'){
      $h1['codigo'] = $value->codigo;
      $h1['persona'] = $value->persona;
      $h1['diff'] = $value->diff;
      $h1['Ocupacion_persona'] = $value->Ocupacion_persona;
      $obj = (object) $h1;
      array_push($h, $obj);
    }    
  }
}
  return $h;
} 
public function ControlAcceso($otro=false){ 
  $rol = $this->session->userdata('rol');
  return ($rol=="Administrador" || $otro) ? true : false;
}
/* protected function Fun_tokenPass($password){
 return $this->tokenPass.$password.$this->tokenPass1; 
} */

/* 
public function Acceso_Denegado(){
      $this->Cargar_Plantilla('plantilla/error_404');
} */
/* 
public function Cargar_Plantilla($dir='plantilla/error_403',$param=array()){
  $this->load->view('plantilla/header');
	$this->load->view('plantilla/menuleft');
	$this->load->view('plantilla/menutop',$this->data_general);			
	$this->load->view($dir,$param);
	$this->load->view('plantilla/footer');
} */
/* 
public function Redirect(){
  ($this->data_general['_redirect']) ? redirect(base_url().$this->data_general['_redirect']) : $this->Cargar_Plantilla();  
} */

public function Cambio_Horario(){
  $time = time();  
  $hoy = date($time);
  $fecha = strtotime($hoy);
  $anno = date('Y',$fecha);
  $primerDomingoMayo = strtotime("first sunday of May $anno");
  $primerDomingoNoviembre = strtotime("first sunday of November $anno");
  ($fecha >= $primerDomingoMayo && $fecha<=$primerDomingoNoviembre) ? $gtm = 4: $gtm=5;
  return 60*60*$gtm;
}
/* 
public function ListarEnum()
	{		
    $tabla = $this->input->get('tabla');
    $campo = $this->input->get('campo');
		$data['tabla'] = $tabla;
		$data['campo'] = $campo;		
		echo json_encode($this->usuario_m->ListarEnum($data));
	} */
} 