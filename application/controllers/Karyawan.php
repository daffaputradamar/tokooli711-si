<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Karyawan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (!isset($_SESSION['level'])) {
            redirect('login');
        } elseif ($_SESSION['level'] == 'karyawan') {
            redirect('home');
        }
        $this->load->model('Karyawan_model');
        $this->load->model('Percobaan_karyawan_model');
        $this->load->library('form_validation');
        $this->load->model('CodeGenerator');
    }
    public function _rule()
    {
        $this->form_validation->set_rules('kode_karyawan', 'kode karyawan', 'trim|required');
        $this->form_validation->set_rules('nama_karyawan', 'nama karyawan', 'trim|required');
        $this->form_validation->set_rules('alamat_karyawan', 'alamat karyawan', 'trim|required');
        $this->form_validation->set_rules('telp_karyawan', 'telp karyawan', 'trim|required');
        $this->form_validation->set_rules('username', 'username', 'trim|required');
        $this->form_validation->set_rules('password', 'password', 'trim|required');
        $this->form_validation->set_rules('start_working_hour', 'Jam Mulai Kerja', 'trim|required');
        $this->form_validation->set_rules('end_working_hour', 'Jam Selesai Kerja', 'trim|required');
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
        $config['total_rows'] = $this->Karyawan_model->total_rows($cari);
        $karyawan = $this->Karyawan_model->get_limit_data($config['per_page'], $start, $cari);


        $this->pagination->initialize($config);

        $data = array(
            'karyawan_data' => $karyawan,
            'cari' => $cari,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('karyawan/karyawan_list', $data);
        $this->load->view('foot');
    }

    public function view($id)
    {
        $this->load->view('nav');
        $this->load->library('pagination');

        $cari = urldecode($this->input->get('cari'));
        $start = intval($this->input->get('start'));

        if ($cari <> '') {
            $config['base_url'] = base_url() . 'karyawan/view/'.$id.'?cari=' . urlencode($cari);
            $config['first_url'] = base_url() . 'karyawan/view/'.$id.'?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = base_url() . 'karyawan/view/'.$id;
            $config['first_url'] = base_url() . 'karyawan/view/'.$id;
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = true;
        $percobaan_barang_list = $this->Percobaan_karyawan_model->get_percobaan_list_by_karyawan($id, $cari);
        $config['total_rows'] = count($percobaan_barang_list);

        $row = $this->Karyawan_model->selectById($id);
        if ($row) {
            $data = array(
                'kode_karyawan' => $row->kode_karyawan,
                'nama_karyawan' => $row->nama_karyawan,
                'alamat_karyawan' => $row->alamat_karyawan,
                'telp_karyawan' => $row->telp_karyawan,
                'username' => $row->username,
                'password' => $row->password,
                'cari' => $cari,
                'pagination' => $this->pagination->create_links(),
                'total_rows' => $config['total_rows'],
                'start' => $start,
            );

            $data['percobaan_barang'] = $this->Percobaan_karyawan_model->get_percobaan_by_karyawan($id);
            $data['percobaan_barang_list'] = $percobaan_barang_list;

            $this->load->view('karyawan/karyawan_read', $data);
        }
        $this->load->view('foot');
    }

    public function datainsert()
    {
        $this->load->view('nav');
        $data = array(
            'kode_karyawan' => set_value('kode_karyawan', $this->CodeGenerator->buatkode('karyawan', 'kode_karyawan', 10, 'KRY')),
            'nama_karyawan' => set_value('nama_karyawan'),
            'alamat_karyawan' => set_value('alamat_karyawan'),
            'telp_karyawan' => set_value('telp_karyawan'),
            'username' => set_value('username'),
            'password' => set_value('password'),
            'start_working_hour' => set_value('start_working_hour', '08:00:00'),
            'end_working_hour' => set_value('end_working_hour', '17:00:00'),
        );
        $this->load->view('karyawan/karyawan_form', $data);
        $this->load->view('foot');
    }

    public function insert()
    {
        $this->_rule();

        if ($this->form_validation->run() == false) {
            $this->datainsert();
        } else {
            $data = array(
                'kode_karyawan' => $this->input->post('kode_karyawan'),
                'nama_karyawan' => $this->input->post('nama_karyawan'),
                'alamat_karyawan' => $this->input->post('alamat_karyawan'),
                'telp_karyawan' => $this->input->post('telp_karyawan'),
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'start_working_hour' => $this->input->post('start_working_hour'),
                'end_working_hour' => $this->input->post('end_working_hour'),
            );

            $this->Karyawan_model->insert($data);
            redirect(site_url('karyawan'));
        }
    }

    public function dataupdate($id)
    {
        $this->load->view('nav');
        $row = $this->Karyawan_model->selectById($id);

        if ($row) {
            $data = array(
                'kode_karyawan' => set_value('kode_karyawan', $row->kode_karyawan),
                'nama_karyawan' => set_value('nama_karyawan', $row->nama_karyawan),
                'alamat_karyawan' => set_value('alamat_karyawan', $row->alamat_karyawan),
                'telp_karyawan' => set_value('telp_karyawan', $row->telp_karyawan),
                'username' => set_value('username', $row->username),
                'password' => set_value('password', $row->password),
                'start_working_hour' => set_value('start_working_hour', $row->start_working_hour),
                'end_working_hour' => set_value('end_working_hour', $row->end_working_hour),
            );
            $this->load->view('karyawan/karyawan_form', $data);
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
                'kode_karyawan' => $this->input->post('kode_karyawan'),
                'nama_karyawan' => $this->input->post('nama_karyawan'),
                'alamat_karyawan' => $this->input->post('alamat_karyawan'),
                'telp_karyawan' => $this->input->post('telp_karyawan'),
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'start_working_hour' => $this->input->post('start_working_hour'),
                'end_working_hour' => $this->input->post('end_working_hour'),
            );

            $this->Karyawan_model->update($this->uri->segment(3), $data);
            redirect(site_url('karyawan'));
        }
    }

    public function delete($id)
    {
        $row = $this->Karyawan_model->selectById($id);

        if ($row) {
            $this->Karyawan_model->delete($id);

            redirect(site_url('karyawan'));
        }
    }

    public function resetpercobaan($id)
    {
        $row = $this->Karyawan_model->selectById($id);

        if ($row) {
            $this->Percobaan_karyawan_model->resetpercobaan($id);

            redirect(site_url('karyawan'));

        }
    }

    public function detailpercobaan()
    {
        $karyawan = $_GET['karyawan'];
        $barang = $_GET['barang'];

        $data = $this->Percobaan_karyawan_model->get_detail_percobaan($karyawan, $barang);

        header('Content-Type: application/json');
        echo(json_encode($data));
        return;
    }
}

/* End of file Karyawan.php */
/* Location: ./application/controllers/Karyawan.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
