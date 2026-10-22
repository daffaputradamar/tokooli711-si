<?php
//created maruf 14/06/2016
defined('BASEPATH') OR exit('No direct script access allowed');

class CodeGenerator extends CI_Model {

	public function buatkode($table,$field, $panjang, $inisial){
		
		// Only well-formed codes (prefix + digits) count. A stray code like
		// 'TRB000EST3' used to sort highest, get "incremented" as a string
		// (deprecated in PHP 8.3) and be handed out again for every new row.
		$sql=$this->db->query(
			"select max(cast(substring(".$field.", ".(strlen($inisial) + 1).") as unsigned)) as angka from ".$table." where ".$field." regexp ?",
			array('^'.$inisial.'[0-9]+$')
		)->result();

		$angka=strval((int) $sql[0]->angka + 1);
		$tmp="";
		for ($i=1; $i<=($panjang-strlen($angka)-strlen($inisial));$i++){
			$tmp=$tmp."0";
		}
		return $inisial.$tmp.$angka ;
	}	
	function rp($angka){	$angka = number_format((float) $angka);	$angka = str_replace(',', '.', $angka);	$angka ="$angka";	return $angka;}

}

/* End of file CodeGenerator.php */
/* Location: ./application/models/CodeGenerator.php */