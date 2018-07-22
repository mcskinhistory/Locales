<?php

namespace Gigadrive\i18n;

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
	public static $componentName;

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
			return trim(strtolower($phrase));
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
		}

		$locale = self::getLocale($code);
		if(!is_null($locale))
			return $locale;

		// TODO: Check for settings associated with the user's account and the browser language
		return self::getLocale("en");
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
	public static function Instance(){
		static $inst = null;
		if($inst == null){
			$inst = new self(LOCALE_COMPONENT);
		}

		return $inst;
	}

	/**
	 * Constructor
	 * 
	 * @access protected
	 */
	protected function __construct(){
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
			$folder = __DIR__ . "/" . self::$componentName . "/";

			if(file_exists($folder) && is_dir($folder)){
				// TODO
			}

			// TODO: Save to cache
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
}
