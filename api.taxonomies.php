<?php

/**
 * Taxonomies: API
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

class API_taxonomies extends API {

	// class properties
  private $pkg;

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

  }// end: __construct

}
