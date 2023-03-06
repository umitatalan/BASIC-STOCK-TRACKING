<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model','product');
		$this->load->model('transaction_model','transaction');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('templates/header');
		$this->load->view('product_view');
		$this->load->view('templates/footer');
	}

	public function ajax_list()
	{
		$list = $this->product->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $product) 
		{
			$no++;
			$row = array();

			if (isset($_POST['withid'])){
				$row[] = $product->id;
			}
			
			$row[] = $product->Name;
			$row[] = $product->Balance;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Düzenle" onclick="edit_product('."'".$product->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Düzenle</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void()" title="Sil" onclick="delete_product('."'".$product->id."'".')"><i class="glyphicon glyphicon-trash"></i> Sil</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->product->count_all(),
						"recordsFiltered" => $this->product->count_filtered(),
						"data" => $data,
				);

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->product->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$data = array(
				'Name' => $this->input->post('Name'),
				'Balance' => $this->input->post('Balance')
			);
		$insert = $this->product->save($data);
		echo json_encode(array("status" => TRUE, 'id' => $insert));
	}

	public function ajax_update()
	{
		$data = array(
				'Name' => $this->input->post('Name'),
				'Balance' => $this->input->post('Balance')
			);
		$this->product->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->product->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

}
