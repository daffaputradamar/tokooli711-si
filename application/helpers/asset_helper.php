<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('asset_url')) {
	/**
	 * URL of a file under the web root, versioned by its modification time.
	 *
	 * Browsers may then cache assets for a long time (see .htaccess) and still
	 * pick up a changed file immediately, because its URL changes with it.
	 *
	 * @param string $path Path relative to the web root, e.g. 'assets/css/app.css'
	 * @return string
	 */
	function asset_url($path)
	{
		$path = ltrim($path, '/');
		$file = FCPATH . $path;
		$version = is_file($file) ? filemtime($file) : '';
		return base_url() . $path . ($version ? '?v=' . $version : '');
	}
}
