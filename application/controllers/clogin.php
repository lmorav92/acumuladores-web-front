<?php
/**
* 
*/
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH . 'core/Main_Controller.php');
class CLogin extends Main_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('mlogin');
		#$this->load->model('reporte_m');		
	}

	public function index()
	{
				
			#$this->load->view('plantilla/header');
			#$this->load->view('plantilla/menuleft');
			#$this->load->view('plantilla/menutop');
			#$this->load->view('vportada');
			#$this->load->view('plantilla/footer');	
			#$this->Cargar_Plantilla('vportada');
			//var_dump("excito");
	}
	

	public function Ingresar()
	{
		$usu = $this->input->post('xUsuario');
		$pass = md5($this->input->post('xPassword'));			
		$res = $this->mlogin->Ingresar($usu, $pass);//retorna 1 si es correcto, 0 en caso contrario
		//var_dump($res);
		
	}

	public function CerrarSesion()
	{
		$this->session->sess_destroy();
		redirect(base_url());
	}
	
	
}