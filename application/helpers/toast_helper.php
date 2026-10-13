<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * One-shot toast messages that survive a redirect.
 *
 * Controllers call set_toast() before redirect(); foot.php hands the queue
 * to assets/js/app.js, which shows them on the next page.
 */

if (!function_exists('set_toast')) {
	/**
	 * Queues a toast for the next page view.
	 *
	 * @param string $type success|error|warning|info
	 * @param string $message Plain text (escaped when shown)
	 * @return void
	 */
	function set_toast($type, $message)
	{
		if (session_id() === '') {
			session_start();
		}
		$_SESSION['app_toasts'][] = array('type' => $type, 'message' => $message);
	}
}

if (!function_exists('take_toasts')) {
	/**
	 * Returns and clears the queued toasts.
	 *
	 * @return array
	 */
	function take_toasts()
	{
		if (session_id() === '' || empty($_SESSION['app_toasts'])) {
			return array();
		}
		$toasts = $_SESSION['app_toasts'];
		unset($_SESSION['app_toasts']);
		return $toasts;
	}
}

if (!function_exists('json_reply')) {
	/**
	 * Sends a JSON response and stops. Used by endpoints the pages call with fetch().
	 *
	 * @param int $status HTTP status code
	 * @param array $data Payload; errors use array('ok' => FALSE, 'message' => ...)
	 * @return void
	 */
	function json_reply($status, $data)
	{
		$CI =& get_instance();
		$CI->output->set_status_header($status);
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}
}
