<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Person extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('person_model','person');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('templates/header');
		$this->load->view('person_view');
		$this->load->view('templates/footer');
	}

	public function ajax_list()
	{
		$list = $this->person->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) 
		{
			$no++;
			$row = array();

			if (isset($_POST['withid'])){
				$row[] = $person->id;
			}

			$row[] = $person->Name;
			$row[] = $person->Surname;
			$row[] = $person->Phone;
			$row[] = $person->Email;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Düzenle" onclick="edit_person('."'".$person->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Düzenle</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void()" title="Sil" onclick="delete_person('."'".$person->id."'".')"><i class="glyphicon glyphicon-trash"></i> Sil</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->person->count_all(),
						"recordsFiltered" => $this->person->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->person->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$data = array(
				'Name' => $this->input->post('Name'),
				'Surname' => $this->input->post('Surname'),
				'Phone' => $this->input->post('Phone'),
				'Email' => $this->input->post('Email')
			);
		$insert = $this->person->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$data = array(
				'Name' => $this->input->post('Name'),
				'Surname' => $this->input->post('Surname'),
				'Phone' => $this->input->post('Phone'),
				'Email' => $this->input->post('Email')
			);
		$this->person->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->person->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}
}
