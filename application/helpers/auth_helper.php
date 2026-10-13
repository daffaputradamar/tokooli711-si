<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('require_sync_key')) {
	/**
	 * Guards the endpoints another server calls during sync. Does nothing while
	 * no sync_api_key is configured, so existing setups keep working.
	 *
	 * The key arrives as ?sync_key=... (a URL parameter keeps cross-origin
	 * requests "simple", so no CORS change is needed on either server).
	 *
	 * @return void Sends 403 JSON and exits when the key is wrong
	 */
	function require_sync_key()
	{
		$CI =& get_instance();
		$expected = (string) $CI->config->item('sync_api_key');
		if ($expected === '') {
			return;
		}
		$given = (string) $CI->input->get('sync_key');
		if ($given === '' || !hash_equals($expected, $given)) {
			json_reply(403, array('status' => FALSE, 'ok' => FALSE, 'message' => 'Kunci sinkronisasi tidak valid.'));
		}
	}
}

if (!function_exists('sync_target')) {
	/**
	 * URL on the sync target server, with the shared key attached when one is set.
	 *
	 * @param string $path e.g. 'penjualan/save_penjualan'
	 * @return string
	 */
	function sync_target($path)
	{
		$CI =& get_instance();
		$url = rtrim((string) $CI->config->item('sync_target_url'), '/') . '/' . ltrim($path, '/');
		$key = (string) $CI->config->item('sync_api_key');
		if ($key !== '') {
			$url .= (strpos($url, '?') === FALSE ? '?' : '&') . 'sync_key=' . rawurlencode($key);
		}
		return $url;
	}
}

if (!function_exists('require_login')) {
	/**
	 * Sends visitors without a session to the login page.
	 *
	 * Call from a controller constructor after session_start().
	 *
	 * @param array $public_methods Methods that stay reachable without a login
	 *                              (e.g. endpoints the sync server calls)
	 * @param bool $block_cashier Also send plain cashiers (level "karyawan") to
	 *                            the cashier page, like the master-data controllers do
	 * @return void
	 */
	function require_login($public_methods = array(), $block_cashier = FALSE)
	{
		$CI =& get_instance();
		if (in_array($CI->router->fetch_method(), $public_methods, TRUE)) {
			// Open to the sync server; without a session it must show the key
			if (!isset($_SESSION['level'])) {
				require_sync_key();
			}
			return;
		}
		if (!isset($_SESSION['level'])) {
			redirect('login');
		}
		if ($block_cashier && $_SESSION['level'] == 'karyawan') {
			redirect('home');
		}
	}
}
