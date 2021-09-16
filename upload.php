<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link href="base.css" type="text/css" rel="stylesheet" media="all">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
</head>
<body>
	

<?php
include("SjisToUtf8EncodingFilter.php");

stream_filter_register(
    'sjis_to_utf8_encoding_filter',
    SjisToUtf8EncodingFilter::class
);

if (is_uploaded_file($_FILES["csvfile"]["tmp_name"])) {
  $file_tmp_name = $_FILES["csvfile"]["tmp_name"];
  $file_name = $_FILES["csvfile"]["name"];

  //拡張子を判定
  if (pathinfo($file_name, PATHINFO_EXTENSION) != 'csv') {
    $err_msg = 'CSVファイルのみ対応しています。';
  } else {
    //ファイルをdataディレクトリに移動
    if (move_uploaded_file($file_tmp_name, "./data/" . $file_name)) {
      //後で削除できるように権限を644に
      chmod("./data/" . $file_name, 0644);
      $msg = $file_name . "をアップロードしました。";
    echo($msg);
      $file = './data/'.$file_name;
      $fp   = fopen($file, "r");
stream_filter_append($fp, 'sjis_to_utf8_encoding_filter');
      //配列に変換する
      while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
        $asins[] = $data;
      }
      fclose($fp);
      //ファイルの削除
      unlink('./data/'.$file_name);
    } else {
      $err_msg = "ファイルをアップロードできません。";
    }
  }
} else {
  $err_msg = "ファイルが選択されていません。";
}

echo($err_msg);
?>

<?php
//平均をとる。普通は配列だがわかりやすいように。
$average1=0;
$average2=0;
$average3=0;
$average4=0;
$average5=0;
$i =0;
//配列を吐き出す。
foreach($asins as $key){
$average1 = $average1 + $key[1];
$average2= $average2 + $key[2];
$average3= $average3 + $key[3];
$average4= $average4 + $key[4];
$average5= $average5 + $key[5];
$i = $i + 1;
};

$average1 = round($average1/$i,1);
$average2= round($average2/$i,1);
$average3= round($average3/$i,1);
$average4= round($average4/$i,1);
$average5= round($average5/$i,1);
$average6 = $average1+$average2+$average3+$average4+$average5;
$average6 = round($average6/5,1);

$average1_2 = round($average1*20,-1)/20;
$average2_2= round($average2*20,-1)/20;
$average3_2= round($average3*20,-1)/20;
$average4_2= round($average4*20,-1)/20;
$average5_2= round($average5*20,-1)/20;
$average6_2 =round($average6*20,-1)/20;
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">  <canvas id="myRaderChart"></canvas></div>
		<div class="col-md-6"><h1>A君の総合評価</h1>
		<table class="table">
			<tr>
				<th>コスパ</th>
				<td><b><?php echo($average1); ?></b>

				<span class="star5_rating" data-rate="<?php echo($average1_2); ?>"></span></td>
			</tr>
			<tr>
				<th>リピ度</th>
				<td><b><?php echo($average2); ?></b>
				<span class="star5_rating" data-rate="<?php echo($average2_2); ?>"></span></td>
			</tr>
			<tr>
				<th>スタイル</th>
				<td><b><?php echo($average3); ?></b>
				<span class="star5_rating" data-rate="<?php echo($average3_2); ?>"></span></td>
			</tr>
			<tr>
				<th>おすすめ度</th>
				<td><b><?php echo($average4); ?></b>
				<span class="star5_rating" data-rate="<?php echo($average4_2); ?>"></span></td>
			</tr>
			<tr>
				<th>面白度</th>
				<td><b><?php echo($average5); ?></b>
				<span class="star5_rating" data-rate="<?php echo($average5_2); ?>"></span></td>
			</tr>
		</table>
<h2>総合評価</h2>
<p><b><?php echo($average5); ?></b>
<span class="star5_rating" data-rate="<?php echo($average6_2); ?>"></span>
		</p>
		</div>

	</div>
</div>


  <!-- CDN -->
　<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

  <script>
    var ctx = document.getElementById("myRaderChart");
    var myRadarChart = new Chart(ctx, {
        type: 'radar', 
        data: { 
            labels: ["コスパ", "リピ度", "スタイル", "おすすめ度", "面白度"],
            datasets: [{
                label: 'Aさん',
                data: [<?php echo($average1); ?>,
					<?php echo($average2); ?>,
					<?php echo($average3); ?>,
					<?php echo($average4); ?>,
					<?php echo($average5); ?>],
                backgroundColor: 'RGBA(225,95,150, 0.5)',
                borderColor: 'RGBA(225,95,150, 1)',
                borderWidth: 1,
                pointBackgroundColor: 'RGB(46,106,177)'
            }]
        },
        options: {
            title: {
                display: true,
                text: 'おすすめチャート'
            },
            scale:{
                ticks:{
                    suggestedMin: 0,
                    suggestedMax: 5,
                    stepSize: 0.5,
                    callback: function(value, index, values){
                        return  value +  'Pt'
                    }
                }
            }
        }
    });
    </script>
</body>
</html>

