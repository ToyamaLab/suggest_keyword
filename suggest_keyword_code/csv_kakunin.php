<?php
header('Content-Type: text/html; charset=UTF-8');
?>

<?php
session_start();
if(isset($_POST["word"])){
  $word=$_POST["word"];
}else if(isset($_GET['word'])){
  $word=$_GET["word"];
}

if(isset($_GET["category"])){
  $category=$_GET["category"];
}else if(isset($_POST["category"])){
  $category=$_POST["category"];
}

if(isset($_GET["number"])){
  $number=$_GET["number"];
}else{
  $number=0;
}

?>

<html><head>
  <title>その他に当たるものを表示</title>
</head>
<body>

<form action="csv_kakunin.php" method="post">
<?php
echo '<br><br><br><br><br>';
if(isset($word)){
  echo "<input type=\"search\" name=\"word\" value=$word>";
  echo "と完全一致の単語";
}else{
 echo "<input type=\"search\" name=\"word\">";
 echo "と完全一致の単語";
}
echo "<input type=\"hidden\" name=\"number\" value=0>";
?>
<br>
<input type="submit" name="submit" value="検索">
</form>

<?php
if(isset($word)){
  if($word != "入力してください"){
    $str = "/home/toyama/yuna/local/python/bin/python3 test_7.py " . $word;
    $re = shell_exec($str);
    $python = explode("\n", $re);

    $command="/home/toyama/yuna/local/python/bin/python3 test8.py " . $word;
    exec($command,$python_2);
    $comma_separated = implode(",", $python_2);

    if(isset($category)){
    }else{
      if($comma_separated!=''){
        $command="/home/toyama/yuna/local/python/bin/python3 test_4.py " . $comma_separated . " Thing";
        exec($command,$python_3);
        $category=$python_3[0];
      }
    }
  }
}
$constr = "host=localhost port=5432 dbname=yuna user=yuna password=yuna";
$conn = pg_connect($constr);
$result = pg_query($conn, "SELECT * FROM cover_category;");
$rows = pg_num_rows($result);

$num=0;
$category_new=array();
$category_match_new=array();
$count_new=array();
for($j=0; $j<$rows; $j++){  //結果行数分のループ
  $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
  $category_new[$j]=$rows['category'];
  $category_match_new[$j]=$rows['category_match'];
  $count_new[$j]=$rows['count'];
  $num++;
}

$check = 'No';
$category_match_0=array();
$category_match_1=array();
$count=array();

$num_0=0;
$num_1=0;

$str = "/home/toyama/yuna/local/python/bin/python3 test8.py " . $word;
$re = shell_exec($str);
$output = explode("\n", $re);
$output_2 = implode(',', $output);

$str = "/home/toyama/yuna/local/python/bin/python3 csv_test.py " . $output_2;
$re = shell_exec($str);
$output_change = explode("\n", $re);

if(isset($output_change[0])){
  for($i=0;;$i++){
    if(isset($python[$i])){
      $python[$i] = mb_convert_encoding($python[$i], "UTF-8", "auto");
      for($j=0; $j<$num; $j++){  //結果行数分のループ
        if($output_change[0]==$category_new[$j] && $python[$i]==$category_match_new[$j]){
          $count[$num_0] = $count_new[$j]+1;
          $category_match_0[$num_0]=$python[$i];
          $num_0++;
          $check = 'Yes';
        }
      }
      if($check=='No'){
        $category_match_1[$num_1]=$python[$i];
        $num_1++;
      }
      $check = 'No';
    }else{
      break;
    }
  }
}
$count_0=implode(",", $count);
$comma_separated_0 = implode(",", $category_match_0);
$comma_separated_1 = implode(",", $category_match_1);

if(isset($word) && isset($output_change[0]) && isset($number)){
  if(isset($count_0) && isset($comma_separated_0)){
    if($number==0){
      header("Location: csv_kakunin_assist.php?word=".$word.'&category='.$output_change[0].'&count='.$count_0.'&category_match='.$comma_separated_0.'&number='.$number);
    }
  }
  if(isset($comma_separated_1)){
    if($number==1){
      header("Location: csv_kakunin_assist.php?word=".$word.'&category='.$output_change[0].'&count='.$number.'&category_match='.$comma_separated_1.'&number='.$number);
    }
  }
}

#if($number==2){
  for($i=0;;$i++){
    if(isset($python[$i])){
      if((strpos($python[$i], '?') === false) && (strpos($python[$i], 'wikiPage') === false)  && (strpos($python[$i], 'birth') === false) && (strpos($python[$i], '画像') === false) && (strpos($python[$i], 'image') === false) && (strpos($python[$i], '写真') === false) && (strpos($python[$i], 'death') === false)){
        if($python[$i]!='date' && $python[$i]!='section' && $python[$i]!='width' && $python[$i]!='thumbnail' && $python[$i]!='id' && $python[$i]!='width'){
          echo "<a href=\"https://www.google.com/search?q=$word+$python[$i]\" target=\"_blank\" rel=\"nofollow noopener\">$python[$i]</a><br>";
        }
      }
    }else{
      break;
    }
  }
#}

pg_close($conn);

?>

</body></html>
