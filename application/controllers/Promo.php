<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Promo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Promo_model');
        $this->load->library('form_validation');
        $this->load->model('CodeGenerator');
        session_start();
    }

    public function _rule()
    {
        $this->form_validation->set_rules('text', 'text', 'trim|required');
    }

    public function index()
    {
        $this->load->view('nav');
        $this->load->library('pagination');

        $cari = urldecode($this->input->get('cari'));
        $start = intval($this->input->get('start'));

        if ($cari <> '') {
            $config['base_url'] = base_url() . 'karyawan?cari=' . urlencode($cari);
            $config['first_url'] = base_url() . 'karyawan?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = base_url() . 'karyawan';
            $config['first_url'] = base_url() . 'karyawan';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = true;
        $promo = $this->Promo_model->selectByAll($cari);
        $config['total_rows'] = count($promo);


        $this->pagination->initialize($config);

        $data = array(
            'promo_data' => $promo,
            'cari' => $cari,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('promo/promo_list', $data);
        $this->load->view('foot');
    }

    public function datainsert()
    {
        $this->load->view('nav');
        $data = array(
            'text' => set_value('text', ''),
            'dtfrom' => set_value('dtfrom', date('Y-m-d')),
            'dtthru' => set_value('dtthru', date("Y-m-t")),
        );
        $this->load->view('promo/promo_form', $data);
        $this->load->view('foot');
    }


    public function insert()
    {
        $this->_rule();

        if ($this->form_validation->run() == false) {
            $this->datainsert();
        } else {
            $data = array(
            'text' => $this->input->post('text'),
            'dtfrom' => $this->input->post('dtfrom'),
            'dtthru' => $this->input->post('dtthru')
        );

            $this->Promo_model->insert($data);
            redirect(site_url('promo'));
        }
    }

    public function dataupdate($id)
    {
        $this->load->view('nav');
        $row = $this->Promo_model->selectById($id);

        if ($row) {
            $data = array(
                'text' => set_value('text', $row->text),
                'dtfrom' => set_value('dtfrom', date('Y-m-d', strtotime($row->dtfrom))),
                'dtthru' => set_value('dtthru', date("Y-m-d", strtotime($row->dtthru))),
            );

            $this->load->view('promo/promo_form', $data);
        }
        $this->load->view('foot');
    }

    public function update()
    {
        $this->_rule();

        if ($this->form_validation->run() == false) {
            $this->dataupdate($this->uri->segment(3));
        } else {
            $data = array(
                'text' => $this->input->post('text'),
                'dtfrom' => $this->input->post('dtfrom'),
                'dtthru' => $this->input->post('dtthru')
            );

            $this->Promo_model->update($this->uri->segment(3), $data);

            redirect(site_url('promo'));
        }
    }

    public function delete($id)
    {
        $row = $this->Promo_model->selectById($id);

        if ($row) {
            $this->Promo_model->delete($id);

            redirect(site_url('promo'));
        }
    }

    public function toggle($id)
    {
        $row = $this->Promo_model->selectById($id);

        if ($row) {
            $this->Promo_model->toggle($id, !$row->isactive);

            redirect(site_url('promo'));
        }
    }
}
