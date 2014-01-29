<?php
	//error_reporting(0);
	error_reporting (E_ERROR);
	@header('Content-Type: text/html; charset=utf-8'); 
	 
	include_once("config.php");
	 
	$parser = new IkeaParser();
	$parser->parseStart();
	
class IkeaParser{
	//DB CONFIG
	var $db_host = DB_HOST;
	var $db_name = DB_NAME;
	var $db_user = DB_USER;
	var $db_password = DB_PASSWORD;
	
	//DATA
	var $id = 0;
	var $brand = "";
	var $title = "";
	var $description = "";
	var $size = "";
	var $url = "";
	
	var $urldonor = "http://www.alibaba.com";
	
	
	
		public function IkeaParser(){}
	
		public function parseStart(){
			//коннект к базе
			
			$this->con = mysql_connect($this->db_host, $this->db_user, $this->db_password);
			if (!$this->con){ die('Could not connect: ' . mysql_error()); }
			mysql_select_db($this->db_name, $this->con) or die(mysql_error());
			
			mysql_set_charset( 'utf8' );	

			// $this->parsePage("http://www.alibaba.com/Fruit_sid103_151");
			// die();
			// $this->parsePage("http://www.alibaba.com/Almonds_sid10401");
			// die();

			// $this->getPersonalInfo("http://in1024095983.fm.alibaba.com/contactinfo.html");
			// die();

			$content_category = $this->goCURL("http://www.alibaba.com/companies");
			$dom_category = new DOMDocument();
			$dom_category->loadHTML('<?xml encoding="utf-8">'.$content_category);
			$xpath_category = new DOMXPath($dom_category);
			$res_node = $xpath_category->query("//dt/a");
			$meta_ctn = 0;
			foreach ($res_node as $node) {
				echo "META Category"."<br>";
				flush();
				$url_category = $node->getAttribute('href');

				$this->parseCategory($url_category);

				$sql = "INSERT IGNORE INTO `parsed_urls` 
					(
						`parsed`
						  
					) VALUES
					(
						'".$url_category."'
					)
				";
				mysql_query($sql, $this->con);
			}




			

			// $content = $this->goCURL("http://www.alibaba.com/Home-Garden_s15");
			// $dom = new DOMDocument();
			// $dom->loadHTML('<?xml encoding="utf-8">'.$content);
			// $xpath = new DOMXPath($dom);
			// $res_node = $xpath->query("//li/a");
			// foreach ($res_node as $node) {
			// 	//print_r($node);
			// 	$url = $node->getAttribute('href');
			// 	if ($url[0]=="/")
			// 		echo $url."<br>";
			// }
			// die();

			// $content = $this->goCURL("http://www.alibaba.com/Hazelnuts_sid10408_2");
			// $dom = new DOMDocument();
			// $dom->loadHTML('<?xml encoding="utf-8">'.$content);
			// $xpath = new DOMXPath($dom);
			// $res_node = $xpath->query("//h2[@class='title ellipsis']/a");
			// if ($res_node->length==0)
			// 	echo "empty";
			// foreach ($res_node as $node) {
			// 	//print_r($node);
			// 	$url = $node->getAttribute('href');
			// 	echo $url."<br>";
			// }
			// die();

			// $content = $this->goCURL("http://www.alibaba.com/member/bukhaidze/contactinfo.html");
			// $dom = new DOMDocument();
			// $dom->loadHTML('<?xml encoding="utf-8">'.$content);
			// $xpath = new DOMXPath($dom);
			// $res_node = $xpath->query("//h2[@class='title ellipsis']/a");
			// foreach ($res_node as $node) {
			// 	//print_r($node);
			// 	$url = $node->getAttribute('href');
			// 	echo $url."<br>";
			// }
			// die();

			//$this->getPersonalInfo("http://www.alibaba.com/member/bukhaidze/contactinfo.html");





			
			//$this->dom = new DOMDocument();
						
			// $this->getPersonalInfo("http://triangletyre.en.alibaba.com/contactinfo.html");
			//  $this->getPersonalInfo("http://ideal88.en.alibaba.com/contactinfo.html");
			//  $this->getPersonalInfo("http://zcjx.en.alibaba.com/contactinfo.html");
			// $this->getPersonalInfo("http://xiangdiwiremesh.en.alibaba.com/contactinfo.html");
			//$this->getPersonalInfo("http://in1021848442.trustpass.alibaba.com/contactinfo.html");
			//$this->getPersonalInfo("http://sunimpexfze.trustpass.alibaba.com/contactinfo.html#top-nav-bar");
			//$this->getPersonalInfo("http://www.alibaba.com/member/es1020425124/contactinfo.html#top-nav-bar");
			
			
		}

		function parseCategory($url)
		{
			$productQuery = 'SELECT * FROM `parsed_urls` WHERE parsed="'.$url.'" ';
			$sql = mysql_query($productQuery);
			$is = 0;
			while($row = mysql_fetch_array($sql)){
				$is++;
			}
			if($is > 0)
			{
				return;
			}

			$content_subcat = $this->goCURL($url);

			$dom_subcat = new DOMDocument();
			$dom_subcat->loadHTML('<?xml encoding="utf-8">'.$content_subcat);
			$xpath_subcat = new DOMXPath($dom_subcat);
			//$res_node_subcat = $xpath_subcat->query("//li/a");
			$res_node_subcat = $xpath_subcat->query("//div/ul/li/a");
			foreach ($res_node_subcat as $node_subcat) {
				//print_r($node_subcat);
				$url_subcat = $node_subcat->getAttribute('href');
				if ($url_subcat[0]=="/")
				{
					//echo $this->urldonor.$url_subcat."<br>";
					//echo $url_subcat;;
					$this->parsePage($this->urldonor.$url_subcat);

					$sql = "INSERT IGNORE INTO `parsed_urls` 
						(
							`parsed`
							  
						) VALUES
						(
							'".$this->urldonor.$url_subcat."'
						)
					";
					mysql_query($sql, $this->con);
				}
										
			}
			
			unset($dom_subcat);
			unset($xpath_subcat);
			unset($res_node_subcat);
		}

		function parsePage($url)
		{
			$productQuery = 'SELECT * FROM `parsed_urls` WHERE parsed="'.$url.'" ';
			$sql = mysql_query($productQuery);
			$is = 0;
			while($row = mysql_fetch_array($sql)){
				$is++;
			}
			if($is > 0)
			{
				return;
			}	

			$content_page = $this->goCURL($url);
			//echo $url_subcat;
			//echo $content_page;

			$dom_page = new DOMDocument();
			$dom_page->loadHTML('<?xml encoding="utf-8">'.$content_page);
			$xpath_page = new DOMXPath($dom_page);
			$res_node_page = $xpath_page->query("//h2[@class='title ellipsis']/a");
			$i = 2;
			//print_r($res_node_page);
			echo "Category page ".$url."<br>";
			echo "Page 1"."<br>";
			flush();
			$parsed = false;
			while ($res_node_page->length>0)
			{	
				echo "Page ".$i."<br>";
				flush();
				if (!$parsed)
				{
					foreach ($res_node_page as $node_page) {
						//print_r($node_page);
						$url_page = $node_page->getAttribute('href');
						$url_page = str_replace("company_profile", "contactinfo", $url_page);
						echo "Page url_page $url_page<br>";
						$this->getPersonalInfo($url_page);
						//echo $url_page."<br>";
					}	
					$sql = "INSERT IGNORE INTO `parsed_urls` 
						(
							`parsed`
							  
						) VALUES
						(
							'".$url."_".($i-1)."'
						)
					";
					if (mysql_query($sql, $this->con))
					{

					}
					else
					{
						die($sql.'<br>Error 2: ' . mysql_error());
					}
				}
				
				$productQuery = 'SELECT * FROM `parsed_urls` WHERE parsed="'.($url."_".$i).'" ';
				$sql = mysql_query($productQuery);
				$is = 0;
				while($row = mysql_fetch_array($sql)){
					$is++;
				}
				if($is > 0)
				{
					$parsed = true;
				}
				else
				{
					$parsed = false;	
				}
				if ($parsed)
				{
					$i++;
					continue;
				}
				else
				{
					$content_page = $this->goCURL($url."_".$i++);
					unset($dom_page);
					unset($xpath_page);
					$dom_page = new DOMDocument();
					$dom_page->loadHTML('<?xml encoding="utf-8">'.$content_page);
					$xpath_page = new DOMXPath($dom_page);
					$res_node_page = $xpath_page->query("//h2[@class='title ellipsis']/a");	
				}
				
			}
			unset($dom_page);
			unset($xpath_page);
			unset($res_node_page);
			// if ($res_node_page->length==0)
			// 	echo "empty";
			//die();
		}

		function getPersonalInfo($url)
		{
			echo "begin url ".$url." ";
			preg_match( '#(.[^>]+?)/contactinfo.html.*$#s', $url, $value );	//			.[^>]+?

			$productQuery = 'SELECT * FROM `alibaba_data` WHERE url="'.$value[1].'" ';
			$sql = mysql_query($productQuery);
			$is = 0;
			while($row = mysql_fetch_array($sql)){
				$is++;
			}
			if($is > 0)
			{
				return;
			}	
			
			$content = $this->goCURL($url);

			if ($content)
			{
				$dom = new DOMDocument();
				$dom->loadHTML('<?xml encoding="utf-8">'.$content);
				$xpath = new DOMXPath($dom);
				$res_node = $xpath->query("//div[@id='contact-person']");

				$company_name ='';
				$operational_address ='';
				$website ='';
				
				if ($res_node->length==0)
				{
					$res_node = $xpath->query("//p/a[text()='here']");
					foreach ($res_node as $node_page) {
						$url = $node_page->getAttribute('href');
					}

					preg_match( '#(.[^>]+?)/contactinfo.html.*$#s', $url, $value );	

					$productQuery = 'SELECT * FROM `alibaba_data` WHERE url="'.$value[1].'" ';
					$sql = mysql_query($productQuery);
					$is = 0;
					while($row = mysql_fetch_array($sql)){
						$is++;
					}
					if($is > 0)
					{
						unset($res_node);
						unset($xpath);
						unset($dom);
						return;
					}

					$content = $this->goCURL($url);
					$dom->loadHTML('<?xml encoding="utf-8">'.$content);
					$xpath = new DOMXPath($dom);
					$res_node = $xpath->query("//div[@id='contact-person']");
				}
				echo $url;
				foreach ($res_node as $node) {
					//print_r($node);
					$page = $node->nodeValue;
					// echo $page;
					// die();
					$company_info = $xpath->query( "//div[@class='company-contact-information']" );
					// print_r($company_info);
					// die();
					$url ='';
					foreach ($company_info as $comp) {
						$_page = $comp->nodeValue;						
						//echo $_page;
						preg_match( '#((.[^>]+?)Company Name:)?(.[^>]+?)(Operational Address:(.[^>]+?))?(Website:(.[^>]+?))?(Website on alibaba.com:(.[^>]+?))?Aliexpress.com Store:.[^>]+?$#s', $_page, $_value );					

						$company_name = mysql_real_escape_string(trim($_value[3]));
						$operational_address = mysql_real_escape_string(trim($_value[5]));
						$website = trim($_value[7]);
						$url = trim($_value[9]);
						//print_r($_value);
					}
					unset($company_info);
					
					//echo $page;
					preg_match( '#(.[^>]+?)(Department:(.[^>]+?))?(Job Title:(.[^>]+?))?Contact Supplier.[^>]+?(Telephone:(.[^>]+?))?(Mobile Phone:(.[^>]+?))?(Fax:(.[^>]+?))?(Address:(.[^>]+?))?(Zip:(.[^>]+?))?(Country/Region:(.[^>]+?))?(Province/State:(.[^>]+?))?(City:(.[^>]+?))?$#s', $page, $value );					
					// if (empty($value))
					// {
					// 	preg_match( '#(.[^>]+?)Department:(.[^>]+?)Contact Supplier.[^>]+?Telephone:(.[^>]+?)Mobile Phone:(.[^>]+?)Fax:(.[^>]+?)Address:(.[^>]+?)Zip:(.[^>]+?)Country/Region:(.[^>]+?)Province/State:(.[^>]+?)City:(.[^>]+?)$#s', $page, $value );					
					// }
					// print_r($value);
					// die();
					// return;
					// preg_match( '#Department:(.[^>]+?)Title:#s', $page, $value );
					// $department = trim($value[1]);
					//preg_match( '#Title:(.[^>]+?)Contact Supplier#s', $page, $value );
					// preg_match( '#Title:(.[^>]+?)Contact Supplier#s', $page, $value );
					// $title = trim($value[1]);
					// preg_match( '#Telephone:(.[^>]+?)Mobile Phone:#s', $page, $value );
					// $telephone = trim($value[1]);
					// preg_match( '#Mobile Phone:(.[^>]+?)Fax:#s', $page, $value );
					// $mobile = trim($value[1]);
					// preg_match( '#Fax:(.[^>]+?)Address:#s', $page, $value );
					// $fax = trim($value[1]);
					// preg_match( '#Address:(.[^>]+?)Zip:#s', $page, $value );
					// $address = trim($value[1]);
					// preg_match( '#Zip:(.[^>]+?)Country/Region:#s', $page, $value );
					// $zip = trim($value[1]);
					// preg_match( '#Country/Region:(.[^>]+?)Province/State:#s', $page, $value );
					// $country = trim($value[1]);
					// preg_match( '#Province/State:(.[^>]+?)City:#s', $page, $value );
					// $province = trim($value[1]);
					// preg_match( '#City:(.[^>]+?)$#s', $page, $value );
					// $city = trim($value[1]);
					//echo $name.$title.$telephone.$mobile.$fax.$address.$zip.$country.$province.$city;
					$name = mysql_real_escape_string(trim($value[1]));
					$department = mysql_real_escape_string(trim($value[3]));
					$title = mysql_real_escape_string(trim($value[5]));
					$telephone = trim($value[7]);
					$mobile = trim($value[9]);
					$fax = trim($value[11]);
					$address = mysql_real_escape_string(trim($value[13]));
					$zip = trim($value[15]);
					$country = mysql_real_escape_string(trim($value[17]));
					$province = '';
					if (isset($value[19]))
						$province = mysql_real_escape_string(trim($value[19]));
					$city = '';
					if (isset($value[21]))
						$city = mysql_real_escape_string(trim($value[21]));
					
					$sql = "INSERT IGNORE INTO `alibaba_data` 
						(
							`company_name`,
							  `operational_address`,
							  `zip`,
							  `country_region`,
							  `province_state`,
							  `city`,
							  `address`,
							  `telephone`,
							  `mobile phone`,
							  `fax`,
							  `website`,
							  `url`,
							  `name`,
							  `job title`					 
						) VALUES
						(
							'".$company_name."', 
							'".$operational_address."', 
							'".$zip."', 
							'".$country."', 
							'".$province."', 
							'".$city."', 
							'".$address."', 
							'".$telephone."', 
							'".$mobile."', 
							'".$fax."',
							'".$website."',
							'".$url."',
							'".$name."',
							'".$title."'
						)
					";
					
					echo " inserting <br>";
					flush();
					
					if(mysql_query($sql, $this->con)){
					} 
					else 	die($sql.'<br>Error: ' . mysql_error());
					
				}
				unset($res_node);
				unset($xpath);
				unset($dom);	
				libxml_use_internal_errors(false);
				 libxml_use_internal_errors(true);
				 echo "person memory".memory_get_peak_usage(true) . "\r\n"; flush();		
				
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
