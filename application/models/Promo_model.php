<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Promo_model extends CI_Model
{
    public $table = 'promo';
    public $primary = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function selectByAll($cari)
    {
        $this->db->or_like('text', $cari);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result();
    }

    public function selectTopOne($tgl_transaksi = null)
    {
        if($tgl_transaksi == null) {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($tgl_transaksi));
        }

        $this->db->where('dtfrom <=', $date);
        $this->db->where('dtthru >=', $date);
        $this->db->where('isactive', true);
        $this->db->limit(1);
        return $this->db->get($this->table)->row();
    }

    public function selectById($id)
    {
        $this->db->where($this->primary, $id);
        return $this->db->get($this->table)->row();
    }

    public function update($id, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }

    public function toggle($id, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, array(
            'isactive' => $data
        ));
    }

    public function total_rows()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
}
