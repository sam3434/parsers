<?php
	// @header('Content-Type: text/html; charset=utf-8'); 				
	include_once("config.php");
	$paging = 10000;
	//include_once("xls.php");
if(isset($_REQUEST['category'])){
		$con = mysql_connect(DB_HOST,DB_USER, DB_PASSWORD);
		if (!$con){ die('Could not connect: ' . mysql_error()); }
		mysql_select_db(DB_NAME, $con) or die(mysql_error());	
		mysql_set_charset( 'utf8' );
		
		if($_REQUEST['category'] == 'all'){
			$productsQuery = 'SELECT * FROM alibaba_data ';
			$sql = mysql_query($productsQuery);
		}
		else
		{
			$productsQuery = 'SELECT * FROM alibaba_data limit '.(($_REQUEST['category']-1)*$paging).', '.$paging;
			$sql = mysql_query($productsQuery);	
		}
		
		
		//echo "<a style='border: 2px solid #000; font-weight: bold; color: #000;' href='export.php'>ВСЕ КАТЕГОРИИ</a> ";
		
/** Error reporting */
error_reporting(E_ERROR);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once './PHPExcel-develop/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
		$n = 2;
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(60);
        $title = array(
		    'font' => array(
		        'name' => 'Arial',
		        // 'size' => 14,
		        'bold' => true,
		        'color' => array(
		            'rgb' => '1e1e1e'
		        ),
		    ),
		    'borders' => array(
		        'bottom' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => array(
		                'rgb' => '000000'
		            )
		        ),
		        'right' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => array(
		                'rgb' => '000000'
		            )
		        )
		    ),
		    'fill' => array(
		        'type' => PHPExcel_Style_Fill::FILL_SOLID,
		        'startcolor' => array(
		            'rgb' => 'F20C2B',
		        ),
		    ),
		);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'company_name')
		            ->setCellValue('B1', 'operational_address')
		            ->setCellValue('C1', 'zip')
		            ->setCellValue('D1', 'country_region')
		            ->setCellValue('E1', 'province_state')
		            ->setCellValue('F1', 'city')
		            ->setCellValue('G1', 'address')
		            ->setCellValue('H1', 'telephone')
		            ->setCellValue('I1', 'mobile phone')
		            ->setCellValue('J1', 'fax')
		            ->setCellValue('K1', 'website')
		            ->setCellValue('L1', 'url')
		            ->setCellValue('M1', 'name')
		            ->setCellValue('N1', 'job title');
		$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:N1")->applyFromArray($title);	
		while($row = mysql_fetch_array($sql)){
			
			//echo "<a href='export.php?category=".$row['category']."'>".$row["title"]."</a> ";				`company_name`,
						
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$n, $row['company_name'])
			            ->setCellValue('B'.$n, $row['operational_address'])
			            ->setCellValue('C'.$n, $row['zip'])
			            ->setCellValue('D'.$n, $row['country_region'])
			            ->setCellValue('E'.$n, $row['province_state'])
			            ->setCellValue('F'.$n, $row['city'])
			            ->setCellValue('G'.$n, $row['address'])
			            ->setCellValue('H'.$n, $row['telephone'])
			            ->setCellValue('I'.$n, $row['mobile phone'])
			            ->setCellValue('J'.$n, $row['fax'])
			            ->setCellValue('K'.$n, $row['website'])
			            ->setCellValue('L'.$n, $row['url'])
			            ->setCellValue('M'.$n, $row['name'])
			            ->setCellValue('N'.$n, $row['job title']);
			            
			$n++;
		}		


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
		
/*-------------------------------------------------------------------------------------*/

//$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:A3");
//$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "C5:R95");
/*-------------------------------------------------------------------------------------*/

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$_REQUEST['category'].'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
mysql_close($con);
exit;
}
else{


		
		//echo "<a style='border: 2px solid #000; font-weight: bold; color: #000;' href='export.php'>ВСЕ КАТЕГОРИИ</a> ";
			
			//по завершению закрываем коннект с базой
			
			
			
		
		
	
	
	echo '
<style>
	a{
		border: 1px solid #ccc; 
		margin: 5px 10px; 
		border-radius: 3px; 
		padding: 3px; 
		display: block;
	}
	a:hover{
		background: #ccc;
	}
</style>';
		$con = mysql_connect(DB_HOST,DB_USER, DB_PASSWORD);
		if (!$con){ die('Could not connect: ' . mysql_error()); }
		mysql_select_db(DB_NAME, $con) or die(mysql_error());	
		mysql_set_charset( 'utf8' );
		
		$categoriesQuery = 'SELECT count(*) FROM alibaba_data';
		
		$sql = mysql_query($categoriesQuery);
		$row = mysql_fetch_array($sql);
		$count = $row["count(*)"];
		$pages = floor($count/$paging) + 1;
		for ($i=0; $i < $pages; $i++) { 
			echo "<a href='export.php?category=".($i+1)."'>"."Выкачать пользователей с ".($i*$paging+1)." по ".(($i+1)*$paging)."</a> ";
		}

			
		
		echo "<a style='border: 2px solid #000; font-weight: bold; color: #000;' href='export.php?category=all'>ВЫКАЧАТЬ ВСЕ</a> ";
			
			//по завершению закрываем коннект с базой
		mysql_close($con);
		
		
		
		
			//по завершению закрываем коннект с базой
		


}