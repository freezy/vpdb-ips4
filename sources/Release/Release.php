<?php
/**
 * @brief        Image Model
 * @author        <a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright    (c) Invision Power Services, Inc.
 * @license        https://www.invisioncommunity.com/legal/standards/
 * @package        Invision Community
 * @subpackage    Gallery
 * @since        04 Mar 2014
 */

namespace IPS\vpdb;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (!defined('\IPS\SUITE_UNIQUE_KEY')) {
	header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 403 Forbidden');
	exit;
}

/**
 * Image Model
 */
class _Release extends \IPS\Content\Item implements
	\IPS\Content\Searchable,
	\IPS\Content\ReadMarkers,
	\IPS\Content\MetaData,
	\IPS\Content\Lockable
{
	/**
	 * @brief    Application
	 */
	public static $application = 'vpdb';

	/**
	 * @brief    Module
	 */
	public static $module = 'releases';

	/**
	 * @brief    Comment Class
	 */
	public static $commentClass = 'IPS\vpdb\Release\Comment';

	/**
	 * This is to cheat field accesses.
	 *
	 * @brief    Database Column Map
	 */
	public static $databaseColumnMap = array(
		'title' => 'title'
	);

	/**
	 * Used in moderator log
	 * @brief	Title
	 */
	public static $title = 'vpdb_release';

	/**
	 * @var array Release fetched from VPDB
	 */
	public $release;

	/**
	 * @var bool If false, only game and release IDs are set.
	 */
	protected $populated = false;

	/**
	 * Construct with data from VPDB
	 * @param $release array|string Release details from VPDB
	 */
	public function __construct($release)
	{
		if (is_string($release)) {
			if (\strpos($release, '/') !== false) {
				list($gameId, $releaseId) = explode("/", $release);
			} else {
				$releaseId = $release;
				$gameId = null;
			}
			$this->release = (object)['id' => $releaseId, 'game' => (object)['id' => $gameId]];
		} else {
			$this->release = $release;
			$this->populated = true;
		}
	}

	/**
	 * Load Record
	 *
	 * @see        \IPS\Db::build
	 * @param    int|string $id ID
	 * @param    string $idField The database column that the $id parameter pertains to (NULL will use static::$databaseColumnId)
	 * @param    mixed $extraWhereClause Additional where clause(s) (see \IPS\Db::build for details) - if used will cause multiton store to be skipped and a query always ran
	 * @return    static
	 * @throws    \InvalidArgumentException
	 * @throws    \OutOfRangeException
	 */
	public static function load($id, $idField = NULL, $extraWhereClause = NULL)
	{
		return new \IPS\vpdb\Release($id);
	}

	/**
	 * Get comments output
	 * @return    string
	 */
	public function renderComments()
	{
		return \IPS\Theme::i()->getTemplate('releases')->comments($this);
	}

	/**
	 * Get URL
	 *
	 * @param    string|NULL $action Action
	 * @return    \IPS\Http\Url
	 */
	public function url($action = NULL)
	{
		if ($action) {
			return \IPS\Http\Url::internal('app=vpdb&module=releases&controller=viewRelease&id=' . $this->release->id . '&gameId=' . $this->release->game->id . '&do=' . $action);
		} else {
			return \IPS\Http\Url::internal('app=vpdb&module=releases&controller=viewRelease&id=' . $this->release->id . '&gameId=' . $this->release->game->id);
		}
	}

	/**
	 * Fetch Meta Data
	 *
	 * @return    array
	 * @throws    \BadMethodCallException
	 */
	public function getMeta()
	{
		// TODO check wtf this is
		return array();
	}

	/**
	 * Supported Meta Data Types
	 *
	 * @return    array
	 */
	public static function supportedMetaDataTypes()
	{
		return array();
	}

	public function getReleaseId()
	{
		return $this->release->id;
	}

	public function getGameId()
	{
		return $this->release->game->id;
	}

	/**
	 * Needed during comment creation
	 * @return string
	 */
	public function get_id()
	{
		return $this->release->game->id . '/' . $this->release->id;
	}

	/**
	 * Needed when logging event after deleting
	 * @return string
	 */
	public function get_title() {
		return $this->getReleaseId();
	}
}