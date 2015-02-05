<link rel="stylesheet" type="text/css" href="../style.css" media="all">

<?php
session_start();
require('../dbconnect.php');

if(!isset($_SESSION['join'])){
	header('Location: index.php');
	exit();
}

if(!empty($_POST)){
	$sql = sprintf('INSERT INTO members SET name="%s", email="%s", password="%s" , picture="%s" , created="%s"' , 
		mysqli_real_escape_string($db, $_SESSION['join']['name']) ,
		mysqli_real_escape_string($db, $_SESSION['join']['email']) ,
		mysqli_real_escape_string($db, sha1($_SESSION['join']['password'])) ,
		mysqli_real_escape_string($db, $_SESSION['join']['image']) ,
		date('Y-m-d H:i:s')
	);
	mysqli_query($db ,$sql) or die(mysqli_error($db));
	unset($_SESSION['join']);

	header('location: http://' . $_SERVER['HTTP_HOST'] . '/Twitter_bbs/join/thanks.php');
	exit();
}

?>

<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['name'] , ENT_QUOTES , 'UTF-8'); ?>
		</dd>
		<dt>メールアドレス</dt>
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['email'] , ENT_QUOTES , 'UTF-8'); ?>
		</dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
		<img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'] , ENT_QUOTES , 'UTF-8') ;?>" width="100" height="100" alt="" />
		</dd>
	</dl>
	<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
	<input type="submit" value="登録する" /></div>
</form>