<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sync Controller
 * 
 * Handles backup synchronization between two servers
 * Allows comparing transactions and syncing missing ones
 */
class Sync extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Penjualan_model');
        $this->load->model('Pembelian_model');
        $this->load->model('Penjualan_detail_model');
        $this->load->model('Pembelian_detail_model');
        $this->load->model('Barang_model');
        $this->load->model('CodeGenerator');
        session_start();
    }

    /**
     * Main sync dashboard
     */
    public function index()
    {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['kode']) || $_SESSION['level'] != 'admin') {
            redirect(site_url('login'));
            return;
        }

        $data = array(
            'sync_enabled' => $this->config->item('sync_enabled'),
            'sync_target_url' => $this->config->item('sync_target_url'),
            'sync_receive_enabled' => $this->config->item('sync_receive_enabled'),
        );

        $this->load->view('nav');
        $this->load->view('sync/sync_dashboard', $data);
        $this->load->view('foot');
    }

    /**
     * API endpoint to get sync config as JSON
     */
    public function get_config()
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'sync_enabled' => $this->config->item('sync_enabled'),
                'sync_target_url' => $this->config->item('sync_target_url'),
                'sync_receive_enabled' => $this->config->item('sync_receive_enabled')
            )));
    }

    /**
     * API endpoint to compare penjualan transactions with remote server
     */
    public function compare_penjualan()
    {
        $date = $this->input->get('date');
        $limit = $this->input->get('limit') ? intval($this->input->get('limit')) : 5;
        
        if (empty($date)) {
            $date = date('d-m-Y');
        }
        
        $date_formatted = date('d-m-Y', strtotime($date));
        
        // Get local transactions
        $this->db->where('tanggal_jual', $date_formatted);
        $this->db->order_by('kode_jual', 'DESC');
        $this->db->limit($limit);
        $local_data = $this->db->get('penjualan')->result();
        
        $local_codes = array();
        foreach ($local_data as $item) {
            $local_codes[] = $item->kode_jual;
        }
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'date' => $date_formatted,
                'local_codes' => $local_codes,
                'local_count' => count($local_codes)
            )));
    }

    /**
     * API endpoint to compare pembelian transactions with remote server
     */
    public function compare_pembelian()
    {
        $date = $this->input->get('date');
        $limit = $this->input->get('limit') ? intval($this->input->get('limit')) : 5;
        
        if (empty($date)) {
            $date = date('d-m-Y');
        }
        
        $date_formatted = date('d-m-Y', strtotime($date));
        
        // Get local transactions
        $this->db->where('tanggal_beli', $date_formatted);
        $this->db->order_by('kode_beli', 'DESC');
        $this->db->limit($limit);
        $local_data = $this->db->get('pembelian')->result();
        
        $local_codes = array();
        foreach ($local_data as $item) {
            $local_codes[] = $item->kode_beli;
        }
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'date' => $date_formatted,
                'local_codes' => $local_codes,
                'local_count' => count($local_codes)
            )));
    }

    /**
     * API endpoint to get penjualan data for syncing (full data with details)
     */
    public function get_penjualan_for_sync()
    {
        $kode_jual = $this->input->get('kode_jual');
        
        if (empty($kode_jual)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'kode_jual is required'
                )));
        }
        
        $penjualan = $this->Penjualan_model->selectById($kode_jual);
        
        if (!$penjualan) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'Transaction not found'
                )));
        }
        
        $details = $this->Penjualan_detail_model->selectById($kode_jual);
        $detail_array = array();
        
        foreach ($details as $detail) {
            $detail_array[] = array(
                'kode_barang' => $detail->kode_barang,
                'nama_barang' => $detail->nama_barang,
                'harga_jual' => $detail->harga_jual,
                'harga_beli' => $detail->harga_beli,
                'jumlah' => $detail->jumlah,
                'subtotal' => $detail->subtotal,
                'is_using_rupiah' => false
            );
        }
        
        $result = array(
            'kode_jual' => $penjualan->kode_jual,
            'tanggal_jual' => $penjualan->tanggal_jual,
            'waktu_jual' => $penjualan->waktu_jual,
            'kode_admin' => $penjualan->kode_admin,
            'kode_karyawan' => $penjualan->kode_karyawan,
            'keterangan' => $penjualan->keterangan,
            'nomor_polisi' => $penjualan->nomor_polisi,
            'km_kendaraan' => $penjualan->km_kendaraan,
            'ongkos_karyawan' => $penjualan->ongkos_karyawan,
            'total' => $penjualan->total,
            'bayar' => $penjualan->bayar,
            'pelanggan' => $penjualan->pelanggan,
            'details' => $detail_array
        );
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'data' => $result
            )));
    }

    /**
     * API endpoint to get pembelian data for syncing (full data with details)
     */
    public function get_pembelian_for_sync()
    {
        $kode_beli = $this->input->get('kode_beli');
        
        if (empty($kode_beli)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'kode_beli is required'
                )));
        }
        
        $pembelian = $this->Pembelian_model->selectById($kode_beli);
        
        if (!$pembelian) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'Transaction not found'
                )));
        }
        
        $details = $this->Pembelian_detail_model->selectById($kode_beli);
        $detail_array = array();
        
        foreach ($details as $detail) {
            $detail_array[] = array(
                'kode_barang' => $detail->kode_barang,
                'harga_beli' => $detail->harga_beli,
                'jumlah' => $detail->jumlah,
                'subtotal' => $detail->subtotal
            );
        }
        
        $result = array(
            'kode_beli' => $pembelian->kode_beli,
            'tanggal_beli' => $pembelian->tanggal_beli,
            'waktu_beli' => $pembelian->waktu_beli,
            'kode_admin' => $pembelian->kode_admin,
            'no_faktur' => $pembelian->no_faktur,
            'total' => $pembelian->total,
            'kode_suplier' => $pembelian->kode_suplier,
            'details' => $detail_array
        );
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'data' => $result
            )));
    }

    /**
     * Bulk check which kode_jual exist locally
     */
    public function bulk_check_penjualan()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $codes = isset($input['codes']) ? $input['codes'] : array();
        
        if (empty($codes)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'codes array is required'
                )));
        }
        
        $existing = array();
        $missing = array();
        
        foreach ($codes as $code) {
            $exists = $this->Penjualan_model->selectById($code);
            if ($exists) {
                $existing[] = $code;
            } else {
                $missing[] = $code;
            }
        }
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'existing' => $existing,
                'missing' => $missing
            )));
    }

    /**
     * Bulk check which kode_beli exist locally
     */
    public function bulk_check_pembelian()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $codes = isset($input['codes']) ? $input['codes'] : array();
        
        if (empty($codes)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_header('Access-Control-Allow-Origin: *')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'codes array is required'
                )));
        }
        
        $existing = array();
        $missing = array();
        
        foreach ($codes as $code) {
            $exists = $this->Pembelian_model->selectById($code);
            if ($exists) {
                $existing[] = $code;
            } else {
                $missing[] = $code;
            }
        }
        
        return $this->output
            ->set_content_type('application/json')
            ->set_header('Access-Control-Allow-Origin: *')
            ->set_output(json_encode(array(
                'status' => true,
                'existing' => $existing,
                'missing' => $missing
            )));
    }
}

/* End of file Sync.php */
/* Location: ./application/controllers/Sync.php */
