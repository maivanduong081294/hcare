<?php
	function pre($var)
	{
		echo '<pre>';
                    print_r($var);
                echo"</pre>";
	}
	
	function pr($var)
	{
		echo '<pre>';
                    print_r($var);
                echo"</pre>";
	}
	
	
	class Business_Emailing_Common_common
	{
		static $_session_name = 'email_campaign_id';
		
		public static function genFileName($listid, $ext = ".txt")
		{
			$words = Business_Emailing_Common_Utils::generateRandomWord(6);
			return $listid.'-'.date('YMDhis').'-'.$words. $ext;
		}
		
		public static function getEmailCampaignId()
		{
			if (isset($_SESSION[self::$_session_name])) 
				return $_SESSION[self::$_session_name];
			else
				return 0; 
		}
		
		public static function setEmailCampaignId($value = 0)
		{
			$_SESSION[self::$_session_name] = $value;
		}
		
		public static function isValidEmail($email)
		{
			return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
		}
		
		public static function getUrlContent($url)
		{
			$c = curl_init();
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_URL, $url);
			$contents = curl_exec($c);
			curl_close($c);
			return $contents;
		}
		
		public function getDirContent($dir)
		{
			$arrDir = array();
			if (is_dir($dir)) 
			{
	   			if ($dh = opendir($dir)) 
	   			{
	        		while (($file = readdir($dh)) !== false  ) 
	        		{
	        			if (is_dir($dir.$file) && $file!='.' && $file!='..' && $file!='.svn')
	        				$arrDir[] = $file;	
	        		}
	        		closedir($dh);
	   			}
    		}
    		return $arrDir; 
		}	
		
		public static function displayFCK($field_name,$value,$configs = array(), &$oFCKeditor)
		{
			$config = Zend_Registry::get('configuration');
				
	        // get fck editor directory
	        $fckeditor = $config->FckDir;
	        // require fck editor engine
	        require_once($fckeditor."/fckeditor.php");
	        $basePath = "/fckeditor/";
	        $oFCKeditor = new FCKeditor($field_name) ;
	        // set base path
	        $oFCKeditor->BasePath    = $basePath ;
	        //set value
	        $oFCKeditor->Value        = $value;
	        // set config
	        foreach($configs as $config_name => $config_value)
	        {
	            if(isset($oFCKeditor->$config_name))
	                    $oFCKeditor->$config_name = $config_value;
	        }
	        // show fck
//	        return $oFCKeditor->Create() ;
		}
		
		public static function getTemplateContent($template_name)
		{
			$config = Zend_Registry::get('configuration');
			$base_path = ROOT_PATH."/www/email_templates/".$template_name;
			$base_path .= "/index.html";
			$content = file_get_contents($base_path);
			$src = Business_Emailing_Common_Utils::grepSRC($content);
			$domain_name = $config->domain->name;
			foreach($src as $link)
			{
				$link = $domain_name . "/email_templates/".$template_name. '/'. $link;
				$replace[] = $link; 	
			}
			$content = str_replace($src, $replace, $content);
			return $content;
		}
		
		public static function getFileContent($file)
		{
			$tmp_name = $file['tmp_name'];
			$content = file_get_contents($tmp_name);
			unlink($tmp_name);
			return $content;
		}
		
		public static function _getUrlContent($url)
		{
			// Create a stream
			$opts = array(
			  'http'=>array(
			    'method'=>"GET",
			    'header'=>"Accept-language: en\r\n"
			  )
			);
			
			$context = stream_context_create($opts);
			
			// Open the file using the HTTP headers set above
			$file = file_get_contents($url, false, $context);
			return $file;
		}

		public static function getContentByCurl($url) 
		{
			$curlHandle = curl_init(); // init curl
			curl_setopt($curlHandle, CURLOPT_URL, $url); // set the url to fetch
			curl_setopt($curlHandle, CURLOPT_HEADER, 0);
			curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlHandle, CURLOPT_TIMEOUT,100);
			$content = curl_exec($curlHandle);
			curl_close($curlHandle);
			return $content;
		}
		
		public static function gen_time()
		{
			$time = array();
			for($i=0;$i<24;$i++)
			{
				$hour = $i;
				$minutes = 0;
				$time[] = $hour . ":" . $minutes. "0"; 
				$time[] = $hour . ":" . ($minutes + 30); 
			}
			return $time;
		}
	
		public static function activeLink($id)
		{
			echo '<script>';
		 	echo '$("#' . $id . '").addClass("active")'; 
			echo '</script>';
		}	
		public static function activeHeaderLink($id, $classname='bold')
		{
			echo '<script>';
		 	echo '$("#' . $id . '").addClass("'.$classname . '")'; 
			echo '</script>';
		}
		public static function convertDateToSave($date)
		{
			//date format type 09 Dec 2009 hh:mm:ss
			$time = strtotime($date);
			return date("Y-m-d H:i:s",$time);
		}

		public static function timeBetweenTwoDate($date1, $date2)
		{
			$start = strtotime($date1);
			$end = strtotime($date2);
			if ($start == $end)
				return "";
			if ($start > $end)
			{
				$temp = $start;
				$start = $end;
				$end = $temp;
				
			}
			$seconds = 0;
			while($start < $end)
			{
				$seconds++;
				$start++;
			}
			$one_day = 24 * 60 * 60;
			$one_hour = 60 * 60;
			$one_minute = 60;
			
			$result = array();
			$result['days'] = (int)($seconds / $one_day);
			$result['hours']= (int)( ( $seconds %  $one_day )  / $one_hour);
			$result['minutes']= (int) ( ( ( $seconds %  $one_day ) % $one_hour ) / $one_minute);
			$result['seconds']= (int) ( ( ( $seconds %  $one_day ) % $one_hour ) % $one_minute);
			
			$result['hours'] = $result['hours'] + $result['days']*24;
			return $result;
		}
		
		public function compareDate($date1, $date2)
		{
			if (strtotime($date1) > strtotime($date2))
				return 1;
			elseif (strtotime($date1) == strtotime($date2))
				return 0;
			return -1;
		}
		
		public static function link_to_title($link)
		{
			return str_replace(array("http://","/","&","?",";","=","/"),"_",$link);
		}
		
		public static function addDayToDate($date, $operator = '+', $day, $format = 'Y-m-d')
		{
			$date = $date . " " . $operator . $day . " day";
			$date =  strtotime($date);
			return date($format, $date);
			
		}
		
		
		public static function getCountriesListForChart()
		{
			$_countries = array(
				"AL" => "Albania",
				"AG" => "Algeria",
				"AO" => "Angola",
				"AC" => "Antigua and Barbuda",
				"AR" => "Argentina",
				"AM" => "Armenia",
				"AS" => "Australia",
				"AU" => "Austria",
				"AJ" => "Azerbaijan",
				"BF" => "Bahamas",
				"BA" => "Bahrain",
				"BG" => "Bangladesh",
				"BB" => "Barbados",
				"BO" => "Belarus",
				"BE" => "Belgium",
				"BH" => "Belize",
				"BN" => "Benin",
				"BT" => "Bhutan",
				"BL" => "Bolivia",
				"BK" => "Bosnia and Herzegovina",
				"BC" => "Botswana",
				"BR" => "Brazil",
				"IO" => "British Indian Ocean Territory",
				"VI" => "British Virgin Islands",
				"BX" => "Brunei Darussalam",
				"BU" => "Bulgaria",
				"UV" => "Burkina Faso",
				"BY" => "Burundi",
				"CB" => "Cambodia",
				"CM" => "Cameroon",
				"CA" => "Canada",
				"CV" => "Cape Verde",
				"CJ" => "Cayman Islands",
				"CT" => "Central African Republic",
				"CD" => "Chad",
				"CI" => "Chile",
				"CH" => "China",
				"KT" => "Christmas Island",
				"CK" => "Cocos (Keeling) Islands",
				"CO" => "Colombia",
				"CQ" => "Northern Mariana Islands",
				"CN" => "Comoros",
				"CF" => "Congo",
				"CW" => "Cook Islands",
				"CG" => "Democratic Republic of the Congo",
				"CS" => "Costa Rica",
				"IV" => "Cote d'Ivoire",
				"HR" => "Croatia",
				"CU" => "Cuba",
				"CY" => "Cyprus",
				"EZ" => "Czech Republic",
				"DA" => "Denmark",
				"DJ" => "Djibouti",
				"DO" => "Dominica",
				"DR" => "Dominican Republic",
				"EC" => "Ecuador",
				"EG" => "Egypt",
				"ES" => "El Salvador",
				"EK" => "Equatorial Guinea",
				"ER" => "Eritrea",
				"EN" => "Estonia",
				"ET" => "Ethiopia",
				"FK" => "Falkland Islands (Islas Malvinas)",
				"FO" => "Faroe Islands",
				"FM" => "Federated States of Micronesia",
				"FJ" => "Fiji",
				"FI" => "Finland",
				"FR" => "France",
				"FG" => "French Guiana",
				"GB" => "Gabon",
				"GA" => "Gambia",
				"GG" => "Georgia",
				"GM" => "Germany",
				"GH" => "Ghana",
				"GI" => "Gibraltar",
				"GO" => "Glorioso Islands",
				"GR" => "Greece",
				"GL" => "Greenland",
				"GJ" => "Grenada",
				"GP" => "Guadeloupe",
				"GQ" => "Guam",
				"GT" => "Guatemala",
				"GV" => "Guinea",
				"GK" => "Guernsey",
				"PU" => "Guinea-Bissau",
				"GY" => "Guyana",
				"HA" => "Haiti",
				"HO" => "Honduras",
				"HQ" => "Howland Island",
				"HU" => "Hungary",
				"IC" => "Iceland",
				"IN" => "India",
				"IO" => "British Indian Ocean Territory",
				"ID" => "Indonesia",
				"IR" => "Iran",
				"IZ" => "Iraq",
				"EI" => "Ireland",
				"IM" => "Isle of Man",
				"IS" => "Israel",
				"IT" => "Italy",
				"JM" => "Jamaica",
				"JN" => "Jan Mayen",
				"JA" => "Japan",
				"DQ" => "Jarvis Island",
				"JE" => "Jersey",
				"JQ" => "Johnston Atoll",
				"JO" => "Jordan",
				"JU" => "Juan De Nova Island",
				"KZ" => "Kazakhstan",
				"KE" => "Kenya",
				"KS" => "South Korea",
				"KU" => "Kuwait",
				"KG" => "Kyrgyzstan",
				"LA" => "Laos",
				"LG" => "Latvia",
				"LE" => "Lebanon",
				"LT" => "Lesotho",
				"LI" => "Liberia",
				"LY" => "Libyan Arab Jamahiriya",
				"LS" => "Liechtenstein",
				"LH" => "Lithuania",
				"LU" => "Luxembourg",
				"MK" => "Macedonia,TFYR",
				"MA" => "Madagascar",
				"MI" => "Malawi",
				"MY" => "Malaysia",
				"MV" => "Maldives",
				"ML" => "Mali",
				"MT" => "Malta",
				"RM" => "Marshall Islands",
				"MB" => "Martinique",
				"MR" => "Mauritania",
				"MP" => "Mauritius",
				"MX" => "Mexico",
				"MQ" => "Midway Islands",
				"MD" => "Moldova",
				"MG" => "Mongolia",
				"MO" => "Morocco",
				"MZ" => "Mozambique",
				"BM" => "Myanmar (Burma)",
				"WA" => "Namibia",
				"NR" => "Nauru",
				"NP" => "Nepal",
				"NT" => "Netherlands Antilles",
				"NL" => "Netherlands",
				"NC" => "New Caledonia",
				"NZ" => "New Zealand",
				"NU" => "Nicaragua",
				"NG" => "Niger",
				"NI" => "Nigeria",
				"NE" => "Niue",
				"NF" => "Norfolk Island",
				"KN" => "North Korea",
				"CQ" => "Northern Mariana Islands",
				"NO" => "Norway",
				"GZ" => "Occupied Palestinian Territories",
				"MU" => "Oman",
				"PS" => "Pacific Islands (Palau)",
				"PK" => "Pakistan",
				"PM" => "Panama",
				"PP" => "Papua New Guinea",
				"PF" => "Paracel Islands",
				"PA" => "Paraguay",
				"PE" => "Peru",
				"RP" => "Philippines",
				"PC" => "Pitcairn Islands",
				"PL" => "Poland",
				"PO" => "Portugal",
				"RQ" => "Puerto Rico",
				"QA" => "Qatar",
				"RE" => "Reunion",
				"RO" => "Romania",
				"RS" => "Russian Federation",
				"RW" => "Rwanda",
				"SC" => "Saint Kitts and Nevis",
				"ST" => "Saint Lucia",
				"SH" => "St. Helena",
				"WS" => "Western Samoa",
				"TP" => "Sao Tome and Principe",
				"SA" => "Saudi Arabia",
				"SG" => "Senegal",
				"SR" => "Serbia",
				"KV" => "Kosovo",
				"MW" => "Montenegro",
				"SE" => "Seychelles",
				"SL" => "Sierra Leone",
				"SN" => "Singapore",
				"LO" => "Slovakia",
				"SI" => "Slovenia",
				"BP" => "Solomon Islands",
				"SO" => "Somalia",
				"SF" => "South Africa",
				"SP" => "Spain",
				"CE" => "Sri Lanka",
				"VC" => "St. Vincent and the Grenadines",
				"SU" => "Sudan",
				"TE" => "East Timor",
				"NS" => "Suriname",
				"WZ" => "Swaziland",
				"SW" => "Sweden",
				"SZ" => "Switzerland",
				"SY" => "Syrian Arab Republic",
				"TW" => "Taiwan",
				"TI" => "Tajikistan",
				"TZ" => "United Republic of Tanzania",
				"TH" => "Thailand",
				"TO" => "Togo",
				"TD" => "Trinidad and Tobago",
				"TS" => "Tunisia",
				"TU" => "Turkey",
				"TX" => "Turkmenistan",
				"UG" => "Uganda",
				"UP" => "Ukraine",
				"TC" => "United Arab Emirates",
				"UK" => "United Kingdom",
				"US" => "United States",
				"UY" => "Uruguay",
				"UZ" => "Uzbekistan",
				"NH" => "Vanuatu",
				"VE" => "Venezuela",
				"VM" => "Viet Nam",
				"WQ" => "Wake Island",
				"WI" => "Western Sahara",
				"WS" => "Western Samoa",
				"YM" => "Yemen",
				"ZA" => "Zambia",
				"ZI" => "Zimbabwe"
			);
				return $_countries;
		}
		
		public static function getCountriesList($code = '')
		{
			$_countries = array(
				'AF'=>'AFGHANISTAN',
				'AL'=>'ALBANIA',
				'DZ'=>'ALGERIA',
				'AS'=>'AMERICAN SAMOA',
				'AD'=>'ANDORRA',
				'AO'=>'ANGOLA',
				'AI'=>'ANGUILLA',
				'AQ'=>'ANTARCTICA',
				'AG'=>'ANTIGUA AND BARBUDA',
				'AR'=>'ARGENTINA',
				'AM'=>'ARMENIA',
				'AW'=>'ARUBA',
				'AU'=>'AUSTRALIA',
				'AT'=>'AUSTRIA',
				'AZ'=>'AZERBAIJAN',
				'BS'=>'BAHAMAS',
				'BH'=>'BAHRAIN',
				'BD'=>'BANGLADESH',
				'BB'=>'BARBADOS',
				'BY'=>'BELARUS',
				'BE'=>'BELGIUM',
				'BZ'=>'BELIZE',
				'BJ'=>'BENIN',
				'BM'=>'BERMUDA',
				'BT'=>'BHUTAN',
				'BO'=>'BOLIVIA',
				'BA'=>'BOSNIA AND HERZEGOVINA',
				'BW'=>'BOTSWANA',
				'BV'=>'BOUVET ISLAND',
				'BR'=>'BRAZIL',
				'IO'=>'BRITISH INDIAN OCEAN TERRITORY',
				'BN'=>'BRUNEI DARUSSALAM',
				'BG'=>'BULGARIA',
				'BF'=>'BURKINA FASO',
				'BI'=>'BURUNDI',
				'KH'=>'CAMBODIA',
				'CM'=>'CAMEROON',
				'CA'=>'CANADA',
				'CV'=>'CAPE VERDE',
				'KY'=>'CAYMAN ISLANDS',
				'CF'=>'CENTRAL AFRICAN REPUBLIC',
				'TD'=>'CHAD',
				'CL'=>'CHILE',
				'CN'=>'CHINA',
				'CX'=>'CHRISTMAS ISLAND',
				'CC'=>'COCOS (KEELING) ISLANDS',
				'CO'=>'COLOMBIA',
				'KM'=>'COMOROS',
				'CG'=>'CONGO',
				'CD'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
				'CK'=>'COOK ISLANDS',
				'CR'=>'COSTA RICA',
				'CI'=>'COTE D IVOIRE',
				'HR'=>'CROATIA',
				'CU'=>'CUBA',
				'CY'=>'CYPRUS',
				'CZ'=>'CZECH REPUBLIC',
				'DK'=>'DENMARK',
				'DJ'=>'DJIBOUTI',
				'DM'=>'DOMINICA',
				'DO'=>'DOMINICAN REPUBLIC',
				'TP'=>'EAST TIMOR',
				'EC'=>'ECUADOR',
				'EG'=>'EGYPT',
				'SV'=>'EL SALVADOR',
				'GQ'=>'EQUATORIAL GUINEA',
				'ER'=>'ERITREA',
				'EE'=>'ESTONIA',
				'ET'=>'ETHIOPIA',
				'FK'=>'FALKLAND ISLANDS (MALVINAS)',
				'FO'=>'FAROE ISLANDS',
				'FJ'=>'FIJI',
				'FI'=>'FINLAND',
				'FR'=>'FRANCE',
				'GF'=>'FRENCH GUIANA',
				'PF'=>'FRENCH POLYNESIA',
				'TF'=>'FRENCH SOUTHERN TERRITORIES',
				'GA'=>'GABON',
				'GM'=>'GAMBIA',
				'GE'=>'GEORGIA',
				'DE'=>'GERMANY',
				'GH'=>'GHANA',
				'GI'=>'GIBRALTAR',
				'GR'=>'GREECE',
				'GL'=>'GREENLAND',
				'GD'=>'GRENADA',
				'GP'=>'GUADELOUPE',
				'GU'=>'GUAM',
				'GT'=>'GUATEMALA',
				'GN'=>'GUINEA',
				'GW'=>'GUINEA-BISSAU',
				'GY'=>'GUYANA',
				'HT'=>'HAITI',
				'HM'=>'HEARD ISLAND AND MCDONALD ISLANDS',
				'VA'=>'HOLY SEE (VATICAN CITY STATE)',
				'HN'=>'HONDURAS',
				'HK'=>'HONG KONG',
				'HU'=>'HUNGARY',
				'IS'=>'ICELAND',
				'IN'=>'INDIA',
				'ID'=>'INDONESIA',
				'IR'=>'IRAN, ISLAMIC REPUBLIC OF',
				'IQ'=>'IRAQ',
				'IE'=>'IRELAND',
				'IL'=>'ISRAEL',
				'IT'=>'ITALY',
				'JM'=>'JAMAICA',
				'JP'=>'JAPAN',
				'JO'=>'JORDAN',
				'KZ'=>'KAZAKSTAN',
				'KE'=>'KENYA',
				'KI'=>'KIRIBATI',
				'KP'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
				'KR'=>'KOREA REPUBLIC OF',
				'KW'=>'KUWAIT',
				'KG'=>'KYRGYZSTAN',
				'LA'=>'LAO PEOPLES DEMOCRATIC REPUBLIC',
				'LV'=>'LATVIA',
				'LB'=>'LEBANON',
				'LS'=>'LESOTHO',
				'LR'=>'LIBERIA',
				'LY'=>'LIBYAN ARAB JAMAHIRIYA',
				'LI'=>'LIECHTENSTEIN',
				'LT'=>'LITHUANIA',
				'LU'=>'LUXEMBOURG',
				'MO'=>'MACAU',
				'MK'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
				'MG'=>'MADAGASCAR',
				'MW'=>'MALAWI',
				'MY'=>'MALAYSIA',
				'MV'=>'MALDIVES',
				'ML'=>'MALI',
				'MT'=>'MALTA',
				'MH'=>'MARSHALL ISLANDS',
				'MQ'=>'MARTINIQUE',
				'MR'=>'MAURITANIA',
				'MU'=>'MAURITIUS',
				'YT'=>'MAYOTTE',
				'MX'=>'MEXICO',
				'FM'=>'MICRONESIA, FEDERATED STATES OF',
				'MD'=>'MOLDOVA, REPUBLIC OF',
				'MC'=>'MONACO',
				'MN'=>'MONGOLIA',
				'MS'=>'MONTSERRAT',
				'MA'=>'MOROCCO',
				'MZ'=>'MOZAMBIQUE',
				'MM'=>'MYANMAR',
				'NA'=>'NAMIBIA',
				'NR'=>'NAURU',
				'NP'=>'NEPAL',
				'NL'=>'NETHERLANDS',
				'AN'=>'NETHERLANDS ANTILLES',
				'NC'=>'NEW CALEDONIA',
				'NZ'=>'NEW ZEALAND',
				'NI'=>'NICARAGUA',
				'NE'=>'NIGER',
				'NG'=>'NIGERIA',
				'NU'=>'NIUE',
				'NF'=>'NORFOLK ISLAND',
				'MP'=>'NORTHERN MARIANA ISLANDS',
				'NO'=>'NORWAY',
				'OM'=>'OMAN',
				'PK'=>'PAKISTAN',
				'PW'=>'PALAU',
				'PS'=>'PALESTINIAN TERRITORY, OCCUPIED',
				'PA'=>'PANAMA',
				'PG'=>'PAPUA NEW GUINEA',
				'PY'=>'PARAGUAY',
				'PE'=>'PERU',
				'PH'=>'PHILIPPINES',
				'PN'=>'PITCAIRN',
				'PL'=>'POLAND',
				'PT'=>'PORTUGAL',
				'PR'=>'PUERTO RICO',
				'QA'=>'QATAR',
				'RE'=>'REUNION',
				'RO'=>'ROMANIA',
				'RU'=>'RUSSIAN FEDERATION',
				'RW'=>'RWANDA',
				'SH'=>'SAINT HELENA',
				'KN'=>'SAINT KITTS AND NEVIS',
				'LC'=>'SAINT LUCIA',
				'PM'=>'SAINT PIERRE AND MIQUELON',
				'VC'=>'SAINT VINCENT AND THE GRENADINES',
				'WS'=>'SAMOA',
				'SM'=>'SAN MARINO',
				'ST'=>'SAO TOME AND PRINCIPE',
				'SA'=>'SAUDI ARABIA',
				'SN'=>'SENEGAL',
				'SC'=>'SEYCHELLES',
				'SL'=>'SIERRA LEONE',
				'SG'=>'SINGAPORE',
				'SK'=>'SLOVAKIA',
				'SI'=>'SLOVENIA',
				'SB'=>'SOLOMON ISLANDS',
				'SO'=>'SOMALIA',
				'ZA'=>'SOUTH AFRICA',
				'GS'=>'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
				'ES'=>'SPAIN',
				'LK'=>'SRI LANKA',
				'SD'=>'SUDAN',
				'SR'=>'SURINAME',
				'SJ'=>'SVALBARD AND JAN MAYEN',
				'SZ'=>'SWAZILAND',
				'SE'=>'SWEDEN',
				'CH'=>'SWITZERLAND',
				'SY'=>'SYRIAN ARAB REPUBLIC',
				'TW'=>'TAIWAN, PROVINCE OF CHINA',
				'TJ'=>'TAJIKISTAN',
				'TZ'=>'TANZANIA, UNITED REPUBLIC OF',
				'TH'=>'THAILAND',
				'TG'=>'TOGO',
				'TK'=>'TOKELAU',
				'TO'=>'TONGA',
				'TT'=>'TRINIDAD AND TOBAGO',
				'TN'=>'TUNISIA',
				'TR'=>'TURKEY',
				'TM'=>'TURKMENISTAN',
				'TC'=>'TURKS AND CAICOS ISLANDS',
				'TV'=>'TUVALU',
				'UG'=>'UGANDA',
				'UA'=>'UKRAINE',
				'AE'=>'UNITED ARAB EMIRATES',
				'GB'=>'UNITED KINGDOM',
				'US'=>'UNITED STATES',
				'UM'=>'UNITED STATES MINOR OUTLYING ISLANDS',
				'UY'=>'URUGUAY',
				'UZ'=>'UZBEKISTAN',
				'VU'=>'VANUATU',
				'VE'=>'VENEZUELA',
				'VN'=>'VIET NAM',
				'VG'=>'VIRGIN ISLANDS, BRITISH',
				'VI'=>'VIRGIN ISLANDS, U.S.',
				'WF'=>'WALLIS AND FUTUNA',
				'EH'=>'WESTERN SAHARA',
				'YE'=>'YEMEN',
				'YU'=>'YUGOSLAVIA',
				'ZM'=>'ZAMBIA',
				'ZW'=>'ZIMBABWE',
					
			);
			if ($code != '')
			{
				if(array_key_exists($code,$_countries))
					return $_countries[$code];
			}
			else
				return $_countries;
		}
		
		public static function getTimezoneList($zone = '')
		{
			$timezoneTable = array(
				"-12" => "(GMT -12:00) Eniwetok, Kwajalein",
				"-11" => "(GMT -11:00) Midway Island, Samoa",
				"-10" => "(GMT -10:00) Hawaii",
				"-9" => "(GMT -9:00) Alaska",
				"-8" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
				"-7" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
				"-6" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
				"-5" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
				"-4" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
				"-3.5" => "(GMT -3:30) Newfoundland",
				"-3" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
				"-2" => "(GMT -2:00) Mid-Atlantic",
				"-1" => "(GMT -1:00 hour) Azores, Cape Verde Islands",
				"0" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
				"1" => "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
				"2" => "(GMT +2:00) Kaliningrad, South Africa",
				"3" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
				"3.5" => "(GMT +3:30) Tehran",
				"4" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
				"4.5" => "(GMT +4:30) Kabul",
				"5" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
				"5.5" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
				"6" => "(GMT +6:00) Almaty, Dhaka, Colombo",
				"7" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
				"8" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
				"9" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
				"9.5" => "(GMT +9:30) Adelaide, Darwin",
				"10" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
				"11" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
				"12" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
			);
			if ($zone != '')
			{
				if(array_key_exists($zone,$timezoneTable))
					return $timezoneTable[$zone];
			}
			else 
				return $timezoneTable;
		}
		/**
		 * Them 1 ngay vao ngay hien tai, Fix loi thang 2
		 * format: Y-m-d H:i:s
		 *
		 * @param Datetime $date
		 */
		static function addMonthToDate($date, $numofmonths)
		{
			$datetime = $date;
			for($i=0;$i<$numofmonths;$i++)
			{
				$time = strtotime($datetime);
				$day = date("d",$time);
				$month = intval(date("m",$time));
				$year = date("Y",$time);
				$hour = date("H",$time);
				$minutes = date("i",$time);
				$seconds = date("s",$time);
				if($month == 1)
				{
					$maxDayOfFeb = date("t",strtotime("$year-02-01 00:00:00"));
//					$date = new DateTime("$year-$month-$day $hour:$minutes:$seconds");
//					$datetime = $date->format("Y-m-d H:i:s");
//					$date->modify("+$maxDayOfFeb day");
//					$datetime = $date->format("Y-m-d H:i:s");
					$month += 1;
					$maxDayOfFeb = date("t",strtotime("$year-02-01 00:00:00"));
					$maxDayOfFeb -= 1;
					$date = new DateTime("$year-$month-$maxDayOfFeb $hour:$minutes:$seconds");
					$datetime = $date->format("Y-m-d H:i:s");
				}
				else
				{
					$date = new DateTime("$year-$month-$day $hour:$minutes:$seconds");
					$date->modify("+1 month");
					$datetime = $date->format("Y-m-d H:i:s");
				}
			}
			$time = new DateTime($datetime);
			return $time;
		}
		
		static function tripMark($str)
		{
			$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
			$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
			$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
			$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
			$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
			$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
			$str = preg_replace("/(đ)/", 'd', $str);
			$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
			$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
			$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
			$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
			$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
			$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
			$str = preg_replace("/(Đ)/", 'D', $str);
			return $str;
		}
		
		static function deleteAllFileInDirWithExt($dir, $ext = array('xml'))
		{
			if(is_dir($dir))
			{
				$dh = opendir($dir);
				while ($file = readdir($dh)) 
				{
					if ($file != '.' && $file != '..')
					{
						$file_ext = end(explode(".", $file));
						if (in_array($file_ext, $ext))
						{
							unlink($dir."/".$file);
						}
					}
    			}
    			return 1;
			}
			else
				return 0; // not is dir 
		}
		
		static function getCurrentUrl()
		{
			$config = Zend_Registry::get('configuration');
			$domain_name = $config->domain->name;
			return $domain_name.$_SERVER["REQUEST_URI"];
		}
		
		static function getUsernameByPid($provider_id)
		{
			$providerModel = Business_Emailing_Provider::getInstance();
			$provider = $providerModel->getDetail($provider_id);
			$uid = $provider['uid'];
			$userModel = Business_Emailing_User::getInstance();
			$user = $userModel->getUserById($uid);
			return $user['name'];
		}
		
		static function writeFileToTmpDir($content, $ext = ".bmp")
		{
			$tmp_dir = BASE_PATH."/tmp/";
			if (!is_dir($tmp_dir))
				mkdir($tmp_dir,0777,true);
			
			$filename = self::genFileName(rand(1,100),$ext);
			$path = $tmp_dir.$filename;
			$fh = fopen($path,"w+");
			fwrite($fh, $content);
			fclose($fh);
			return $path;
		}
		
		static function deleteFileFromTmpDir($filename)
		{
			$tmp_dir = BASE_PATH."/tmp/";
			$path = $tmp_dir.$filename;
			if (is_file($path))
				unlink($path);
		}
		
		static function png2bmp($filename)
		{
			if (is_file($filename))
			{
				$imageOgj = imagecreatefrompng($filename);
				self::imagebmp($imageOgj, $filename.".bmp");
			}
		}
		
		static function imagebmp(&$im, $filename = "")
		{
			if (!$im) return false;
			$w = imagesx($im);
			$h = imagesy($im);
			$result = '';
		
			if (!imageistruecolor($im)) {
				$tmp = imagecreatetruecolor($w, $h);
				imagecopy($tmp, $im, 0, 0, 0, 0, $w, $h);
				imagedestroy($im);
				$im = & $tmp;
			}
			
			$biBPLine = $w * 3;
			$biStride = ($biBPLine + 3) & ~3;
			$biSizeImage = $biStride * $h;
			$bfOffBits = 54;
			$bfSize = $bfOffBits + $biSizeImage;
			
			$result .= substr('BM', 0, 2);
			$result .= pack ('VvvV', $bfSize, 0, 0, $bfOffBits);
			$result .= pack ('VVVvvVVVVVV', 40, $w, $h, 1, 24, 0, $biSizeImage, 0, 0, 0, 0);
			
			$numpad = $biStride - $biBPLine;
			for ($y = $h - 1; $y >= 0; --$y) {
				for ($x = 0; $x < $w; ++$x) {
					$col = imagecolorat ($im, $x, $y);
					$result .= substr(pack ('V', $col), 0, 3);
				}
				for ($i = 0; $i < $numpad; ++$i)
					$result .= pack ('C', 0);
			}
			
			if($filename==""){
				echo $result;
			}
			else
			{
				$file = fopen($filename, "wb");
				fwrite($file, $result);
				fclose($file);
			}
			return true;
		}
		
		static function getIntroText($string)
		{
			return substr($string,0,100)." ... ";
		}

		/**
		 * Enter description here...
		 *
		 * @param int $pid
		 * @param Char $type // "A": pay as you go; "M": pay monthly
		 */		
		static function getPurchaseId($pid, $type = "A", $number)
		{
			return $type.$pid."-".date("dmy")."-".$number;
		}
		
		static function getLastPurchaseNumber($purchase_id)
		{
			$result = explode("-",$purchase_id);
			if (is_array($result))
			{
				$date = $result[1];
				$cur_date = date("dmy");
				
				if ($date == $cur_date)
					return $result[count($result)-1];
				else
					return 0;		
			}
			
		}
		
		/**
		 * return array in arrayList with max value of colname 
		 *
		 * @param arrayList $data
		 * @param string $colname
		 */
		static function maxArray($data = array(), $colname)
		{
			if (!empty($data) && is_array($data) )
			{
				foreach($data as $line)
				{
					if (!array_key_exists($colname,$line))
						return array();
					$max_value_arr[] = $line[$colname];
				}
				if (is_array($max_value_arr))
				{
					$max_value = max($max_value_arr);
				}
				// get array with max value pos
				foreach($data as $line)
				{
					if ($line[$colname] == $max_value)
					{
						return $line;
					}
				}
			}
			else
				return array();
		}
	}
?>