<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Penjualan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Penjualan_model');
        $this->load->model('Percobaan_karyawan_model');
        $this->load->library('form_validation');
        $this->load->model('CodeGenerator');
        $this->load->model('Karyawan_model');
        $this->load->model('Admin_model');
        $this->load->model('Barang_model');
        $this->load->model('Penjualan_detail_model');
        $this->load->model('Promo_model');
        session_start();
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
        $this->load->library('pagination');

        $config['per_page'] = 10;
        $config['page_query_string'] = true;

        $cari = urldecode($this->input->get('cari'));
        $filterkaryawan = empty(urldecode($this->input->get('filterkaryawan'))) ? "ALL" : urldecode($this->input->get('filterkaryawan'));

        $filter_barang = urldecode($this->input->get('kode_barang'));
        $filter_tgl_awal = urldecode($this->input->get('tgl_awal'));
        $filter_tgl_akhir = urldecode($this->input->get('tgl_akhir'));

        $start = intval($this->input->get('start'));

        $penjualan_all = $this->Penjualan_model->get_limit_data_filter($config['per_page'], $start, $filter_barang, $filter_tgl_awal, $filter_tgl_akhir, $filterkaryawan, $cari);

        $base_url_penjualan = base_url() . 'penjualan';
        if ($cari <> '') {
            $config['base_url'] = $base_url_penjualan . '?cari=' . urlencode($cari);
            $config['first_url'] = $base_url_penjualan . '?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = $base_url_penjualan . '?';
            $config['first_url'] = $base_url_penjualan . '?';
        }

        if ($filterkaryawan <> '') {
            $config['base_url'] .= '&filterkaryawan=' . urlencode($filterkaryawan);
            $config['first_url'] .= '&filterkaryawan=' . urlencode($filterkaryawan);
        }

        if (!empty($filter_barang)) { 
            $config['base_url'] .= '&kode_barang=' . urlencode($filter_barang) . '&tgl_awal=' . urlencode($filter_tgl_awal) . '&tgl_akhir=' . urlencode($filter_tgl_akhir);
            $config['first_url'] .= '&kode_barang=' . urlencode($filter_barang) . '&tgl_awal=' . urlencode($filter_tgl_awal) . '&tgl_akhir=' . urlencode($filter_tgl_akhir);
        }


        $penjualan = array_slice($penjualan_all, $start, $config['per_page']);
        $config['total_rows'] = count($penjualan_all);

        $this->pagination->initialize($config);

        $data = array(
            'penjualan_data' => $penjualan,
            'cari' => $cari,
            'kode_barang' => $filter_barang,
            'tgl_awal' => $filter_tgl_awal,
            'tgl_akhir' => $filter_tgl_akhir,
            'filterkaryawan' => $filterkaryawan,
            'karyawan' => $this->Karyawan_model->selectByAll(),
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
            'listbarang' => $this->Barang_model->selectByAll()
        );

        $this->load->view('nav');
        $this->load->view('penjualan/penjualan_list', $data);
        $this->load->view('foot');
    }

    public function view($id)
    {
        $this->load->view('nav');
        $row = $this->Penjualan_model->selectById($id);
        if ($row) {
            $data = array(
                'kode_jual' => $row->kode_jual,
                'tanggal_jual' => $row->tanggal_jual,
                'kode_admin' => $row->kode_admin,
                'kode_karyawan' => $row->kode_karyawan,
                'keterangan' => $row->keterangan,
                'nomor_polisi' => $row->nomor_polisi,
                'km_kendaraan' => $row->km_kendaraan,
                'ongkos_karyawan' => $row->ongkos_karyawan,
                'total' => $row->total,
                'bayar' => $row->bayar,
                'pelanggan' => $row->pelanggan,
            );
            $data['listkaryawan'] = $this->Karyawan_model->selectByAll();
            $data['listbarang'] = $this->Barang_model->selectByAll();
            $data['listdetail'] = $this->Penjualan_detail_model->selectById($data['kode_jual']);
            $this->load->view('penjualan/penjualan_read', $data);
        }
        $this->load->view('foot');
    }

    public function datainsert()
    {
        $this->load->view('nav');
        // echo $this->session->userdata('kode');
        $data = array(

            'kode_jual' => set_value('kode_jual', $this->CodeGenerator->buatkode('penjualan', 'kode_jual', 10, 'TRJ')),
            'tanggal_jual' => set_value('tanggal_jual', date('d-m-Y')),
            'kode_admin' => set_value('kode_admin', $_SESSION['kode']),
            'kode_karyawan' => set_value('kode_karyawan'),
            'keterangan' => set_value('keterangan'),
            'nomor_polisi' => set_value('nomor_polisi'),
            'km_kendaraan' => set_value('km_kendaraan'),
            'ongkos_karyawan' => set_value('ongkos_karyawan', 0),
            'total' => set_value('total', $this->Penjualan_detail_model->totalall($this->CodeGenerator->buatkode('penjualan', 'kode_jual', 10, 'TRJ'))),
            'bayar' => set_value('bayar'),
            'pelanggan' => set_value('pelanggan'),
        );
        $data['listkaryawan'] = $this->Karyawan_model->selectByAll();
        $data['listbarang'] = $this->Barang_model->selectByAll();
        $data['listdetail'] = $this->Penjualan_detail_model->selectById($data['kode_jual']);
        $this->load->view('penjualan/penjualan_form', $data);
        $this->load->view('foot');
    }

    public function insert()
    {
        $kode_user = $_SESSION['kode'];

        $keterangan = $this->input->post('keterangan');
        $ongkos_karyawan = !empty($this->input->post('ongkos_karyawan')) ? $this->input->post('ongkos_karyawan') : 0;
        $pelanggan = $this->input->post('pelanggan');
        $nomor_polisi = $this->input->post('nomor_polisi');
        $km_kendaraan = $this->input->post('km_kendaraan');
        $_SESSION[$kode_user . 'keterangan'] = $keterangan;
        $_SESSION[$kode_user . 'ongkos_karyawan'] = $ongkos_karyawan;
        $_SESSION[$kode_user . 'pelanggan'] = $pelanggan;
        $_SESSION[$kode_user . 'nomor_polisi'] = $nomor_polisi;
        $_SESSION[$kode_user . 'km_kendaraan'] = $km_kendaraan;

        if ($this->input->post('jumlah') == "") {
            echo "<script>alert('Jumlah harus diisi')</script>";

            if ($this->uri->segment(3) <> "" and $this->uri->segment(3) == 1) {
                redirect(site_url('home'), 'refresh');
            } else {
                redirect(site_url('penjualan/insert'), 'refresh');
            }
        }

        if ($this->input->post('submitlist') <> "") {
            $this->insert_detail_temp($this->input);
            return $this->return_redirect();
        }

        $this->_rule();

        if (!$this->form_validation->run()) {
            $this->return_redirect();
        }

        $kode_jual = $this->CodeGenerator->buatkode('penjualan', 'kode_jual', 10, 'TRJ');
        $detail_penjualan = $_SESSION[$kode_user . 'detailbarang'];
        $total_all = 0;
        $total_all += isset($_SESSION[$_SESSION['kode'] . 'ongkos_karyawan']) ? $_SESSION[$_SESSION['kode'] . 'ongkos_karyawan'] : 0;

        foreach ($detail_penjualan as $penjualandetail) {
            $total_all += $penjualandetail['is_using_rupiah'] ? $penjualandetail['subtotal'] : $penjualandetail['harga_jual'] * $penjualandetail['jumlah'];
            $data = array(
                'kode_jual' => $kode_jual,
                'kode_barang' => $penjualandetail['kode_barang'],
                'harga_jual' => $penjualandetail['harga_jual'],
                'harga_beli' => $penjualandetail['harga_beli'],
                'jumlah' => $penjualandetail['jumlah'],
                'subtotal' => $penjualandetail['is_using_rupiah'] ? $penjualandetail['subtotal'] : (float)$penjualandetail['harga_jual'] * (float)$penjualandetail['jumlah'],
            );
            $this->Penjualan_detail_model->insert($data);
        }

        $data = array(
            'kode_jual' => $kode_jual,
            'tanggal_jual' => $this->input->post('tanggal_jual'),
            'tanggal_jual_date' => date('Y-m-d', strtotime($this->input->post('tanggal_jual'))),
            'waktu_jual' => date("h:i:s a"),
            'kode_admin' => $this->input->post('kode_admin'),
            'kode_karyawan' => $this->input->post('kode_karyawan'),
            'keterangan' => $this->input->post('keterangan'),
            'nomor_polisi' => $this->input->post('nomor_polisi'),
            'km_kendaraan' => $this->input->post('km_kendaraan'),
            'ongkos_karyawan' => $this->input->post('ongkos_karyawan'),
            'total' => $total_all,
            'bayar' => $this->input->post('bayar'),
            'pelanggan' => $this->input->post('pelanggan'),
        );
        $this->Penjualan_detail_model->updatestok($kode_jual);
        $this->Penjualan_model->insert($data);

        unset($_SESSION[$kode_user . 'keterangan']);
        unset($_SESSION[$kode_user . 'ongkos_karyawan']);
        unset($_SESSION[$kode_user . 'pelanggan']);
        unset($_SESSION[$kode_user . 'nomor_polisi']);
        unset($_SESSION[$kode_user . 'km_kendaraan']);
        unset($_SESSION[$kode_user . 'detailbarang']);

        if ($this->uri->segment(3) <> "" and $this->uri->segment(3) == 1) {
            if ($this->input->post('cetak') == "on") {
                echo "<script type='text/javascript' language='javascript'>window.open('" . base_url() . 'penjualan/struk/' . $kode_jual . "')</script>";
            }

            redirect(site_url('home'), 'refresh');
        } else {
            redirect(site_url('penjualan'), 'refresh');
        }
    }

    public function dataupdate($id)
    {
        $this->load->view('nav');
        $row = $this->Penjualan_model->selectById($id);

        if ($row) {
            $data = array(

                'kode_jual' => set_value('kode_jual', $row->kode_jual),
                'tanggal_jual' => set_value('tanggal_jual', $row->tanggal_jual),
                'kode_admin' => set_value('kode_admin', $row->kode_admin),
                'kode_karyawan' => set_value('kode_karyawan', $row->kode_karyawan),
                'keterangan' => set_value('keterangan', $row->keterangan),
                'nomor_polisi' => set_value('nomor_polisi', $row->nomor_polisi),
                'km_kendaraan' => set_value('km_kendaraan', $row->km_kendaraan),
                'ongkos_karyawan' => set_value('ongkos_karyawan', $row->ongkos_karyawan),
                'total' => set_value('total', $row->total),
                'bayar' => set_value('bayar', $row->bayar),
                'pelanggan' => set_value('pelanggan', $row->pelanggan),
            );
            $this->load->view('penjualan/penjualan_form', $data);
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
                'kode_jual' => $this->input->post('kode_jual'),
                'tanggal_jual' => $this->input->post('tanggal_jual'),
                'kode_admin' => $this->input->post('kode_admin'),
                'kode_karyawan' => $this->input->post('kode_karyawan'),
                'keterangan' => $this->input->post('keterangan'),
                'nomor_polisi' => $this->input->post('nomor_polisi'),
                'km_kendaraan' => $this->input->post('km_kendaraan'),
                'ongkos_karyawan' => $this->input->post('ongkos_karyawan'),
                'total' => $this->Penjualan_detail_model->totalall($this->input->post('kode_jual')),
                'bayar' => $this->input->post('bayar'),
                'pelanggan' => $this->input->post('pelanggan'),
            );

            $this->Penjualan_model->update($this->uri->segment(3), $data);

            redirect(site_url('penjualan'));
        }
    }

    public function delete($id)
    {
        $row = $this->Penjualan_model->selectById($id);

        if ($row) {
            $this->Penjualan_model->delete($id);
            $this->Penjualan_detail_model->deletebykey($id);
            redirect(site_url('penjualan'));
        }
    }
    public function struk($id)
    {
        $row = $this->Penjualan_model->selectById($id);
        if ($row) {
            $data = array(
                'kode_jual' => $row->kode_jual,
                'tanggal_jual' => $row->tanggal_jual,
                'waktu_jual' => $row->waktu_jual,
                'kode_admin' => $row->kode_admin,
                'kode_karyawan' => $row->kode_karyawan,
                'keterangan' => $row->keterangan,
                'nomor_polisi' => $row->nomor_polisi,
                'km_kendaraan' => $row->km_kendaraan,
                'ongkos_karyawan' => $row->ongkos_karyawan,
                'total' => $row->total,
                'bayar' => $row->bayar,
                'pelanggan' => $row->pelanggan,
                'promo' => $this->Promo_model->selectTopOne($row->tanggal_jual)
            );
            $data['listkaryawan'] = $this->Karyawan_model->selectByAll();
            $data['listbarang'] = $this->Barang_model->selectByAll();
            $data['listdetail'] = $this->Penjualan_detail_model->selectById($data['kode_jual']);

            $row_kasir = $this->Karyawan_model->selectById($row->kode_admin);
            if ($row_kasir) {
                $data['kasir'] = $row_kasir;
            } else {
                $row_admin = $this->Admin_model->SelectById($row->kode_admin);
                $data['kasir'] = $row_admin;
            }

            $this->load->view('penjualan/struk', $data);
        }
    }


    private function insert_detail_temp($input)
    {
        $kode_user = $_SESSION['kode'];

        $barang = $this->Barang_model->selectById($input->post('kode_barang'));
        $this->checkStok($barang, $input);

        $data = array(
            'kode_barang' => $input->post('kode_barang'),
            'nama_barang' => $barang->nama_barang,
            'harga_jual' => $barang->harga_jual,
            'harga_beli' => $barang->harga_beli,
            'jumlah' => $input->post('jumlah'),
            'subtotal' => ($input->post('rupiah') <> -1) ? $input->post('rupiah') : (float)$barang->harga_jual * (float)$input->post('jumlah'),
            'is_using_rupiah' => ($input->post('rupiah') <> -1) ? true : false,
        );

        $_SESSION[$kode_user . 'detailbarang'][$input->post('kode_barang')] = $data;
    }

    private function checkStok($barang, $input)
    {
        $kode_user = $_SESSION['kode'];

        if ($barang->stok >= $input->post('jumlah')) {
            return true;
        } else {
            echo "<script>alert('Stok kurang')</script>";

            if ($_SESSION['level'] != "admin") {
                $this->Percobaan_karyawan_model->insert(array(
                    'id_barang' => $input->post('kode_barang'),
                    'id_karyawan' => $kode_user,
                    'isactive' => 1
                ));
            }

            $this->return_redirect();
        }
    }

    private function return_redirect()
    {
        if ($this->uri->segment(3) <> "" and $this->uri->segment(3) == 1) {
            return redirect(site_url('home'), 'refresh');
        } else {
            return redirect(site_url('penjualan/insert'), 'refresh');
        }
    }
}

/* End of file Penjualan.php */
/* Location: ./application/controllers/Penjualan.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
