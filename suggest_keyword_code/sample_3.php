<?php
$word=$_GET["word"];
?>

<?php
  $constr = "host=localhost port=5432 dbname=yuna user=yuna password=yuna";
  $conn = pg_connect($constr);
  $result = pg_query($conn, "SELECT * FROM suggest_word;");
  $rows = pg_num_rows($result);

  for($i=0; $i<$rows; $i++){  //結果行数分のループ
    $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
    if($word==$rows['primary_word']){
      if($word==$rows['suggest_word']){
        $count_new=$rows['count']+1;
        $query="update suggest_word set count=$1 where primary_word=$2 and suggest_word=$3";
        $result = pg_prepare($conn, "my_query", $query);
        $result = pg_execute($conn, "my_query", array($count_new, $word, $word));
        $url="https://www.google.com/search?q=$word";
        header("Location:$url");
        exit;
      }
    }
  }

  $query="insert into suggest_word(primary_word,suggest_word,count) values ($1,$2,$3)";
  $result = pg_prepare($conn, "my_query", $query);
  $count=1;
  $result = pg_execute($conn, "my_query", array($word, $word, $count));
  $url="https://www.google.com/search?q=$word";
  header("Location:$url");
  exit;

  //DBとの接続を閉じる
  pg_close($conn);
?>
