<?php
if(isset($_GET["word"])){
  $word=$_GET["word"];
}else if(isset($_POST["word"])){
  $word=$_POST["word"];
}
if(isset($_GET["word_2"])){
  $word_2=$_GET["word_2"];
}else if(isset($_POST["word_2"])){
  $word_2=$_POST["word_2"];
}
if(isset($_GET["number"])){
  $number=$_GET["number"];
}else if(isset($_POST["number"])){
  $number=$_POST["number"];
}
?>
<?php
  $constr = "host=localhost port=5432 dbname=yuna user=yuna password=yuna";
  $conn = pg_connect($constr);
  $result = pg_query($conn, "SELECT * FROM count;");
  $rows = pg_num_rows($result);

  for($i=0; $i<$rows; $i++){  //結果行数分のループ
    $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
    if($number==$rows['id']){
      $count_new=$rows['count']+1;
      $query="update count set count=$1 where id=$2";
      $result = pg_prepare($conn, "my_query", $query);
      $result = pg_execute($conn, "my_query", array($count_new, $number));
      if($number==4){
        $url='http://trezia.db.ics.keio.ac.jp/yuna/suggest_word/sotsuron/sample_3.php?word='.$word;
        header("Location:$url");
        exit;
      }else{
        $url='http://trezia.db.ics.keio.ac.jp/yuna/suggest_word/sotsuron/sample_4.php?word='.$word.'&word_2='.$word_2;
        header("Location:$url");
        exit;
      }
    }
  }

  //DBとの接続を閉じる
  pg_close($conn);
?>
