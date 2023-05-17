<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Karyawan_model extends CI_Model
{

    public $table = 'karyawan';
    public $primary = 'kode_karyawan';
 
    function __construct()
    {
        parent::__construct();
    }

   
    function selectByAll()
    {
        return $this->db->get($this->table)->result();
    }

    function selectById($id)
    {
        $this->db->where($this->primary, $id);
        return $this->db->get($this->table)->row();
    }
    
    function total_rows() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function get_limit_data($limit, $start = 0, $cari = NULL) {
        $this->db->like('kode_karyawan', $cari);
	$this->db->or_like('nama_karyawan', $cari);
	$this->db->or_like('alamat_karyawan', $cari);
    $this->db->or_like('telp_karyawan', $cari);
    $this->db->or_like('username', $cari);
	$this->db->or_like('password', $cari);
	$this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
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

    function reset($id)
    {
        $data = array(
            'percobaan_stok' => 0
            );
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    function updateTry($id) {
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