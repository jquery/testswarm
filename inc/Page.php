<?php
/**
 * The Page class manages the response for requests via index.php.
 *
 * This includes:
 * - the HTML skin (doctype, head, body format)
 * - queue of javascript files and stylesheets
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */

abstract class Page {
	/**
	 * @var $context TestSwarmContext: Needs to be protected instead of private
	 * in order for extending Page classes to access the context.
	 */
	protected $context;

	/**
	 * @var $action Action|null: An Action object
	 */
	protected $action;

	/** @var $metaTags array: Attribute-arrays for html_tag() */
	protected $metaTags = array(
		array( 'charset' => 'UTF-8' ),
		array( 'http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge' ),
	);

	/** @var $headScripts array: URLs for <script src> */
	protected $headScripts = array();

	/** @var $bodyScripts array: URLs for <script src> */
	protected $bodyScripts = array();

	/** @var $styleSheets array: URLs for <link rel=stylesheet href> */
	protected $styleSheets = array();

	protected $title;
	protected $rawDisplayTitle; // optional, fallsback to title + subtitle
	protected $subTitle;
	protected $content;

	protected $frameOptions = 'DENY';

	/**
	 * The execution method is where a Page invokes the main
	 * action logic. This logic should be handled by an Action class
	 * so that the Api can easily re-use it.
	 * @example
	 * <code>
	 * $action = FooAction::newFromContext( $this->getContext() );
	 * $action->doAction();
	 *	$this->setAction( $action );
	 *	$this->content = $this->initContent();
	 * </code>
	 */
	public function execute() {
		// By default a Page has no executable logic, only content.
		$this->content = $this->initContent();
	}

	/**
	 * Set the page title (to be used in the prefix of <title> and in the
	 * main <h1> of the HTML skin in Page::output().
	 * @param $title string: Page title (should not be escaped in any way)
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * @return string: The page name
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Override the page title for the <h1> of the HTML skin in Page::output().
	 * @param $title string: Page title (should not be escaped in any way)
	 */
	public function setDisplayTitle( $title ) {
		$this->rawDisplayTitle = htmlspecialchars( $title );
	}
	public function setRawDisplayTitle( $html ) {
		$this->rawDisplayTitle = $html;
	}

	/**
	 * @return string: The page name
	 */
	public function getDisplayTitleHtml() {
		if ( $this->rawDisplayTitle ) {
			return $this->rawDisplayTitle;
		}
		$title = $this->getTitle() . ( $this->getSubTitle() ? ": {$this->getSubTitle()}" : $this->getSubTitle() );
		return $title;
	}

	/**
	 * Depending on the page, there may be a sub title.
	 * For a page like "home" this will not be set, but for a "job" or "user"
	 * page this would be set to the associated title of the current item.
	 */
	public function setSubTitle( $title ) {
		$this->subTitle = $title;
	}

	/**
	 * @return string|null
	 */
	public function getSubTitle() {
		return $this->subTitle;
	}

	/**
	 * This method generates the actual content and stores it in the
	 * internal $content property. If a page has no actual content,
	 * (i.e. a page that redirects after a POST submission), then it
	 * should perform it's redirect in the execute(), and leave this method
	 * unimplemented and not call it from execute().
	 */
	protected function initContent() {
		return '<!-- ' . htmlspecialchars( __CLASS__ ) . ' has no content -->';
	}

	/**
	 * @return string|null: The raw HTML content of the page,
	 * or null if it has none.
	 */
	public function getContent() {
		return $this->content;
	}

	/** @param bool|string $val
	 * - false: Allow all framing
	 * - 'SAMEORIGIN'
	 * - 'DENY'
	 */
	public function setFrameOptions( $val ) {
		$this->frameOptions = $val;
	}

	/** @return bool|string */
	public function getFrameOptions() {
		return $this->frameOptions;
	}

	protected function getSelfPath() {
		$className = strtolower( get_class( $this ) );
		if ( substr( $className, -4 ) === 'page' ) {
			$path = substr( $className, 0, -4 );
		} else {
			$path = $className;
		}
		$item = $this->getContext()->getRequest()->getVal( 'item' );
		if ( $item !== null ) {
			$path .= "/$item";
		}
		return $path;
	}

	protected function getPageLink( $path, $label ) {
		return
			html_tag_open( 'li', array(
				// Apply "active" if path equals selfpath (/foo == /foo)
				// or is contained within (/foo/123 == /foo), but not when
				// it only starts with the same (/foobar != /foo).
				'class' => ( $this->getSelfPath() === $path ||
				( strpos( $this->getSelfPath(), "$path/" ) === 0 ) ) ? 'active' : null,
			) )
			.  html_tag( 'a', array(
				'href' => swarmpath( $path === 'home' ? '' : $path )
			), $label
			)
			. '</li>' . "\n";
	}

	/**
	 * Be careful to never throw exceptions from here if we're already
	 * on the Error500Page. e.g. if content of FooPage is empty, this throws
	 * an exception but then index.php instantiates a new page (Error500Page),
	 * which does have content. So any exception thrown from here (either directly
	 * or indirectly from an Action class), should either be caught or made sure
	 * that it doesn't occurr for a Error500Page).
	 */
	public function output() {
		$this->execute();

		if ( !$this->getContent() ) {
			throw new SwarmException( 'Page `content` must not be empty.' );
		}
		if ( !$this->getTitle() ) {
			throw new SwarmException( 'Page `title` must not be empty.' );
		}
		if ( headers_sent( $filename, $linenum ) ) {
			throw new SwarmException( "Headers already sent in `$filename` on line $linenum." );
		}

		header( 'Content-Type: text/html; charset=UTF-8' );

		$frameOptions = $this->getFrameOptions();
		if ( $frameOptions ) {
			header( 'X-Frame-Options: ' . $frameOptions, true );
		}

		$context = $this->getContext();
		$request = $context->getRequest();
		$auth = $context->getAuth();

		// ProjectsAction could throw an exception, which needs to be caught here,
		// since Error500Page (exception handler) also uses Page::output() eventually.
		// @todo: Find a cleaner way to deal with exceptions in the final page out,
		// because page output is also used on the Error500Page.

		$projects = array();

		if ( !isset( $this->exceptionObj ) ) {
			try {
				$projectsAction = ProjectsAction::newFromContext( $context );
				$projectsAction->doAction();
				$projects = $projectsAction->getData();

			} catch ( Exception $e ) {
				$pageObj = Error500Page::newFromContext( $context );
				$pageObj->setExceptionObj( $e );
				$pageObj->output();
				exit;
			}
		}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" class="no-js">
<head>
<?php

	foreach ( $this->metaTags as $metaTag ) {
		echo "\t" . html_tag( 'meta', $metaTag ) . "\n";
	}

	$subTitleSuffix = $this->getSubTitle() ? ": {$this->getSubTitle()}" : "";
	$htmlTitle = $this->getTitle() . $subTitleSuffix . ' - ' . $context->getConf()->web->title;
	$displayTitleHtml = $this->getDisplayTitleHtml();
?>
	<title><?php echo htmlentities( $htmlTitle ); ?></title>
	<link rel="stylesheet" href="<?php echo swarmpath( 'external/bootstrap/css/bootstrap.css', 'hash' ); ?>">
	<link rel="stylesheet" href="<?php echo swarmpath( 'external/bootstrap/css/bootstrap-responsive.css', 'hash' ); ?>">
	<link rel="stylesheet" href="<?php echo swarmpath( 'css/testswarm.css', 'hash' ); ?>">
	<script>
	(function (h) { h.className = h.className.replace(/\bno-js\b/,'js')})(document.documentElement);
	SWARM = <?php
	$infoAction = InfoAction::newFromContext( $context );
	$infoAction->doAction();
	echo json_encode2( $infoAction->getData() );
?>;
	SWARM.auth = <?php
	echo json_encode2( $auth );
?>;
	</script>
<?php

	foreach ( $this->styleSheets as $styleSheet ) {
		echo "\t" . html_tag( 'link', array( 'rel' => 'stylesheet', 'href' => $styleSheet ) ) . "\n";
	}

	foreach ( $this->headScripts as $headScript ) {
		echo "\t" . html_tag( 'script', array( 'src' => $headScript ) ) . "\n";
	}
?></head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="<?php echo swarmpath( '' );?>"><?php echo htmlspecialchars( $context->getConf()->web->title ); ?></a>
				<ul class="nav">
					<?php echo $this->getPageLink( 'home', 'Home' ); ?>
					<li class="dropdown<?php
						if ( strpos( $this->getSelfPath(), 'projects' ) === 0 ) {
							echo ' active';
						}
					?>">
						<a href="<?php echo swarmpath( 'projects' ); ?>" class="dropdown-toggle" data-toggle="dropdown">
							Projects
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<?php echo $this->getPageLink( 'projects', 'All projects' ); ?>
							<li class="divider"></li>
							<li class="nav-header">Projects</li>
<?php
foreach ( $projects as $project ) {
	echo $this->getPageLink( "project/{$project['id']}", $project['displayTitle'] );
}
?>
						</ul>
					</li>
					<?php echo $this->getPageLink( 'clients', 'Clients' ); ?>
					<?php echo $this->getPageLink( 'info', 'Info' ); ?>
				</ul>
				<ul class="nav pull-right">
<?php
if ( $auth ) {
?>
					<li><a href="<?php echo htmlspecialchars( swarmpath( "project/{$auth->project->id}" ) ); ?>"><?php echo htmlspecialchars( $auth->project->display_title ) ;?></a></li>
					<li><a href="<?php echo swarmpath( "addjob" );?>">Add job</a></li>
					<li><a href="<?php echo swarmpath( 'logout' ); ?>" class="swarm-logout-link">Logout</a></li>
<?php
} else {
	echo $this->getPageLink( 'login', 'Login' );
}
?>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="hero-unit">
			<h1><?php echo $displayTitleHtml; ?></h1>
		</div>
<?php
	echo $this->getContent();
?>

		<hr>
		<footer class="swarm-page-footer">
			<p>Powered by <a href="https://github.com/jquery/testswarm">TestSwarm</a>:
			<a href="https://github.com/jquery/testswarm">Source Code</a>
			| <a href="https://github.com/jquery/testswarm/issues">Issue Tracker</a>
			| <a href="https://github.com/jquery/testswarm/wiki">About</a>
			| <a href="https://twitter.com/testswarm">Twitter</a>
			</p>
		</footer>
	</div>
	<script src="<?php echo swarmpath( 'external/jquery/jquery.js', 'hash' ); ?>"></script>
	<script src="<?php echo swarmpath( 'external/bootstrap/js/bootstrap-dropdown.js', 'hash' ); ?>"></script>
	<script src="<?php echo swarmpath( 'js/pretty.js', 'hash' ); ?>"></script>
	<script src="<?php echo swarmpath( 'js/testswarm.js', 'hash' ); ?>"></script><?php

	foreach ( $this->bodyScripts as $bodyScript ) {
		echo "\n\t" . html_tag( 'script', array( 'src' => $bodyScript ) );
	}

	if ( $context->getConf()->debug->dbLogQueries ) {
		$queryLog = $context->getDB()->getQueryLog();
		$queryLogHtml = '<hr><h3>Database query log</h3><div class="well"><ul class="unstyled">';
		foreach ( $queryLog as $i => $queryInfo ) {
			if ( $i !== 0 ) {
				$queryLogHtml .= '<hr>';
			}
			$queryLogHtml .= '<li>'
				. '<pre>' . htmlspecialchars( $queryInfo["sql"] ) . '</pre>'
				. '<table class="table table-bordered table-condensed"><tbody><tr>'
				. '<td>Caller: <code>' . htmlspecialchars( $queryInfo['caller'] ) . '</code></td>'
				. '<td>Num rows: <code>' . htmlspecialchars( $queryInfo['numRows'] ) . '</code></td>'
				. '<td>Insert ID: <code>' . htmlspecialchars( $queryInfo['insertId'] ) . '</code></td>'
				. '<td>Affected rows: <code>' . htmlspecialchars( $queryInfo['affectedRows'] ) . '</code></td>'
				. '<td>Query time: <code>' . htmlspecialchars( substr( $queryInfo['queryTime'], 0, 8 ) ) . '</code></td>'
				. '</tr></table>'
				. '</li>';
		}
		$queryLogHtml .= '</ul>';
		echo $queryLogHtml;
	}

?>
</body>
</html>
<?php
	// End of Page::output
	}

	/**
	 * Small messages
	 */
	public function outputMini( $title, $message = null ) {
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlspecialchars(
		$title
		. ' - '
		. $this->getContext()->getConf()->web->title
	); ?></title>
	<link rel="stylesheet" href="<?php echo swarmpath( 'external/bootstrap/css/bootstrap.css' ); ?>">
	<link rel="stylesheet" href="<?php echo swarmpath( 'css/testswarm.css' ); ?>">
</head>
<body>
<div class="hero-unit">
	<h1><?php echo htmlspecialchars( $title ); ?></h1>
<?php
	if ( $message ) {
?>
	<p><?php echo htmlspecialchars( $message );?></p>
<?php
	}
?>
</div>
</body>
</html>
<?php
	}

	/**
	 * Alternate response handler for X-Swarm-Partial requests.
	 *
	 * @see https://github.com/jquery/testswarm/issues/215
	 */
	final public function outputPartial() {
		try {
			$this->execute();
			echo $this->getContent();
		} catch ( Exception $e ) {
			self::httpStatusHeader( 500 );
		}
	}

	/**
	 * Useful utility function to send a redirect as reponse and close the request.
	 * @param $target string: Url
	 * @param $code int: 30x
	 */
	protected function redirect( $target = '', $code = 302 ) {
		session_write_close();
		self::httpStatusHeader( $code );
		header( 'Content-Type: text/html; charset=utf-8' );
		header( 'Location: ' . $target );

		exit;
	}

	/**
	 * @see Action::addTimestampsTo
	 * @param array $data
	 * @param string $propNamePrefix
	 * @return string: HTML
	 */
	protected static function getPrettyDateHtml( $data, $propNamePrefix, $attr = array() ) {
		return html_tag( 'span', array(
			'data-timestamp' => $data[$propNamePrefix . 'ISO'],
			'title' => $data[$propNamePrefix . 'LocalFormatted'],
			'class' => 'pretty' . ( isset( $attr['class'] ) ? ' ' . $attr['class'] : '' )
		) + $attr, $data[$propNamePrefix . 'LocalShort'] );
	}

	protected function setRobots( $value ) {
		// Set both the header and the meta tag,
		// so that the value also applies to pages where the html output is overwritten
		// (such as Api responses and RunresultsPage),
		// https://developers.google.com/webmasters/control-crawl-index/docs/robots_meta_tag
		header( "X-Robots-Tag: $value", true );
		$this->metaTags[] = array( 'name' => 'robots', 'content' => $value );
	}

	final public static function getHttpStatusMsg( $code ) {
		static $httpCodes = array(
			200 => 'OK',
			204 => 'No Content',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
		);
		return isset( $httpCodes[$code] ) ? $httpCodes[$code] : null;
	}

	final public static function httpStatusHeader( $code ) {
		$message = self::getHttpStatusMsg( $code );
		if ( $message ) {
			header( $_SERVER['SERVER_PROTOCOL'] . " $code $message", true, $code );
		}
	}

	final public static function getPageClassByName( $pageName ) {
		$className = ucfirst( $pageName ) . 'Page';
		return class_exists( $className ) ? $className : null;
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		// self refers to the origin class (abstract Page)
		// static refers to the current class (FoobarPage)
		$page = new static();
		$page->context = $context;

		$versionInfo = $context->getVersionInfo();
		$page->metaTags[] = array( 'name' => 'generator', 'content' => $versionInfo['TestSwarm'] );

		return $page;
	}

	final protected function getContext() {
		return $this->context;
	}

	final protected function setAction( Action $action ) {
		$this->action = $action;
	}

	final protected function getAction() {
		return $this->action;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	final private function __construct() {}
}
