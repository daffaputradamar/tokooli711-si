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

        if ($this->input->post('submitlist') <> "") {
            $this->insert_detail_temp($this->input);
            return $this->return_redirect();
        }

        $this->_rule();

        if (!$this->form_validation->run()) {
            return $this->return_redirect();
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

        $payload = array(
            'kode_jual' => $data['kode_jual'],
            'tanggal_jual' => $data['tanggal_jual'],
            'kode_admin' => $data['kode_admin'],
            'kode_karyawan' => $data['kode_karyawan'],
            'keterangan' => $data['keterangan'],
            'nomor_polisi' => $data['nomor_polisi'],
            'km_kendaraan' => $data['km_kendaraan'],
            'ongkos_karyawan' => $data['ongkos_karyawan'],
            'bayar' => $data['bayar'],
            'pelanggan' => $data['pelanggan'],
            'details' => $detail_penjualan, // Include the detail data
        );


        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;

        // if ($this->uri->segment(3) <> "" and $this->uri->segment(3) == 1) {
        //     if ($this->input->post('cetak') == "on") {
        //         echo "<script type='text/javascript' language='javascript'>window.open('" . base_url() . 'penjualan/struk/' . $kode_jual . "')</script>";
        //     }

        //     redirect(site_url('home'), 'refresh');
        // } else {
        //     redirect(site_url('penjualan'), 'refresh');
        // }
    }

    // private function insert_temp($input)
    // {
    //     $kode_user = $_SESSION['kode'];

    //     $barang = $this->Barang_model->selectById($input->post('kode_barang'));
    //     $this->checkStok($barang, $input);

    //     $data = array(
    //         'kode_barang' => $input->post('kode_barang'),
    //         'nama_barang' => $barang->nama_barang,
    //         'harga_jual' => $barang->harga_jual,
    //         'harga_beli' => $barang->harga_beli,
    //         'jumlah' => $input->post('jumlah'),
    //         'subtotal' => ($input->post('rupiah') <> -1) ? $input->post('rupiah') : (float)$barang->harga_jual * (float)$input->post('jumlah'),
    //         'is_using_rupiah' => ($input->post('rupiah') <> -1) ? true : false,
    //     );

    //     $_SESSION[$kode_user . 'detailbarang'][$input->post('kode_barang')] = $data;
    // }

    private function send_data_to_api($data, $detail_penjualan)
    {
        // Prepare the data payload for the API
        $payload = array(
            'kode_jual' => $data['kode_jual'],
            'tanggal_jual' => $data['tanggal_jual'],
            'kode_admin' => $data['kode_admin'],
            'kode_karyawan' => $data['kode_karyawan'],
            'keterangan' => $data['keterangan'],
            'nomor_polisi' => $data['nomor_polisi'],
            'km_kendaraan' => $data['km_kendaraan'],
            'ongkos_karyawan' => $data['ongkos_karyawan'],
            'bayar' => $data['bayar'],
            'pelanggan' => $data['pelanggan'],
            'details' => $detail_penjualan, // Include the detail data
        );

        // API endpoint URL
        $api_url = "https://kurik.my.id/penjualan/save_penjualan"; // Replace with the actual API URL

        // Initialize cURL
        $ch = curl_init($api_url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute the cURL request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            log_message('error', 'API Call Error: ' . curl_error($ch));
        } elseif ($http_code != 200) {
            log_message('error', 'API Call Failed with HTTP Code ' . $http_code . ': ' . $response);
        } else {
            log_message('info', 'API Call Successful: ' . $response);
        }

        // Close cURL
        curl_close($ch);
    }

    public function save_penjualan()
    {
        // Check if receiving sync is enabled
        if (!$this->config->item('sync_receive_enabled')) {
            $response = [
                'status' => false,
                'message' => 'This server is not configured to receive sync data',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Get the JSON input from the request
        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            $response = [
                'status' => false,
                'message' => 'Invalid input data',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Validate required fields in the input
        if (empty($input['kode_jual']) || empty($input['tanggal_jual']) || empty($input['details'])) {
            $response = [
                'status' => false,
                'message' => 'Missing required fields',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Check if transaction already exists
        $existing = $this->Penjualan_model->selectById($input['kode_jual']);
        if ($existing) {
            $response = [
                'status' => true,
                'message' => 'Transaction already exists, skipping',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Insert the main penjualan data
        $totalall = 0;
        try {

            // Insert the details
            foreach ($input['details'] as $detail) {
                $row = $this->Barang_model->selectById($detail['kode_barang']);
                $harga_jual = $row->harga_jual;
                $harga_beli = $row->harga_beli;

                $detail_data = [
                    'kode_jual' => $input['kode_jual'],
                    'kode_barang' => $detail['kode_barang'],
                    'harga_jual' => $harga_jual,
                    'harga_beli' => $harga_beli,
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['is_using_rupiah'] ? $detail['subtotal'] : (float)$harga_jual * (float)$detail['jumlah']
                ];
                $totalall += $detail_data['subtotal'];

                $this->Penjualan_detail_model->insert($detail_data);
            }

            // Prepare the main penjualan data
            $data = [
                'kode_jual' => $input['kode_jual'],
                'tanggal_jual' => $input['tanggal_jual'],
                'tanggal_jual_date' => date('Y-m-d', strtotime($input['tanggal_jual'])),
                'waktu_jual' => date("h:i:s a"),
                'kode_admin' => $input['kode_admin'] ?? null,
                'kode_karyawan' => $input['kode_karyawan'] ?? null,
                'keterangan' => $input['keterangan'] ?? '',
                'nomor_polisi' => $input['nomor_polisi'] ?? '',
                'km_kendaraan' => $input['km_kendaraan'] ?? 0,
                'ongkos_karyawan' => $input['ongkos_karyawan'] ?? 0,
                'total' => $totalall ?? $input['total'] ?? 0,
                'bayar' => $input['bayar'] ?? 0,
                'pelanggan' => $input['pelanggan'] ?? '',
            ];
            $this->Penjualan_model->insert($data);

            $this->Penjualan_detail_model->updatestok($input['kode_jual']);

            // Send success response
            $response = [
                'status' => true,
                'message' => 'Data saved successfully',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } catch (Exception $e) {
            // Handle any exceptions and send error response
            $response = [
                'status' => false,
                'message' => 'Failed to save data: ' . $e->getMessage(),
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
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

    /**
     * API endpoint to get penjualan by date
     * Returns the latest transactions for a given date
     */
    public function get_by_date()
    {
        $date = $this->input->get('date');
        $limitParam = $this->input->get('limit');
        $limit = (is_numeric($limitParam) && intval($limitParam) > 0) ? intval($limitParam) : null;

        if (empty($date)) {
            $date = date('d-m-Y');
        }

        $date_formatted = date('d-m-Y', strtotime($date));

        $this->db->where('tanggal_jual', $date_formatted);
        $this->db->order_by('kode_jual', 'DESC');
        if (!is_null($limit)) {
            $this->db->limit($limit);
        }
        $penjualan_list = $this->db->get('penjualan')->result();

        $result = array();
        foreach ($penjualan_list as $penjualan) {
            $details = $this->Penjualan_detail_model->selectById($penjualan->kode_jual);
            $detail_array = array();
            foreach ($details as $detail) {
                $detail_array[] = array(
                    'kode_barang' => $detail->kode_barang,
                    'nama_barang' => $detail->nama_barang,
                    'harga_jual' => $detail->harga_jual,
                    'harga_beli' => $detail->harga_beli,
                    'jumlah' => $detail->jumlah,
                    'subtotal' => $detail->subtotal,
                );
            }

            $result[] = array(
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
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => true,
                'data' => $result
            )));
    }

    /**
     * API endpoint to check if a specific kode_jual exists
     */
    public function check_exists()
    {
        $kode_jual = $this->input->get('kode_jual');

        if (empty($kode_jual)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'kode_jual is required'
                )));
        }

        $exists = $this->Penjualan_model->selectById($kode_jual);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => true,
                'exists' => !empty($exists),
                'kode_jual' => $kode_jual
            )));
    }

    /**
     * API endpoint to get sync config
     */
    public function get_sync_config()
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => true,
                'sync_enabled' => $this->config->item('sync_enabled'),
                'sync_target_url' => $this->config->item('sync_target_url'),
                'sync_receive_enabled' => $this->config->item('sync_receive_enabled')
            )));
    }
}

/* End of file Penjualan.php */
/* Location: ./application/controllers/Penjualan.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
