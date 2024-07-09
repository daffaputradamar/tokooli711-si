<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Barang_model extends CI_Model
{

    public $table = 'barang';
    public $primary = 'kode_barang';
 
    function __construct()
    {
        parent::__construct();
    }

   
    function selectByAll()
    {   
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    function selectById($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->row();
    }
    
    function total_rows() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function get_limit_data($limit, $start = 0, $cari = NULL) {
        $this->db->like('kode_barang', $cari);
	$this->db->or_like('nama_barang', $cari);
    $this->db->or_like('merk.kode_merk', $cari);
    $this->db->or_like('merk', $cari);
	$this->db->or_like('harga_beli', $cari);
	$this->db->or_like('harga_jual', $cari);
	$this->db->or_like('stok', $cari);
	$this->db->or_like('barang.keterangan', $cari);
    $this->db->limit($limit, $start);
    $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    function getAll_by($cari = NULL) {
        $this->db->like('kode_barang', $cari);
	$this->db->or_like('nama_barang', $cari);
    $this->db->or_like('merk.kode_merk', $cari);
    $this->db->or_like('merk', $cari);
	$this->db->or_like('harga_beli', $cari);
	$this->db->or_like('harga_jual', $cari);
	$this->db->or_like('stok', $cari);
	$this->db->or_like('barang.keterangan', $cari);
    $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    function getMovementByBarang($kode_barang, $tgl_awal, $tgl_akhir)
    {
        $sql_penjualan = "
            SELECT pd.kode_jual kode_trans, 'Penjualan' jenis_trans, p.tanggal_jual tanggal_trans, pd.jumlah
            FROM penjualan p
            JOIN penjualan_detail pd ON p.kode_jual = pd.kode_jual
            WHERE pd.kode_barang = '$kode_barang'
            AND p.tanggal_jual_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
            ORDER BY p.tanggal_jual_date DESC
        ";
        $penjualan = $this->db->query($sql_penjualan)->result();

        $sql_pembelian = "
            SELECT bd.kode_beli kode_trans, 'Pembelian' jenis_trans, b.tanggal_beli tanggal_trans, bd.jumlah
            FROM pembelian b
            JOIN pembelian_detail bd ON b.kode_beli = bd.kode_beli
            WHERE bd.kode_barang = '$kode_barang'
            AND b.tanggal_beli_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
            ORDER BY b.tanggal_beli_date DESC
        ";
        $pembelian = $this->db->query($sql_pembelian)->result();

        $merged = array_merge($penjualan, $pembelian);
        usort($merged, function($a, $b) {
            $dateA = DateTime::createFromFormat('d-m-Y', $a->tanggal_trans);
            $dateB = DateTime::createFromFormat('d-m-Y', $b->tanggal_trans);
        
            return $dateA <=> $dateB;
        });
        return $merged;
    }

    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    function update($id, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    function delete($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }

}

/* End of file Barang_model.php */
/* Location: ./application/models/Barang_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */