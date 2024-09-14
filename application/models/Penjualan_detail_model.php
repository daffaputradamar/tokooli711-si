<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Penjualan_detail_model extends CI_Model
{
    public $table = 'penjualan_detail';
    public $primary = 'kode_jual';

    public function __construct()
    {
        parent::__construct();
    }


    public function selectByAll()
    {
        $this->db->join('barang', 'barang.kode_barang = penjualan_detail.kode_barang', 'left');
        return $this->db->get($this->table)->result();
    }

    public function selectByKodeJual($kode_jual)
    {
        $this->db->where_in('kode_jual', $kode_jual);
        $this->db->join('barang', 'barang.kode_barang = penjualan_detail.kode_barang', 'left');
        return $this->db->get($this->table)->result();
    }

    public function selectById($id)
    {
        $this->db->join('barang', 'barang.kode_barang = penjualan_detail.kode_barang', 'left');
        $this->db->where($this->primary, $id);
        return $this->db->get($this->table)->result();

    }
    public function jumlahbyid($id, $barang)
    {
        $this->db->where($this->primary, $id);
        $this->db->where('kode_barang', $barang);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function totalall($id)
    {
        //$jumlah1=$this->db->query("select ongkos_karyawan from penjualan where kode_jual='$id'")->row();
        $jumlah = $this->db->query("select sum(subtotal) as total from penjualan_detail where kode_jual='$id'")->row();
        //echo $jumlah1->ongkos_karyawan;
        return $jumlah->total;
    }

    public function total_rows()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $cari = null)
    {
        $this->db->like('', $cari);
        $this->db->or_like('kode_jual', $cari);
        $this->db->or_like('kode_barang', $cari);
        $this->db->or_like('harga_jual', $cari);
        $this->db->or_like('harga_beli', $cari);
        $this->db->or_like('jumlah', $cari);
        $this->db->or_like('subtotal', $cari);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    public function update($id, $barang, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->where('kode_barang', $barang);
        $this->db->update($this->table, $data);
    }

    public function delete($id, $barang)
    {
        $this->db->where($this->primary, $id);
        $this->db->where('kode_barang', $barang);
        $this->db->delete($this->table);
    }
    public function deletebykey($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }
    public function updatestok($id)
    {
        $this->db->where($this->primary, $id);
        $data = $this->db->get('penjualan_detail')->result();
        foreach ($data as $row) {
            $this->db->query("update barang set stok=stok-".$row->jumlah." where kode_barang='$row->kode_barang' ");
        }
    }
    public function jumlahAll()
    {
        $data = $this->db->query("select sum(jumlah) as jumlah from penjualan_detail")->row();
        return $data->jumlah;
    }

}

/* End of file Penjualan_detail_model.php */
/* Location: ./application/models/Penjualan_detail_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
