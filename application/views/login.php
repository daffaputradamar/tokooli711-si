<?php
$error = isset($error) ? $error : '';
$username = isset($username) ? $username : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8"/>
	<title>Masuk &middot; Toko Oli 711</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta name="theme-color" content="#4d42c4"/>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;650;700&display=swap" rel="stylesheet">
	<link href="<?= base_url() ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= base_url() ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?= asset_url('assets/css/app.css') ?>" rel="stylesheet" type="text/css"/>
	<link rel="shortcut icon" href="<?= base_url() ?>assets/logonew.png"/>
	<style>
		.login-shell {
			display: grid;
			grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
			min-height: 100vh;
		}

		.login-brand {
			position: relative;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 48px;
			overflow: hidden;
			background: linear-gradient(150deg, #5b4fd9 0%, #4d42c4 45%, #3b6fd0 100%);
			color: #fff;
		}

		.login-brand::after {
			content: "";
			position: absolute;
			right: -120px;
			bottom: -160px;
			width: 460px;
			height: 460px;
			border-radius: 50%;
			background: radial-gradient(circle, rgba(63, 193, 201, .45), rgba(63, 193, 201, 0) 70%);
		}

		.login-logo {
			position: relative;
			z-index: 1;
			width: 220px;
			max-width: 70%;
		}

		.login-pitch {
			position: relative;
			z-index: 1;
			max-width: 420px;
		}

		body.app .login-pitch h1 {
			margin: 0 0 12px;
			color: #fff;
			font-size: 34px;
			font-weight: 700;
			line-height: 1.15;
		}

		.login-pitch p {
			margin: 0;
			color: rgba(255, 255, 255, .8);
			font-size: 15px;
		}

		.login-foot {
			position: relative;
			z-index: 1;
			color: rgba(255, 255, 255, .6);
			font-size: 12.5px;
		}

		.login-panel {
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 32px 20px;
			background: var(--canvas);
		}

		.login-card {
			width: 100%;
			max-width: 380px;
		}

		body.app .login-card h2 {
			margin: 0 0 6px;
			font-size: 24px;
			font-weight: 700;
		}

		.login-card .login-sub {
			margin: 0 0 24px;
			color: var(--muted);
		}

		.login-input {
			position: relative;
		}

		.login-input > .fa {
			position: absolute;
			top: 50%;
			left: 14px;
			transform: translateY(-50%);
			color: var(--muted);
		}

		body.app .login-input > .form-control {
			height: 46px;
			padding-left: 40px;
		}

		.login-toggle {
			position: absolute;
			top: 50%;
			right: 6px;
			width: 36px;
			height: 36px;
			border: 0;
			border-radius: 8px;
			background: none;
			color: var(--muted);
			transform: translateY(-50%);
		}

		.login-toggle:hover {
			background: var(--line-2);
			color: var(--ink);
		}

		body.app .login-card .btn-lg {
			height: 48px;
			margin-top: 8px;
		}

		.login-mobile-logo {
			display: none;
		}

		@media (max-width: 860px) {
			.login-shell {
				grid-template-columns: 1fr;
			}

			.login-brand {
				display: none;
			}

			.login-panel {
				align-items: flex-start;
				min-height: 100vh;
				padding-top: 0;
				background:
					linear-gradient(160deg, #5b4fd9 0%, #4d42c4 50%, #3b6fd0 100%) top / 100% 240px no-repeat,
					var(--canvas);
			}

			.login-mobile-logo {
				display: block;
				width: 180px;
				margin: 40px auto 28px;
			}

			.login-card {
				padding: 24px 20px;
				border-radius: 16px;
				background: #fff;
				box-shadow: var(--shadow);
			}
		}
	</style>
</head>
<body class="app">
<div class="login-shell">
	<section class="login-brand" aria-hidden="true">
		<img class="login-logo" src="<?= base_url() ?>assets/logonew%20white.png" alt=""/>
		<div class="login-pitch">
			<h1>Kasir, stok, dan laporan bengkel dalam satu tempat.</h1>
			<p>Masuk untuk mulai melayani pelanggan hari ini.</p>
		</div>
		<div class="login-foot">&copy; <?= date('Y') ?> Toko Oli 711</div>
	</section>

	<main class="login-panel">
		<div>
			<img class="login-mobile-logo" src="<?= base_url() ?>assets/logonew%20white.png" alt="Toko Oli 711"/>
			<div class="login-card">
				<h2>Masuk</h2>
				<p class="login-sub">Gunakan akun yang diberikan admin toko.</p>

				<?php if ($error != '') { ?>
				<div class="alert alert-danger" role="alert">
					<i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
				</div>
				<?php } ?>

				<?php echo form_open('login/index', 'class="login-form" autocomplete="off"'); ?>
					<div class="form-group">
						<label for="username">Username</label>
						<div class="login-input">
							<i class="fa fa-user"></i>
							<input class="form-control" type="text" id="username" name="username" autocomplete="username"
								autocapitalize="none" spellcheck="false" required
								value="<?= htmlspecialchars($username) ?>"<?= $username == '' ? ' autofocus' : '' ?>/>
						</div>
					</div>
					<div class="form-group">
						<label for="psswd">Password</label>
						<div class="login-input">
							<i class="fa fa-lock"></i>
							<input class="form-control" type="password" id="psswd" name="psswd" autocomplete="current-password" required
								<?= $username != '' ? ' autofocus' : '' ?>/>
							<button type="button" class="login-toggle" id="togglePassword" aria-label="Tampilkan password">
								<i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
					<button type="submit" name="login" value="Login" class="btn btn-primary btn-lg btn-block">
						Masuk <i class="fa fa-arrow-right"></i>
					</button>
				</form>
			</div>
		</div>
	</main>
</div>

<script>
	(function () {
		var toggle = document.getElementById('togglePassword');
		var input = document.getElementById('psswd');
		toggle.addEventListener('click', function () {
			var show = input.type === 'password';
			input.type = show ? 'text' : 'password';
			toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
			toggle.firstElementChild.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
			input.focus();
		});
	})();
</script>
</body>
</html>
