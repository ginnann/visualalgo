<?php
require_once("parameter.php");
if($_SERVER['REQUEST_METHOD']==='GET'){
	$imputalgo = $_GET['imputalgo'];
}
$fmt="svg";
$algtype="case";

//set-----------------------------------
		//imputalgo normalization
		$imputalgo_opend=$imputalgo;
		$imputalgo_opend=mb_convert_kana($imputalgo_opend, 'kvrns');
		$imputalgo_opend=urlencode($imputalgo_opend);
		$pattern = '/('.urlencode("'").'|'.urlencode("’").'|'.urlencode("‘").'|'.urlencode("＇").'|'.urlencode("`").')/';
		$imputalgo_opend=preg_replace($pattern, '\'', $imputalgo_opend );
		$pattern = '/('.' |'.urlencode('　').'|%20'.')/';
		$imputalgo_opend=preg_replace($pattern,'', $imputalgo_opend); //delete space


		$imputalgo_opend=preg_replace('/\'w/',"w'",$imputalgo_opend);
		$imputalgo_opend=preg_replace('/2\'|\'2/',"2'",$imputalgo_opend); //wrong arw

		//Rw->r etc...
		for($i=0 ; $i<strlen($imputalgo_opend)-1 ; $i++){
			if(substr($imputalgo_opend,$i+1,1)===w){
				$imputalgo_opend=substr_replace($imputalgo_opend,strtolower(substr($imputalgo_opend,$i,1)),$i,2);
			}
		}

		$imputalgo_opend=preg_replace('/'.urlencode(×).'/',"*",$imputalgo_opend);

		//decode------------------------------------------
		$imputalgo_opend = urldecode($imputalgo_opend);


		//(r)-> x , (u) -> y , (f) -> z
		$patterns=array('/(\([xyzruf2\']*)([r])([2\']{0,2}[xyzruf2\']*\))/',
										'/(\([xyzruf2\']*)([u])([2\']{0,2}[xyzruf2\']*\))/',
										'/\(([xyzruf2\']*)([f])([2\']{0,2}[xyzruf2\']*)\)/'
										);
		$replace=array('\1x\3','\1y\3','\1z\3');
		$imputalgo_opend = preg_replace($patterns ,$replace,$imputalgo_opend);


		//multiply open
		$imputalgo_opend = multiply_open($imputalgo_opend);

		//commutator open
			// Mapping from power to chr to represent it
			global $ALG_POW;
			$ALG_POW = Array ('', "2", "'");
			$imputalgo_opend = commutator_open($imputalgo_opend);


		//set array $split0 (each move)
		preg_match_all('/([UDLRFBudlrfbMESxyz])(([\'w2]|%27)*)/', $imputalgo_opend, $split);
		$split0=$split[0];

		//set array $tejun
		    $tejun=array();
				print_tejun(0);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./index.css">
	<title>VisualAlgo</title>
</head>
<body>
	<?php
	//↓test space↓-------------------------------------------------------------
	//↑test spece↑--------------------------------------------------------
	 ?>

<form action="" method="GET">
<div class="forms">
  <div class="form-set">
    <div class = "h" id="tejun_nyuryoku">手順入力</div>
    <div class = "in">
			<input type="text" value="<?php if($imputalgo!='') echo $imputalgo; ?>" name="imputalgo" id="algo" placeholder="例)xUR'U'LURU'R'w">
    </div>
  </div>
  <div id="other" style="display: none;" >
    <div class="form-set">
    	<div class = "h">手順の種類</div>
			<SELECT name = "stage">
				<OPTION value="" <?php if($stage=='') echo selected; ?>>指示しない</OPTION>
				<OPTION value="ll" <?php if($stage=='ll') echo selected; ?>>LL(OLL,PLL)</OPTION>
				<OPTION value="f2l" <?php if($stage=='f2l') echo selected; ?>>F2L</OPTION>
			</SELECT>
  	</div>
		<div class="form-set">
    	<div class = "h">配色選択</div>
			<SELECT class = "form-control" name = "sch">
				<OPTION value="wcs" <?php if($sch=='' OR $sch=='wcs') echo selected; ?>>標準配色</OPTION>
				<OPTION value="jcs" <?php if($sch=='jcs') echo selected; ?>>日本配色</OPTION>
			</SELECT>
  	</div>
		<div class="form-set">
    	<div class = "h">上面の色</div>
			<SELECT name = "sch_t">
				<OPTION value="white" <?php if($sch_t=='white') echo selected; ?>>White</OPTION>
				<OPTION value="yellow" <?php if($sch_t=='' OR $sch_t=='yellow') echo selected; ?>>Yellow</OPTION>
				<OPTION value="red" <?php if($sch_t=='red') echo selected; ?>>Red</OPTION>
				<OPTION value="blue" <?php if($sch_t=='blue') echo selected; ?>>Blue</OPTION>
				<OPTION value="orange" <?php if($sch_t=='orange') echo selected; ?>>Orange</OPTION>
				<OPTION value="green" <?php if($sch_t=='green') echo selected; ?>>Green</OPTION>
			</SELECT>
  	</div>
		<div class="form-set">
    	<div class = "h">前面の色</div>
			<SELECT class = "form-control" name = "sch_f">
				<OPTION value="white" <?php if($sch_f=='' OR $sch_f=='white') echo selected; ?>>White</OPTION>
				<OPTION value="yellow" <?php if($sch_f=='yellow') echo selected; ?>>Yellow</OPTION>
				<OPTION value="red" <?php if($sch_f=='red') echo selected; ?>>Red</OPTION>
				<OPTION value="blue" <?php if($sch_f=='' OR $sch_f=='blue') echo selected; ?>>Blue</OPTION>
				<OPTION value="orange" <?php if($sch_f=='orange') echo selected; ?>>Orange</OPTION>
				<OPTION value="green" <?php if($sch_f=='green') echo selected; ?>>Green</OPTION>
			</SELECT>
  	</div>
  </div><!-- other -->
</div><!-- form -->
<div class="buttons">
  <button type="submit" class="btn">GO!</button>
	<button type="button" onClick="hyoji1(0)" class="btn"> 詳細設定 </button>
</div>
</form>
<script>function hyoji1(num){if (num == 0){document.getElementById("other").style.display="block";}else{document.getElementById("other").style.display="none";}}</script>
<div class="result">
	<div class="initialstate">
		<?php
			$algorithm = $tejun[0];
			if($stage==="ll"){
				$view="plan";
				include "img.php";
				$view="";
			}else{
				include"img.php";
			}
		?>
	</div>
	<div class="tejun">
		<?php echo $imputalgo ?>
	</div>

	<div class="moves">
		<?php
			//display

			for($i=0 ; $i < count($tejun) + 1 ; $i++){

				//div .moveline
				if($i % 4 == 0){
					echo   '<div class="moveline">';
				}

				//div .onemove
				echo '<div class="onemove">';
				echo '<img src="./arw.png" ';
				if($i==0){echo 'style="visibility:hidden;"';}
				echo '>';

				//when R2, L2, etc...
				if($split0[$i][1]==='2'){
					echo '<span class="badge">180</span>';
				}

				if($i == count($tejun)){
					//finallstate
					$algorithm="";
					$arw="";
					include"img.php";
				}else{
					// echo $split0[$i] ;
					$algorithm = $tejun[$i];

					//arw
					$arw='';
					switch($split0[$i][0]){
						case 'x':
							$arw = ',F6F0';
						case 'r':
							$arw .= ',F7F1';
						case 'R':
							$arw .= ',F8F2';
						break;

						case 'l':
							$arw = ',F1F7';
						case 'L':
							$arw .= ',F0F6';
						break;

						case 'y':
							$arw = ',F8F6';
						case 'u':
							$arw .= ',F5F3';
						case 'U':
							$arw .= ',F2F0';
						break;

						case 'd':
							$arw = ',F3F5';
						case 'D':
							$arw .= ',F6F8';
						break;

						case 'z':
							$arw = ',R2R8';
						case 'f':
							$arw .= ',R1R7';
						case 'F':
							$arw .= ',R0R6';
						break;

						case 'b':
							$arw = ',R7R1';
						case 'B':
							$arw .= ',R8R2';
						break;

						case 'M':
							$arw = ',F1F7';
						break;
						case 'S':
							$arw = ',R1R7';
						break;
						case 'E':
							$arw = ',F3F5';
						break;

						default:
						break;
					}

					// when R',L' etc...
					if($split0[$i][1]==='\''||$split0[$i][2]==='\''){
						$s = 2;
						while($s < strlen($arw)){
							$t=$s+2;
							$j=$arw[$s];//swap
							$arw[$s]=$arw[$t];
							$arw[$t]=$j;
							$s=$s+5;
						}
					}

					//cube display
					include "img.php";

				}

				// /div onemove
				echo '</div>   <!-- onemove -->';

				// /div .moveline
				if($i%4==3 ||$i == count($tejun)){
					echo '</div> <!-- moveline -->';
				}
			}

	  ?>
	</div><!-- moves -->
</div><!-- result -->
</body>
</html>


<?php
//functions-----------------------------------------------------
		//
		function print_tejun($i=0){
			global $split0,$tejun;
			if($split0[ $i ]){
				print_tejun( $i + 1 );
				$tejun[$i] = $split0[$i] .$tejun[$i + 1 ] ;
			}
		}

		//for open multiplication
		function multiply_open($array){
			while(preg_match('/\(([^\(\)]*)\)[*]{0,1}(\d*)/',$array,$multi)){
				$replace = $multi[1];
				for($i=1;$i<$multi[2];$i++){
					$replace .= $multi[1];
				}
				$array=str_replace($multi[0],$replace,$array);
			}
			return $array;
		}

		//for open commutator (commutator_open, commu_open_sub, commu_open_operate)
		function commutator_open($array){
			while(preg_match('/\[[^\[\]]*\]/',$array,$commutator)){
				preg_match_all('/[^\:\;\,\]]*[\:\;\,\]]/', substr($commutator[0],1), $commu_s0);
				$array=preg_replace('/\[[^\[\]]*\]/',commu_open_sub($commu_s0[0]),$array);
			}
			return preg_replace("/( |　|%20)/", '', $array);
		}
		function commu_open_sub($array){
			if(substr($array[0],-1) === ']'){
				return substr($array[0],0,-1);
			}else{
				return commu_open_operate($array[0],commu_open_sub(array_slice($array,1)));
			}
		}
		function commu_open_operate($a_,$b){
			$operator=substr($a_,-1);
			$a=substr($a_,0,-1);
			switch ($operator){
				case ';':
				case ':':
					return $a . $b . invert_alg($a) ;
				break;
				case ',':
					return $a . $b . invert_alg($a) . invert_alg($b);
				break;
				default:
				break;
			}
		}

		// Inverts an NxN cube algorithm
		function invert_alg($alg){
			global $ALG_POW;
			$inv = "";
			$pow = 1;
			$pre = '';
			$i = strlen($alg) - 1;
			while($i >= 0){
				$c = substr($alg, $i, 1);
				$mv = fcs_move_id($c);
				if($mv != -1){
					// Retrive layer depth
					if($i > 0){
						$pre = substr($alg, $i-1, 1);
						if(!is_numeric($pre) || ($i > 1
						&& fcs_move_id(substr($alg, $i-2, 1)) != -1))
							$pre = '';
						else	$i--;
					}
					// Invert and add the move
					$inv .= $pre . $c . $ALG_POW[3 - $pow] . ' ';
					$pow = 1; $pre = '';
				}
				else $pow = move_pow(substr($alg, $i, 1));
				$i--;
			}
			return $inv;
		}
		// Returns the power of a move with given suffix
		function move_pow($char){
			switch($char){
				case "2" : return 2;
				case "'" : return 3;
				case "3" : return 3;
			}
			return 1;
		}
		// Maps move names to a move id
		function fcs_move_id($move){
			switch($move){
				case 'y': return 0;
				case 'x': return 1;
				case 'z': return 2;
				case 'E': return 3;
				case 'M': return 4;
				case 'S': return 5;
				case 'U': return 6;
				case 'R': return 7;
				case 'F': return 8;
				case 'D': return 9;
				case 'L': return 10;
				case 'B': return 11;
				case 'u': return 12;
				case 'r': return 13;
				case 'f': return 14;
				case 'd': return 15;
				case 'l': return 16;
				case 'b': return 17;
			}
			return -1;
		}
?>
