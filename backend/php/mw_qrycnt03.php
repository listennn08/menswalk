<?php
error_reporting(-1);
ini_set('display_errors', 'On');

include('wp-config.php');

$DB_NAME		= constant("DB_NAME");
$DB_USER		= constant("DB_USER");
$DB_PASSWORD	= constant("DB_PASSWORD");
$DB_HOST		= constant("DB_HOST");
$arrAPIKEY		= constant("MW_APIKEY");

$CHARLEN = 3;	//一個中文字的長度 byte

libxml_use_internal_errors(false);   //supress warning messages
//================= Ready for MySQL operation =================================
$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if (!$link) {
	echo "error connect:[$DB_HOST][$DB_USER][$DB_PASSWORD][$DB_NAME]\n";
    die("Connection failed: " . mysqli_connect_error());
}

mb_internal_encoding ('utf8');
mysqli_query ($link, "SET CHARACTER SET 'utf8'");
mysqli_query ($link, "SET NAMES 'utf8'");
//================= Ready for MySQL operation =================================

//----------- Usage ----------------------

$apikey = $_GET["apikey"];
$region = $_GET["region"];
$aid = $_GET["id"];
$qdate = $_GET["date"];
$yyyymm = $_GET["yyyymm"];

//------ Global variables
header('Content-Type: application/json; charset=utf-8');

$arrRegion = array(
"南投縣",
"嘉義市",
"嘉義縣",
"基隆市",
"宜蘭縣",
"屏東縣",
"彰化縣",
"新北市",
"新竹市",
"新竹縣",
"桃園市",
"澎湖縣",
"臺中市",
"臺北市",
"臺南市",
"臺東縣",
"花蓮縣",
"苗栗縣",
"連江縣",
"金門縣",
"雲林縣",
"高雄市"	
);

//http://menswalk.prjlife.com/mw_qrycnt03.php?apikey=cat&id=C1_315081500H_000009&yyyymm=202005
//============================
if (in_array($apikey, $arrAPIKEY)) { 
	doQueryMonth($aid, $yyyymm);
} else {
	echo "Invalid call.";
}
?>

<?
//---------- Housekeeping

die();

function doQueryMonth($id, $month) {
	global $link;

	$sql = "SELECT A.yyyymmdd AS d,
(SELECT IFNULL(SUM(B.mencount),0)
FROM mw_mencount_detail B
WHERE B.id = ?
  AND A.yyyymmdd = B.cdate) AS c
FROM mw_date A
WHERE A.yyyymm = ?";	
	$statement = $link->prepare($sql);
	$statement->bind_param('ss', $id, $month);
	$statement->execute();
	$result = $statement->get_result();	
	
	$json = array();

	if(mysqli_num_rows($result)){
		while($row=mysqli_fetch_assoc($result)){
			$json[]=$row;
		}
	}

	echo json_encode($json, JSON_NUMERIC_CHECK); 
}
?>
