<?php

/**
 * Taxonomies: Modifier
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

class Modifier_taxonomies extends Modifier
{

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
   * index
   *
   * @access      public
   * @return      string
   */
  public function index($value, $parameters=array())
  {
    $group_slug = isset($parameters[0])
                  ? $parameters[0]
                  : FALSE;

    if (!$group_slug) return FALSE;

		$taxonomies = $this->core->getTaxonomiesBySlug($group_slug, $this->taxonomies);
    $label      = isset($taxonomies[$value])
                  ? $taxonomies[$value]
                  : FALSE;

    return $label;
  }// end: index

}// end: class
