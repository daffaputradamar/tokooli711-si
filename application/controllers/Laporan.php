<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
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
        $this->load->model('Penjualan_model');
        $this->load->model('Pembelian_model');
        $this->load->model('Karyawan_model');
        $this->load->model('Barang_model');
        $this->load->model('Penjualan_detail_model');
        $this->load->model('Pembelian_detail_model');
        $this->load->model('CodeGenerator');
    }
    public function beli()
    {
        $this->load->view('nav');
        $data['barang'] = $this->Barang_model->selectByAll();
        $this->load->view('laporan/beliform', $data);

        $this->load->view('foot');

        if ($_POST) {
            if ($this->input->post('mulai') <> "") {
                redirect('laporan/pbeli/'.date('d-m-Y', strtotime($this->input->post('mulai')))."/".$this->input->post('rad'), 'refresh');
            }
            if ($this->input->post('kode_barang') <> "" && $this->input->post('tgl_awal') <> "" && $this->input->post('tgl_akhir') <> "") {
                redirect('laporan/pbeli_2/'.date('d-m-Y', strtotime($this->input->post('tgl_awal')))."/".date('d-m-Y', strtotime($this->input->post('tgl_akhir')))."/".$this->input->post('kode_barang'), 'refresh');
            }
        }
    }
    public function pbeli($mulai)
    {
        $this->load->view('nav');

        $data['listbeli'] = $this->Pembelian_model->laporan($mulai, $this->uri->segment(4));
        $data['listdetail'] = $this->Pembelian_detail_model->selectByAll();
        $data['hbeli'] = $this->Pembelian_model->beliByTgl($this->uri->segment(3), $this->uri->segment(4));
        $this->load->view('laporan/pbeli', $data);
        $this->load->view('foot');
    }
    public function pbeliprint($mulai)
    {
        $data['listbeli'] = $this->Pembelian_model->laporan($mulai, $this->uri->segment(4));
        $data['listdetail'] = $this->Pembelian_detail_model->selectByAll();
        $data['hbeli'] = $this->Pembelian_model->beliByTgl($this->uri->segment(3), $this->uri->segment(4));
        $this->load->view('laporan/pbeliprint', $data);
    }
    public function pjual($mulai)
    {
        $data['listjual'] = $this->Penjualan_model->laporan($mulai, $this->uri->segment(4));

        $kode_jual = array();
        $data['listdetail'] = array();
        foreach ($data['listjual'] as $val) {
            array_push($kode_jual, $val->kode_jual);
        }
        if(!empty($kode_jual)){
            $data['listdetail'] = $this->Penjualan_detail_model->selectByKodeJual($kode_jual);
        }
        $data['byTgl'] = $this->Penjualan_model->omsetByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hbeli'] = $this->Penjualan_model->beliByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hjual'] = $this->Penjualan_model->jualByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['htotal'] = $this->Penjualan_model->totalByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hgaji'] = $this->Penjualan_model->gajiAllby($this->uri->segment(3), $this->uri->segment(4));
        $this->load->view('nav');
        $this->load->view('laporan/pjual', $data);
        $this->load->view('foot');
    }
    public function pjualprint($mulai)
    {
        $data['listjual'] = $this->Penjualan_model->laporan($mulai, $this->uri->segment(4));

        $kode_jual = array();
        $data['listdetail'] = array();
        foreach ($data['listjual'] as $val) {
            array_push($kode_jual, $val->kode_jual);
        }
        if(!empty($kode_jual)){
            $data['listdetail'] = $this->Penjualan_detail_model->selectByKodeJual($kode_jual);
        }
        $data['byTgl'] = $this->Penjualan_model->omsetByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hbeli'] = $this->Penjualan_model->beliByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hjual'] = $this->Penjualan_model->jualByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['htotal'] = $this->Penjualan_model->totalByTgl($this->uri->segment(3), $this->uri->segment(4));
        $data['hgaji'] = $this->Penjualan_model->gajiAllby($this->uri->segment(3), $this->uri->segment(4));
        $this->load->view('laporan/pjualprint', $data);
    }
    public function jual()
    {
        if ($_POST) {
            // print_r($this->input->post('tanggal_per_kasir'));
            // return;
            if ($this->input->post('mulai') <> "") {
                redirect('laporan/pjual/'.date('d-m-Y', strtotime($this->input->post('mulai')))."/".$this->input->post('rad'), 'refresh');
            }
            if ($this->input->post('kode_barang') <> "" && $this->input->post('tgl_awal') <> "" && $this->input->post('tgl_akhir') <> "") {
                redirect('laporan/pjual_2/'.date('d-m-Y', strtotime($this->input->post('tgl_awal')))."/".date('d-m-Y', strtotime($this->input->post('tgl_akhir')))."/".$this->input->post('kode_barang'), 'refresh');
            }
        }

        $this->load->view('nav');
        $data['listkaryawan'] = $this->Karyawan_model->selectByAll();
        $data['listgaji'] = $this->Penjualan_model->gajiall(date('d-m-Y'), 1);
        $data['semua'] = $this->Penjualan_model->omsetAll();
        $data['byTgl'] = $this->Penjualan_model->omsetByTgl(date('d-m-Y'), 1);
        $data['totaltr'] = $this->Penjualan_detail_model->total_rows();
        $data['barang'] = $this->Barang_model->selectByAll();
        $data['totalkr'] = $this->Karyawan_model->total_rows();
        $data['penjualan_per_kasir'] = $this->Penjualan_model->get_all_sum_penjualan_by_kasir(date('d-m-Y'));
        if ($this->uri->segment(3) == 1) {
            $data['pesan'] = "Isi data dengan lengkap";
        } else {
            $data['pesan'] = "";
        }

        $this->load->view('laporan/jualform', $data);

        $this->load->view('foot');
    }

    public function pjual_2()
    {
        $data['listjual'] = $this->Penjualan_model->laporan2($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
        // var_dump($data);
        $this->load->view('laporan/pjualprint_2', $data);
    }

    public function pbeli_2()
    {
        $data['listjual'] = $this->Pembelian_model->laporan2($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
        // var_dump($data);
        $this->load->view('laporan/pbeliprint_2', $data);
    }


    public function laporan_barang()
    {
        if (isset($_GET['cari'])) {
            $cari = $_GET['cari'];
        } else {
            $cari = null;
        }
        $this->load->view('nav');
        $data['listbarang'] = $this->Barang_model->getAll_by($cari);
        $this->load->view('laporan/barang', $data);
        $this->load->view('foot');
    }
    public function laporan_barangprint()
    {
        if (isset($_GET['cari'])) {
            $cari = $_GET['cari'];
        } else {
            $cari = null;
        }
        $data['listbarang'] = $this->Barang_model->getAll_by($cari);
        $this->load->view('laporan/barangprint', $data);
    }
}

/* End of file Laporan.php */
/* Location: ./application/controllers/Laporan.php */
