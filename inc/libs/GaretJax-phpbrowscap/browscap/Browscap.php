<?php

/**
 * Browscap.ini parsing class with caching and update capabilities
 *
 * PHP version 5
 *
 * Copyright (c) 2006-2012 Jonathan Stoppani
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    Browscap
 * @author     Jonathan Stoppani <jonathan@stoppani.name>
 * @copyright  Copyright (c) 2006-2012 Jonathan Stoppani
 * @version    1.0
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/GaretJax/phpbrowscap/
 */
class Browscap
{
	/**
	 * Current version of the class.
	 */
	const VERSION = '1.0';

	/**
	 * Different ways to access remote and local files.
	 *
	 * UPDATE_FOPEN: Uses the fopen url wrapper (use file_get_contents).
	 * UPDATE_FSOCKOPEN: Uses the socket functions (fsockopen).
	 * UPDATE_CURL: Uses the cURL extension.
	 * UPDATE_LOCAL: Updates from a local file (file_get_contents).
	 */
	const UPDATE_FOPEN = 'URL-wrapper';
	const UPDATE_FSOCKOPEN = 'socket';
	const UPDATE_CURL = 'cURL';
	const UPDATE_LOCAL = 'local';

	/**
	 * Options for regex patterns.
	 *
	 * REGEX_DELIMITER: Delimiter of all the regex patterns in the whole class.
	 * REGEX_MODIFIERS: Regex modifiers.
	 */
	const REGEX_DELIMITER = '@';
	const REGEX_MODIFIERS = 'i';

	/**
	 * The values to quote in the ini file
	 */
	const VALUES_TO_QUOTE = 'Browser|Parent';

	/**
	 * Definitions of the function used by the uasort() function to order the
	 * userAgents array.
	 *
	 * ORDER_FUNC_ARGS: Arguments that the function will take.
	 * ORDER_FUNC_LOGIC: Internal logic of the function.
	 */
	const ORDER_FUNC_ARGS = '$a, $b';
	const ORDER_FUNC_LOGIC = '$a=strlen($a);$b=strlen($b);return$a==$b?0:($a<$b?1:-1);';

	/**
	 * The headers to be sent for checking the version and requesting the file.
	 */
	const REQUEST_HEADERS = "GET %s HTTP/1.0\r\nHost: %s\r\nUser-Agent: %s\r\nConnection: Close\r\n\r\n";

	/**
	 * Options for auto update capabilities
	 *
	 * $remoteVerUrl: The location to use to check out if a new version of the
	 *                browscap.ini file is available.
	 * $remoteIniUrl: The location from which download the ini file.
	 *                The placeholder for the file should be represented by a %s.
	 * $timeout: The timeout for the requests.
	 * $updateInterval: The update interval in seconds.
	 * $errorInterval: The next update interval in seconds in case of an error.
	 * $doAutoUpdate: Flag to disable the automatic interval based update.
	 * $updateMethod: The method to use to update the file, has to be a value of
	 *                an UPDATE_* constant, null or false.
	 */
	public $remoteIniUrl = 'http://browsers.garykeith.com/stream.asp?BrowsCapINI';
	public $remoteVerUrl = 'http://browsers.garykeith.com/versions/version-date.asp';
	public $timeout = 5;
	public $updateInterval = 432000;  // 5 days
	public $errorInterval = 7200;  // 2 hours
	public $doAutoUpdate = true;
	public $updateMethod = null;

	/**
	 * The path of the local version of the browscap.ini file from which to
	 * update (to be set only if used).
	 *
	 * @var string
	 */
	public $localFile = null;

	/**
	 * The useragent to include in the requests made by the class during the
	 * update process.
	 *
	 * @var string
	 */
	public $userAgent = 'Browser Capabilities Project - PHP Browscap/%v %m';

	/**
	 * Flag to enable only lowercase indexes in the result.
	 * The cache has to be rebuilt in order to apply this option.
	 *
	 * @var bool
	 */
	public $lowercase = false;

	/**
	 * Flag to enable/disable silent error management.
	 * In case of an error during the update process the class returns an empty
	 * array/object if the update process can't take place and the browscap.ini
	 * file does not exist.
	 *
	 * @var bool
	 */
	public $silent = false;

	/**
	 * Where to store the cached PHP arrays.
	 *
	 * @var string
	 */
	public $cacheFilename = 'cache.php';

	/**
	 * Where to store the downloaded ini file.
	 *
	 * @var string
	 */
	public $iniFilename = 'browscap.ini';

	/**
	 * Path to the cache directory
	 *
	 * @var string
	 */
	public $cacheDir = null;

	/**
	 * Flag to be set to true after loading the cache
	 *
	 * @var bool
	 */
	protected $_cacheLoaded = false;

	/**
	 * Where to store the value of the included PHP cache file
	 *
	 * @var array
	 */
	protected $_userAgents = array();
	protected $_browsers = array();
	protected $_patterns = array();
	protected $_properties = array();

	/**
	 * Constructor class, checks for the existence of (and loads) the cache and
	 * if needed updated the definitions
	 *
	 * @param string $cache_dir
	 */
	public function __construct($cache_dir)
	{
		// has to be set to reach E_STRICT compatibility, does not affect system/app settings
		date_default_timezone_set(date_default_timezone_get());

		if (!isset($cache_dir)) {
			throw new Browscap_Exception(
				'You have to provide a path to read/store the browscap cache file'
			);
		}

		$old_cache_dir = $cache_dir;
		$cache_dir = realpath($cache_dir);

		if (false === $cache_dir) {
			throw new Browscap_Exception(
				sprintf('The cache path %s is invalid. Are you sure that it exists and that you have permission to access it?', $old_cache_dir)
			);
		}

		// Is the cache dir really the directory or is it directly the file?
		if (substr($cache_dir, -4) === '.php') {
			$this->cacheFilename = basename($cache_dir);
			$this->cacheDir = dirname($cache_dir);
		} else {
			$this->cacheDir = $cache_dir;
		}

		$this->cacheDir .= DIRECTORY_SEPARATOR;
	}

	/**
	 * Gets the information about the browser by User Agent
	 *
	 * @param string $user_agent  the user agent string
	 * @param bool $return_array  whether return an array or an object
	 * @throws Browscap_Exception
	 * @return stdObject  the object containing the browsers details. Array if
	 *                    $return_array is set to true.
	 */
	public function getBrowser($user_agent = null, $return_array = false)
	{
		// Load the cache at the first request
		if (!$this->_cacheLoaded) {
			$cache_file = $this->cacheDir . $this->cacheFilename;
			$ini_file = $this->cacheDir . $this->iniFilename;

			// Set the interval only if needed
			if ($this->doAutoUpdate && file_exists($ini_file)) {
				$interval = time() - filemtime($ini_file);
			} else {
				$interval = 0;
			}

			// Find out if the cache needs to be updated
			if (!file_exists($cache_file) || !file_exists($ini_file) || ($interval > $this->updateInterval)) {
				try {
					$this->updateCache();
				} catch (Browscap_Exception $e) {
					if (file_exists($ini_file)) {
						// Adjust the filemtime to the $errorInterval
						touch($ini_file, time() - $this->updateInterval + $this->errorInterval);
					} else if ($this->silent) {
						// Return an array if silent mode is active and the ini db doesn't exsist
						return array();
					}

					if (!$this->silent) {
						throw $e;
					}
				}
			}

			$this->_loadCache($cache_file);
		}

		// Automatically detect the useragent
		if (!isset($user_agent)) {
			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			} else {
				$user_agent = '';
			}
		}

		$browser = array();
		foreach ($this->_patterns as $key => $pattern) {
			if (preg_match($pattern . 'i', $user_agent)) {
				$browser = array(
					$user_agent, // Original useragent
					trim(strtolower($pattern), self::REGEX_DELIMITER),
					$this->_userAgents[$key]
				);

				$browser = $value = $browser + $this->_browsers[$key];

				while (array_key_exists(3, $value) && $value[3]) {
					$value = $this->_browsers[$value[3]];
					$browser += $value;
				}

				if (!empty($browser[3])) {
					$browser[3] = $this->_userAgents[$browser[3]];
				}

				break;
			}
		}

		// Add the keys for each property
		$array = array();
		foreach ($browser as $key => $value) {
			if ($value === 'true') {
				$value = true;
			} else if ($value === 'false') {
				$value = false;
			}
			$array[$this->_properties[$key]] = $value;
		}

		return $return_array ? $array : (object) $array;
	}

	/**
	 * Parses the ini file and updates the cache files
	 *
	 * @return bool whether the file was correctly written to the disk
	 */
	public function updateCache()
	{
		$ini_path = $this->cacheDir . $this->iniFilename;
		$cache_path = $this->cacheDir . $this->cacheFilename;

		// Choose the right url
		if ($this->_getUpdateMethod() == self::UPDATE_LOCAL) {
			$url = $this->localFile;
		} else {
			$url = $this->remoteIniUrl;
		}

		$this->_getRemoteIniFile($url, $ini_path);

		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			$browsers = parse_ini_file($ini_path, true, INI_SCANNER_RAW);
		} else {
			$browsers = parse_ini_file($ini_path, true);
		}

		array_shift($browsers);

		$this->_properties = array_keys($browsers['DefaultProperties']);
		array_unshift(
			$this->_properties,
			'browser_name',
			'browser_name_regex',
			'browser_name_pattern',
			'Parent'
		);

		$this->_userAgents = array_keys($browsers);
		usort(
			$this->_userAgents,
			create_function(self::ORDER_FUNC_ARGS, self::ORDER_FUNC_LOGIC)
		);

		$user_agents_keys = array_flip($this->_userAgents);
		$properties_keys = array_flip($this->_properties);

		$search = array('\*', '\?');
		$replace = array('.*', '.');

		foreach ($this->_userAgents as $user_agent) {
			$pattern = preg_quote($user_agent, self::REGEX_DELIMITER);
			$this->_patterns[] = self::REGEX_DELIMITER
			                   . '^'
			                   . str_replace($search, $replace, $pattern)
			                   . '$'
			                   . self::REGEX_DELIMITER;

			if (!empty($browsers[$user_agent]['Parent'])) {
				$parent = $browsers[$user_agent]['Parent'];
				$browsers[$user_agent]['Parent'] = $user_agents_keys[$parent];
			}

			foreach ($browsers[$user_agent] as $key => $value) {
				$key = $properties_keys[$key] . ".0";
				$browser[$key] = $value;
			}

			$this->_browsers[] = $browser;
			unset($browser);
		}
		unset($user_agents_keys, $properties_keys, $browsers);

		// Save the keys lowercased if needed
		if ($this->lowercase) {
			$this->_properties = array_map('strtolower', $this->_properties);
		}

		// Get the whole PHP code
		$cache = $this->_buildCache();

		// Save and return
		return (bool) file_put_contents($cache_path, $cache, LOCK_EX);
	}

	/**
	 * Loads the cache into object's properties
	 *
	 * @return void
	 */
	protected function _loadCache($cache_file)
	{
		require $cache_file;

		$this->_browsers = $browsers;
		$this->_userAgents = $userAgents;
		$this->_patterns = $patterns;
		$this->_properties = $properties;

		$this->_cacheLoaded = true;
	}

	/**
	 * Parses the array to cache and creates the PHP string to write to disk
	 *
	 * @return string the PHP string to save into the cache file
	 */
	protected function _buildCache()
	{
		$cacheTpl = "<?php\n\$properties=%s;\n\$browsers=%s;\n\$userAgents=%s;\n\$patterns=%s;\n";

		$propertiesArray = $this->_array2string($this->_properties);
		$patternsArray = $this->_array2string($this->_patterns);
		$userAgentsArray = $this->_array2string($this->_userAgents);
		$browsersArray = $this->_array2string($this->_browsers);

		return sprintf(
			$cacheTpl,
			$propertiesArray,
			$browsersArray,
			$userAgentsArray,
			$patternsArray
		);
	}

	/**
	 * Updates the local copy of the ini file (by version checking) and adapts
	 * his syntax to the PHP ini parser
	 *
	 * @param string $url  the url of the remote server
	 * @param string $path  the path of the ini file to update
	 * @throws Browscap_Exception
	 * @return bool if the ini file was updated
	 */
	protected function _getRemoteIniFile($url, $path)
	{
		// Check version
		if (file_exists($path) && filesize($path)) {
			$local_tmstp = filemtime($path);

			if ($this->_getUpdateMethod() == self::UPDATE_LOCAL) {
				$remote_tmstp = $this->_getLocalMTime();
			} else {
				$remote_tmstp = $this->_getRemoteMTime();
			}

			if ($remote_tmstp < $local_tmstp) {
				// No update needed, return
				touch($path);
				return false;
			}
		}

		// Get updated .ini file
		$browscap = $this->_getRemoteData($url);


		$browscap = explode("\n", $browscap);

		$pattern = self::REGEX_DELIMITER
		         . '('
		         . self::VALUES_TO_QUOTE
		         . ')="?([^"]*)"?$'
		         . self::REGEX_DELIMITER;


		// Ok, lets read the file
		$content = '';
		foreach ($browscap as $subject) {
			$subject = trim($subject);
			$content .= preg_replace($pattern, '$1="$2"', $subject) . "\n";
		}
		
		if ($url != $path) {
			if (!file_put_contents($path, $content)) {
				throw new Browscap_Exception("Could not write .ini content to $path");
			}
		}
		return true;
	}

	/**
	 * Gets the remote ini file update timestamp
	 *
	 * @throws Browscap_Exception
	 * @return int the remote modification timestamp
	 */
	protected function _getRemoteMTime()
	{
		$remote_datetime = $this->_getRemoteData($this->remoteVerUrl);
		$remote_tmstp = strtotime($remote_datetime);

		if (!$remote_tmstp) {
			throw new Browscap_Exception("Bad datetime format from {$this->remoteVerUrl}");
		}

		return $remote_tmstp;
	}

	/**
	 * Gets the local ini file update timestamp
	 *
	 * @throws Browscap_Exception
	 * @return int the local modification timestamp
	 */
	protected function _getLocalMTime()
	{
		if (!is_readable($this->localFile) || !is_file($this->localFile)) {
			throw new Browscap_Exception("Local file is not readable");
		}

		return filemtime($this->localFile);
	}

	/**
	 * Converts the given array to the PHP string which represent it.
	 * This method optimizes the PHP code and the output differs form the
	 * var_export one as the internal PHP function does not strip whitespace or
	 * convert strings to numbers.
	 *
	 * @param array $array the array to parse and convert
	 * @return string the array parsed into a PHP string
	 */
	protected function _array2string($array)
	{
		$strings = array();

		foreach ($array as $key => $value) {
			if (is_int($key)) {
				$key = '';
			} else if (ctype_digit((string) $key) || strpos($key, '.0')) {
				$key = intval($key) . '=>' ;
			} else {
				$key = "'" . str_replace("'", "\'", $key) . "'=>" ;
			}

			if (is_array($value)) {
				$value = $this->_array2string($value);
			} else if (ctype_digit((string) $value)) {
				$value = intval($value);
			} else {
				$value = "'" . str_replace("'", "\'", $value) . "'";
			}

			$strings[] = $key . $value;
		}

		return 'array(' . implode(',', $strings) . ')';
	}

	/**
	 * Checks for the various possibilities offered by the current configuration
	 * of PHP to retrieve external HTTP data
	 *
	 * @return string the name of function to use to retrieve the file
	 */
	protected function _getUpdateMethod()
	{
		// Caches the result
		if ($this->updateMethod === null) {
			if ($this->localFile !== null) {
				$this->updateMethod = self::UPDATE_LOCAL;
			} else if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
				$this->updateMethod = self::UPDATE_FOPEN;
			} else if (function_exists('fsockopen')) {
				$this->updateMethod = self::UPDATE_FSOCKOPEN;
			} else if (extension_loaded('curl')) {
				$this->updateMethod = self::UPDATE_CURL;
			} else {
				$this->updateMethod = false;
			}
		}

		return $this->updateMethod;
	}

	/**
	 * Retrieve the data identified by the URL
	 *
	 * @param string $url the url of the data
	 * @throws Browscap_Exception
	 * @return string the retrieved data
	 */
	protected function _getRemoteData($url)
	{
		ini_set('user_agent', $this->_getUserAgent());
		
		switch ($this->_getUpdateMethod()) {
			case self::UPDATE_LOCAL:
				$file = file_get_contents($url);

				if ($file !== false) {
					return $file;
				} else {
					throw new Browscap_Exception('Cannot open the local file');
				}
			case self::UPDATE_FOPEN:
				$file = file_get_contents($url);

				if ($file !== false) {
					return $file;
				} // else try with the next possibility (break omitted)
			case self::UPDATE_FSOCKOPEN:
				$remote_url = parse_url($url);
				$remote_handler = fsockopen($remote_url['host'], 80, $c, $e, $this->timeout);

				if ($remote_handler) {
					stream_set_timeout($remote_handler, $this->timeout);

					if (isset($remote_url['query'])) {
						$remote_url['path'] .= '?' . $remote_url['query'];
					}

					$out = sprintf(
						self::REQUEST_HEADERS,
						$remote_url['path'],
						$remote_url['host'],
						$this->_getUserAgent()
					);

					fwrite($remote_handler, $out);

					$response = fgets($remote_handler);
					if (strpos($response, '200 OK') !== false) {
						$file = '';
						while (!feof($remote_handler)) {
							$file .= fgets($remote_handler);
						}

						$file = str_replace("\r\n", "\n", $file);
						$file = explode("\n\n", $file);
						array_shift($file);

						$file = implode("\n\n", $file);

						fclose($remote_handler);

						return $file;
					}
				} // else try with the next possibility
			case self::UPDATE_CURL:
				$ch = curl_init($url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->_getUserAgent());

				$file = curl_exec($ch);

				curl_close($ch);

				if ($file !== false) {
					return $file;
				} // else try with the next possibility
			case false:
				throw new Browscap_Exception('Your server can\'t connect to external resources. Please update the file manually.');
		}
	}

	/**
	 * Format the useragent string to be used in the remote requests made by the
	 * class during the update process.
	 *
	 * @return string the formatted user agent
	 */
	protected function _getUserAgent()
	{
		$ua = str_replace('%v', self::VERSION, $this->userAgent);
		$ua = str_replace('%m', $this->_getUpdateMethod(), $ua);

		return $ua;
	}
}

/**
 * Browscap.ini parsing class exception
 *
 * @package    Browscap
 * @author     Jonathan Stoppani <jonathan@stoppani.name>
 * @copyright  Copyright (c) 2006-2012 Jonathan Stoppani
 * @version    1.0
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/GaretJax/phpbrowscap/*/
class Browscap_Exception extends Exception
{}

