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
	 * Reloads phrases and locale data from the file system
	 * 
	 * @access public
	 */
	public function reload(){
		$folder = __DIR__ . "/" . i18n::$componentName . "/" . $this->code . "/";

		if(file_exists($folder) && is_dir($folder)){
			if(file_exists($folder . "translation.json") && file_exists($folder . "settings.json")){
				$settings = json_decode(file_get_contents($folder . "settings.json"),true);

				if(isset($settings["name"]))
					$this->name = $settings["name"];

				if(isset($settings["localizedName"]))
					$this->localizedName = $settings["localizedName"];

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
		$phrase = trim(strtolower($phrase));

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
}