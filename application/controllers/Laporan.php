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
        $data['tahun_beli_list'] = $this->Pembelian_model->get_tahun_list();
        $this->load->view('laporan/beliform', $data);

        $this->load->view('foot');

        if ($_POST) {
            if ($this->input->post('mulai') <> "") {
                redirect('laporan/pbeli/' . date('d-m-Y', strtotime($this->input->post('mulai'))) . "/" . $this->input->post('rad'), 'refresh');
            }
            if ($this->input->post('kode_barang') <> "" && $this->input->post('tgl_awal') <> "" && $this->input->post('tgl_akhir') <> "") {
                redirect('laporan/pbeli_2/' . date('d-m-Y', strtotime($this->input->post('tgl_awal'))) . "/" . date('d-m-Y', strtotime($this->input->post('tgl_akhir'))) . "/" . $this->input->post('kode_barang'), 'refresh');
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
        if (!empty($kode_jual)) {
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
        if (!empty($kode_jual)) {
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
                redirect('laporan/pjual/' . date('d-m-Y', strtotime($this->input->post('mulai'))) . "/" . $this->input->post('rad'), 'refresh');
            }
            if ($this->input->post('kode_barang') <> "" && $this->input->post('tgl_awal') <> "" && $this->input->post('tgl_akhir') <> "") {
                redirect('laporan/pjual_2/' . date('d-m-Y', strtotime($this->input->post('tgl_awal'))) . "/" . date('d-m-Y', strtotime($this->input->post('tgl_akhir'))) . "/" . $this->input->post('kode_barang'), 'refresh');
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
        $data['tahun_jual_list'] = $this->Penjualan_model->get_tahun_list();
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
        // return;
        $this->load->view('laporan/pjualprint_2', $data);
    }

    public function pbeli_2()
    {
        $data['listbeli'] = $this->Pembelian_model->laporan2($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
        // var_dump($data);
        // return;
        $this->load->view('laporan/pbeliprint_2', $data);
    }

    public function export_jual_tahunan()
    {
        $tahun = $this->input->post('tahun');
        if (empty($tahun)) {
            redirect('laporan/jual');
            return;
        }

        $query = $this->Penjualan_model->laporan_tahunan($tahun);
        $data = $query->result();

        if (empty($data)) {
            $this->session->set_flashdata('message', 'Tidak ada data untuk tahun ' . $tahun);
            redirect('laporan/jual');
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan_Penjualan_' . $tahun . '.csv"');
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, array('Periode', 'Total Penjualan'));
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, array($row->periode, $row->total_penjualan));
        }
        
        fclose($output);
        exit;
    }

    public function export_beli_tahunan()
    {
        $tahun = $this->input->post('tahun');
        $tampilkan_supplier = $this->input->post('tampilkan_supplier');

        if (empty($tahun)) {
            redirect('laporan/beli');
            return;
        }

        // Choose query based on supplier toggle
        if ($tampilkan_supplier) {
            $query = $this->Pembelian_model->laporan_tahunan($tahun);
        } else {
            $query = $this->Pembelian_model->laporan_tahunan_no_supplier($tahun);
        }

        $data = $query->result();

        if (empty($data)) {
            $this->session->set_flashdata('message', 'Tidak ada data untuk tahun ' . $tahun);
            redirect('laporan/beli');
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan_Pembelian_' . $tahun . '.csv"');
        $output = fopen('php://output', 'w');

        // Write header based on supplier toggle
        if ($tampilkan_supplier) {
            fputcsv($output, array('Periode', 'Nama Supplier', 'Total Pembelian'));
            // Data with supplier
            foreach ($data as $row) {
                fputcsv($output, array($row->periode, $row->nama_suplier, $row->total_pembelian));
            }
        } else {
            fputcsv($output, array('Periode', 'Total Pembelian'));
            // Data without supplier
            foreach ($data as $row) {
                fputcsv($output, array($row->periode, $row->total_pembelian));
            }
        }

        fclose($output);
        exit;
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

    public function movement()
    {
        $this->load->view('nav');
        $data['barang'] = $this->Barang_model->selectByAll();
        $this->load->view('laporan/movementform', $data);

        $this->load->view('foot');

        if ($_POST) {
            if ($this->input->post('kode_barang') <> ""
                && $this->input->post('tgl_awal') <> "" && $this->input->post('wkt_awal') <> ""
                && $this->input->post('tgl_akhir') <> "" && $this->input->post('wkt_akhir') <> "") {

                $kode_barang = $this->input->post('kode_barang');
                $start_time = date('d-m-Y', strtotime($this->input->post('tgl_awal'))) . "/" . $this->input->post('wkt_awal');
                $end_time = date('d-m-Y', strtotime($this->input->post('tgl_akhir'))) . "/" . $this->input->post('wkt_akhir');

                redirect('laporan/movementprint/' . $kode_barang . "/" . $start_time . "/" . $end_time, 'refresh');
            }
        }
    }

    public function movementprint()
    {
        $kode_barang = $this->uri->segment(3);
        $tgl_awal = $this->uri->segment(4); // Date start
        $waktu_awal = $this->uri->segment(5); // Time start
        $tgl_akhir = $this->uri->segment(6); // Date end
        $waktu_akhir = $this->uri->segment(7); // Time end

        // Combine date and time into a DateTime format
        $datetime_awal = date('Y-m-d H:i:s', strtotime("$tgl_awal $waktu_awal"));
        $datetime_akhir = date('Y-m-d H:i:s', strtotime("$tgl_akhir $waktu_akhir"));

        // Update model call with the datetime format
        $data['barang'] = $this->Barang_model->selectById($kode_barang);
        $data_trans = $this->Barang_model->get_movement_by_barang($kode_barang, $datetime_awal, $datetime_akhir);

        // New array to store the modified transactions
        $modified_transactions = [];
        $cumulative_sum = 0;

        // Iterate over the transactions
        foreach ($data_trans as $transaction) {
            if ($transaction->jenis_trans === 'Penjualan') {
                // Add the penjualan amount to the cumulative sum
                $cumulative_sum += $transaction->jumlah;

                // Add the penjualan transaction to the modified array
                $modified_transactions[] = $transaction;

            } elseif ($transaction->jenis_trans === 'Pembelian') {
                // Insert a new row with the cumulative sum before the Pembelian
                if ($cumulative_sum > 0) {
                    $modified_transactions[] = (object) [
                        "kode_trans" => null,
                        "jenis_trans" => "Jumlah Kumulatif Penjualan",
                        "tanggal_trans" => '',
                        "jumlah" => $cumulative_sum
                    ];
                }

                // Add the pembelian transaction to the modified array
                $modified_transactions[] = $transaction;

                // Reset the cumulative sum after each pembelian
                $cumulative_sum = 0;
            }
        }

        // After the loop, check if there are any remaining Penjualan
        if ($cumulative_sum > 0) {
            $modified_transactions[] = (object) [
                "kode_trans" => null,
                "jenis_trans" => "Jumlah Kumulatif Penjualan",
                "tanggal_trans" => '',
                "jumlah" => $cumulative_sum
            ];
        }

        $data['listbarang'] = $modified_transactions;

        $this->load->view('laporan/movementprint', $data);
    }

    public function history_stok()
    {
        $this->load->library('pagination');

        $start = intval($this->input->get('start'));
        $config['per_page'] = 10;
        $config['page_query_string'] = true;

        $this->load->view('nav');

        $data_trans = array(
            'list' => [],
            'total_count' => 0
        );

        if ($_POST) {
            $kode_barang = $this->input->post('kode_barang');
            $datetime_awal = date('Y-m-d H:i:s', strtotime("{$this->input->post('tgl_awal')} {$this->input->post('wkt_awal')}"));
            $datetime_akhir = date('Y-m-d H:i:s', strtotime("{$this->input->post('tgl_akhir')} {$this->input->post('wkt_akhir')}"));

            $data_trans_db = $this->Barang_model->get_list_history_by_barang($kode_barang, $datetime_awal, $datetime_akhir, $config['per_page'], $start);

            $data_trans['list'] = $data_trans_db['list'];
            $data_trans['total_count'] = $data_trans_db['total_count'];
        
            $data['kode_barang'] = $kode_barang;
            $data['tgl_awal'] = $this->input->post('tgl_awal');
            $data['wkt_awal'] = $this->input->post('wkt_awal');
            $data['tgl_akhir'] = $this->input->post('tgl_akhir');
            $data['wkt_akhir'] = $this->input->post('wkt_akhir');
        }

        $config['total_rows'] = $data_trans['total_count'];

        $data['barang'] = $this->Barang_model->selectByAll();
        $data['history'] = $data_trans['list'];
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start'] = $start;

        $this->load->view('laporan/historystokbarang', $data);
        $this->load->view('foot');

    }

    public function update_history_stok($kode_barang)
    {
        $this->load->model('Barang_model');

        if ($_POST) {
            $history_id = $this->input->post('id');
            $history_stok = $this->input->post('stok');

            $this->Barang_model->update_history_by_id($history_id, $history_stok);
            
            redirect('laporan/history_stok/' . $history_id . '/update');
        }
        // Load the model if needed
        $this->load->view('nav');

        // Fetch the stock history based on the kode_barang
        $data['history'] = $this->Barang_model->get_history_by_id($kode_barang);

        // Check if the history exists for this kode_barang
        if (empty($data['history'])) {
            show_404(); // Or handle the case where there's no history
        }

        // Pass the data to the view for rendering the edit form
        $this->load->view('laporan/historystokbarangedit', $data);
        $this->load->view('foot');

    }
}

/* End of file Laporan.php */
/* Location: ./application/controllers/Laporan.php */
