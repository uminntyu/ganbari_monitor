!DOCUTYPE html>
<html lang="ja">
  <head>
    <meta http-equiv="content-type" content="text/html charset=UTF-8">
    <meta http-equiv="refresh" content="10" >
    <title>カウンター</title>
    <link rel="stylesheet" type="text/css" href="./page.css">
  </head>
  <body  background="background_kari.png" >

    <?php
      //現在日時取得
      $today = date("Y/m/d");
//      echo "today:$today<br/>";

      //データベースにアクセス
      $db = new mysqli("IP address","User name","Password", "DB name");
      if($db->connect_error){
//        echo $db->connect_error;
        exit();
      }else{
        $db->set_charset("utf8");
      }

      //takosagyo_logの最新データの日時取得
      $sql = "select date from takosagyo_log order by date desc limit 1;";
      if($result = $db->query($sql)){
         $row = $result->fetch_assoc(); //連想配列取得
         $lastday = $row["date"];
//         echo "lastday:$lastday<br/>";
         $result->close();
      }

      //日付を比較
      if(strcmp($today, $lastday) == 0){
        $flag_insert = false;
      }else{
        $flag_insert = true;
      }
//      echo "flag_insert:".(($flag_insert)?'true':'false')."<br/>";

      //takosagyo_logの最新データのsum取得
      $pt_lastday = 0;
      $sql = "select date, sum from takosagyo_log order by date desc limit 2;";
      if($result = $db->query($sql)){
        while($row = $result->fetch_assoc()) //連想配列取得
        {
          if($today != $row["date"]){
            $pt_lastday = $row["sum"];
           }
        }
        $result->close();
      }
//      echo "pt_lastday:$pt_lastday<br/>";

      //今日のデータを登録
      if($flag_insert){
        $sql = "insert into takosagyo_log values('$today', 0, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);";
        if($db->query($sql)){
//          echo "insert<br/>";
        }
      }

      //今日のポイント集計
      $pt_today=0;
//      $today = "2019/08/27";
      $sql = "select kazu, time from takosagyo1 inner join stdtime on takosagyo1.buhin=stdtime.buhin where up_date='$today';";
      if($result= $db->query($sql)){
        while( $row = $result->fetch_assoc()) //連想配列取得
        {
          $pt_today += $row["kazu"] * $row["time"];
        }
         $result->close();
      }
//      echo "pt_today:$pt_today<br/>";

      //今日のデータを更新
      $sql = "update takosagyo_log set sum=$pt_today where date='$today';";
      if($db->query($sql)){
//         echo "update";
      }


      //データベースを閉じる
      $db->close();
    ?>

    <div class="center">
      <p id = "pt_today"><?php echo $pt_today ?></p>
      <p id = "pt_yesterday"><?php echo $pt_lastday ?></p>
    </div>

  </body>
</html>
