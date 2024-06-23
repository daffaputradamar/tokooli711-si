<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Karyawan_model extends CI_Model
{
    public $table = 'karyawan';
    public $primary = 'kode_karyawan';

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
        return $this->db->query("
            SELECT 
            k.kode_karyawan
            , k.nama_karyawan
            , k.alamat_karyawan
            , k.telp_karyawan
            , CASE WHEN pk.id_barang IS NOT NULL THEN 1 ELSE 0 END isblocked
            FROM karyawan k
            LEFT JOIN (
                SELECT pk.id_karyawan, pk.id_barang, COUNT(1) jml_percobaan
                FROM percobaan_karyawan pk
                WHERE pk.isactive = 1
                GROUP BY pk.id_karyawan, pk.id_barang
                HAVING COUNT(1) >= 2
                ORDER BY jml_percobaan DESC
            ) pk ON k.kode_karyawan = pk.id_karyawan
            WHERE 
                k.kode_karyawan LIKE '%$cari%'
                OR k.nama_karyawan LIKE '%$cari%'
                OR k.alamat_karyawan LIKE '%$cari%'
                OR k.telp_karyawan LIKE '%$cari%'
            GROUP BY
                k.kode_karyawan,
                k.nama_karyawan,
                k.alamat_karyawan,
                k.telp_karyawan
            LIMIT $start, $limit;
        ")->result();
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

    public function updateTry($id)
    {
        $this->db->query("
            UPDATE karyawan
            SET percobaan_stok = percobaan_stok + 1
            WHERE kode_karyawan = '$id' 
        ");
    }

}

/* End of file Karyawan_model.php */
/* Location: ./application/models/Karyawan_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
