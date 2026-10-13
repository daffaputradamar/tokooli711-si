<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Formatting helpers shared by the report (laporan) views.
 */

if (!function_exists('lap_rp')) {
	/**
	 * Formats a number as Rupiah, e.g. 1250000 -> "Rp 1.250.000".
	 *
	 * @param float|int|string|null $n Amount
	 * @return string
	 */
	function lap_rp($n)
	{
		$n = (float) $n;
		return ($n < 0 ? '-' : '') . 'Rp ' . number_format(abs(round($n)), 0, ',', '.');
	}
}

if (!function_exists('lap_num')) {
	/**
	 * Formats a quantity, dropping a pointless ",00" (12.00 -> "12", 2.5 -> "2,5").
	 *
	 * @param float|int|string|null $n Quantity
	 * @return string
	 */
	function lap_num($n)
	{
		$n = (float) $n;
		if ($n == floor($n)) {
			return number_format($n, 0, ',', '.');
		}
		return rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
	}
}

if (!function_exists('lap_bulan')) {
	/**
	 * Indonesian month name.
	 *
	 * @param int $m Month number 1-12
	 * @return string
	 */
	function lap_bulan($m)
	{
		$names = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$m = (int) $m;
		return isset($names[$m]) ? $names[$m] : '';
	}
}

if (!function_exists('lap_tanggal')) {
	/**
	 * "12-10-2026" or "2026-10-12" -> "12 Oktober 2026"; with $monthly -> "Oktober 2026".
	 *
	 * @param string $date Any strtotime()-parsable date
	 * @param bool $monthly Show month and year only
	 * @return string
	 */
	function lap_tanggal($date, $monthly = FALSE)
	{
		$ts = strtotime($date);
		if ($ts === FALSE) {
			return (string) $date;
		}
		$label = lap_bulan(date('n', $ts)) . ' ' . date('Y', $ts);
		return $monthly ? $label : date('j', $ts) . ' ' . $label;
	}
}
