<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Daniel A., https://github.com/blitze
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace blitze\tags\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * SiteMaker Content Tags Filter Content Event listener.
 */
class filter implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $tags_table;

	/** @var string */
	protected $tags_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface			$db					Database connection
	 * @param string									$tags_table			Tags Table
	 * @param string									$tags_data_table	Tags Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, $tags_table, $tags_data_table)
	{
		$this->db = $db;
		$this->tags_table = $tags_table;
		$this->tags_data_table = $tags_data_table;
	}

	/**
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'blitze.content.view.filter'	=> 'filter_by_tag',
		);
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function filter_by_tag(\phpbb\event\data $event)
	{
		if (isset($event['filters']['tag']))
		{
			$sql_array = $event['sql_array'];

			$sql_array = array_merge_recursive($sql_array, array(
				'FROM'	=> array(
					$this->tags_data_table	=> 'tags_data',
					$this->tags_table		=> 'tags',
				),
				'WHERE'	=> array(
					't.topic_id = tags_data.topic_id',
					'tags_data.tag_id = tags.tag_id',
					$this->db->sql_in_set('tags.tag_name', array_map('urldecode', $event['filters']['tag'])),
				),
			));

			$event['sql_array'] = $sql_array;
		}
	}
}
