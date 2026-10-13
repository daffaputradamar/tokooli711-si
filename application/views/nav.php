<?php
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';
$can_see_sales = !empty($_SESSION['can_see_sales']);
$is_admin = ($level == "admin");
$is_karyawan_admin = ($level == "karyawan_admin");
$has_sidebar = ($is_admin || $is_karyawan_admin || $can_see_sales);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$seg1 = strtolower($this->uri->segment(1));
$seg2 = strtolower($this->uri->segment(2));

$is_master = in_array($seg1, array('barang', 'merk', 'karyawan', 'suplier', 'promo'));
$is_laporan = ($seg1 == 'laporan');
$is_ramal = in_array($seg1, array('ramal', 'ramal_harga'));

$laporan_items = array(
	'' => 'Ringkasan',
	'laporan_barang' => 'Laporan Barang',
	'movement' => 'Movement Barang',
	'history_stok' => 'History Stok Barang',
	'stok_cutoff' => 'Cut-off Stok Bulanan',
	'beli' => 'Laporan Pembelian',
	'jual' => 'Laporan Penjualan'
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8"/>
	<title>Toko Oli 711</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta name="theme-color" content="#4d42c4"/>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;650;700&display=swap" rel="stylesheet">
	<link href="<?= base_url() ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/global/css/plugins-md.css" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= asset_url('assets/css/app.css') ?>" rel="stylesheet" type="text/css"/>
	<?php if ($seg1 == '' || $seg1 == 'home') { ?>
	<link href="<?= asset_url('assets/css/pos.css') ?>" rel="stylesheet" type="text/css"/>
	<?php } ?>
	<?php if ($seg1 == 'laporan' || $seg1 == 'sync') { ?>
	<link href="<?= asset_url('assets/css/report.css') ?>" rel="stylesheet" type="text/css"/>
	<?php } ?>
	<?php if ($seg1 == 'sync') { ?>
	<link href="<?= asset_url('assets/css/sync.css') ?>" rel="stylesheet" type="text/css"/>
	<?php } ?>
	<link rel="shortcut icon" href="<?= base_url() ?>assets/logonew.png"/>
	<script src="<?= base_url() ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
</head>
<body class="app page-<?= $seg1 == '' ? 'home' : htmlspecialchars($seg1) ?><?= $has_sidebar ? ' has-sidebar' : '' ?>">

<?php if ($has_sidebar) { ?>
<aside class="app-sidebar" id="appSidebar" aria-label="Menu utama">
	<a class="app-brand" href="<?= base_url() ?>home">
		<img src="<?= base_url() ?>assets/logonew.png" alt=""/>
		<span>Toko Oli 711</span>
	</a>
	<nav class="app-nav">
		<div class="app-nav-section">Transaksi</div>
		<ul>
			<li>
				<a class="app-nav-link<?= ($seg1 == '' || $seg1 == 'home') ? ' is-active' : '' ?>" href="<?= base_url() ?>home">
					<i class="fa fa-shopping-cart"></i><span>Kasir</span>
				</a>
			</li>
			<?php if ($is_admin || $can_see_sales) { ?>
			<li>
				<a class="app-nav-link<?= in_array($seg1, array('penjualan', 'penjualan_detail')) ? ' is-active' : '' ?>" href="<?= base_url() ?>penjualan">
					<i class="fa fa-list-alt"></i><span>Penjualan</span>
				</a>
			</li>
			<?php } ?>
			<?php if ($is_admin || $is_karyawan_admin) { ?>
			<li>
				<a class="app-nav-link<?= in_array($seg1, array('pembelian', 'pembelian_detail')) ? ' is-active' : '' ?>" href="<?= base_url() ?>pembelian">
					<i class="fa fa-truck"></i><span>Pembelian</span>
				</a>
			</li>
			<?php } ?>
		</ul>

		<?php if ($is_admin) { ?>
		<div class="app-nav-section">Data</div>
		<ul>
			<li class="app-nav-group<?= $is_master ? ' is-open has-active' : '' ?>">
				<button type="button" class="app-nav-link" data-nav-toggle>
					<i class="fa fa-database"></i><span>Master Data</span><i class="fa fa-chevron-right app-caret"></i>
				</button>
				<ul class="app-subnav">
					<li><a href="<?= base_url() ?>barang"<?= $seg1 == 'barang' ? ' class="is-active"' : '' ?>>Barang</a></li>
					<li><a href="<?= base_url() ?>merk"<?= $seg1 == 'merk' ? ' class="is-active"' : '' ?>>Merk</a></li>
					<li><a href="<?= base_url() ?>karyawan"<?= $seg1 == 'karyawan' ? ' class="is-active"' : '' ?>>Karyawan</a></li>
					<li><a href="<?= base_url() ?>suplier"<?= $seg1 == 'suplier' ? ' class="is-active"' : '' ?>>Suplier</a></li>
					<li><a href="<?= base_url() ?>promo"<?= $seg1 == 'promo' ? ' class="is-active"' : '' ?>>Promo</a></li>
				</ul>
			</li>
			<li class="app-nav-group<?= $is_laporan ? ' is-open has-active' : '' ?>">
				<button type="button" class="app-nav-link" data-nav-toggle>
					<i class="fa fa-bar-chart"></i><span>Laporan</span><i class="fa fa-chevron-right app-caret"></i>
				</button>
				<ul class="app-subnav">
					<?php
					// Result pages (pjual, pbeli, ...) belong to their form's menu entry
					$active_slug = $seg2;
					if (in_array($seg2, array('pjual', 'pjualprint'))) {
						$active_slug = 'jual';
					}
					elseif (in_array($seg2, array('pbeli', 'pbeliprint'))) {
						$active_slug = 'beli';
					}
					elseif ($seg2 == 'index') {
						$active_slug = '';
					}
					foreach ($laporan_items as $slug => $label) { ?>
					<li><a href="<?= base_url() ?>laporan<?= $slug !== '' ? '/' . $slug : '' ?>"<?= ($is_laporan && $active_slug === $slug) ? ' class="is-active"' : '' ?>><?= $label ?></a></li>
					<?php } ?>
				</ul>
			</li>
		</ul>

		<div class="app-nav-section">Analitik</div>
		<ul>
			<li class="app-nav-group<?= $is_ramal ? ' is-open has-active' : '' ?>">
				<button type="button" class="app-nav-link" data-nav-toggle>
					<i class="fa fa-line-chart"></i><span>Peramalan</span><i class="fa fa-chevron-right app-caret"></i>
				</button>
				<ul class="app-subnav">
					<li><a href="<?= base_url() ?>ramal"<?= ($seg1 == 'ramal' && $seg2 != 'index2') ? ' class="is-active"' : '' ?>>Ramal Stok</a></li>
					<li><a href="<?= base_url() ?>ramal/index2"<?= ($seg1 == 'ramal' && $seg2 == 'index2') ? ' class="is-active"' : '' ?>>Pengujian Ramal Stok</a></li>
					<li><a href="<?= base_url() ?>ramal_harga"<?= ($seg1 == 'ramal_harga' && $seg2 != 'index2') ? ' class="is-active"' : '' ?>>Ramal Harga</a></li>
					<li><a href="<?= base_url() ?>ramal_harga/index2"<?= ($seg1 == 'ramal_harga' && $seg2 == 'index2') ? ' class="is-active"' : '' ?>>Pengujian Ramal Harga</a></li>
				</ul>
			</li>
			<li>
				<a class="app-nav-link<?= $seg1 == 'sync' ? ' is-active' : '' ?>" href="<?= base_url() ?>sync">
					<i class="fa fa-refresh"></i><span>Sync Dashboard</span>
				</a>
			</li>
		</ul>
		<?php } ?>
	</nav>
</aside>
<div class="app-backdrop" data-nav-close></div>
<?php } ?>

<div class="app-main">
	<header class="app-topbar">
		<?php if ($has_sidebar) { ?>
		<button type="button" class="app-burger" data-nav-open aria-label="Buka menu" aria-controls="appSidebar">
			<i class="fa fa-bars"></i>
		</button>
		<?php } ?>
		<a class="app-topbar-brand" href="<?= base_url() ?>home">
			<img src="<?= base_url() ?>assets/logonew.png" alt=""/>
			<span>Toko Oli 711</span>
		</a>
		<div class="app-topbar-spacer"></div>

		<span class="app-clock" title="Waktu server">
			<i class="fa fa-clock-o"></i>
			<span id="server-time"
				data-epoch="<?php echo time(); ?>"
				data-utc-offset="<?php echo (int) date('Z'); ?>"
				data-sync-url="<?php echo base_url('home/server_time'); ?>"><?php echo date('H:i:s'); ?></span>
		</span>

		<div class="dropdown app-user">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="app-avatar"><?= htmlspecialchars(substr($username, 0, 1)) ?></span>
				<span class="app-user-name"><?= htmlspecialchars($username) ?></span>
				<i class="fa fa-angle-down text-muted"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<?php if ($is_admin) { ?>
				<li>
					<a href="<?= base_url() ?>admin/update/ADM00001"><i class="fa fa-user"></i> Profil Saya</a>
				</li>
				<li class="divider"></li>
				<?php } ?>
				<li>
					<a href="<?= base_url() ?>logout/logout"><i class="fa fa-sign-out"></i> Keluar</a>
				</li>
			</ul>
		</div>
	</header>

	<main class="app-content page-content">
