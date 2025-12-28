<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pembelian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian_model');
        $this->load->library('form_validation');
        $this->load->model('CodeGenerator');
        $this->load->model('Barang_model');
        $this->load->model('Pembelian_detail_model');
        $this->load->model('Suplier_model');
        session_start();
    }

    public function _rule()
    {
        $this->form_validation->set_rules('kode_beli', 'kode beli', 'trim|required');
        $this->form_validation->set_rules('tanggal_beli', 'tanggal beli', 'trim|required');
        $this->form_validation->set_rules('kode_admin', 'kode admin', 'trim|required');
        $this->form_validation->set_rules('total', 'total', 'trim|required');
        $this->form_validation->set_rules('kode_suplier', 'total', 'trim|required');
    }

    public function index()
    {
        $this->load->library('pagination');

        $config['per_page'] = 10;
        $config['page_query_string'] = true;

        $cari = urldecode($this->input->get('cari'));
        $start = intval($this->input->get('start'));

        $filter_barang = urldecode($this->input->get('kode_barang'));
        $filter_tgl_awal = urldecode($this->input->get('tgl_awal'));
        $filter_tgl_akhir = urldecode($this->input->get('tgl_akhir'));

        $pembelian_all = $this->Pembelian_model->get_limit_data_filter($config['per_page'], $start, $filter_barang, $filter_tgl_awal, $filter_tgl_akhir, $cari);

        $base_url_pembelian = base_url() . 'pembelian';
        if ($cari <> '') {
            $config['base_url'] = $base_url_pembelian . '?cari=' . urlencode($cari);
            $config['first_url'] = $base_url_pembelian . '?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = $base_url_pembelian . '?';
            $config['first_url'] = $base_url_pembelian . '?';
        }

        if (!empty($filter_barang)) {
            $config['base_url'] .= '&kode_barang=' . urlencode($filter_barang) . '&tgl_awal=' . urlencode($filter_tgl_awal) . '&tgl_akhir=' . urlencode($filter_tgl_akhir);
            $config['first_url'] .= '&kode_barang=' . urlencode($filter_barang) . '&tgl_awal=' . urlencode($filter_tgl_awal) . '&tgl_akhir=' . urlencode($filter_tgl_akhir);
        }

        $pembelian = array_slice($pembelian_all, $start, $config['per_page']);
        $config['total_rows'] = count($pembelian_all);

        $this->pagination->initialize($config);


        $data = array(
            'pembelian_data' => $pembelian,
            'cari' => $cari,
            'kode_barang' => $filter_barang,
            'tgl_awal' => $filter_tgl_awal,
            'tgl_akhir' => $filter_tgl_akhir,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
            'listbarang' => $this->Barang_model->selectByAll()
        );


        $this->load->view('nav');

        $this->load->view('pembelian/pembelian_list', $data);
        $this->load->view('foot');
    }

    public function view($id)
    {
        $row = $this->Pembelian_model->selectById($id);
        $this->load->view('nav');
        if ($row) {
            $data = array(
                'kode_beli' => $row->kode_beli,
                'tanggal_beli' => $row->tanggal_beli,
                'waktu_beli' => $row->waktu_beli,
                'no_faktur' => $row->no_faktur,
                'kode_suplier' => $row->kode_suplier,
                'kode_admin' => $row->kode_admin,
                'total' => $row->total,
            );
            $data['listbarang'] = $this->Barang_model->selectByAll();
            $data['listdetail'] = $this->Pembelian_detail_model->selectById($data['kode_beli']);
            $this->load->view('pembelian/pembelian_read', $data);
        }
        $this->load->view('foot');
    }

    public function datainsert()
    {
        $total_all = 0;

        $detail_pembelian = isset($_SESSION[$_SESSION['kode'] . 'detailbarang_pembelian']) ? $_SESSION[$_SESSION['kode'] . 'detailbarang_pembelian'] : array();
        ;
        foreach ($detail_pembelian as $pembeliandetail) {
            $total_all += $pembeliandetail['harga_beli'] * $pembeliandetail['jumlah'];
        }

        $data = array(

            'kode_beli' => set_value('kode_beli', '-'),
            // $this->CodeGenerator->buatkode('pembelian', 'kode_beli', 10, 'TRB')
            'tanggal_beli' => set_value('tanggal_beli', date('d-m-Y')),
            'kode_admin' => set_value('kode_admin', $_SESSION['kode']),
            'no_faktur' => set_value('no_faktur', '-'),
            'total' => set_value('total', $this->Pembelian_detail_model->totalall($this->input->post('kode_beli'))),
            'kode_suplier' => set_value('kode_suplier'),
            'total' => set_value($total_all)
        );
        $data['listbarang'] = $this->Barang_model->selectByAll();
        // $data['listdetail']=$this->Pembelian_detail_model->selectById($data['kode_beli']);
        $data['listdetail'] = isset($_SESSION[$_SESSION['kode'] . 'detailbarang_pembelian']) ? $_SESSION[$_SESSION['kode'] . 'detailbarang_pembelian'] : array();
        $data['totalall'] = $total_all;

        // unset($_SESSION[$_SESSION['kode'].'detailbarang_pembelian']);
        // print_r($data['listdetail']);
        // return;

        $data['listsuplier'] = $this->Suplier_model->selectByAll();
        $this->load->view('nav');
        $this->load->view('pembelian/pembelian_form', $data);
        $this->load->view('foot');
    }

    public function insert()
    {
        $kode_user = $_SESSION['kode'];

        if ($this->input->post('submitlist') <> "") {
            $tanggal_beli = $this->input->post('tanggal_beli');
            $no_faktur = $this->input->post("no_faktur");
            $kode_supplier = $this->input->post("kode_suplier");

            $_SESSION[$kode_user . 'tanggal_beli'] = $tanggal_beli;
            $_SESSION[$kode_user . 'no_faktur'] = $no_faktur;
            $_SESSION[$kode_user . 'kode_suplier'] = $kode_supplier;

            if ($this->input->post('jumlah') <> "") {
                $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
                //var_dump($barang);
                $data = array(
                    // 'kode_beli' => $this->input->post('kode_beli'),
                    'kode_barang' => $this->input->post('kode_barang'),
                    'nama_barang' => $barang->nama_barang,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $barang->harga_jual,
                    'jumlah' => $this->input->post('jumlah'),
                    'subtotal' => (float)$barang->harga_beli * (float)$this->input->post('jumlah'),
                );

                $_SESSION[$kode_user . 'detailbarang_pembelian'][$this->input->post('kode_barang')] = $data;
                // $this->Pembelian_detail_model->insert($data);
                return redirect(site_url('pembelian/insert'), 'refresh');
            }
        }

        $this->_rule();
        if ($this->form_validation->run() == false and $this->input->post('total') == null) {
            return $this->datainsert();
        }

        $kode_beli = $this->CodeGenerator->buatkode('pembelian', 'kode_beli', 10, 'TRB');
        $detail_pembelian = $_SESSION[$kode_user . 'detailbarang_pembelian'];
        $total_all = 0;

        foreach ($detail_pembelian as $pembeliandetail) {
            $total_all += $pembeliandetail['harga_beli'] * $pembeliandetail['jumlah'];
            $data = array(
                'kode_beli' => $kode_beli,
                'kode_barang' => $pembeliandetail['kode_barang'],
                'harga_beli' => $pembeliandetail['harga_beli'],
                'jumlah' => $pembeliandetail['jumlah'],
                'subtotal' => (float)$pembeliandetail['harga_beli'] * (float)$pembeliandetail['jumlah'],
            );
            $this->Pembelian_detail_model->insert($data);
        }

        $data = array(
            'kode_beli' => $this->CodeGenerator->buatkode('pembelian', 'kode_beli', 10, 'TRB'),
            'tanggal_beli' => $this->input->post('tanggal_beli'),
            'tanggal_beli_date' => date('Y-m-d', strtotime($this->input->post('tanggal_beli'))),
            'waktu_beli' => date("h:i:s a"),
            'kode_admin' => $this->input->post('kode_admin'),
            'no_faktur' => $this->input->post('no_faktur'),
            'total' => $this->Pembelian_detail_model->totalall($kode_beli),
            'kode_suplier' => $this->input->post('kode_suplier'),
        );
        $this->Pembelian_detail_model->updatestok($kode_beli, $_SESSION["username"]);
        $this->Pembelian_model->insert($data);

        unset($_SESSION[$kode_user . 'detailbarang_pembelian']);
        unset($_SESSION[$kode_user . 'tanggal_beli']);
        unset($_SESSION[$kode_user . 'no_faktur']);
        unset($_SESSION[$kode_user . 'kode_suplier']);

        $payload = array(
            'kode_beli' => $data['kode_beli'],
            'tanggal_beli' => $data['tanggal_beli'],
            'kode_admin' => $data['kode_admin'],
            'no_faktur' => $data['no_faktur'],
            'kode_suplier' => $data['kode_suplier'],
            'details' => $detail_pembelian, // Include the detail data
        );

        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function send_data_to_api($data, $detail_pembelian)
    {
        // Prepare the data payload for the API
        $payload = array(
            'kode_beli' => $data['kode_beli'],
            'tanggal_beli' => $data['tanggal_beli'],
            'kode_admin' => $data['kode_admin'],
            'no_faktur' => $data['no_faktur'],
            'kode_suplier' => $data['kode_suplier'],
            'details' => $detail_pembelian, // Include the detail data
        );

        // API endpoint URL
        $api_url = "http://localhost:81/tokooli711-si/pembelian/save_pembelian"; // Replace with the actual API URL

        // Initialize cURL
        $ch = curl_init($api_url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Prevent following redirects
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output

        // Execute the cURL request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            log_message('error', 'API Call Error: ' . curl_error($ch));
        } elseif ($http_code == 303) {
            $redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            log_message('info', 'Redirected to: ' . $redirect_url);
        } elseif ($http_code != 200) {
            log_message('error', 'API Call Failed with HTTP Code ' . $http_code . ': ' . $response);
        } else {
            log_message('info', 'API Call Successful: ' . $response);
        }

        // Close cURL
        curl_close($ch);
    }

    public function save_pembelian()
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
        if (empty($input['kode_beli']) || empty($input['tanggal_beli']) || empty($input['details'])) {
            $response = [
                'status' => false,
                'message' => 'Missing required fields',
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Check if transaction already exists
        $existing = $this->Pembelian_model->selectById($input['kode_beli']);
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
                $harga_beli = $row->harga_beli;

                $detail_data = [
                    'kode_beli' => $input['kode_beli'],
                    'kode_barang' => $detail['kode_barang'],
                    'harga_beli' => $harga_beli,
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => (float)$harga_beli * (float)$detail['jumlah'],
                ];
                $totalall += $detail_data['subtotal'];

                $this->Pembelian_detail_model->insert($detail_data);
            }

            // Prepare the main penjualan data
            $data = [
                'kode_beli' => $input['kode_beli'],
                'tanggal_beli' => $input['tanggal_beli'],
                'tanggal_beli_date' => date('Y-m-d', strtotime($input['tanggal_beli'])),
                'waktu_beli' => date("h:i:s a"),
                'kode_admin' => $input['kode_admin'] ?? null,
                'no_faktur' => $input['no_faktur'] ?? '-',
                'total' => $totalall ?? $input['total'] ?? 0,
                'kode_suplier' => $input['kode_suplier'] ?? '',
            ];
            $this->Pembelian_model->insert($data);

            $this->Pembelian_detail_model->updatestok($input['kode_beli'], "API Sync");

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
        $row = $this->Pembelian_model->selectById($id);

        if ($row) {
            $data = array(

                'kode_beli' => set_value('kode_beli', $row->kode_beli),
                'tanggal_beli' => set_value('tanggal_beli', $row->tanggal_beli),
                'kode_admin' => set_value('kode_admin', $row->kode_admin),
                'total' => set_value('total', $this->Pembelian_detail_model->totalall($this->uri->segment(3))),
                'kode_suplier' => set_value('kode_suplier', $row->kode_suplier),
            );
            $data['listbarang'] = $this->Barang_model->selectByAll();
            $data['listdetail'] = $this->Pembelian_detail_model->selectById($data['kode_beli']);
            $this->load->view('pembelian/pembelian_form', $data);
        }
        $this->load->view('foot');
    }

    public function update()
    {
        if ($this->input->post('submitlist') <> "") {
            if ($this->input->post('jumlah') <> "") {
                $cek = $this->Pembelian_detail_model->jumlahbyid($this->input->post('kode_beli'), $this->input->post('kode_barang'));
                var_dump($cek);
                if ($cek == 0) {
                    $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
                    //var_dump($barang);
                    $data = array(
                        'kode_beli' => $this->input->post('kode_beli'),
                        'kode_barang' => $this->input->post('kode_barang'),
                        'harga_beli' => $barang->harga_beli,
                        'jumlah' => $this->input->post('jumlah'),
                        'subtotal' => (float)$barang->harga_beli * (float)$this->input->post('jumlah'),
                    );

                    $this->Pembelian_detail_model->insert($data);
                    redirect(site_url('pembelian/update/' . $this->uri->segment(3)), 'refresh');
                } else {
                    $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
                    //var_dump($barang);
                    $data = array(
                        'kode_beli' => $this->input->post('kode_beli'),
                        'kode_barang' => $this->input->post('kode_barang'),
                        'harga_beli' => $barang->harga_beli,
                        'jumlah' => $this->input->post('jumlah'),
                        'subtotal' => (float)$barang->harga_beli * (float)$this->input->post('jumlah'),
                    );

                    $this->Pembelian_detail_model->update($data['kode_beli'], $data['kode_barang'], $data);
                    redirect(site_url('pembelian/update/' . $this->uri->segment(3)), 'refresh');
                }
            }
        } else {
            $this->_rule();

            if ($this->form_validation->run() == false) {
                $this->dataupdate($this->uri->segment(3));
            } else {
                $data = array(
                    'kode_beli' => $this->input->post('kode_beli'),
                    'tanggal_beli' => $this->input->post('tanggal_beli'),
                    'kode_admin' => $this->input->post('kode_admin'),
                    'no_faktur' => $this->input->post('no_faktur'),
                    'total' => $this->Pembelian_detail_model->totalall($this->uri->segment(3)),
                );

                $this->Pembelian_model->update($this->uri->segment(3), $data);

                redirect(site_url('pembelian'));
            }
        }

        $this->load->view('foot');
    }

    public function delete($id)
    {
        $row = $this->Pembelian_model->selectById($id);

        if ($row) {
            $this->Pembelian_model->delete($id);
            $this->Pembelian_detail_model->deletebykey($id);

            redirect(site_url('pembelian'));
        }
    }
    public function struk($id)
    {
        $row = $this->Pembelian_model->selectById($id);
        if ($row) {
            $data = array(
                'kode_beli' => $row->kode_beli,
                'tanggal_beli' => $row->tanggal_beli,
                'waktu_beli' => $row->waktu_beli,
                'kode_admin' => $row->kode_admin,
                'total' => $row->total,
                'kode_suplier' => $row->kode_suplier,
            );
            $data['listbarang'] = $this->Barang_model->selectByAll();
            $data['listdetail'] = $this->Pembelian_detail_model->selectById($data['kode_beli']);
            $this->load->view('pembelian/struk', $data);
        }
    }

    /**
     * API endpoint to get pembelian by date
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

        $this->db->where('tanggal_beli', $date_formatted);
        $this->db->order_by('kode_beli', 'DESC');
        if (!is_null($limit)) {
            $this->db->limit($limit);
        }
        $pembelian_list = $this->db->get('pembelian')->result();

        $result = array();
        foreach ($pembelian_list as $pembelian) {
            $details = $this->Pembelian_detail_model->selectById($pembelian->kode_beli);
            $detail_array = array();
            foreach ($details as $detail) {
                $detail_array[] = array(
                    'kode_barang' => $detail->kode_barang,
                    'harga_beli' => $detail->harga_beli,
                    'jumlah' => $detail->jumlah,
                    'subtotal' => $detail->subtotal,
                );
            }

            $result[] = array(
                'kode_beli' => $pembelian->kode_beli,
                'tanggal_beli' => $pembelian->tanggal_beli,
                'waktu_beli' => $pembelian->waktu_beli,
                'kode_admin' => $pembelian->kode_admin,
                'no_faktur' => $pembelian->no_faktur,
                'total' => $pembelian->total,
                'kode_suplier' => $pembelian->kode_suplier,
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
     * API endpoint to check if a specific kode_beli exists
     */
    public function check_exists()
    {
        $kode_beli = $this->input->get('kode_beli');

        if (empty($kode_beli)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => false,
                    'message' => 'kode_beli is required'
                )));
        }

        $exists = $this->Pembelian_model->selectById($kode_beli);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => true,
                'exists' => !empty($exists),
                'kode_beli' => $kode_beli
            )));
    }
}

/* End of file Pembelian.php */
/* Location: ./application/controllers/Pembelian.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
