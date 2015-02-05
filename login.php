<link rel="stylesheet" type="text/css" href="style.css" media="all">

<?php
require('dbconnect.php');

session_start();

if(isset($_COOKIE['email'])){
	if($_COOKIE['email'] != ''){
		$_POST['email'] = $_COOKIE['email'];
		$_POST['password'] = $_COOKIE['password'];
		$_POST['save'] = 'on';
	}
}

if(!empty($_POST)){
	if($_POST['email'] != '' && $_POST['password'] != ''){
		$sql = sprintf('SELECT * FROM members WHERE email="%s" AND password="%s"' ,
						mysqli_real_escape_string($db , $_POST['email']),
						mysqli_real_escape_string($db , sha1($_POST['password']))
		);
		$record = mysqli_query($db , $sql) or die(mysqli_error($db));
		var_dump($record);
		if($table = mysqli_fetch_assoc($record)){
			//ログイン成功時
			$_SESSION['id'] = $table['id'];
			$_SESSION['time'] = time();

			//ログイン情報を記録する
			if($_POST['save'] == 'on'){
				setcookie('email' , $_POST['email'] , time()+60*60*24*14);
				setcookie('password' , $_POST['password'], time()+60*60*24*14);
			}
			header('location: http://' . $_SERVER['HTTP_HOST'] . '/Twitter_bbs/index.php');
			exit();
		} else {
			$error['login'] = 'failed';
		}
	} else {
		$error['login'] = 'blank';
	}
}

?>

<div id="lead">
<p>メールアドレスとパスワードを記入してログインして下さい。</p>
<p>入会手続きがまだの方はこちらからどうぞ。</p>
<p>&raquo;<a href="join/index.php">入会手続きをする</a></p>
</div>
<form action="" method="post">
	<dl>
		<dt>メールアドレス</dt>
		<dd>
		<?php if(isset($_POST['email'])): ?>
		<input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email']); ?>" />
		<?php endif; ?>
		<?php if(!isset($_POST['email'])): ?>
		<input type="text" name="email" size="35" maxlength="255" />
		<?php endif; ?>

		<?php if(isset($error['login'])): ?>
			<?php if($error['login'] == 'blank'): ?>
			<p class="error">*メールアドレスとパスワードをご記入下さい</p>
			<?php endif; ?>
			<?php if($error['login'] == 'failed') :?>
			<p class="error">*ログインに失敗しました。正しくご記入下さい。</p>
			<?php endif; ?>
		<?php endif; ?>
		</dd>

		<dt>パスワード</dt>
		<dd>
		<?php if(isset($_POST['password'])): ?>
		<input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password']); ?>" />
		<?php endif; ?>
		<?php if(!isset($_POST['password'])): ?>
		<input type="password" name="password" size="35" maxlength="255" />
		<?php endif; ?>
		</dd>
		<dd>
		<input id="save" type="checkbox" name="save" value="on">
			<label for="save">次回からは自動的にログインする</label>
		</dd>
	</dl>
	<div><input type="submit" value="ログインする" /></div>
</form>
