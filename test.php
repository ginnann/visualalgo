<?php
$imputalgo=$_GET['imputalgo'];
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <form action="" method="GET">
    <div class="forms">
      <div class="form-set">
        <div class = "h" id="tejun_nyuryoku">手順入力</div>
        <div class = "in">
    			<input type="text" value="<?php if($imputalgo!='') echo $imputalgo; ?>" name="imputalgo" id="algo" placeholder="例)xUR'U'LURU'R'w">
        </div>
      </div>
    </div><!-- form -->
    <?php
    echo "入力: ".$imputalgo."<br>";
    echo "encode: ".$enco=urlencode($imputalgo)."<br>";
    echo "replace: ".substr_replace($enco,"'",$imputalgo)."<br>";
     ?>
  </body>
</html>
