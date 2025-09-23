<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pembelian_detail extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian_detail_model');
        $this->load->library('form_validation');
        session_start();
        if (!isset($_SESSION['level'])) {
            redirect('login');
        } elseif ($_SESSION['level'] == 'karyawan') {
            redirect('home');
        }
    }
    public function _rule()
    {
        $this->form_validation->set_rules('kode_beli', 'kode beli', 'trim|required');
        $this->form_validation->set_rules('kode_barang', 'kode barang', 'trim|required');
        $this->form_validation->set_rules('harga_beli', 'harga beli', 'trim|required');
        $this->form_validation->set_rules('jumlah', 'jumlah', 'trim|required');
        $this->form_validation->set_rules('subtotal', 'subtotal', 'trim|required');
    }

    public function index()
    {
        $this->load->view('nav');
        $this->load->library('pagination');
        $cari = urldecode($this->input->get('cari'));
        $start = intval($this->input->get('start'));

        if ($cari <> '') {
            $config['base_url'] = base_url() . 'pembelian_detail?cari=' . urlencode($cari);
            $config['first_url'] = base_url() . 'pembelian_detail?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = base_url() . 'pembelian_detail';
            $config['first_url'] = base_url() . 'pembelian_detail';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = true;
        $config['total_rows'] = $this->Pembelian_detail_model->total_rows($cari);
        $pembelian_detail = $this->Pembelian_detail_model->get_limit_data($config['per_page'], $start, $cari);


        $this->pagination->initialize($config);

        $data = array(
            'pembelian_detail_data' => $pembelian_detail,
            'cari' => $cari,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('pembelian_detail/pembelian_detail_list', $data);
        $this->load->view('foot');
    }

    public function view($id)
    {
        $this->load->view('nav');
        $row = $this->Pembelian_detail_model->selectById($id);
        if ($row) {
            $data = array(
        'kode_beli' => $row->kode_beli,
        'kode_barang' => $row->kode_barang,
        'harga_beli' => $row->harga_beli,
        'jumlah' => $row->jumlah,
        'subtotal' => $row->subtotal,
        );
            $this->load->view('pembelian_detail/pembelian_detail_read', $data);
        }
        $this->load->view('foot');
    }

    public function datainsert()
    {
        $this->load->view('nav');
        $data = array(

        'kode_beli' => set_value('kode_beli'),
        'kode_barang' => set_value('kode_barang'),
        'harga_beli' => set_value('harga_beli'),
        'jumlah' => set_value('jumlah'),
        'subtotal' => set_value('subtotal'),
    );
        $this->load->view('pembelian_detail/pembelian_detail_form', $data);
        $this->load->view('foot');
    }

    public function insert()
    {
        $this->_rule();

        if ($this->form_validation->run() == false) {
            $this->datainsert();
        } else {
            $data = array(
        'kode_beli' => $this->input->post('kode_beli'),
        'kode_barang' => $this->input->post('kode_barang'),
        'harga_beli' => $this->input->post('harga_beli'),
        'jumlah' => $this->input->post('jumlah'),
        'subtotal' => $this->input->post('subtotal'),
        );

            $this->Pembelian_detail_model->insert($data);
            redirect(site_url('pembelian_detail'));
        }
    }

    public function dataupdate($id)
    {
        $this->load->view('nav');
        $row = $this->Pembelian_detail_model->selectById($id);

        if ($row) {
            $data = array(

        'kode_beli' => set_value('kode_beli', $row->kode_beli),
        'kode_barang' => set_value('kode_barang', $row->kode_barang),
        'harga_beli' => set_value('harga_beli', $row->harga_beli),
        'jumlah' => set_value('jumlah', $row->jumlah),
        'subtotal' => set_value('subtotal', $row->subtotal),
        );
            $this->load->view('pembelian_detail/pembelian_detail_form', $data);
        }
        $this->load->view('foot');
    }

    public function update($id, $barang)
    {
        $this->_rule();

        if ($this->form_validation->run() == false) {
            $this->dataupdate($this->uri->segment(3));
        } else {
            $data = array(
        'kode_beli' => $this->input->post('kode_beli'),
        'kode_barang' => $this->input->post('kode_barang'),
        'harga_beli' => $this->input->post('harga_beli'),
        'jumlah' => $this->input->post('jumlah'),
        'subtotal' => $this->input->post('subtotal'),
        );

            $this->Pembelian_detail_model->update($id, $barang, $data);

            redirect(site_url('pembelian/insert'));
        }
    }

    public function delete($barang)
    {
        // $row = $this->Pembelian_detail_model->selectById($id);

        // if ($row) {
        // $this->Pembelian_detail_model->delete($id,$barang);

        unset($_SESSION[$_SESSION['kode'] . 'detailbarang_pembelian'][$barang]);

        redirect(site_url('pembelian/insert'));
        // }
    }

    public function get_product_detail()
    {
        // Clear any output that might have been sent
        ob_clean();
        
        header('Content-Type: application/json');

        $kode_barang = $this->input->post('kode_barang');

        if (!$kode_barang) {
            echo json_encode([
                'success' => false,
                'message' => 'Kode barang tidak valid'
            ]);
            exit;
        }

        // Load Barang model to get product details
        $this->load->model('Barang_model');
        $this->load->model('Merk_model');

        $barang = $this->Barang_model->selectById($kode_barang);

        if ($barang) {
            // Get merk name if available
            $merk = null;
            if (isset($barang->kode_merk) && $barang->kode_merk) {
                $merk_data = $this->Merk_model->selectById($barang->kode_merk);
                $merk = $merk_data ? $merk_data->merk : null;
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'kode_barang' => isset($barang->kode_barang) ? $barang->kode_barang : '',
                    'nama_barang' => isset($barang->nama_barang) ? $barang->nama_barang : '',
                    'nama_merk' => $merk,
                    'stok' => isset($barang->stok) ? $barang->stok : '0',
                    'harga_beli' => isset($barang->harga_beli) ? $barang->harga_beli : '0',
                    'harga_jual' => isset($barang->harga_jual) ? $barang->harga_jual : '0'
                ]
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
        }
        exit; // Ensure no additional output
    }

}

/* End of file Pembelian_detail.php */
/* Location: ./application/controllers/Pembelian_detail.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
