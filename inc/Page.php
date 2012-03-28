<?php
/**
 * The Page class manages the response for requests via index.php,
 * this includes:
 * - the HTML skin (doctype, head, body format)
 * - queue of javascript files and stylesheets
 * - the main try-catch that shows the "TestSwarm: Internal error" page
 *
 * @author Timo Tijhof
 * @since 0.3.0
 * @package TestSwarm
 */

abstract class Page {
	/**
	 * @var $context TestSwarmContext: Needs to be protected instead of private
	 * in order for extending Page classes to access the context. 
	 */
	protected $context;

	/**
	 * @var $action string|null: Class name
	 */
	protected $actionClass;

	/**
	 * @var $action Action|null: An Action object
	 */
	protected $action;

	/** @var $metaTags array: Attribute-arrays for html_tag() */
	protected $metaTags = array(
		array( "charset" => "UTF-8" ),
		array( "http-equiv" => "X-UA-Compatible", "content" => "IE=edge" ),
	);

	/** @var $headScripts array: URLs for <script src> */
	protected $headScripts = array();

	/** @var $bodyScripts array: URLs for <script src> */
	protected $bodyScripts = array();

	/** @var $styleSheets array: URLs for <link rel=stylesheet href> */
	protected $styleSheets = array();

	protected $title;
	protected $subTitle;
	protected $content;

	/**
	 * The execution method is where a Page invokes the main
	 * action logic. This logic should be handled by an Action class
	 * so that the Api can easily re-use it.
	 * @example
	 * <code>
	 * $this->actionClass = 'LoginAction';
	 *	parent::execute();
	 * </code>
	 */
	public function execute() {
		if ( $this->actionClass !== null ) {
			$className = $this->actionClass;
			$this->action = $className::newFromContext( $this->getContext() );
			$this->action->doAction();
		}

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
		return "<!-- " . htmlspecialchars( __CLASS__ ) . " has no content -->";
	}

	/**
	 * @return string|null: The raw HTML content of the page,
	 * or null if it has none.
	 */
	public function getContent() {
		return $this->content;
	}

	public function output() {
		try {
			$this->execute();

			if ( !$this->getContent() ) {
				throw new SwarmException( "Page `content` must not be empty." );
			}
			if ( !$this->getTitle() ) {
				throw new SwarmException( "Page `title` must not be empty." );
			}
			if ( headers_sent( $filename, $linenum ) ) {
				throw new SwarmException( "Headers already sent in `$filename` on line $linenum." );
			}
		} catch ( Exception $e ) {
			$this->handleException( $e );
		}

		header( "Content-Type: text/html; charset=utf-8" );

		$request = $this->getContext()->getRequest();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head><?php

	foreach ( $this->metaTags as $metaTag ) {
		echo "\n\t" . html_tag( 'meta', $metaTag );
	}

	$subTitleSuffix = $this->getSubTitle() ? ": {$this->getSubTitle()}" : "";
	$displayTitle = $this->getTitle() . $subTitleSuffix;


	$htmlTitle = $displayTitle . " - " . $this->getContext()->getConf()->web->title;
?>
	<title><?php echo htmlentities( $htmlTitle ); ?></title>
	<link rel="stylesheet" href="<?php echo swarmpath( "css/site.css" ); ?>">
	<script>window.SWARM = <?php echo json_encode( array(
		// Export a simplified version of the TestSwarm configuration object to the browser
		// (not the entire object since it also contains DB password and such..).
		"web" => array(
			"contextpath" => swarmpath( "" ),
			"ajax_update_interval" => $this->getContext()->getConf()->web->ajax_update_interval,
		),
		"client" => $this->getContext()->getConf()->client,
	) ); ?>;</script><?php

	foreach ( $this->styleSheets as $styleSheet ) {
		echo "\n\t" . html_tag( "link", array( "rel" => "stylesheet", "href" => $styleSheet ) );
	}

	foreach ( $this->headScripts as $headScript ) {
		echo "\n\t" . html_tag( "script", array( "src" => $headScript ) );
	}
?>
</head>
<body>
	<ul class="nav">
<?php
	if ( $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes" ) {
		$username = htmlspecialchars( $request->getSessionData( "username" ) );
?>
		<li><strong><a href="<?php echo swarmpath( "user/$username" ); ?>"><?php echo $username;?></a></strong></li>
		<li><a href="<?php echo swarmpath( "run/{$username}" );?>">Join the Swarm</a></li>
		<li><a href="<?php echo swarmpath( "logout" ); ?>">Logout</a></li>
<?php
	} else {
?>
		<li><a href="<?php echo swarmpath( "login" ); ?>">Login</a></li>
		<li><a href="<?php echo swarmpath( "signup" ); ?>">Signup</a></li>
<?php
	}
?>
	</ul>
	<h1><a href="<?php echo swarmpath( "" ); ?>">
		<img src="<?php echo swarmpath( "images/testswarm_logo_wordmark.png" ); ?>" alt="TestSwarm" title="TestSwarm">
	</a></h1>
	<h2><?php echo htmlspecialchars( $displayTitle ); ?></h2>
	<div id="main">
<?php
	echo $this->getContent();
?>
	</div>
	<div id="footer">Powered by <a href="//github.com/jquery/testswarm">TestSwarm</a>:
		<a href="//github.com/jquery/testswarm">Source Code</a>
		| <a href="//github.com/jquery/testswarm/issues">Issue Tracker</a>
		| <a href="//github.com/jquery/testswarm/wiki">About</a>
		| <a href="//groups.google.com/group/testswarm">Discuss</a>
		| <a href="//twitter.com/testswarm">Twitter</a>
	</div><?php

	foreach ( $this->bodyScripts as $bodyScript ) {
		echo "\n\t" . html_tag( "script", array( "src" => $bodyScript ) );
	}

?>
</body>
</html>
<?php
	// End of Page::output
	}

	final public function handleException( Exception $e ) {

		header( $_SERVER["SERVER_PROTOCOL"] . " 500 TestSwarm Internal Error", true, 500 );

		$this->setTitle( "Internal error" );
		$this->setSubTitle( null );

		$msg = '<div class="errorbox">An internal error occurred.'
				. ' The following error message was caught:<br><strong><code>'
				. nl2br( htmlspecialchars( $e->getMessage() ) ) . '</code></strong></div>';

		if ( $this->getContext()->getConf()->debug->show_exception_details ) {
			$msg .=
				'<p>Backtrace:</p><pre>' . nl2br( htmlspecialchars( $e->getTraceAsString() ) ) .
				"</pre>\n";
		} else {
			$msg .=
				'<p>Set <b><code>show_exception_details = 1;</code></b> ' .
				'in the <code>[debug]</code> section at the bottom of <code>testswarm.ini</code> to show detailed debugging information.</p>';
		}

		$this->content = $msg;
	}

	final public static function getPageClassByName( $pageName ) {
		$className = ucfirst( strtolower( $pageName ) ) . 'Page';
		return class_exists( $className ) ? $className : null;
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		// self refers to the origin class (abstract Page)
		// static refers to the current class (FoobarPage)
		$page = new static();
		$page->context = $context;
		return $page;
	}

	final protected function getContext() {
		return $this->context;
	}

	final protected function getAction() {
		return $this->action;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	final private function __construct() {}
}
