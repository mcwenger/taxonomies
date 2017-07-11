<?php

/**
 * Taxonomy: Plugin
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

class Plugin_taxonomies extends Plugin {

	var $meta = array(
		'name'       => 'Taxonomies',
		'version'    => '1.0.0',
		'author'     => 'Mike Wenger, Q Digital Studio',
		'author_url' => 'http://qdigitalstudio.com'
	);

	// class properties
  private $pkg;
	private $key;
	private $taxonomies;

  /**
   * Constructor
   *
   * @access      public
   * @return      void
   */
  public function __construct()
  {
    parent::__construct();

    $this->pkg = $this->core->pkg;
		$this->key = $this->core->key;
		$this->taxonomies = isset($this->core->getTaxonomies()[$this->key])
												? $this->core->getTaxonomies()[$this->key]
												: array();

  }// end: __construct

	//-----------------------------------------------------------

	/**
   * listing
   *
   * @access      public
   * @return      string
   */
	public function listing()
	{
		$group_slug = $this->fetchParam('group_slug');
		$taxonomies = $this->core->getTaxonomiesBySlug($group_slug, $this->taxonomies);

		if (empty($taxonomies)) return FALSE;

		$output 		   = '';
		$count 				 = 1;
		$total_options = count($taxonomies);

		foreach($taxonomies as $value=>$label)
		{
			$data 								 = array();
			$data['value'] 				 = $value;
			$data['label'] 				 = $label;
			$data['total_options'] = $total_options;
			$data['index'] 				 = $count;

			$output .= Parse::template($this->content, $data);
			$count ++;
		}
    return $output;

	}// end: listing

	//-----------------------------------------------------------

	/**
   * name
   *
   * @access      public
   * @return      string
   */
	public function name()
	{
		$slug       = $this->fetchParam('slug', FALSE);
		$group_slug = $this->fetchParam('group_slug', '');
		$taxonomies = $this->core->getTaxonomiesBySlug($group_slug, $this->taxonomies);

		if (!$slug OR empty($taxonomies)) return FALSE;

		return isset($taxonomies[$slug])
					 ? $taxonomies[$slug]
					 : FALSE;

	}// end: name

}// end: class
