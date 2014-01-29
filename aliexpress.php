<?php
	//error_reporting(0);
	error_reporting (E_ERROR);
	@header('Content-Type: text/html; charset=utf-8'); 
	 
	include_once("config.php");
	 
	$parser = new AlieParser();
	$parser->parseStart();
	
class AlieParser{
	//DB CONFIG
	var $db_host = DB_HOST;
	var $db_name = DB_NAME;
	var $db_user = DB_USER;
	var $db_password = DB_PASSWORD;

	var $begin_id = 100000;
	var $end_id = 	1199999;
	
	var $urldonor = "http://www.aliexpress.com";
	
	
	
		public function AlieParser(){}
	
		public function parseStart(){
			//коннект к базе
			
			$this->con = mysql_connect($this->db_host, $this->db_user, $this->db_password);
			if (!$this->con){ die('Could not connect: ' . mysql_error()); }
			mysql_select_db($this->db_name, $this->con) or die(mysql_error());
			
			mysql_set_charset( 'utf8' );	

			$sql = "SELECT last_parsed_id FROM `last_parsed_aliexpress` LIMIT 1";
			$handle = mysql_query($sql, $this->con);
			$res = mysql_fetch_assoc($handle);
			$last_parsed = $res['last_parsed_id'];

			if ($last_parsed>$this->begin_id)
			{
				$this->begin_id = $last_parsed;
			}
			for ($i=$this->begin_id; $i <= $this->end_id; $i++) { 
				$this->getPersonalInfo("http://www.aliexpress.com/store/contactinfo/".$i.".html");
				$sql = "UPDATE `last_parsed_aliexpress` SET last_parsed_id='".$i."'";
				mysql_query($sql, $this->con);	
				
			}

			
		}

		
		function getPersonalInfo($url)
		{
			$content = $this->goCURL($url);
			$person = array();
			$keys = array("Company Name", "Street Address", "City", "Province/State", "Country/Region", "Zip", "Telephone", "Mobile Phone", "Fax", "Department", "Position", "Website");
			echo $url;
			if ($content)
			{
				$dom = new DOMDocument();
				$dom->loadHTML('<?xml encoding="utf-8">'.$content);
				$xpath = new DOMXPath($dom);
				$res_node = $xpath->query("//table[@class='tablefixed']/tbody/tr");
				foreach ($res_node as $node) {
					$res = explode(":", $node->textContent, 2);
					$person[$res[0]] = $res[1];
					
				}
				if ($res_node->length==0)
				{
					echo "    empty --------";
					echo "<br>";
					flush();
					return;
				}
				echo "    inserted ++++++++";	
				echo "<br>";	
				flush();

				foreach ($keys as $key) {
					if (!isset($person[$key]))
						$person[$key] = "";
				}
				$res_name = $xpath->query("//span[@class='contactName']");
				$name = "";
				foreach ($res_name as $n) {
					$name = $n->nodeValue;
				}

				
				$person["Contact Person"] = $name;
				// print_r($person);
				// die();
				unset($res_node);
				unset($xpath);
				unset($dom);

				$sql = "INSERT IGNORE INTO `aliexpress_data` 
					(
							`company_name`,
						  `street_address`,
						  `zip`,
						  `country_region`,
						  `province_state`,
						  `city`,
						  `telephone`,
						  `mobile phone`,
						  `fax`,
						  `website`,
						  `name`,
						  `position`					 
					) VALUES
					(
						'".mysql_real_escape_string(trim($person["Company Name"]))."', 
						'".mysql_real_escape_string(trim($person["Street Address"]))."', 
						'".trim($person["Zip"])."', 
						'".mysql_real_escape_string(trim($person["Country/Region"]))."', 
						'".mysql_real_escape_string(trim($person["Province/State"]))."', 
						'".mysql_real_escape_string(trim($person["City"]))."', 
						'".trim($person["Telephone"])."', 
						'".trim($person["Mobile Phone"])."', 
						'".trim($person["Fax"])."', 
						'".trim($person["Website"])."',
						'".mysql_real_escape_string(trim($person["Contact Person"]))."',
						'".mysql_real_escape_string(trim($person["Position"]))."'
						
					)
				";
				
				if(mysql_query($sql, $this->con)){
				} 
				else 	die($sql.'<br>Error: ' . mysql_error());			
				
				return;
			}
		}

		//загрузка материала
		function goCURL($urld){
			//usleep(100000);
			$ch = curl_init ();
			curl_setopt ($ch , CURLOPT_URL , $urld);
			curl_setopt ($ch , CURLOPT_USERAGENT , "Mozilla/7.0");
			curl_setopt ($ch , CURLOPT_RETURNTRANSFER , 1 );
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}
		
				
}
