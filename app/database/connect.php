<?php

function db_conn(){
    //DBと接続
    try {
        $db_name = ''; //データベース名
        $db_id   = ''; //アカウント名
        $db_pw   = ''; //パスワード：MAMPは'root'
        $db_host = ''; //DBホスト
        $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}

?>
