<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Stok_cutoff_model
 *
 * Handles monthly end-of-month stock cut-off snapshots.
 * Each cut-off records the stok, harga_beli and harga_jual of every
 * barang at the moment the cut-off is processed.
 */
class Stok_cutoff_model extends CI_Model
{
    public $table = 'stok_cutoff';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns list of distinct periods (YYYY-MM) that have been cut off,
     * ordered newest first.
     *
     * @return array
     */
    public function get_periode_list()
    {
        $sql = "SELECT periode,
                       COUNT(*)                AS jumlah_barang,
                       SUM(nilai_stok_beli)    AS total_nilai_beli,
                       SUM(nilai_stok_jual)    AS total_nilai_jual,
                       MAX(created_at)         AS created_at,
                       MAX(created_by)         AS created_by
                FROM {$this->table}
                GROUP BY periode
                ORDER BY periode DESC";
        return $this->db->query($sql)->result();
    }

    /**
     * Check whether a cut-off for the given period already exists.
     *
     * @param  string $periode  YYYY-MM
     * @return bool
     */
    public function is_cutoff_done($periode)
    {
        $this->db->where('periode', $periode);
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Perform a stock cut-off for the given period.
     * Snapshots every row in barang (joined with merk) into stok_cutoff.
     * Will not insert if the period already has rows (idempotent guard).
     *
     * @param  string $periode  YYYY-MM
     * @param  string $user     Username performing the action
     * @return bool  TRUE on success
     */
    public function do_cutoff($periode, $user)
    {
        if ($this->is_cutoff_done($periode)) {
            return FALSE;
        }

        $periode  = $this->db->escape_str($periode);
        $user     = $this->db->escape_str($user);
        $now      = date('Y-m-d H:i:s');

        $sql = "INSERT INTO {$this->table}
                    (periode, kode_barang, nama_barang, merk, stok, harga_beli, harga_jual, created_at, created_by)
                SELECT
                    '{$periode}',
                    b.kode_barang,
                    b.nama_barang,
                    COALESCE(m.merk, ''),
                    b.stok,
                    b.harga_beli,
                    b.harga_jual,
                    '{$now}',
                    '{$user}'
                FROM barang b
                LEFT JOIN merk m ON b.kode_merk = m.kode_merk";

        return $this->db->query($sql);
    }

    /**
     * Delete all rows for a given period (to allow a redo).
     *
     * @param  string $periode  YYYY-MM
     * @return bool
     */
    public function delete_cutoff($periode)
    {
        $this->db->where('periode', $periode);
        return $this->db->delete($this->table);
    }

    /**
     * Retrieve all barang rows for a specific period, ordered by kode_barang.
     *
     * @param  string $periode  YYYY-MM
     * @return array
     */
    public function get_cutoff_detail($periode)
    {
        $this->db->where('periode', $periode);
        $this->db->order_by('kode_barang', 'ASC');
        return $this->db->get($this->table)->result();
    }
}

/* End of file Stok_cutoff_model.php */
/* Location: ./application/models/Stok_cutoff_model.php */
