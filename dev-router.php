<?php
/**
 * Router for PHP's built-in dev server (emulates the .htaccess rewrite).
 *
 * Usage: CI_BASE_URL=http://localhost:8000/ php -S localhost:8000 dev-router.php
 *
 * Defaults to CI_ENV=testing, which hides the PHP 8 deprecation notices that
 * CodeIgniter 3.0.4 emits. Set CI_ENV=development to see every notice.
 *
 * Sync is OFF locally unless CI_SYNC_ENABLED=1 is set, so saving a sale here
 * never pushes to the live sync server. To test the Sync Dashboard, run a
 * second copy as a fake target server on another port with its own database:
 *
 *   CI_DB_NAME=tokooli_remote CI_DEV_CORS=1 CI_BASE_URL=http://localhost:8002/ php -S localhost:8002 dev-router.php
 *   CI_SYNC_ENABLED=1 CI_SYNC_TARGET_URL=http://localhost:8002 CI_BASE_URL=http://localhost:8000/ php -S localhost:8000 dev-router.php
 */
$_SERVER['CI_ENV'] = getenv('CI_ENV') ? getenv('CI_ENV') : 'testing';

if (getenv('CI_SYNC_ENABLED') === FALSE) {
	putenv('CI_SYNC_ENABLED=0');
}

// The fake target server is called cross-origin by the main copy
if (getenv('CI_DEV_CORS')) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
	header('Access-Control-Allow-Headers: Content-Type');
	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		return TRUE;
	}
}

// Not parse_url(): it reads '//assets/x' as host 'assets'.
$parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$path = rawurldecode($parts[0]);

// Views emit base_url() . '/assets/...' which yields '//assets/...'; Apache
// merges the slashes, the built-in server does not.
$clean = preg_replace('#/{2,}#', '/', $path);
if ($clean !== $path && is_file(__DIR__ . $clean))
{
	$query = isset($parts[1]) ? '?' . $parts[1] : '';
	header('Location: ' . str_replace('%2F', '/', rawurlencode($clean)) . $query, TRUE, 302);
	return TRUE;
}

if ($path !== '/' && is_file(__DIR__ . $path))
{
	return FALSE;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
chdir(__DIR__);
require __DIR__ . '/index.php';
