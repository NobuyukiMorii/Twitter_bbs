<?php
session_start();

//セッション情報を削除
$_SESSION = array();
if(ini_get("session.use_cookies")){
	$params = session_get_cookie_params();
	setcookie(session_name(), '' , time() - 4200,
		$params["path"] , $params["domain"],
		$params["secure"], $params["httponly"]
	);
}
session_destroy();

//Cookieも同様に削除
setcookie('email' , '' , time()-3600);
setcookie('password' , '' , time()-3600);

header('Location: login.php');
exit();
?>