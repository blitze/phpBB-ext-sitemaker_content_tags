<?php
/**
 *
 * @package blitze
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\tags\services;

/**
 * SiteMaker Content Tags Form field.
 */
class field extends \blitze\content\services\form\field\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \blitze\tags\services\tags */
	protected $tags;

	/** @var \blitze\sitemaker\services\util */
	protected $util;

	/** @var string */
	protected $delimitter;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language                  $language       Language object
	 * @param \phpbb\request\request_interface			$request		Request object
	 * @param \blitze\sitemaker\services\template		$ptemplate		Sitemaker template object
	 * @param \phpbb\controller\helper					$helper			Controller helper class
	 * @param \blitze\tags\services\tags				$tags			Tags object
	 * @param \blitze\sitemaker\services\util			$util       	Sitemaker utility object
	 */
	public function __construct(\phpbb\language\language $language, \phpbb\request\request_interface $request, \blitze\sitemaker\services\template $ptemplate, \phpbb\controller\helper $helper, \blitze\tags\services\tags $tags, \blitze\sitemaker\services\util $util)
	{
		parent::__construct($language, $request, $ptemplate);

		$this->helper = $helper;
		$this->tags = $tags;
		$this->util = $util;
	}

	/**
	 * @inheritdoc
	 */
	public function get_default_props()
	{
		return array(
			'type'			=> 'list',
			'colour'		=> 'dynamic',
			'max_tags'		=> 0,
			'is_db_field'	=> true,
		);
	}

	/**
	 * @inheritdoc
	 */
	public function display_field(array $data, array $topic_data, $display_mode, $view_mode)
	{
		if (empty($data['field_value']))
		{
			return '';
		}

		$this->delimitter = $this->language->lang('COMMA_SEPARATOR');

		$tag_type = ($view_mode !== 'print') ? $data['field_props']['type'] : 'list';
		$tag_color = $data['field_props']['colour'];

		$list = $this->get_html_list($data, $tag_type, $tag_color, $display_mode);

		return $this->show_tags($list, $tag_color, $tag_type);
	}

	/**
	 * @inheritdoc
	 */
	public function get_submitted_value(array $data)
	{
		$data['field_value'] = join(',', (array) $data['field_value']);
		return parent::get_submitted_value($data);
	}

	/**
	 * @inheritdoc
	 */
	public function show_form_field(array &$data, $forum_id = 0, $topic_id = 0)
	{
		$data['tag_search_url'] = $this->helper->route('blitze_tags_search');

		$this->ptemplate->assign_vars($data);

		$this->util->add_assets(array(
			'js'   => array(
				'@blitze_tags/assets/jquery-ui/jquery-ui.min.js',
				'@blitze_tags/assets/tags.min.js',
			),
			'css'   => array(
				'@blitze_tags/assets/jquery-ui/jquery-ui.min.css',
				'@blitze_tags/assets/tags.min.css',
			)
		));

		$field = $this->get_name();
		return $this->ptemplate->render_view('blitze/tags', "field.html", $field . '_field');
	}

	/**
	 * @inheritdoc
	 */
	public function save_field(array $field_data, array $topic_data)
	{
		$value = $this->to_array($field_data['field_value']);
		$this->tags->save($topic_data['topic_id'], $value);
	}

	/**
	 * @param string $value
	 * @param string $callable
	 * @param string $content_type
	 * @param string $display_mode
	 * @return array
	 */
	protected function get_html_list(array $data, $tag_type, &$tag_color, $display_mode)
	{
		$tags = $this->to_array($data['field_value']);

		$get_tag_props = 'get_' . $this->get_display_type($tag_type, $tag_color) . '_props';
		$get_tag_row = 'get_' . ($display_mode === 'preview' ? 'preview' : 'tag') . '_row';

		$list = array();
		foreach ($tags as $row)
		{
			$row = $this->$get_tag_row($row, $data['content_type']);
			$tag_props = $this->$get_tag_props($row);

			$list[] = '<span' . join(' ', $tag_props) . '><a href="' . $row['tag_url'] . '">' . ucwords(censor_text($row['tag_name'])) . '</a></span>';
		}

		return $list;
	}

	/**
	 * @param string $tag_type
	 * @param string $tag_color
	 * @return string
	 */
	protected function get_display_type($tag_type, &$tag_color)
	{
		if (($tag_type === 'label' || $tag_type === 'badge') && $tag_color === 'dynamic')
		{
			$tag_type = 'dynamic_tag';
			$tag_color = 'primary';
		}
		return $tag_type;
	}

	/**
	 * @param array $row
	 * @param string $content_type
	 * @return array
	 */
	protected function get_tag_row(array $row, $content_type)
	{
		$row['tag_url'] = $this->helper->route('blitze_content_type_filter', array(
			'type'			=> $content_type,
			'filter_type'	=> 'tag',
			'filter_value'	=> urlencode($row['tag_name']),
		));

		return $row;
	}

	/**
	 * @param string $tag
	 * @return array
	 */
	protected function get_preview_row($tag)
	{
		return array(
			'tag_name'		=> $tag,
			'tag_colour'	=> $this->tags->get_color($tag),
			'tag_url'		=> '#preview',
		);
	}

	/**
	 * @return array
	 */
	protected function get_list_props()
	{
		return array();
	}

	/**
	 * @return array
	 */
	protected function get_button_props()
	{
		$this->delimitter = ' ';
		return array(' class="button"');
	}

	/**
	 * @return array
	 */
	protected function get_label_props()
	{
		return array(' class="info"');
	}

	/**
	 * @return array
	 */
	protected function get_badge_props()
	{
		return $this->get_label_props();
	}

	/**
	 * @param array $row
	 * @return array
	 */
	protected function get_dynamic_tag_props(array $row)
	{
		return array_merge(
			$this->get_label_props(),
			array(' style="background: #' . $row['tag_colour'] . '"')
		);
	}

	/**
	 * @param array $list
	 * @param string $tag_color
	 * @param string $tag_type
	 * @return string
	 */
	protected function show_tags(array $list, $tag_color, $tag_type)
	{
		if ($tag_type === 'label' || $tag_type === 'badge')
		{
			return '<span class="sm-' . $tag_type . ' ' . $tag_color . '-color">' . join('', $list) . '</span>';
		}
		else
		{
			return join($this->delimitter, $list);
		}
	}

	/**
	 * @param string $tags
	 * @return array
	 */
	protected function to_array($tags)
	{
		return is_array($tags) ? $tags : explode(',', $tags);
	}

	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return 'tags';
	}

	/**
	 * @inheritdoc
	 */
	public function get_langname()
	{
		return 'TAGS_FIELD';
	}
}
