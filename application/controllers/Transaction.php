<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('transaction_model','transaction');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('templates/header');
		$this->load->view('transaction_view');
		$this->load->view('templates/footer');
	}

	public function ajax_list()
	{
		$list = $this->transaction->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $transaction) 
		{
			$no++;
			$row = array();
			$row[] = $transaction->DateTime;
			$row[] = $transaction->CustomerName . ' ' . $transaction->CustomerSurname;
			$row[] = $transaction->ProductName;
			$row[] = $transaction->Description;
			$row[] = $transaction->Amount;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Düzenle" onclick="edit_transaction('."'".$transaction->TransactionId."'".')"><i class="glyphicon glyphicon-pencil"></i> Düzenle</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void()" title="Sil" onclick="delete_transaction('."'".$transaction->TransactionId."'".')"><i class="glyphicon glyphicon-trash"></i> Sil</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->transaction->count_all(),
						"recordsFiltered" => $this->transaction->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->transaction->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$data = array(
				'DateTime' => $this->input->post('DateTime'),
				'CustomerId' => $this->input->post('CustomerId'),
				'ProductId' => $this->input->post('ProductId'),
				'Direction' => $this->input->post('Direction'),
				'Amount' => $this->input->post('Amount'),
				'Description' => $this->input->post('Description')
			);
		$insert = $this->transaction->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$data = array(
			'DateTime' => $this->input->post('DateTime'),
			'CustomerId' => $this->input->post('CustomerId'),
			'ProductId' => $this->input->post('ProductId'),
			'Direction' => $this->input->post('Direction'),
			'Amount' => $this->input->post('Amount'),
			'Description' => $this->input->post('Description')
		);
		$this->transaction->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->transaction->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

}
