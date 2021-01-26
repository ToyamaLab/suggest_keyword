<?php
header('Content-Type: text/html; charset=UTF-8');
?>

<html><head>
 <link rel="stylesheet" type="text/css" href="css_test.css">
  <title>まとめ</title>
  <style type="text/css">

div.blocka {
/* 使えません */
}
div.green {
   height: auto;
   min-height: 400%;
   float: left;
   width: 25%;
   background-color: #AAFFCC;
}
div.blue {
   height: auto;
   min-height: 400%;
   float: left;
   width: 25%;
   background-color: #AACCFF;
}
div.yellow{
   height: auto;
   min-height: 400%;
   float: left;
   width: 25%;
   background-color: #FFFFBB;
}
div.pink {
   height: auto;
   min-height: 400%;
   float: left;
   width: 25%;
   background-color: #FFDDDD;
}

#menu div {
  position: relative;
}

.arrow_box {
  display: none;
  position: relative;
  padding: 8px;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  border-radius: 0px;
  background: #000;
  color: #ff0;
}

.arrow_box:after {
  position: absolute;
  bottom: 100%;
  left: 10%;
  width: 0;
  height: 0;
  margin-left: -10px;
  border: solid transparent;
  border-color: rgba(51, 51, 51, 0);
  border-bottom-color: #000;
  border-width: 10px;
  pointer-events: none;
  content: " ";
}

span:hover + p.arrow_box {
  display: block;
}

</style>
</head>
<body>

<?php
if(isset($_POST["word"])){
    $word=$_POST["word"];
}else if(isset($_GET["word"])){
    $word=$_GET["word"];
}

if(isset($_POST["word_2"])){
  $word_2=$_POST["word_2"];
}

if(empty($word) && !empty($word_2)){
  $word=$word_2;
  $word_2=null;
}

if(isset($_POST["comma_separated_sample_2"])){
  $comma_separated_sample_2 = $_POST["comma_separated_sample_2"];
}else{
  $comma_separated_sample_2 = '';
}

if(isset($_POST["category_popup"])){
  $category_popup=$_POST["category_popup"];
}else{
  $category_popup="Thing";
}
if(isset($_POST["comma_separated_popup"])){
  $comma_separated_popup = $_POST["comma_separated_popup"];
}else{
  $comma_separated_popup = '';
}
if(isset($_POST["php"])){
  $php = $_POST["php"];
}else{
  $php = 'DB';
}
if(isset($_POST["search"])){
  $search = $_POST["search"];
}else{
  $search = 'DBpedia';
}
?>

<?php

echo '<form action="matome_copy.php" method="post">';
$word = str_replace(" ", "_", $word);
if(isset($word)){
  echo "<input type=\"search\" name=\"word\" style=\"width:20em;height:2em\" value=$word>";
  $word = str_replace("_", " ", $word);
}else{
  echo "<input type=\"search\" name=\"word\" style=\"width:20em;height:2em\">";
}
if(isset($word_2)){
  echo "<input type=\"search\" name=\"word_2\" style=\"width:20em;height:2em\" value=$word_2>";
}else{
  echo "<input type=\"search\" name=\"word_2\" style=\"width:20em;height:2em\">";
}
echo '<input type="submit" name="submit" value="検索">';
echo '</form>';
?>

<?php
$constr = "host=localhost port=5432 dbname=yuna user=yuna password=yuna";
$conn = pg_connect($constr);
$result_suggest_word = pg_query($conn, "SELECT * FROM suggest_word;");
$rows_suggest_word = pg_num_rows($result_suggest_word);

$primary_word_already=array();
$suggest_word_already=array();
$count_already=array();
$num_suggest_word=0;

for($j=0; $j<$rows_suggest_word; $j++){  //結果行数分のループ
  $rows_suggest_word = pg_fetch_array($result_suggest_word, NULL, PGSQL_ASSOC);
  $primary_word_already[$j]=$rows_suggest_word['primary_word'];
  $suggest_word_already[$j]=$rows_suggest_word['suggest_word'];
  $count_already[$j]=$rows_suggest_word['count'];
  $num_suggest_word++;
}

if(isset($word) && isset($word_2)){
  if($word!='' && $word_2!=''){
    $url = 'http://trezia.db.ics.keio.ac.jp/yuna/suggest_word/sotsuron/count.php?word='.$word.'&word_2='.$word_2.'&number=5';
    header("Location:$url");
    exit;
  }
}

echo '<div class="pink">';

echo 'DB内の単語を取得';echo '<br><br>';

$suggest_sample_2=array();
$count_suggest=array();
$num=0;

if(isset($word)){
  for($i=0; $i<$num_suggest_word; $i++){
    $rows_suggest_word = pg_fetch_array($result_suggest_word, NULL, PGSQL_ASSOC);
    if((strpos($primary_word_already[$i], $word) !== false)){//文字列を含むか
      if($word==$primary_word_already[$i]){
        if($word==$suggest_word_already[$i]){
        }else{
          $suggest_sample_2[$num]=$suggest_word_already[$i];
          $count_suggest[$num]=$count_already[$i];
          $num++;
        }
      }else{
        $suggest_sample_2[$num]=$suggest_word_already[$i];
        $count_suggest[$num]=$count_already[$i];
        $num++;
      }
    }
  }

for($i=0; $i<($num-1); $i++){
  for($j=($num-1); $j>$i; $j--){
    if($count_suggest[$j-1]<$count_suggest[$j]){
      $temp = $count_suggest[$j-1];
      $count_suggest[$j-1] = $count_suggest[$j];
      $count_suggest[$j] = $temp;

      $str = $suggest_sample_2[$j-1];
      $suggest_sample_2[$j-1] = $suggest_sample_2[$j];
      $suggest_sample_2[$j] = $str;
    }
  }
}

  for($i=0;;$i++){
    if(isset($suggest_sample_2[$i])){
      $form="form4_" . $i;
      $java="javascript:" . $form . ".submit()";
      echo "<form style=\"display: inline\" action=\"count.php\" method=\"post\" name=$form>";
      echo "<input type=\"hidden\" name=\"word\" value=$word>";
      echo "<input type=\"hidden\" name=\"word_2\" value=$suggest_sample_2[$i]>";
      echo "<input type=\"hidden\" name=\"number\" value=1>";
      echo "<a href=$java>$suggest_sample_2[$i]</a>";
      echo "<br>";
      echo "</form>";
    }else{
      break;
    }
  }
}
?>
</div>


<div class="yellow">
<?php
echo '述語取得(完全一致のみ)';
#popup_test_3
echo '<form action="matome_copy.php" method="post">';
echo "<input type=\"hidden\" name=\"php\" value='picup'>";
echo "<input type=\"hidden\" name=\"comma_separated_sample_2\" value=$comma_separated_sample_2>";
echo "<input type=\"hidden\" name=\"comma_separated_popup\" value=$comma_separated_popup>";
$word = str_replace(" ", "_", $word);
echo "<input type=\"hidden\" name=\"word\" value=$word>";
$word = str_replace("_", " ", $word);
if(isset($word_2)){
  echo "<input type=\"hidden\" name=\"word_2\" value=$word_2>";
}
echo "<input type=\"hidden\" name=\"search\" value=\"Google\">";
echo '<input type="submit" name="submit" value="Google">';
echo '</form>';

echo '<form action="matome_copy.php" method="post">';
echo "<input type=\"hidden\" name=\"php\" value='picup'>";
echo "<input type=\"hidden\" name=\"comma_separated_sample_2\" value=$comma_separated_sample_2>";
echo "<input type=\"hidden\" name=\"comma_separated_popup\" value=$comma_separated_popup>";
$word = str_replace(" ", "_", $word);
echo "<input type=\"hidden\" name=\"word\" value=$word>";
$word = str_replace("_", " ", $word);
if(isset($word_2)){
  echo "<input type=\"hidden\" name=\"word_2\" value=$word_2>";
}
echo "<input type=\"hidden\" name=\"search\" value=\"DBpedia\">";
echo '<input type="submit" name="submit" value="DBpedia">';
echo '</form>';

$constr = "host=127.0.0.1 dbname=yuna user=yuna password=yuna";
$conn = pg_connect($constr);
$result = pg_query($conn, "SELECT * FROM cover_category;");
$rows = pg_num_rows($result);

$str = "/home/toyama/yuna/local/python/bin/python3 test8.py " . $word;
$re = shell_exec($str);
$output = explode("\n", $re);
$output_2 = implode(',', $output);

$str = "/home/toyama/yuna/local/python/bin/python3 csv_test.py " . $output_2;
$re = shell_exec($str);
$output_change = explode("\n", $re);

$category=array();
$category_match=array();
$count=array();
$num=0;

for($i=0; $i<$rows; $i++){
  $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
  array_push($category,$rows['category']);
  array_push($category_match,$rows['category_match']);
  array_push($count,$rows['count']);
  $num+=1;
}

if(!empty($output_change[0])){
  $category_set=array();
  $category_match_set=array();
  $count_set=array();
  $number=0;

  for($i=0; $i<$num; $i++){
    if(strpos($category[$i],$output_change[0]) !== false){
      $category_set[$number]=$category[$i];
      $category_match_set[$number]=$category_match[$i];
      $count_set[$number]=$count[$i];
      $number++;
    }
  }

  for($i=0; $i<($number-1); $i++){
    for($j=($number-1); $j>$i; $j--){
      if($count_set[$j-1]<$count_set[$j]){
        $temp = $count_set[$j-1];
        $count_set[$j-1] = $count_set[$j];
        $count_set[$j] = $temp;

        $str = $category_set[$j-1];
        $category_set[$j-1] = $category_set[$j];
        $category_set[$j] = $str;

        $str = $category_match_set[$j-1];
        $category_match_set[$j-1] = $category_match_set[$j];
        $category_match_set[$j] = $str;
      }
    }
  }

  $number_new=0;
  $val = array_unique($category_match_set);
  $category_match_set_new=array();

  for($i=0;$i<$number;$i++){
    if(isset($val[$i])){
      $category_match_set_new[$number_new]=$val[$i];
      $number_new++;
    }
  }
}

if(isset($word)){
  if($word != ""){
    $word = str_replace("_", " ", $word);
    $str = "/home/toyama/yuna/local/python/bin/python3 mouse_over.py \"" . $word . "\"";
    $re = shell_exec($str);
    $python = explode("\n", $re);
    $word = str_replace(" ", "_", $word);
  }

  $mouseover=array();
  $detail=array();
  $num_mouseover=0;

  for($i=0;;$i++){
    if(isset($python[$i])){
      $mouse_over = explode("::", $python[$i]);
      if(isset($mouse_over[0]) && isset($mouse_over[1])){
      if($mouse_over[1]!=$word){
      if((strpos($mouse_over[0], '?') === false) && (strpos($mouse_over[0], 'wikiPage') === false) && (strpos($mouse_over[0], '画像') === false) && (strpos($mouse_over[0], 'image') === false) && (strpos($mouse_over[0], 'activeYears') === false) && (strpos($mouse_over[0], '写真') === false) && (strpos($mouse_over[0], '更新日') === false) && (strpos($mouse_over[0], '表記') === false)){
      if($mouse_over[0]!='雑多な内容の箇条書き' && $mouse_over[0]!='民族' && $mouse_over[0]!='活動内容' && $mouse_over[0]!='人名' && $mouse_over[0]!='就任日' && $mouse_over[0]!='国旗' && $mouse_over[0]!='各国語表記' && $mouse_over[0]!='退任日'  && $mouse_over[0]!='当選回数' && $mouse_over[0]!='before' && $mouse_over[0]!='after' && $mouse_over[0]!='titlenote' && $mouse_over[0]!='direction' && $mouse_over[0]!='titlestyle' && $mouse_over[0]!='前代'){
      if($mouse_over[0]!='当代' && $mouse_over[0]!='次代' && $mouse_over[0]!='alias' && $mouse_over[0]!='ラテン文字' && $mouse_over[0]!='showMedals' && $mouse_over[0]!='listclass' && $mouse_over[0]!='text' && $mouse_over[0]!='議論ページ'  && $mouse_over[0]!='サイン' && $mouse_over[0]!='元首' && $mouse_over[0]!='元首職' && $mouse_over[0]!='wikify' && $mouse_over[0]!='period' && $mouse_over[0]!='commonName' && $mouse_over[0]!='caption'){
      if($mouse_over[0]!='年' && $mouse_over[0]!='特筆性' && $mouse_over[0]!='活動備考' && $mouse_over[0]!='時点' && $mouse_over[0]!='モデル名' && $mouse_over[0]!='内容過剰' && $mouse_over[0]!='観点' && $mouse_over[0]!='width'  && $mouse_over[0]!='範囲' && $mouse_over[0]!='years' && $mouse_over[0]!='afternote' && $mouse_over[0]!='beforenote' && $mouse_over[0]!='cctld' && $mouse_over[0]!='titlebar' && $mouse_over[0]!='caption'){
      if($mouse_over[0]!='date' && $mouse_over[0]!='section' && $mouse_over[0]!='thumbnail' && $mouse_over[0]!='id' && $mouse_over[0]!='キャプション' && $mouse_over[0]!='存命人物の出典明記' && $mouse_over[0]!='公式サイト' && $mouse_over[0]!='独自研究'  && $mouse_over[0]!='ソートキー' && $mouse_over[0]!='活動期間' && $mouse_over[0]!='名前' && $mouse_over[0]!='芸名' && $mouse_over[0]!='title' && $mouse_over[0]!='name' && $mouse_over[0]!='活動時期'){
        $mouseover[$num_mouseover]=$mouse_over[0];
        $detail[$num_mouseover]=$mouse_over[1];
        $num_mouseover++;
      }}}}}}
      }
    }else{
      break;
    }
  }
  $str_birthYear='';$str_birthMonth='';$str_birthDate='';$str_deathYear='';$str_deathMonth='';$str_deathDate='';

  for($i=0;;$i++){
    if(isset($mouseover[$i])){
      if($mouseover[$i]=='生年'){ $str_birthYear=$detail[$i];
      }else if($mouseover[$i]=='生月'){ $str_birthMonth=$detail[$i];
      }else if($mouseover[$i]=='生日'){ $str_birthDate=$detail[$i];
      }else if($mouseover[$i]=='没年'){ $str_deathYear=$detail[$i];
      }else if($mouseover[$i]=='没月'){ $str_deathMonth=$detail[$i];
      }else if($mouseover[$i]=='没日'){ $str_deathDate=$detail[$i];
      }
    }else{
      break;
    }
  }

  if(!empty($str_birthYear) && !empty($str_birthMonth) && !empty($str_birthDate)){
    if(!empty($str_deathYear) && !empty($str_deathMonth) && !empty($str_deathDate)){
      $mouseover[$num_mouseover]='生年月日'; $detail[$num_mouseover]=$str_birthYear.'-'.$str_birthMonth.'-'.$str_birthDate;
      $num_mouseover++;
      $mouseover[$num_mouseover]='没年月日'; $detail[$num_mouseover]=$str_deathYear.'-'.$str_deathMonth.'-'.$str_deathDate;
    }else{
      $mouseover[$num_mouseover]='生年月日'; $detail[$num_mouseover]=$str_birthYear.'-'.$str_birthMonth.'-'.$str_birthDate;
    }
  }else{
    if(!empty($str_deathYear) && !empty($str_deathMonth) && !empty($str_deathDate)){
      $mouseover[$num_mouseover]='没年月日'; $detail[$num_mouseover]=$str_deathYear.'-'.$str_deathMonth.'-'.$str_deathDate;
    }
  }

  $num_mouseover=0;
  $check_num_mouseover=0;
  $check_mouseover=0;
  $mouseover_set=array();
  $detail_set=array();
  for($i=0;;$i++){
    if(isset($mouseover[$i])){
      for($j=0;$j<$num_mouseover;$j++){
        if($mouseover[$i]==$mouseover_set[$j]){
          $check_mouseover=1;
          $check_num_mouseover=$j;
        }
      }
      if($check_mouseover==1){
        if(strpos($detail_set[$check_num_mouseover], $detail[$i]) !== false){
        }else if(strpos($detail[$i], $detail_set[$check_num_mouseover]) !== false){
          $detail_set[$check_num_mouseover]=$detail[$i];
        }else{
          $detail_set[$check_num_mouseover]=$detail_set[$check_num_mouseover] . "、" . $detail[$i];
        }
      }else if($check_mouseover==0){
        $mouseover_set[$num_mouseover]=$mouseover[$i];
        $detail_set[$num_mouseover]=$detail[$i];
        $num_mouseover++;
      }
      $check_mouseover=0;
    }else{
      break;
    }
  }

    $mouse_over_decision=array();
    $detail_set_decision=array();
    $num_mouseover=0;
    $check_num_mouseover=0;
    $check_mouseover=0;

      if(!empty($category_match_set_new[0])){
        for($k=0; $k<$number_new; $k++){
          if(strpos($category_match_set_new[$k],'wiki') === false){
            for($i=0;;$i++){
              if(isset($mouseover_set[$i])){
                if($category_match_set_new[$k]==$mouseover_set[$i]){
                  if($mouseover_set[$i]=='Person/height' || $mouseover_set[$i]=='height'){ $mouseover_set[$i]='身長'; }if($mouseover_set[$i]=='Person/weight' || $mouseover_set[$i]=='weight'){ $mouseover_set[$i]='体重'; }if($mouseover_set[$i]=='abstract'){ $mouseover_set[$i]='詳細'; }
                  if($mouseover_set[$i]=='birthDate' || $mouseover_set[$i]=='出生' || $mouseover_set[$i]=='born' || $mouseover_set[$i]=='出生日'){ $mouseover_set[$i]='生年月日'; }if($mouseover_set[$i]=='bloodType'){ $mouseover_set[$i]='血液型'; }if($mouseover_set[$i]=='spouse'){ $mouseover_set[$i]='配偶者'; }
                  if($mouseover_set[$i]=='notableWork'){ $mouseover_set[$i]='代表作'; }if($mouseover_set[$i]=='birthYear'){ $mouseover_set[$i]='生年'; }if($mouseover_set[$i]=='activeYearsStartYear'){ $mouseover_set[$i]='活動開始年'; }if($mouseover_set[$i]=='almaMater' || $mouseover_set[$i]=='school' || $mouseover_set[$i]=='schoolBackground'){ $mouseover_set[$i]='出身校'; }
                  if($mouseover_set[$i]=='affiliation'){ $mouseover_set[$i]='所属'; }if($mouseover_set[$i]=='nationality'){ $mouseover_set[$i]='国籍'; }if($mouseover_set[$i]=='deathYear'){ $mouseover_set[$i]='没年'; }if($mouseover_set[$i]=='deathDate'){ $mouseover_set[$i]='没年月日'; }
                  if($mouseover_set[$i]=='deathPlace'){ $mouseover_set[$i]='死没地'; }if($mouseover_set[$i]=='occupation'){ $mouseover_set[$i]='職業'; }if($mouseover_set[$i]=='party'){ $mouseover_set[$i]='所属政党'; }if($mouseover_set[$i]=='predecessor'){ $mouseover_set[$i]='前任者'; }
                  if($mouseover_set[$i]=='region'){ $mouseover_set[$i]='選挙区'; }if($mouseover_set[$i]=='genre'){ $mouseover_set[$i]='ジャンル'; }if($mouseover_set[$i]=='hometown'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='measurements'){ $mouseover_set[$i]='カップ数'; }
                  if($mouseover_set[$i]=='employer'){ $mouseover_set[$i]='専属契約'; }if($mouseover_set[$i]=='choreographer'){ $mouseover_set[$i]='振付師'; }
                  if($mouseover_set[$i]=='coach'){ $mouseover_set[$i]='コーチ'; }if($mouseover_set[$i]=='country'){ $mouseover_set[$i]='国籍'; }if($mouseover_set[$i]=='formerChoreographer'){ $mouseover_set[$i]='前振付師'; }
                  if($mouseover_set[$i]=='formerCoach'){ $mouseover_set[$i]='前コーチ'; }if($mouseover_set[$i]=='debutWorks'){ $mouseover_set[$i]='デビュー作'; }
                  if($mouseover_set[$i]=='subject'){ $mouseover_set[$i]='主題'; }if($mouseover_set[$i]=='relations'){ $mouseover_set[$i]='親族'; }
                  if($mouseover_set[$i]=='children' || $mouseover_set[$i]=='child' || $mouseover_set[$i]=='parent' || $mouseover_set[$i]=='relative'){ $mouseover_set[$i]='親族'; }
                  if($mouseover_set[$i]=='出生地' || $mouseover_set[$i]=='出身地'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='number'){ $mouseover_set[$i]='背番号'; }if($mouseover_set[$i]=='battingSide'){ $mouseover_set[$i]='打席'; }
                  if($mouseover_set[$i]=='throwingSide'){ $mouseover_set[$i]='投球'; }if($mouseover_set[$i]=='position'){ $mouseover_set[$i]='ポジション'; }
                  if($mouseover_set[$i]=='birthPlace' || $mouseover_set[$i]=='origin'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='company'){ $mouseover_set[$i]='事務所'; }if($mouseover_set[$i]=='description'){ $mouseover_set[$i]='他の活動'; }
                  if($mouseover_set[$i]=='successor'){ $mouseover_set[$i]='後任者'; }if($mouseover_set[$i]=='birthName'){ $mouseover_set[$i]='ニックネーム'; }
                  for($j=0;$j<$num_mouseover;$j++){
                    if($mouseover_set[$i]==$mouseover_set_decision[$j]){
                       $check_mouseover=1; $check_num_mouseover=$j;
                    }
                  }
                  if($check_mouseover==1){
                    if(strpos($detail_set_decision[$check_num_mouseover], $detail_set[$i]) !== false){
                    }else if(strpos($detail_set[$i], $detail_set_decision[$check_num_mouseover]) !== false){
                      $detail_set_decision[$check_num_mouseover]=$detail_set[$i];
                    }else{
                      $detail_set_decision[$check_num_mouseover]= $detail_set_decision[$check_num_mouseover] . "、" . $detail_set[$i];
                    }
                  }else if($check_mouseover==0){
                    $mouseover_set_decision[$num_mouseover]=$mouseover_set[$i]; $detail_set_decision[$num_mouseover]=$detail_set[$i];
                    $num_mouseover++;
                  }
                  $check_mouseover=0;
                }
              }else{
                break;
              }
            }
          }
        }
      }else{
            for($i=0;;$i++){
              if(isset($mouseover_set[$i])){
                  if($mouseover_set[$i]=='Person/height' || $mouseover_set[$i]=='height'){ $mouseover_set[$i]='身長'; }if($mouseover_set[$i]=='Person/weight' || $mouseover_set[$i]=='weight'){ $mouseover_set[$i]='体重'; }if($mouseover_set[$i]=='abstract'){ $mouseover_set[$i]='詳細'; }
                  if($mouseover_set[$i]=='birthDate' || $mouseover_set[$i]=='出生' || $mouseover_set[$i]=='born' || $mouseover_set[$i]=='出生日'){ $mouseover_set[$i]='生年月日'; }if($mouseover_set[$i]=='bloodType'){ $mouseover_set[$i]='血液型'; }if($mouseover_set[$i]=='spouse'){ $mouseover_set[$i]='配偶者'; }
                  if($mouseover_set[$i]=='notableWork'){ $mouseover_set[$i]='代表作'; }if($mouseover_set[$i]=='birthYear'){ $mouseover_set[$i]='生年'; }if($mouseover_set[$i]=='activeYearsStartYear'){ $mouseover_set[$i]='活動開始年'; }if($mouseover_set[$i]=='almaMater' || $mouseover_set[$i]=='school' || $mouseover_set[$i]=='schoolBackground'){ $mouseover_set[$i]='出身校'; }
                  if($mouseover_set[$i]=='affiliation'){ $mouseover_set[$i]='所属'; }if($mouseover_set[$i]=='nationality'){ $mouseover_set[$i]='国籍'; }if($mouseover_set[$i]=='deathYear'){ $mouseover_set[$i]='没年'; }if($mouseover_set[$i]=='deathDate'){ $mouseover_set[$i]='没年月日'; }
                  if($mouseover_set[$i]=='deathPlace'){ $mouseover_set[$i]='死没地'; }if($mouseover_set[$i]=='occupation'){ $mouseover_set[$i]='職業'; }if($mouseover_set[$i]=='party'){ $mouseover_set[$i]='所属政党'; }if($mouseover_set[$i]=='predecessor'){ $mouseover_set[$i]='前任者'; }
                  if($mouseover_set[$i]=='region'){ $mouseover_set[$i]='選挙区'; }if($mouseover_set[$i]=='genre'){ $mouseover_set[$i]='ジャンル'; }if($mouseover_set[$i]=='hometown'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='measurements'){ $mouseover_set[$i]='カップ数'; }
                  if($mouseover_set[$i]=='employer'){ $mouseover_set[$i]='専属契約'; }if($mouseover_set[$i]=='choreographer'){ $mouseover_set[$i]='振付師'; }
                  if($mouseover_set[$i]=='coach'){ $mouseover_set[$i]='コーチ'; }if($mouseover_set[$i]=='country'){ $mouseover_set[$i]='国籍'; }if($mouseover_set[$i]=='formerChoreographer'){ $mouseover_set[$i]='前振付師'; }
                  if($mouseover_set[$i]=='formerCoach'){ $mouseover_set[$i]='前コーチ'; }if($mouseover_set[$i]=='debutWorks'){ $mouseover_set[$i]='デビュー作'; }
                  if($mouseover_set[$i]=='subject'){ $mouseover_set[$i]='主題'; }if($mouseover_set[$i]=='relations'){ $mouseover_set[$i]='親族'; }
                  if($mouseover_set[$i]=='children' || $mouseover_set[$i]=='child' || $mouseover_set[$i]=='parent' || $mouseover_set[$i]=='relative'){ $mouseover_set[$i]='親族'; }
                  if($mouseover_set[$i]=='出生地' || $mouseover_set[$i]=='出身地'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='number'){ $mouseover_set[$i]='背番号'; }if($mouseover_set[$i]=='battingSide'){ $mouseover_set[$i]='打席'; }
                  if($mouseover_set[$i]=='throwingSide'){ $mouseover_set[$i]='投球'; }if($mouseover_set[$i]=='position'){ $mouseover_set[$i]='ポジション'; }
                  if($mouseover_set[$i]=='birthPlace' || $mouseover_set[$i]=='origin'){ $mouseover_set[$i]='出身'; }if($mouseover_set[$i]=='company'){ $mouseover_set[$i]='事務所'; }if($mouseover_set[$i]=='description'){ $mouseover_set[$i]='他の活動'; }
                  if($mouseover_set[$i]=='successor'){ $mouseover_set[$i]='後任者'; }if($mouseover_set[$i]=='birthName'){ $mouseover_set[$i]='ニックネーム'; }
                  for($j=0;$j<$num_mouseover;$j++){
                    if($mouseover_set[$i]==$mouseover_set_decision[$j]){
                       $check_mouseover=1; $check_num_mouseover=$j;
                    }
                  }
                  if($check_mouseover==1){
                    if(strpos($detail_set_decision[$check_num_mouseover], $detail_set[$i]) !== false){
                    }else if(strpos($detail_set[$i], $detail_set_decision[$check_num_mouseover]) !== false){
                      $detail_set_decision[$check_num_mouseover]=$detail_set[$i];
                    }else{
                      $detail_set_decision[$check_num_mouseover]= $detail_set_decision[$check_num_mouseover] . "、" . $detail_set[$i];
                    }
                  }else if($check_mouseover==0){
                    $mouseover_set_decision[$num_mouseover]=$mouseover_set[$i]; $detail_set_decision[$num_mouseover]=$detail_set[$i];
                    $num_mouseover++;
                  }
                  $check_mouseover=0;

              }else{
                break;
              }
            }
      }

  if($search=='Google'){
    echo 'Google';echo '<br><br>';
    for($i=0;$i<10;$i++){
      if(isset($mouseover_set_decision[$i])){
          $form="form2_" . $i;
          $java="javascript:" . $form . ".submit()";
          echo "<form style=\"display: inline\" action=\"count.php\" method=\"post\" name=$form>";
          echo "<input type=\"hidden\" name=\"word\" value=$word>";
          echo "<input type=\"hidden\" name=\"word_2\" value=$mouseover_set_decision[$i]>";
          echo "<input type=\"hidden\" name=\"number\" value=2>";
          echo "<a href=$java>$mouseover_set_decision[$i]</a>";
          echo "<br>";
          echo "</form>";
      }else{
        break;
      }
    }
  }

  if($search=='DBpedia'){
    echo 'DBpedia';echo '<br><br>';
    echo '<div id="menu">';

    for($i=0;$i<10;$i++){
    if(isset($mouseover_set_decision[$i])){
      if($mouseover_set_decision[$i]!='血液型'){
          echo '<div>';
          echo "<span>$mouseover_set_decision[$i]</span>";
          echo "<p class=\"arrow_box\">$detail_set_decision[$i]</p>";
          echo '</div>';
      }
    }else{
      break;
    }
    }
    echo '</div>';
    echo '<br><br><br><br>';
  }
}

pg_close($conn);
?>
</div>


<div class="green">
<?php
echo '目的語取得(完全一致のみ)';echo '<br>';
$str = "/home/toyama/yuna/local/python/bin/python3 a_test.py \"" . $word . "\"";
$re = shell_exec($str);
$a_popup_output = explode("\n", $re);

#$a_popup_output=shuffle($a_popup_output);

if(shuffle($a_popup_output)){
for($i=0 ;$i<10; $i++){
    if(isset($a_popup_output[$i])){
        $form="form3_" . $i;
        $java="javascript:" . $form . ".submit()";
        echo "<form style=\"display: inline\" action=\"count.php\" method=\"post\" name=$form>";
        echo "<input type=\"hidden\" name=\"word\" value=$word>";
        echo "<input type=\"hidden\" name=\"word_2\" value=$a_popup_output[$i]>";
        echo "<input type=\"hidden\" name=\"number\" value=3>";
        echo "<a href=$java>$a_popup_output[$i]</a>";
        echo "<br>";
        echo "</form>";
    } else {
        break;
    }
}
}
?>
</div>


<div class="blue">

<?php
echo '部分文字列を取得';
echo '<form action="matome_copy.php" method="post">';
echo "<input type=\"hidden\" name=\"php\" value='DBpedia'>";
echo "<input type=\"hidden\" name=\"comma_separated_sample_2\" value=$comma_separated_sample_2>";
echo "<input type=\"hidden\" name=\"comma_separated_popup\" value=$comma_separated_popup>";
echo "<input type=\"hidden\" name=\"word\" value=$word>";
if(isset($word_2)){
  echo "<input type=\"hidden\" name=\"word_2\" value=$word_2>";
}
echo '<input type="submit" name="submit" value="検索">';
echo '</form>';

if($php=='DBpedia'){
echo '<form action="matome_copy.php" method="post">';
if(isset($word)){
  echo "<select name=\"category_popup\">";
    echo "<option value=\"Thing\">カテゴリを選択してください</option>";
    $command="/home/toyama/yuna/local/python/bin/python3 test_5.py \"" . $category_popup."\"";
    exec($command,$python_2_popup);
    for($i=0 ;; $i++){
        if(isset($python_2_popup[$i])){
            echo "<option value=$python_2_popup[$i]>$python_2_popup[$i]</option>";
        } else {
            break;
        }
    }
  echo "</select>";
  echo "<input type=\"hidden\" name=\"comma_separated_sample_2\" value=$comma_separated_sample_2>";
  echo "<input type=\"hidden\" name=\"comma_separated_popup\" value=$comma_separated_popup>";
  $word = str_replace(" ", "_", $word);
  echo "<input type=\"hidden\" name=\"word\" value=$word>";
  $word = str_replace("_", " ", $word);
  echo "<input type=\"hidden\" name=\"php\" value=$php>";
  if(isset($word_2)){
    echo "<input type=\"hidden\" name=\"word_2\" value=$word_2>";
  }
  echo '<input type="submit" name="submit" value="検索">';
  echo '</form>';
}
if($category_popup != "Thing"){
    echo "<form action=\"matome_copy.php\" method=\"post\">";
        $command="/home/toyama/yuna/local/python/bin/python3 csv_call_ParentClass.py \"" . $category_popup."\"";
        exec($command,$category_change);
        echo "<input type=\"hidden\" name=\"word\" value=$word>";
        $word = str_replace("_", " ", $word);
        echo "<input type=\"hidden\" name=\"category_popup\" value=$category_change[0]>";
        echo "<input type=\"hidden\" name=\"comma_separated_popup\" value=$comma_separated_popup>";
        echo "<input type=\"hidden\" name=\"php\" value=$php>";
    echo "<input type=\"submit\" name=\"submit\" value=\"一つ前に戻る\">";
    echo "</form>";
}

if(isset($category_popup) && isset($word) && $word!=''){
  if($category_popup != "Thing"){
    $word = str_replace("_", " ", $word);
    $str = "/home/toyama/yuna/local/python/bin/python3 sample_2.py \"" . $word . "\" \"" . $category_popup."\"";
    $word = str_replace(" ", "_", $word);
    $re = shell_exec($str);
    $python_3_popup = explode("\n", $re);
  }else{
    $word = str_replace("_", " ", $word);
    $str = "/home/toyama/yuna/local/python/bin/python3 sample.py \"" . $word."\"";
    $word = str_replace(" ", "_", $word);
    $re = shell_exec($str);
    $python_3_popup = explode("\n", $re);
  }

  if(shuffle($python_3_popup)){
  for($i=0 ;$i<10; $i++){
    if(isset($python_3_popup[$i])){
      $form="form" . $i;
      $java="javascript:" . $form . ".submit()";
      echo "<form style=\"display: inline\" action=\"count.php\" method=\"post\" name=$form>";
      echo "<input type=\"hidden\" name=\"word\" value=$python_3_popup[$i]>";
      echo "<input type=\"hidden\" name=\"number\" value=4>";
      echo "<a href=$java>$python_3_popup[$i]</a>";
      echo "<br>";
      echo "</form>";
    } else {
      break;
    }
  }
  }
}

}
?>
</div>
</body></html>
