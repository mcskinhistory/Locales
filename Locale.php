<?php

namespace Gigadrive\i18n;

/**
 * Represents a Locale translated via weblate
 * 
 * @package i18n
 * @author Gigadrive (support@gigadrivegroup.com)
 * @copyright 2016-2018 Gigadrive
 * @link https://gigadrivegroup.com/dev/technologies
 */
class Locale {
	/**
	 * @access private
	 * @var string $code
	 */
	private $code;

	/**
	 * @access private
	 * @var string $name
	 */
	private $name;

	/**
	 * @access private
	 * @var string $localizedName
	 */
	private $localizedName;

	/**
	 * @access private
	 * @var array $phrases
	 */
	private $phrases;

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param string $code Language code from weblate
	 */
	public function __construct($code){
		$this->code = $code;
	}

	/**
	 * Gets the language code
	 * 
	 * @access public
	 * @return string
	 */
	public function getCode(){
		return $this->code;
	}

	/**
	 * Gets the language code used for flag icons
	 * 
	 * @access public
	 * @return string
	 */
	public function getFlagIconCode(){
		$flag = $this->code;

		switch($flag){
			// danish
			case "da":
				$flag = "dk";
				break;

			// greece
			case "el":
				$flag = "gr";
				break;

			// hebrew
			case "he":
				$flag = "il";
				break;

			// korean
			case "ko":
				$flag = "kr";
				break;

			// ukrainian
			case "uk":
				$flag = "ua";
				break;

			// chinese
			case "zh":
				$flag = "cn";
				break;

			case "zh_Hans":
				$flag = "cn";
				break;

			case "zh_Hant":
				$flag = "cn";
				break;

			// czech
			case "cs":
				$flag = "cz";
				break;

			// english
			case "en":
				$flag = "us";
				break;

			// hindi
			case "hi":
				$flag = "in";
				break;

			// japanese
			case "ja":
				$flag = "jp";
				break;

			// norwegian
			case "nb":
				$flag = "no";
				break;

			// portuguese (brazil)
			case "pt_BR":
				$flag = "br";
				break;
		}

		return $flag;
	}

	/**
	 * Gets the locale name
	 * 
	 * @access public
	 * @return string
	 */
	public function getName(){
		if(is_null($this->name))
			$this->reload();

		return $this->name;
	}

	/**
	 * Gets the localized locale name (returns null for English and similar)
	 * 
	 * @access public
	 * @return string
	 */
	public function getLocalizedName(){
		return $this->localizedName;
	}

	/**
	 * Gets the phrase array
	 * 
	 * @access public
	 * @return array
	 */
	public function getPhrases(){
		if(is_null($this->phrases))
			$this->reload();

		return $this->phrases;
	}

	/**
	 * Gets the name of the timeago translation file.
	 * 
	 * @access public
	 * @return string
	 */
	public function getTimeAgoFileName(){
		$code = $this->code;
		$file = null;

		switch($code){
			case "zh_Hans":
				$file = "zh-CN";
				break;
			case "zh_Hant":
				$file = "zh-TW";
				break;
			case "pt_BR":
				$file = "pt-br";
				break;
			case "nb":
				$file = "no";
				break;
			
		}

		if(is_null($file)){
			$file = $this->getFlagIconCode();
		}

		return "jquery.timeago." . $file . ".js";
	}

	/**
	 * Reloads phrases and locale data from the file system
	 * 
	 * @access public
	 */
	public function reload($componentName){
		if(is_null($componentName))
			$componentName = i18n::Instance()->getComponentName();
		
		$folder = __DIR__ . "/" . $componentName . "/" . $this->code . "/";

		if(file_exists($folder) && is_dir($folder)){
			if(file_exists($folder . "translation.json")){
				$this->name = locale_get_display_language($this->code,"en");
				$this->localizedName = locale_get_display_language($this->code,$this->code);

				$translation = json_decode(file_get_contents($folder . "translation.json"),true);

				$this->phrases = [];
				foreach($translation as $key => $value){
					$this->phrases[$key] = $value;
				}
			}
		}
	}

	/**
	 * Gets whether the locale was properly loaded and validated
	 * 
	 * @access public
	 * @return bool
	 */
	public function isValid(){
		return !is_null($this->name);
	}

	/**
	 * Returns a translated phrase by ID, replacing variables
	 * 
	 * @access public
	 * @param string $phrase
	 * @param array $variables
	 * @return string
	 */
	public function getTranslatedMessage($phrase, $variables = null){
		$phrase = trim($phrase);

		if(array_key_exists($phrase,$this->phrases)){
			$r = $this->phrases[$phrase];

			if(!is_null($variables) && is_array($variables) && count($variables) > 0){
				for ($i = 0; $i < count($variables); $i++) { 
					$var = $variables[$i];

					$r = str_replace("{" . $i . "}",$var,$r);
				}
			}

			return $r;
		} else {
			return $phrase;
		}
	}

	/**
	 * Gets whether a string starts with another
	 * 
	 * @access private
	 * @param string $string The string in subject
	 * @param string $start The string to be checked whether it is the start of $string
	 * @param bool $ignoreCase If true, the case of the strings won't affect the result
	 * @return bool
	 */
	private static function startsWith($string,$start,$ignoreCase = false){
		if(strlen($start) <= strlen($string)){
			if($ignoreCase == true){
				return substr($string,0,strlen($start)) == $start;
			} else {
				return strtolower(substr($string,0,strlen($start))) == strtolower($start);
			}
		} else {
			return false;
		}
	}
}