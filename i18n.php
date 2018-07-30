<?php

namespace Gigadrive\i18n;

use Gigadrive\Util\Util;


/**
 * Internationalization utilities
 * 
 * @package i18n
 * @author Gigadrive (support@gigadrivegroup.com)
 * @copyright 2016-2018 Gigadrive
 * @link https://gigadrivegroup.com/dev/technologies
 */
class i18n {
	/**
	 * @access public
	 * @var string $componentName The locale component name (subfolder)
	 */
	public $componentName;

	/**
	 * Returns a translated phrase by ID, replacing variables, uses the locale found in i18n::getCurrentLanguage()
	 * 
	 * @access public
	 * @param string $phrase
	 * @param array $variables
	 * @return string
	 */
	public static function getTranslatedMessage($phrase, $variables = null){
		$l = self::getCurrentLanguage();

		if(!is_null($l)){
			return $l->getTranslatedMessage($phrase,$variables);
		} else {
			return trim($phrase);
		}
	}

	/**
	 * Gets the current user's locale
	 * 
	 * @access public
	 * @return Locale
	 */
	public static function getCurrentLanguage(){
		$code = null;
		if(isset($_COOKIE["lang"])){
			$code = $_COOKIE["lang"];

			$locale = self::getLocale($code);
			if(!is_null($locale))
				return $locale;
		}

		$browser = self::getBrowserLanguage();
		if($browser != "en"){
			$locale = self::getLocale($browser);
			if(!is_null($locale))
				return $locale;

			foreach(self::Instance()->getLocales() as $locale){
				if(self::startsWith($locale->getCode(),$browser,true)){
					return $locale;
				}
			}
		}

		return self::getLocale("en");
	}

	/**
	 * Gets the user's browser language
	 * 
	 * @access public
	 * @param string $default The default to be returned in case the browser did not send the language (e.g. Googlebot)
	 * @return string A 2 character language code
	 */
	public static function getBrowserLanguage($default = "en") {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			return $default;
			
        return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    }

	/**
	 * Returns a locale defined by the code passed, if the locale can't be found it will default to English, if English can't be found it will return null
	 * 
	 * @access public
	 * @param string $code
	 * @return string
	 */
	public static function getLocale($code){
		$code = trim(strtolower($code));
		$i = self::Instance();

		if(array_key_exists($code,$i->getLocales())){
			return $i->getLocales()[$code];
		} else {
			if($code == "en"){
				return null;
			} else {
				return self::getLocale($code);
			}
		}
	}

	/**
	 * @access private
	 * @var array $locales
	 */
	private $locales;

	/**
	 * Gets the Internationalization class instance
	 * 
	 * @access public
	 * @return i18n
	 */
	public static function Instance($componentName = null){
		static $inst = null;
		if(is_null($inst)){
			$inst = new self($componentName);
		}

		return $inst;
	}

	/**
	 * Constructor
	 * 
	 * @access protected
	 */
	protected function __construct($componentName){
		$this->componentName = $componentName;
		$this->loadLocales();
	}

	/**
	 * Reloads all locales from the file system
	 * 
	 * @access public
	 */
	public function loadLocales(){
		$n = "i18n_locales";

		if(\CacheHandler::existsInCache($n)){
			$this->locales = \CacheHandler::getFromCache($n);
		} else {
			if(is_null($this->locales)){
				$this->locales = [];

				$folder = __DIR__ . "/" . $this->componentName . "/";

				if(file_exists($folder) && is_dir($folder)){
					$files = glob($folder . "*");

					foreach($files as $file){
						if(is_dir($file)){
							$dirName = basename($file);

							$locale = new Locale($dirName);
							$locale->reload($this->componentName);
							
							if($locale->isValid()){
								$this->locales[$locale->getCode()] = $locale;
							}
						}
					}
				}

				\CacheHandler::setToCache($n,$this->locales,20*60);
			}
		}
	}

	/**
	 * Gets all loaded locales
	 * 
	 * @access public
	 * @return array
	 */
	public function getLocales(){
		return $this->locales;
	}

	/**
	 * Returns the component name
	 * 
	 * @access public
	 * @return string
	 */
	public function getComponentName(){
		return $this->componentName;
	}
}
