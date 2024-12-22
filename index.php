<?php

//データベース接続
include_once("./app/database/connect.php");
$pdo = db_conn();

//配列の初期化
$comment_array = array();

//アンケートデータをテーブルから取得してくる
$sql = "SELECT * FROM gs_bm_table";
$statement = $pdo->prepare($sql);
$statement->execute();

$comment_array = $statement;


//書き込みボタン押下した際の挙動
if (isset($_POST["submitButton"])) {

    //配列の初期化
    $error_message = array();
    $escaped = array();

    //パリテーションチェック 名前入力チェック
    if (empty($_POST["username"])) {
        $error_message["username"] = "名前を入力してください";
    } else {
        //エスケープ処理
        $escaped["username"] = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
    }

    //パリテーションチェック 書籍名入力チェック
    if (empty($_POST["bookname"])) {
        $error_message["bookname"] = "書籍名を入力してください";
    } else {
        //エスケープ処理
        $escaped["bookname"] = htmlspecialchars($_POST["bookname"], ENT_QUOTES, "UTF-8");
    }

    //パリテーションチェック URL入力チェック
    if (empty($_POST["urltext"])) {
        $error_message["urltext"] = "URLを入力してください";
    } else {
        //エスケープ処理
        $escaped["urltext"] = htmlspecialchars($_POST["urltext"], ENT_QUOTES, "UTF-8");
    }

    //パリテーションチェック　コメント入力チェック
    if (empty($_POST["comment"])) {
        $error_message["comment"] = "コメントを入力してください";
    } else {
        //エスケープ処理
        $escaped["comment"] = htmlspecialchars($_POST["comment"], ENT_QUOTES, "UTF-8");
    }

    //
    if (empty($error_message)) {
        $post_date = date("Y-m-d H:i:s");

        $pdo->beginTransaction();

        try {
            $sql = "INSERT INTO `gs_bm_table` (`username`,`bookname`,`url`,`comment`,`date`)
                            VALUES(:username, :bookname, :urltext, :comment, :date);";

            $statement = $pdo->prepare($sql);

            //値をセットする。
            $statement->bindValue(":username", $escaped["username"], PDO::PARAM_STR);
            $statement->bindValue(":bookname", $escaped["bookname"], PDO::PARAM_STR);
            $statement->bindValue(":urltext", $escaped["urltext"], PDO::PARAM_STR);
            $statement->bindValue(":comment", $escaped["comment"], PDO::PARAM_STR);
            $statement->bindValue(":date", $post_date, PDO::PARAM_STR);


            $statement->execute();
            $pdo->commit();

            if ($statement === false) {
                //*** function化する！******\
                $error = $stmt->errorInfo();
                exit('SQLError:' . print_r($error, true));
            } else {
                //*** function化する！*****************
                header('Location: index.php');
                exit();
            }
        } catch (Exception $error) {
            $pdo->rollBack();
        }
    }
}

?>


<!-- HTML -->
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブックマーク</title>

    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <header>
        <h1 class="title">ブックマーク</h1>
        <hr>
    </header>

    <!-- パリデーションチェック エラー文吐き出し -->
    <?php if (isset($error_message)) : ?>
        <ul class="errorMessage">
            <?php foreach ($error_message as $error) : ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="threadWrapper">
        <div class="childWrapper">
            <div class="threadTitle">
                <span>【トピック】</span>
                <h1 id="articleTitle">これまで読んだ本の記録</h1>
            </div>

            <!-- コメントの塊 -->
            <section>
                <?php foreach ($comment_array as $comment) : ?>
                    <article>

                        <div class="wrapper">
                            <div class="userArea">
                                <span>名前：</span>
                                <p class="username"><?php echo $comment["username"]; ?></p>
                                <time>：<?php echo $comment["date"]; ?></time>
                            </div>

                            <div class="bookArea">
                                <span>書籍名：</span>
                                <p class="bookname"><?php echo $comment["bookname"]; ?></p>
                            </div>

                            <div class="urlArea">
                                <span>URL：</span>
                                <a href="<?php echo $comment["url"]; ?>">書籍のリンク</a>
                            </div>
                            <br>
                            <div>
                                <span>感想：</span>
                                <p class="comment"><?php echo $comment["comment"]; ?></p>

                            </div>

                            <div>
                                <?php 
                                $amznURL = $comment["url"];
                                $split1 = explode("/dp/", $amznURL);
                                $split2 = explode("/ref", $split1[1]);
                                $amznID = $split2[0];
                                $urlstr = "https://images.amazon.com/images/P/" . $amznID . ".09_SL30_.jpg";
                                ?>

                                <img src="<?php echo $urlstr ?>">
                                <p>------------------------------</p>
                            </div>
                        </div>
                    </article>
                <?php endforeach ?>
            </section>

            <!-- 入力欄 -->
            <form class="formWrapper" method="POST">
                <div>
                    <h3>ー入力欄ー</h3>
                </div>
                <div>
                    <input type="submit" value="書き込む" name="submitButton">
                    <label>名前：</label>
                    <input type="text" name="username">
                </div>

                <hr>

                <div>
                    <label>タイトル：</label>
                    <input type="text" name="bookname" size="30">
                </div>

                <div>
                    <label>紹介URL：</label>
                    <input type="text" name="urltext" size="58">
                </div>

                <div>
                    <textarea class="commentTextArea" name="comment"></textarea>
                </div>

            </form>

        </div>
    </div>

    <!-- jQueryの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>