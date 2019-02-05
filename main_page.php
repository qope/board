<?php
	include('connect.php');
	if ( $link !== false ) {
		if (isset($_POST['create'])===true and $_SERVER['REQUEST_METHOD']==='POST'){
        	$give = 'INSERT INTO board (name, comment, thread, book) VALUES ("Bot", "全般的な質問はここに、特定の式についての質問は左のフォームからページ数、式番号をタイトルとするスレッドを立ち上げてください", "general", "'.$_POST['book'].'")';

        	$res = mysqli_query($link, $give);
        	if ( $res !== false ) {
                    $msg = '掲示板の作成に成功しました';
                }else{
                    $err_msg = '掲示板を作成できませんでした';
                }
            header('Location:'.$url.'main_page.php');//二重投稿対策
    	}
		$query  = 'SELECT DISTINCT book FROM board WHERE book is not NULL ORDER BY id ASC';
    	$res    = mysqli_query( $link,$query );  
    	$data = array();
		while( $row = mysqli_fetch_assoc( $res ) ) {
        	array_push( $data, $row);
    		}


    	//最新のコメントを所得
		$query2  = 'SELECT id, name, book, thread, comment, time FROM board ORDER BY id desc limit 5';
    	$res2    = mysqli_query($link, $query2);
    	$data2 = array();
    	while( $row = mysqli_fetch_assoc( $res2 ) ) {
        	array_push( $data2, $row);
    	}
    
	} else {
    echo "データベースの接続に失敗しました";
	}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="style.css">
		<script type="text/x-mathjax-config">
        MathJax.Hub.Config({
          tex2jax: {
            inlineMath: [['$','$'], ['\\(','\\)']],
            processEscapes: true
          },
          CommonHTML: { matchFontHeight: false }
        });
        </script>
        <script async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-AMS_CHTML"></script>
	</head>
	<body>
    <div class="wrap">
		<div class="header">
			ゼミ用掲示板
        </div><!-- /header -->
       	<div class="wrap">
            <div class="main2">
                <div class="book_list">
               		ゼミ一覧<br>
               		<?php
               		if ( $msg     !== '' ) echo '<p>' . $msg . '</p>';
        	                    if ( $err_msg !== '' ) echo '<p style="color:#f00;">' . $err_msg . '</p>';
                        foreach( $data as $key => $val ){
                            echo '<a href="board.php?b='.$val['book'].'">'.$val['book'].'</a><br>';
                        }
            		?>
                    <br>
                    <br>
                </div>
                
                <!--タイトル-->
                <div class="create_book">
                <form method="post" action="">
    	            新規作成<br>
                    <input type="text" name="book" class="book_title"><br>
    	            <input type="submit" name="create" value="作成">
    	        </form>
            	</div> 

                <!--コメント-->
            	<div class = "latest">
	            	新着コメント<br>
	            	<?php
	            	foreach( $data2 as $key => $val ){
	                    //時間データがあれば表示
	                    $str = ($val['time'] == 'none') ? '':', '.$val['time'];
	                    echo '<a href="board.php?b='.$val['book'].'&t='.$val['thread'].'">'.$val['name'].$str.'</a><br>'.'<dd>'.$val['comment']. '</dd>';
	                    $i++;
	                }?>

            	</div>
            </div>
            
    	</div>
    </div>
    </body>
</html>


