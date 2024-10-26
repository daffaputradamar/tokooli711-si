<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if($this->session->userdata('username')==""){
        // 	redirect('login');
        // }
        $this->load->model('Karyawan_model');
        $this->load->model('Percobaan_karyawan_model');
        $this->load->model('Admin_model');
        $this->load->model('CodeGenerator');
        $this->load->model('Penjualan_model');
        $this->load->model('Penjualan_detail_model');
        $this->load->library('form_validation');
        $this->load->model('Barang_model');
        session_start();
        if (!isset($_SESSION['level'])) {
            redirect('login', 'refresh');
        }
    }
    public function _rule()
    {
        $this->form_validation->set_rules('kode_jual', 'kode jual', 'trim|required');
        $this->form_validation->set_rules('tanggal_jual', 'tanggal jual', 'trim|required');
        $this->form_validation->set_rules('kode_admin', 'kode admin', 'trim|required');
        //$this->form_validation->set_rules('kode_karyawan', 'kode karyawan', 'trim|required');
        //$this->form_validation->set_rules('keterangan', 'keterangan', 'trim|required');
        $this->form_validation->set_rules('ongkos_karyawan', 'ongkos karyawan', 'trim|required');
        $this->form_validation->set_rules('total', 'total', 'trim|required');
        $this->form_validation->set_rules('bayar', 'bayar', 'trim|required');
    }
    public function index()
    {
        $this->load->view('nav');

        $detail_penjualan = isset($_SESSION[$_SESSION['kode'].'detailbarang']) ? $_SESSION[$_SESSION['kode'].'detailbarang'] : array();

        $total_all = 0;
        // $total_all +=  isset($_SESSION[ $_SESSION['kode'] . 'ongkos_karyawan']) ? $_SESSION[ $_SESSION['kode'] . 'ongkos_karyawan'] : 0;

        foreach ($detail_penjualan as $penjualandetail) {
            $total_all += $penjualandetail['is_using_rupiah'] ? $penjualandetail['subtotal'] : $penjualandetail['harga_jual'] * $penjualandetail['jumlah'];
        }

        // return print_r($total_all);

        $data = array(

        'kode_jual' => set_value('kode_jual', '-'),
        'tanggal_jual' => set_value('tanggal_jual', date('d-m-Y')),
        'kode_admin' => set_value('kode_admin', $_SESSION['kode']),
        'kode_karyawan' => set_value('kode_karyawan'),
        'keterangan' => set_value('keterangan'),
        'nomor_polisi' => set_value('nomor_polisi'),
        'km_kendaraan' => set_value('km_kendaraan'),
        'ongkos_karyawan' => set_value('ongkos_karyawan', 0),
        'total' => set_value('total', $total_all),
        'bayar' => set_value('bayar', $total_all),
        'pelanggan' => set_value('pelanggan'),
    );
        //$data['listkaryawan']=$this->Karyawan_model->selectByAll();
        $data['listbarang'] = $this->Barang_model->selectByAll();
        $data['listdetail'] = $detail_penjualan;

        $data['listkaryawan'] = $this->Karyawan_model->selectByAll();

        if ($_SESSION['level'] != "admin") {
            $data['karyawan'] = $this->Karyawan_model->selectById($_SESSION['kode']);
            // return print_r($data['percobaan']);
        } else {
            $data['admin'] = $this->Admin_model->selectById($_SESSION['kode']);
        }

        $data['listgaji'] = $this->Penjualan_model->gajiall(date('d-m-Y'));
        $data['semua'] = $this->Penjualan_model->omsetAll();
        $data['byTgl'] = $this->Penjualan_model->beaLainByTgl(date('d-m-Y'), 1);
        $data['byTgl2'] = $this->Penjualan_model->omsetByTgl2(date('d-m-Y'));
        $data['totaltr'] = $this->Penjualan_detail_model->jumlahAll();

        $data['totalkr'] = $this->Karyawan_model->total_rows();

        foreach ($data['listkaryawan'] as $karyawan) {
            $data['kasir'][$karyawan->kode_karyawan] = $this->Penjualan_model->kasir($karyawan->kode_karyawan);
        }

        if ($this->uri->segment(3) == 1) {
            $data['pesan'] = "Isi data dengan lengkap";
        } else {
            $data['pesan'] = "";
        }

        $this->load->view('home', $data);
        $this->load->view('foot');
    }
    public function transaksi()
    {
        if ($this->input->post('kode_karyawan') <> "" and $this->input->post('keterangan') <> "" and $this->input->post('ongkos') <> "") {
            $data = array(
        'kode_jual' => $this->CodeGenerator->buatkode('penjualan', 'kode_jual', 10, 'TRJ'),
        'tanggal_jual' => date('d-m-Y'),
        'kode_admin' => $_SESSION['kode'],
        'kode_karyawan' => $this->input->post('kode_karyawan'),
        'keterangan' => $this->input->post('keterangan'),
        'nomor_polisi' => $this->input->post('nomor_polisi'),
        'km_kendaraan' => $this->input->post('km_kendaraan'),
        'ongkos_karyawan' => $this->input->post('ongkos'),
        'total' => $this->input->post('ongkos'),
        'bayar' => $this->input->post('bayar'),
        'pelanggan' => $this->input->post('pelanggan'),
        );
            $this->Penjualan_model->insert($data);
            echo 'Berhasil Disimpan';
            redirect(site_url('home'));
        } else {
            $data['pesan'] = "Isi dengan lengkap";
            redirect(site_url('home/index/1'));
        }
    }
    public function datainsert()
    {
        //$this->load->view('nav');

        $this->load->view('penjualan/penjualan_form');
        //$this->load->view('foot');
    }

    public function insert()
    {
        $total = 0;

        if ($this->input->post('submitlist') <> "") {
            if ($this->input->post('jumlah') <> "") {
                $cek = $this->Penjualan_detail_model->jumlahbyid($this->input->post('kode_jual'), $this->input->post('kode_barang'));
                //var_dump($cek);
                if ($cek == 0) {
                    $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
                    //var_dump($barang);
                    if ($barang->stok >= $this->input->post('jumlah')) {
                        $data = array(
                        'kode_jual' => $this->input->post('kode_jual'),
                        'kode_barang' => $this->input->post('kode_barang'),
                        'harga_jual' => $barang->harga_jual,
                        'harga_beli' => $barang->harga_beli,
                        'jumlah' => $this->input->post('jumlah'),
                        'subtotal' => (int)$barang->harga_jual * (int)$this->input->post('jumlah'),
                        );

                        $this->Penjualan_detail_model->insert($data);
                        redirect(site_url('penjualan/insert'), 'refresh');
                    } else {
                        echo "<script>alert('Stok kurang')</script>";
                        redirect(site_url('penjualan/insert'), 'refresh');
                    }
                } else {
                    $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
                    //var_dump($barang);
                    if ($barang->stok > $this->input->post('jumlah')) {
                        $data = array(
                        'kode_jual' => $this->input->post('kode_jual'),
                        'kode_barang' => $this->input->post('kode_barang'),
                        'harga_jual' => $barang->harga_jual,
                        'harga_beli' => $barang->harga_beli,
                        'jumlah' => $this->input->post('jumlah'),
                        'subtotal' => (int)$barang->harga_jual * (int)$this->input->post('jumlah'),
                        );

                        $this->Penjualan_detail_model->update($data['kode_jual'], $data['kode_barang'], $data);
                        redirect(site_url('penjualan/insert'), 'refresh');
                    } else {
                        echo "<script>alert('Stok kurang')</script>";
                        redirect(site_url('penjualan/insert'), 'refresh');
                    }
                }
            }
        } else {
            $this->_rule();

            if ($this->form_validation->run() == false) {
                $this->datainsert();
            } else {
                $data = array(
        'kode_jual' => $this->input->post('kode_jual'),
        'tanggal_jual' => $this->input->post('tanggal_jual'),
        'kode_admin' => $this->input->post('kode_admin'),
        'kode_karyawan' => $this->input->post('kode_karyawan'),
        'keterangan' => $this->input->post('keterangan'),
        'nomor_polisi' => $this->input->post('nomor_polisi'),
        'km_kendaraan' => $this->input->post('km_kendaraan'),
        'ongkos_karyawan' => $this->input->post('ongkos_karyawan'),
        'total' => $this->Penjualan_detail_model->totalall($this->input->post('kode_jual')) + (int)$this->input->post('ongkos_karyawan'),
        'bayar' => $this->input->post('bayar'),
        'pelanggan' => $this->input->post('pelanggan'),
        );
                $this->Penjualan_detail_model->updatestok($this->input->post('kode_jual'));
                $this->Penjualan_model->insert($data);
                redirect(site_url('penjualan'));
            }
        }
    }

    public function get_percobaan_stok()
    {
        $data = $this->Percobaan_karyawan_model->get_percobaan_by_karyawan($_SESSION['kode']);
        header('Content-Type: application/json');
        echo(json_encode($data));
        return;
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */
