<?php
include ('connect.php');
if ( $link !== false ) {
    $msg     = '';
    $err_msg = '';
 
    if ( isset( $_POST['send'] ) === true and $_SERVER['REQUEST_METHOD']==='POST') {
        $name     = $_POST['name']   ;
        $comment = $_POST['comment'];
        $thread = $_POST['thread'];
        $book = $_POST['book'];
        $time = $_POST['time'];
        

        if ( $name !== '' && $comment !== '') {
            $query = " INSERT INTO board ( "
                   . "    name , "
                   . "    comment, "
                   . "    thread, "
                   . "    book, "
                   . "    time "
                   . " ) VALUES ( "
                   . "'" . mysqli_real_escape_string( $link, $name ) ."', "
                   . "'" . mysqli_real_escape_string( $link, $comment ) . "', "
                   . "'" . mysqli_real_escape_string( $link, $thread ) . "',"
                   . "'" . mysqli_real_escape_string( $link, $book ) . "',"
                   . "'" . mysqli_real_escape_string( $link, $time ) . "' "
                   ." ) ";
            $res   = mysqli_query( $link, $query );
            
            if ( $res !== false ) {
                $msg = '書き込みに成功しました';
            }else{
                $err_msg = '書き込みに失敗しました';
            }
            header('Location:'.$url.'board.php?t='.$thread.'&b='.$book);//二重投稿対策
        }else{
            $err_msg = '名前とコメントを記入してください';
        }
    }
    if ( isset( $_GET['t'] ) === false ) {$_GET['t'] = "general";}
    if ( isset( $_GET['b'] ) === false ) {$_GET['b'] = "この掲示板についての議論";}
    $query  = 'SELECT id, name, comment, time FROM board WHERE thread="'.$_GET['t'].'" AND book="'.$_GET['b'].'" ORDER BY id ASC';
    $res    = mysqli_query( $link,$query );
    $data = array();
    while( $row = mysqli_fetch_assoc( $res ) ) {
        array_push( $data, $row);
    }

    $query2  = 'SELECT DISTINCT thread FROM board WHERE thread is not NULL AND book="'.$_GET['b'].'" ORDER BY thread DESC';
    $res2    = mysqli_query( $link,$query2 );  
    $data2 = array();
    while( $row = mysqli_fetch_assoc( $res2 ) ) {
        array_push( $data2, $row);
    }
    $data2 = array_reverse($data2);   
} else {
    echo "データベースの接続に失敗しました";
}
 
// データベースへの接続を閉じる
mysqli_close( $link );

//リンク
function url2link($body, $link_title = null){
    $pattern = '/(?<!href=")https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/';
    $body = preg_replace_callback($pattern, function($matches) use ($link_title) {
        $link_title = $link_title ?: $matches[0];
        return "<a href=\"{$matches[0]}\">$link_title</a>";
    }, $body);
    return $body;
}

?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
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
        <!--ここに記事の一覧を表示-->
        <div class="thread_list">
            <a href="main_page.php">ゼミ一覧へ</a><br>
            <br><br>
            <?php
                foreach( $data2 as $key => $val ){
                    echo '<a href="?t='.$val['thread'].'&b='.$_GET["b"].'">'.$val['thread'].'</a><br>';
                }
            ?>
            <br><br>
            新規作成<br>
            <form method="get" action="">
            p<input type="text" name="p" size=1><br>式(<input type="text" name="ex" size=2>)<br>
            <input type="hidden" name="b" value="<?php echo $_GET['b']; ?>">
            <input type="submit" name="create" value="作成"></form>
        </div><!-- /thread_list-->

        <!--スマホ用-->
        <div class="mthread_list">
            <a href="main_page.php">ゼミ一覧へ</a><br>
            <?php
                foreach( $data2 as $key => $val ){
                    echo '<a href="?t='.$val['thread'].'&b='.$_GET["b"].'">'.$val['thread'].'</a>/';
                }
            ?>
            <br>
            新規作成
            <form method="get" action="">
            p<input type="text" name="p" size=1>式(<input type="text" name="ex" size=2>)
            <input type="hidden" name="b" value="<?php echo $_GET['b']; ?>">
            <input type="submit" name="create" value="作成"></form>
        </div><!-- /thread_list-->
        <div class="main">

            <!-- 投稿画面 -->
            <?php if(isset($_GET['create'])===true){
                    //新規作成時の警告
                    //ここのデバッグに注意
                    $t = 'p'.$_GET['p'].'('.$_GET['ex'].')';
                    $_GET['t']= $t;
                    echo "最初のコメントをつけてください。<br>コメントを付けない場合、スレッド".$t."の作成は中止されます<br>";
                    echo '<form method="post" action="?t='.$t.'&b='.$_GET['b'].'">';}
                else{
                    echo '<form method="post" action="">';
                }
                ?>
                名前<br><input type="text" name="pre_name" value="<?php echo $_POST['pre_name'];?>"><br>
                <textarea name="pre_comment" rows="10" cols="60"><?php echo $_POST['pre_comment'];?></textarea>
                <input type="hidden" name="t" value="<?php echo $_GET['t']; ?>">
                <input type="hidden" name="b" value="<?php echo $_GET['b']; ?>">
                <br>
               <input type="submit" name="pre_send" value="プレビュー" />
            </form><!--前はphpの中に入っているので注意-->

            <!-- 投稿確認 -->
            <form method="post" action="">
                <?php
                    if ( isset( $_POST['pre_send'] ) === true ) {
                        echo '<div id="name">'.$_POST['pre_name'].'</div><dd>'.nl2br($_POST['pre_comment']).'</dd><br>';
                        echo '<input type="hidden" name="name" value="'.$_POST['pre_name'].'">';
                        echo '<input type="hidden" name="comment" value="'.$_POST['pre_comment'].'">';
                        echo '<input type="hidden" name="thread" value="'.$_POST['t'].'">';
                        echo '<input type="hidden" name="book" value="'.$_POST['b'].'">';
                        echo '<input type="hidden" name="time" value="'.date("Y/m/d H:i").'">';
                        echo '<input type="submit" name="send" value="書き込む" />';
                    }
                ?>
        	</form>

            
            <!-- ここに、書き込まれたデータを表示する -->
            <?php
                if (isset($_GET['create'])===false){
                    if ( $msg     !== '' ) echo '<p>' . $msg . '</p>';
                    if ( $err_msg !== '' ) echo '<p style="color:#f00;">' . $err_msg . '</p>';
                    $i=1;
                    echo '<div id="thread"><a href="main_page.php">ゼミ一覧</a>＞'.$_GET['b'].'＞'.$_GET['t'].'</div><br>';

                    foreach( $data as $key => $val ){
                        //時間データがあれば表示
                        $str = ($val['time'] == 'none') ? '':', '.$val['time'];
                        echo '<div id="name">'.$i.'. '.$val['name'].$str.'</div><dd>'. url2link(nl2br($val['comment'])) . '</dd><br><br>';
                        $i++;
                        }
                    }
                ?>
	    </div><!-- /main -->
        <!--ここにLaTeXの使用方法を記載-->
        <div class="usage">
            $\LaTeX$の使い方<br>
            数式は\$...\$または<br><pre>\[...\]</pre>で囲ってください<br>
            以下に数式の例を示します<br><br>
            分数<br>
            \frac{1}{3}→$\frac{1}{3}$<br>
            指数・添え字<br>
            \a_{m}^{n}→$a_{m}^{n}$<br>
            初等関数<br>
            \sin x, \sqrt x→$\sin x, \sqrt x$<br>
            定数<br>
            \pi, e→$\pi, e$<br>
            積分記号<br>
            \int_{-\infty}^{\infty}→$\int_{-\infty}^{\infty}$<br>
            総和<br>
            \sum_{i=1}^{n}$\sum_{i=1}^{n}$<br>
            ギリシャ文字<br>
            \alpha,\beta,\gamma→$\alpha,\beta,\gamma$

        </div>
    </div><!-- /wrap --> 
    </body>
</html>