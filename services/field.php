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
	 * @inhericdoc
	 */
	public function get_field_value(array $data)
	{
		$data['field_value'] = join(',', (array) $data['field_value']);
		$value = parent::get_field_value($data);
		return array_map('trim', array_filter(explode(',', strtolower($value))));
	}

	/**
	 * @inheritdoc
	 */
	public function display_field(array $data, array $topic_data, $view_mode)
	{
		$this->preview_tags($data);

		if (empty($data['field_value']))
		{
			return '';
		}

		$this->delimitter = $this->language->lang('COMMA_SEPARATOR');

		$display = ($view_mode !== 'print') ? $data['field_props']['display'] : 'list';
		$label_class = $data['field_props']['label_colour'];
		$callable = 'get_' . $this->get_display_type($display, $label_class) . '_props';
		$list = $this->get_html_list($data['field_value'], $callable, $data['content_type']);

		return $this->show_tags($list, $label_class, $display);
	}

	/**
	 * @inheritdoc
	 */
	public function show_form_field($name, array &$data, $forum_id = 0, $topic_id = 0)
	{
		$data['field_name'] = $name;
		$data['field_value'] = join(',', $this->get_field_value($data));
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
	public function save_field($tag_names, array $field_data, array $topic_data)
	{
		$this->tags->save($topic_data['topic_id'], $tag_names);
	}

	/**
	 * @inheritdoc
	 */
	public function get_default_props()
	{
		return array(
			'display'		=> 'list',
			'label_colour'	=> 'dynamic',
			'max_tags'		=> 0,
			'is_db_field'	=> true,
		);
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

	/**
	 * @param string $display
	 * @param string $label_class
	 * @return string
	 */
	protected function get_display_type($display, &$label_class)
	{
		if ($display === 'label' && $label_class === 'dynamic')
		{
			$display = 'dynamic_label';
			$label_class = 'primary';
		}
		return $display;
	}

	/**
	 * @param array $tags
	 * @param string $callable
	 * @param string $content_type
	 * @return array
	 */
	protected function get_html_list(array $tags, $callable, $content_type)
	{
		$list = array();
		foreach ($tags as $row)
		{
			$tag_url = $this->helper->route('blitze_content_type_filter', array(
				'type'			=> $content_type,
				'filter_type'	=> 'tag',
				'filter_value'	=> urlencode($row['tag_name']),
			));
			$list[] = '<a href="' . $tag_url . '"><span' . join(' ', $this->$callable($row)) . '>' . ucwords(censor_text($row['tag_name'])) . '</span></a>';
		}

		return $list;
	}

	/**
	 * @param array $data
	 * @return void
	 */
	protected function preview_tags(array &$data)
	{
		if ($this->request->is_set('preview') && ($tag_names = $this->get_field_value($data)))
		{
			$data['field_value'] = array();
			foreach ($tag_names as $tag)
			{
				$data['field_value'][] = array(
					'tag_name'		=> $tag,
					'tag_colour'	=> $this->tags->get_color($tag),
				);
			}
		}
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
	 * @param array $row
	 * @return array
	 */
	protected function get_dynamic_label_props(array $row)
	{
		return array_merge(
			$this->get_label_props(),
			array(' style="background: #' . $row['tag_colour'] . '"')
		);
	}

	/**
	 * @param array $list
	 * @param string $label_class
	 * @param string $display_type
	 * @return string
	 */
	protected function show_tags(array $list, $label_class, $display_type)
	{
		if ($display_type === 'label')
		{
			return '<span class="sm-label ' . $label_class . '-color">' . join('', $list) . '</span>';
		}
		else
		{
			return join($this->delimitter, $list);
		}
	}
}
