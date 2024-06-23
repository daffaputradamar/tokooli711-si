<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Percobaan_karyawan_model extends CI_Model
{
    public $table = 'percobaan_karyawan';
    public $primary = 'id_percobaan';

    public function __construct()
    {
        parent::__construct();
    }


    public function selectByAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function selectById($id)
    {
        $this->db->where($this->primary, $id);
        return $this->db->get($this->table)->row();
    }

    public function total_rows()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $cari = null)
    {
        $this->db->like('kode_karyawan', $cari);
        $this->db->or_like('nama_karyawan', $cari);
        $this->db->or_like('alamat_karyawan', $cari);
        $this->db->or_like('telp_karyawan', $cari);
        $this->db->or_like('username', $cari);
        $this->db->or_like('password', $cari);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }

    public function resetpercobaan($id)
    {
        $data = array(
            'isactive' => 0
            );
        $this->db->where('id_karyawan', $id);
        $this->db->where('isactive', 1);
        $this->db->update($this->table, $data);
    }

    public function get_percobaan_by_karyawan($id)
    {
        return $this->db->query("
            SELECT pk.id_barang, b.nama_barang, COUNT(1) jml_percobaan, MAX(createdate) percobaan_terakhir
            FROM percobaan_karyawan pk
            JOIN barang b ON pk.id_barang = b.kode_barang
            WHERE id_karyawan = '$id'
            AND isactive = 1
            GROUP BY pk.id_barang, b.nama_barang
            HAVING COUNT(1) >= 2
        ")->result();
    }

    public function get_detail_percobaan($id, $barang)
    {
        return $this->db->query("
            SELECT pk.id_barang, b.nama_barang, createdate
            FROM percobaan_karyawan pk
            JOIN barang b ON pk.id_barang = b.kode_barang
            WHERE id_karyawan = '$id'
            AND pk.id_barang = '$barang'
            ORDER BY createdate DESC
        ")->result();
    }

    public function get_percobaan_list_by_karyawan($id, $cari)
    {
        return $this->db->query("
            SELECT pk.id_barang, b.nama_barang, COUNT(1) jml_percobaan, MAX(createdate) percobaan_terakhir
            FROM percobaan_karyawan pk
            JOIN barang b ON pk.id_barang = b.kode_barang
            WHERE id_karyawan = '$id'
            AND (
                b.nama_barang LIKE '%$cari%'
            )
            GROUP BY pk.id_barang, b.nama_barang
        ")->result();
    }

}
