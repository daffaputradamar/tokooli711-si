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
        if (!isset($_SESSION['level'])) {
            redirect('login');
        } elseif ($_SESSION['level'] == 'karyawan') {
            redirect('home');
        }
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
        $cari = urldecode($this->input->get('cari'));
        $start = $this->input->get('start') !== null ? intval($this->input->get('start')) : 0;

        $config['per_page'] = 10;
        $config['page_query_string'] = true;

        $is_filter = $this->input->post('filter');
        if (isset($is_filter)) {
            $filter_barang = $this->input->post('kode_barang');
            $filter_tgl_awal = $this->input->post('tgl_awal');
            $filter_tgl_akhir = $this->input->post('tgl_akhir');

            $pembelian_all = $this->Pembelian_model->get_limit_data_filter($filter_barang, $filter_tgl_awal, $filter_tgl_akhir, $config['per_page'], $start);

        } else {
            $pembelian_all = $this->Pembelian_model->get_limit_data($config['per_page'], $start, $cari);
        }

        if ($cari <> '') {
            $config['base_url'] = base_url() . 'pembelian?cari=' . urlencode($cari);
            $config['first_url'] = base_url() . 'pembelian?cari=' . urlencode($cari);
        } else {
            $config['base_url'] = base_url() . 'pembelian';
            $config['first_url'] = base_url() . 'pembelian';
        }

        $pembelian = array_slice($pembelian_all, $start, $config['per_page']);
        $config['total_rows'] = count($pembelian_all);

        $this->pagination->initialize($config);


        $data = array(
            'pembelian_data' => $pembelian,
            'cari' => $cari,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
            'listbarang' => $this->Barang_model->selectByAll()
        );

        if (isset($is_filter)) {
            $data['filter'] = array(
                'kode_barang' => $filter_barang,
                'tgl_awal' => $filter_tgl_awal,
                'tgl_akhir' => $filter_tgl_akhir
            );
        }


        $this->load->view('nav');
        $this->load->library('pagination');

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

        $detail_pembelian = isset($_SESSION[$_SESSION['kode'].'detailbarang_pembelian']) ? $_SESSION[$_SESSION['kode'].'detailbarang_pembelian'] : array();
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
        $data['listdetail'] = isset($_SESSION[$_SESSION['kode'].'detailbarang_pembelian']) ? $_SESSION[$_SESSION['kode'].'detailbarang_pembelian'] : array();
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
        $total = 0;

        $kode_user = $_SESSION['kode'];


        if ($this->input->post('submitlist') <> "") {
            $tanggal_beli = $this->input->post('tanggal_beli');
            $no_faktur = $this->input->post("no_faktur");
            $kode_supplier = $this->input->post("kode_suplier");

            $_SESSION[$kode_user . 'tanggal_beli'] = $tanggal_beli;
            $_SESSION[$kode_user . 'no_faktur'] = $no_faktur;
            $_SESSION[$kode_user . 'kode_suplier'] = $kode_supplier;

            if ($this->input->post('jumlah') <> "") {
                // $cek=$this->Pembelian_detail_model->jumlahbyid($this->input->post('kode_beli'), $this->input->post('kode_barang'));

                $cek = isset($_SESSION[$kode_user . 'detailbarang_pembelian'][$this->input->post('kode_barang')]);

                if (!$cek) {
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
                    redirect(site_url('pembelian/insert'), 'refresh');
                } else {
                    $barang = $this->Barang_model->selectById($this->input->post('kode_barang'));
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
                    // $this->Pembelian_detail_model->update($data['kode_beli'], $data['kode_barang'], $data);
                    redirect(site_url('pembelian/insert'), 'refresh');
                }
            }
        } else {
            $this->_rule();
            if ($this->form_validation->run() == false and $this->input->post('total') == null) {
                $this->datainsert();
            } else {

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
                $this->Pembelian_detail_model->updatestok($kode_beli);
                $this->Pembelian_model->insert($data);
                unset($_SESSION[$kode_user . 'detailbarang_pembelian']);
                redirect(site_url('pembelian'));
            }
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
                    redirect(site_url('pembelian/update/'.$this->uri->segment(3)), 'refresh');
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
                    redirect(site_url('pembelian/update/'.$this->uri->segment(3)), 'refresh');
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
}

/* End of file Pembelian.php */
/* Location: ./application/controllers/Pembelian.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
