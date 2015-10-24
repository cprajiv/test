<?php

class FormHelper{
	
	public function construct(){
	}
	
	
	public function selected( $selected, $current = true, $echo = true ) {
		return $this->checkedSelectedHelper( $selected, $current, $echo, 'selected' );
	}
	
	private function checkedSelectedHelper( $helper, $current, $echo, $type ) {
		if ( (string) $helper === (string) $current )
			$result = " $type='$type'";
		else
			$result = '';
	
		if ( $echo )
			echo $result;
	
		return $result;
	}
	
	public function checked( $checked, $current = true, $echo = true ) {
		return $this->checkedSelectedHelper( $checked, $current, $echo, 'checked' );
	}
	
	public function disabled( $disabled, $current = true, $echo = true ) {
		return $this->checkedSelectedHelper( $disabled, $current, $echo, 'disabled' );
	}

	public function sanitizeStringWithDashes( $title, $raw_title = '', $context = 'display' ) {
		$title = strip_tags($title);
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		$title = str_replace('%', '', $title);
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
		if ($this->checkIfUtf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = $this->utf8UriEncode($title, 200);
		}
	
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
	
		if ( 'save' == $context ) {
			$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );
		$title = str_replace( array(
				'%c2%a1', '%c2%bf',
				'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
				'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
				'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
				'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
				'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
				'%cc%80', '%cc%84', '%cc%8c',
			), '', $title );
	
			$title = str_replace( '%c3%97', 'x', $title );
		}
	
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');
	
		return $title;
	}
	
	
	function sanitizeFileName($filename){
		
		$filename = strtolower($filename);
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
		
		$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_');
	
		$parts = explode('.', $filename);
	
		if (count( $parts ) <= 2 ) {
			return $filename;
		}

		//Start with Multiple extensions	
		$filename 	= array_shift($parts);
		$extension 	= array_pop($parts);
		$mimes		= $this->getBasicMimes();
	
		foreach ( (array) $parts as $part) {
			$filename .= '.' . $part;
	
			if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
				$allowed = false;
				foreach ( $mimes as $ext_preg => $mime_match ) {
					$ext_preg = '!^(' . $ext_preg . ')$!i';
					if ( preg_match( $ext_preg, $part ) ) {
						$allowed = true;
						break;
					}
				}
				if ( !$allowed )
					$filename .= '_';
			}
		}
		$filename .= '.' . $extension;
		
		return $filename;
	}
	
	
	protected function getBasicMimes() {
	
			return array(
			// Image formats
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'bmp' => 'image/bmp',
			'tif|tiff' => 'image/tiff',
			'ico' => 'image/x-icon',
			// Video formats
			'asf|asx' => 'video/x-ms-asf',
			'wmv' => 'video/x-ms-wmv',
			'wmx' => 'video/x-ms-wmx',
			'wm' => 'video/x-ms-wm',
			'avi' => 'video/avi',
			'divx' => 'video/divx',
			'flv' => 'video/x-flv',
			'mov|qt' => 'video/quicktime',
			'mpeg|mpg|mpe' => 'video/mpeg',
			'mp4|m4v' => 'video/mp4',
			'ogv' => 'video/ogg',
			'webm' => 'video/webm',
			'mkv' => 'video/x-matroska',
			// Text formats
			'txt|asc|c|cc|h' => 'text/plain',
			'csv' => 'text/csv',
			'tsv' => 'text/tab-separated-values',
			'ics' => 'text/calendar',
			'rtx' => 'text/richtext',
			'css' => 'text/css',
			'htm|html' => 'text/html',
			'vtt' => 'text/vtt',
			// Audio formats
			'mp3|m4a|m4b' => 'audio/mpeg',
			'ra|ram' => 'audio/x-realaudio',
			'wav' => 'audio/wav',
			'ogg|oga' => 'audio/ogg',
			'mid|midi' => 'audio/midi',
			'wma' => 'audio/x-ms-wma',
			'wax' => 'audio/x-ms-wax',
			'mka' => 'audio/x-matroska',
			// Misc application formats
			'rtf' => 'application/rtf',
			'pdf' => 'application/pdf',
			// MS Office formats
			'doc' => 'application/msword',
			'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
			'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			
			// OpenOffice formats
			'odt' => 'application/vnd.oasis.opendocument.text',
			'odp' => 'application/vnd.oasis.opendocument.presentation',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			'odc' => 'application/vnd.oasis.opendocument.chart'
			);
	}
	
	
	public function checkIfUtf8($str) {
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false;
			for ($j=0; $j<$n; $j++) {
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	public function utf8UriEncode( $utf8_string, $length = 0 ) {
			$unicode = '';
			$values = array();
			$num_octets = 1;
			$unicode_length = 0;
		
			$string_length = strlen( $utf8_string );
			for ($i = 0; $i < $string_length; $i++ ) {
		
				$value = ord( $utf8_string[ $i ] );
		
				if ( $value < 128 ) {
					if ( $length && ( $unicode_length >= $length ) )
						break;
					$unicode .= chr($value);
					$unicode_length++;
				} else {
					if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;
		
					$values[] = $value;
		
					if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
						break;
					if ( count( $values ) == $num_octets ) {
						if ($num_octets == 3) {
							$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
							$unicode_length += 9;
						} else {
							$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
							$unicode_length += 6;
						}
		
						$values = array();
						$num_octets = 1;
					}
				}
			}
		
			return $unicode;
	}
	
	
	public function calculateAgeByDob($dob){
			
			$today       = new DateTime();
			$birth 	     = new DateTime($dob);
			$difference  = $today->diff($birth);
			return $difference->format('%y'); 
     }

    public function calculateDurationByDateTime($datetime){
            $d1 = new DateTime($datetime);
            $d2 = new DateTime(date('Y-m-d H:i:s'));
            $interval = $d2->diff($d1);
            //return round(abs(strtotime($datetime) - time()) / 60). ' hours';
            return $interval->format('%d days, %H hours');
     }
	 
	 public function getApproxDobByAge($age=0){
		 	$d = new DateTime('today -'.intval($age).' years');
			return $d->format('Y-m-d');
	 }
	
	  
	 public function getFirstDayOfBirthYearByAge($age=0){
		 	$d = new DateTime('today -'.intval($age).' years');
			$year = $d->format('Y');
			return date('Y-m-d', strtotime('first day of January '.$year));
	 }
	 
	 public function getLastDayOfBirthYearByAge($age=0){
		 	$d = new DateTime('today -'.intval($age).' years');
			$year = $d->format('Y-m-d');
			return date('Y-m-d', strtotime('last day of December'.$year));
	 }
	 
	 
	 public function getTheDate($date,$format = '',$contains_time=false) {
		
		global $GeneralSettingsController; //mandatory to get db based date format
		
		if(empty($date)) return false;
		
		$db_date_fromat = $GeneralSettingsController->viewGeneralSetting('date_format');
		
		$format = (empty($format) ? ( empty($db_date_fromat) ? 'm-d-Y' : $db_date_fromat) : $format);
		$format = ($contains_time==true) ? $format.' H:i:s T' : $format;
		
				
		if($format == 'G') return strtotime( $date . ' +0000' ); //mysqlformat mostly for db insert againts $created_date
	 
		$timestamp = strtotime( $date );
	 
		if ($format == 'U') return $timestamp; //to get timestamp
		
		return date( $format, $timestamp );

	 }
	 
	 public function changeDateFromat($date='',$from_format='',$to_format=''){
		 
		 if(empty($date) || empty($from_format) || empty($to_format))
		 return '';
		 
		 $newdatetime = DateTime::createFromFormat($from_format,$date);
		 return (string)$newdatetime->format($to_format);
	}
	 
	
	//Sanitize user's input string
	public function sanitizeTextInputfield($str) {
		$filtered = $this->checkIfInvalidUtf8( $str );
	
		if ( strpos($filtered, '<') !== false ) {
			$filtered = $this->stripAllTags( $filtered, true );
		} else {
			$filtered = trim( preg_replace('/[\r\n\t ]+/', ' ', $filtered) );
		}
	
		$found = false;
		while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
			$filtered = str_replace($match[0], '', $filtered);
			$found = true;
		}
	
		if ( $found ) {
			$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
		}
	
		return $filtered;
	}
	
	
	public function checkIfInvalidUtf8($string) {
			$string = (string) $string;
		
			if ( 0 === strlen( $string ) ) {
				return '';
			}
					
			if ( 1 === @preg_match( '/^./us', $string ) ) {
				return $string;
			}
		
			return '';
	}
	
	
	public function stripAllTags($string, $remove_breaks = false) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags($string);
	
		if ( $remove_breaks )
			$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
		
		return trim( $string );
	}
	
	
	public function stripAllTagsFromArray($array,$remove_breaks = false){
		
		if ( is_array( $array ) ) {
			foreach ( $array as $k => $v ) {
				if ( is_array( $v ) ) {
					$array[$k] = $this->stripAllTagsFromArray($v,$remove_breaks);
				} else {
					$array[$k] = $this->stripAllTags($v,$remove_breaks);
				}
			}
		} else {
			$array = $this->stripAllTags($array,$remove_breaks);
		}
	
		return $array;
		
	}
	
	//Add slashes to a string or array of strings
	public function addSlash( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_array( $v ) ) {
					$value[$k] = $this->addSlash( $v );
				} else {
					$value[$k] = addslashes( $v );
				}
			}
		} else {
			$value = addslashes( $value );
		}
	
		return $value;
	}
	
	public function unSlash( $value ) {
		return $this->stripSlashesDeep( $value );
	}
	
	
	public function stripSlashesDeep($value) {
		if ( is_array($value) ) {
			$value = array_map(array($this,'stripSlashesDeep'),$value);
		} elseif ( is_object($value) ) {
			$vars = get_object_vars( $value );
			foreach ($vars as $key=>$data) {
				$value->{$key} = $this->stripSlashesDeep( $data );
			}
		} elseif ( is_string( $value ) ) {
			$value = stripslashes($value);
		}
	
		return $value;
	}
	
	public function addLeadingZeroes($number, $threshold) {
		return sprintf('%0'.$threshold.'s', $number);
	}
	
	
	public function limitStringCharacters($string='',$length=100,$trailing=false){
		
		return (strlen($string) > $length) ? (($trailing) ? substr($string,0,(int)($length-3)).'...' : substr($string,0,$length) ) : $string;
		
	}
	
	public function removeUrlsFromString($string=''){
		
		$urls = $this->extractUrlsFromString($string);
		
		if(!empty($urls))
		$string = str_replace($urls,'',$string);
		
		return $string;
	}
   
	public function extractUrlsFromString($string='') {
		  preg_match_all(
			  "#("
				  . "(?:([\w-]+:)?//?)"
				  . "[^\s()<>]+"
				  . "[.]"
				  . "(?:"
					  . "\([\w\d]+\)|"
					  . "(?:"
						  . "[^`!()\[\]{};:'\".,<>?«»“”‘’\s]|"
						  . "(?:[:]\d+)?/?"
					  . ")+"
				  . ")"
			  . ")#",
			  $string,
			  $string_links
		  );
	  
		  $string_links = array_unique( array_map( 'html_entity_decode', $string_links[0] ) );
	  
		  return array_values( $string_links );
	}
	  
	public function removePhoneNumbersFromString($string=''){
		
		$numbers = $this->extractPhoneNumbersFromString($string);
		
		if(!empty($numbers))
		$string = str_replace($numbers,'',$string);
		
		return $string;
	}
	
	
	public function extractPhoneNumbersFromString($string=''){
		
		preg_match_all("/(\d{3}|\d{4})|(\d{3,+})/i",
                $string,
				$string_phones
		);
		
		$string_phones = array_unique($string_phones[0]);
		return array_values( $string_phones );
	}
	
	
	public function removeEmailsFromString($string=''){
		
		$emails = $this->extractEmailsFromString($string);
		
		if(!empty($emails))
		$string = str_replace($emails,'',$string);
		
		return $string;
	}
	
	public function extractEmailsFromString($string=''){
   
		// '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
		preg_match_all('/[^@\s]*@[^@\s]*\.[^@\s]*/',$string,$matches);

    	return isset($matches[0]) ? $matches[0] : array();
	}
	
	public function removePhoneAndEmails($string=''){
		
		$string = $this->removePhoneNumbersFromString($string);
		$string = $this->removeEmailsFromString($string);
		
		return $string;
		
	}
	
	public function secureDataBySpecificFormat($data, $types){
		global $mysqldb;
		
		if(is_array($data)){
            $i = 0;
			foreach($data as $key=>$val){
				if(!is_array($data[$key])){
                    $data[$key] = $this->cleanData($data[$key], $types[$i]);
					$data[$key] = $mysqldb->escape($data[$key]);
                    $i++;
				}
			}
		}else{
            $data = $this->cleanData($data, $types);
			$data = $mysqldb->escape($data);
		}
		return $data;
	}
	
	
	public function sentizeDataBySpecificFormat($data, $type = ''){
        switch($type) {
            case 'none':
                $data = $data;
                break;
            case 'str':
                $data = settype( $data, 'string');
                break;
            case 'int':
                $data = settype($data, 'integer');
                break;
            case 'float':
                $data = settype( $data, 'float');
                break;
            case 'bool':
                $data = settype( $data, 'boolean');
                break;
            case 'datetime':
                $data = trim( $data );
                $data = preg_replace('/[^\d\-: ]/i', '', $data);
                preg_match( '/^([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2})$/', $data, $matches );
                $data = $matches[1];
                break;
            case 'ts2dt':
                $data = settype( $data, 'integer');
                $data = date('Y-m-d H:i:s', $data);
                break;
		   case 'hexcolor':
                preg_match( '/(#[0-9abcdef]{6})/i', $data, $matches );
                $data = $matches[1];
                break;
            case 'email':
                $data = filter_var($data, FILTER_VALIDATE_EMAIL);
                break;
            default:
                $data = '';
                break;
        }
        return $data;
    }
		
}

?>
