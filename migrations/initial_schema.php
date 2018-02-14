<?php
/**
 *
 * SiteMaker Content Tags. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Daniel A., https://github.com/blitze
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace blitze\tags\migrations;

class initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'sm_tags');
	}

	static public function depends_on()
	{
		return array(
			'\blitze\content\migrations\v30x\m1_initial_schema',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'sm_tags'	=> array(
					'COLUMNS'		=> array(
						'tag_id'		=> array('UINT', null, 'auto_increment'),
						'tag_name'		=> array('VCHAR:55', ''),
						'tag_colour'	=> array('VCHAR:6', ''),
					),
					'PRIMARY_KEY'	=> 'tag_id',
					'KEYS'			=> array(
						'tag_name'		=> array('UNIQUE', 'tag_name'),
					),
				),
				$this->table_prefix . 'sm_tags_data' => array(
					'COLUMNS'        => array(
						'tag_id'		=> array('UINT', 0),
						'topic_id'		=> array('UINT', 0),
					),
					'KEYS'			=> array(
						'topic_id'		=> array('INDEX', 'topic_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'sm_tags',
				$this->table_prefix . 'sm_tags_data',
			),
		);
	}
}
