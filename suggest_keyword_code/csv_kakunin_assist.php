<?php
header('Content-Type: text/html; charset=UTF-8');
?>


<?php
session_start();
$word=$_GET['word'];
$category=$_GET["category"];
$category_match=$_GET["category_match"];
$count=$_GET["count"];
$number=$_GET["number"];

if($number==0){
  if($category_match!='' || $count!=''){
    $command="/home/toyama/yuna/local/python/bin/python3 bunkai.py " . $category_match;
    exec($command,$category_match);
    $command="/home/toyama/yuna/local/python/bin/python3 bunkai.py " . $count;
    exec($command,$count);
  }
}
if($number==1){
  if($category_match!=''){
    $command="/home/toyama/yuna/local/python/bin/python3 bunkai.py " . $category_match;
    exec($command,$category_match);
  }
}

?>

<?php
  $constr = "host=localhost port=5432 dbname=yuna user=yuna password=yuna";
  $conn = pg_connect($constr);
  $result = pg_query($conn, "SELECT * FROM cover_category;");
  $rows = pg_num_rows($result);

  if($number==0){
    $query="update cover_category set count=$1 where category=$2 and category_match=$3";
    $result = pg_prepare($conn, "my_query", $query);#エラー出てます、重複禁止prepareは
    for($i=0;;$i++){
      if(isset($category_match[$i]) && isset($count[$i])){
        if($category_match[$i]!='' || $count[$i]!=''){
          $category_match[$i] = mb_convert_encoding($category_match[$i], "UTF-8", "auto");
          $result = pg_execute($conn, "my_query", array($count[$i], $category, $category_match[$i]));
        }
      }else{
        break;
      }
    }
  }
  if($number==1){
    $query="insert into cover_category(category,category_match,count) values ($1,$2,$3)";
    $result = pg_prepare($conn, "my_query", $query);
    for($i=0;;$i++){
      if(isset($category_match[$i])){
        if($category_match[$i]!=''){
          $category_match[$i] = mb_convert_encoding($category_match[$i], "UTF-8", "auto");//結局？になるだけなので、エラーを消しただけで解決してない
          if((strpos($category_match[$i], '?') === false)){//文字列を含まない
            $result = pg_execute($conn, "my_query", array($category,$category_match[$i],$number));
          }
        }
      }else{
        break;
      }
    }
  }

  $number+=1;

  if($number==1){
    header("Location: csv_kakunin.php?word=".$word.'&category='.$category.'&number='.$number);
  }else{
    header("Location: csv_kakunin.php?word=".$word."&number=".$number);
  }

  pg_close($conn);
?>
