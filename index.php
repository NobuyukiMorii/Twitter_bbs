<link rel="stylesheet" type="text/css" href="style.css" media="all">

<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
  //ログインしている
  $_SESSION['time'] = time();

  $sql = sprintf('SELECT * FROM members WHERE id=%d' , mysqli_real_escape_string($db , $_SESSION['id']));
  $record = mysqli_query($db , $sql) or die(mysql_error($db));
  $member = mysqli_fetch_assoc($record);
} else {
  //ログインしていない
  header('location: http://' . $_SERVER['HTTP_HOST'] . '/Twitter_bbs/login.php');
  exit();
}
//投稿を記録する
if(!empty($_POST)){
  if($_POST['message'] != ''){
    $sql = sprintf('INSERT INTO posts SET member_id=%d, message="%s" , reply_post_id=%d ,created=NOW()',
                    mysqli_real_escape_string($db, $member['id']) ,
                    mysqli_real_escape_string($db, $_POST['message']),
                    mysqli_real_escape_string($db, $_POST['reply_post_id'])
    );
    mysqli_query($db ,$sql) or die(mysqli_error($db));
    header('location: http://' . $_SERVER['HTTP_HOST'] . '/Twitter_bbs/index.php');
    exit();
  }
}
//投稿を取得する
if(isset($_REQUEST['page'])){
  $page = $_REQUEST['page'];
} else {
  $page = 1;
}

if(isset($page)){
  if($page == ''){
    $page = 1;
  }
  $page = max($page , 1);
}

//最終ページを取得する
$sql = 'SELECT COUNT(*) AS cnt FROM posts';
$recordSet = mysqli_query($db, $sql);
$table = mysqli_fetch_assoc($recordSet);
$maxPage = ceil($table['cnt'] / 5);
if(isset($page)){
  $page = min($page , $maxPage);
} else {
  $page = min(1 , 1);
}

$start = ($page -1) * 5;
$start = max(0 , $start);

$sql = sprintf('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT %d,5',$start);
$posts = mysqli_query($db , $sql) or die(mysqli_error($db));

//返信の場合
if(isset($_REQUEST['res'])){
  $sql = sprintf('SELECT m.name, m.picture, p.* FROM members m , posts p WHERE m.id=p.member_id AND p.id=%d ORDER BY p.created DESC' ,
                  mysqli_real_escape_string($db, $_REQUEST['res'])
  );
  $record = mysqli_query($db ,$sql) or die(mysqli_error($db));
  $table = mysqli_fetch_assoc($record);
  $message = '@' . $table['name'] . ' ' . $table['message'];
}

//htmlspecialcharsのショートカット
function h($value){
  return htmlspecialchars($value , ENT_QUOTES , 'UTF-8');
}

//本文内のURLにリンクを設定します
function makeLink($value){
  return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>' , $value);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<meta http-equive="Content-Type" content="text/html"; charset="UTF-8" />
<title>ひとこと掲示板</title>
<head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
    <div style="text-align:right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php echo h($member['name'])?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5">
            <?php if(isset($message)): ?>
              <?php  echo h($message); ?>
            <?php endif; ?>
          </textarea>
          <?php if(isset($_REQUEST['res'])): ?>
            <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
          <?php endif; ?>
        </dd>
      </dl>
      <div>
        <input type="submit" value="投稿する" />
      </div>
    </form>

<?php
while($post = mysqli_fetch_assoc($posts)):
?>

  <div class="msg">
  <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']) ?>" />
  <p><?php echo makeLink(h($post['message'])); ?>
    <span class="name">
    (<?php echo h($post['name']); ?>)
    </sapn>
  [<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]
  </p>

  <p class="day"><a href="view.php?id=<?php echo h($post['id'])?>" ><?php echo h($post['created']) ;?></a>
  <?php 
  if($post['reply_post_id'] > 0):
  ?>
  <a href="view.php?id=<?php echo htmlspecialchars($post['reply_post_id'] , ENT_QUOTES , 'UTF-8'); ?>" >
  返信元のメッセージ
  </a>
  <?php
  endif;
  ?>
  <?php
  if($_SESSION['id'] === $post['member_id']):
  ?>
  [<a href="delete.php?id=<?php echo h($post['id']) ;?>" style="color: #F33;">削除</a>]
  <?php
  endif;
  ?>
  </p>
  </div>

<?php
endwhile;
?>

  <ul class="paging">

  <?php
  if(!isset($page)) {
    echo '<li>前のページへ</li>';
  } elseif($page <= 1){
    echo '<li>前のページへ</li>';
  } else {
    echo '<li><a href="index.php?page=';
    echo $page - 1;
    echo '">前のページへ</a></li>';
  }
  ?>

  <?php
  if(!isset($page)) {
    echo '<li>次のページへ</li>';
  } elseif($page >= $maxPage){
    echo '<li>次のページへ</li>';
  } else {
    echo '<li><a href="index.php?page=';
    echo $page + 1;
    echo '">次のページへ</a></li>';
  }
  ?>

  </ul>

  </div>
  <div id="foot">
    <p><img src="moritter.png" width="300" alt="(C) Nobutyuki Morii" /></p>
  </div>
</div>
</body>

</html>