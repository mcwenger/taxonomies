<?php

/**
 * Taxonomies: Core
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

// include config
include( Path::assemble( BASE_PATH, Config::getAddOnPath('taxonomies'), 'config.php' ) );

class Core_taxonomies extends Core
{

  // class properties
  public $pkg       = TAXONOMY_PKG;
  public $key       = TAXONOMY_KEY;
  private $filename = 'taxonomies.yaml';

	/**
	 * Constructor
	 *
	 * @access      public
	 * @return      void
	 */
  public function __construct()
  {
    parent::__construct();

  }// end: __construct

  //-----------------------------------------------------------

  /**
	 * getTaxonomiesPath
	 *
	 * @access      public
	 * @return      string
	 */

  public function getTaxonomiesPath()
  {
    // pull/set from/to the _config/add-ons/{pkg} location
    $dir_path  = Path::assemble( Config::getConfigPath(), 'add-ons', $this->pkg, '' );
    $file_path = $dir_path . $this->filename;

    // let's make sure the file/dirs exist
    if (!file_exists($dir_path))
    {
      mkdir($dir_path, 0755, true) or die('Failed to create directory structure for ' . $this->pkg);
    }
    if (!file_exists($file_path))
    {
      $mkfile = fopen($file_path, 'w') or die('Unable to create file ' . $this->filename);
      fwrite($mkfile, '');
      fclose($mkfile);
    }
    return $file_path;

  }// end: getTaxonomiesPath

  //-----------------------------------------------------------

  /**
	 * getTaxonomies
	 *
	 * @access      public
	 * @return      array
	 */
  public function getTaxonomies()
  {
		$yaml = YAML::parseFile( $this->getTaxonomiesPath() );

    if (empty($yaml)) $yaml[$this->key] = array();

    return $yaml;

  }// end: getTaxonomies

    //-----------------------------------------------------------

    /**
  	 * getTaxonomiesBySlug
  	 *
  	 * @access      public
  	 * @return      array
  	 */
    public function getTaxonomiesBySlug($group_slug = FALSE, $yaml = FALSE)
    {
      $return = array();
      $yaml   = is_array($yaml) && isset($yaml[$this->key]) ? $yaml : $this->getTaxonomies()[$this->key];

      if (!$group_slug) return $return;

      $group = $this->recursiveArraySearch($group_slug, $yaml);

      if (!isset($yaml[$group]['taxonomies'])) return $return;

      $group = $yaml[$group]['taxonomies'];

      // create value/pair sets
      foreach($group as $taxonomy)
      {
        $return[$taxonomy['taxonomy_slug']] = $taxonomy['taxonomy_name'];
      }

      return $return;

    }// end: getTaxonomiesBySlug

  //-----------------------------------------------------------

  /**
	 * getRelicatorConfig
	 *
	 * @access      public
	 * @return      array
	 */
  public function getRelicatorConfig()
  {
    return array(
      'sets' => array(
        'taxonomy_group' => array(
          'display' => Localization::fetch('taxonomy_group_display_taxonomies'),
          'fields' => array(
            'taxonomy_group_slug' => array(
              'type' => 'text',
              'display' => Localization::fetch('taxonomy_group_slug_display_taxonomies'),
              'instructions' => Localization::fetch('taxonomy_group_slug_instructions_taxonomies'),
              'required' => 'true'
            ),
            'taxonomies' => array(
              'type' => 'grid',
              'display' => Localization::fetch('taxonomies_display_taxonomies'),
              'starting_rows' => 1,
              'min_rows' => 1,
              'max_rows' => 100,
              'fields' => array(
                'taxonomy_name' => array(
                  'display' => Localization::fetch('taxonomy_name_display_taxonomies'),
                  'type' => 'text',
                  'width' => '50%',
                  'required' => 'true'
                ),
                'taxonomy_slug' => array(
                  'display' => Localization::fetch('taxonomy_slug_display_taxonomies'),
                  'type' => 'text',
                  'instructions' => Localization::fetch('taxonomy_slug_instructions_taxonomies'),
                  'width' => '50%',
                  'required' => 'true'
                )
              )
            )
          )
        )
      )
    );

  }// end: getRelicatorConfig

  //-----------------------------------------------------------

  /**
	 * recursiveArraySearch
	 *
	 * @access      public
	 * @return      string/int
	 */
  public function recursiveArraySearch($needle=false,$haystack=false,$needle_as_regex=false)
  {
		if (!$needle && !$haystack) return false;

    foreach($haystack as $key=>$value) {
        $current_key = $key;
        if (
        	$needle === $value
        	OR ($needle_as_regex && preg_match($needle, $value))
        	OR (is_array($value) && $this->recursiveArraySearch($needle,$value,$needle_as_regex) !== false)
        ) {

            return $current_key;
        }
    }
    return false;

  }// end: recursiveArraySearch

  //-----------------------------------------------------------

  /**
   * titleify
   *
   * original Title Case script © John Gruber <daringfireball.net>
   * javascript port © David Gouch <individed.com>
   * PHP port of the above by Kroc Camen <camendesign.com>
   *
   * @param string  $title  Title to title case
   * @return mixed|string
   */
  public function titleify($title)
  {
      //remove HTML, storing it for later
      //       HTML elements to ignore    | tags  | entities
      $regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
      preg_match_all ($regx, $title, $html, PREG_OFFSET_CAPTURE);
      $title = preg_replace ($regx, '', $title);

      //find each word (including punctuation attached)
      preg_match_all ('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
      foreach ($m1[0] as &$m2) {
          //shorthand these- "match" and "index"
          list ($m, $i) = $m2;

          //correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
          //we fix this by recounting the text before the offset using multi-byte aware `strlen`
          $i = mb_strlen (substr ($title, 0, $i), 'UTF-8');

          //find words that should always be lowercase…
          //(never on the first word, and never if preceded by a colon)
          $m = $i>0 && mb_substr ($title, max (0, $i-2), 1, 'UTF-8') !== ':' &&
          !preg_match ('/[\x{2014}\x{2013}] ?/u', mb_substr ($title, max (0, $i-2), 2, 'UTF-8')) &&
          preg_match ('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
              ?	//…and convert them to lowercase
              mb_strtolower ($m, 'UTF-8')

              //else:	brackets and other wrappers
              : (	preg_match ('/[\'"_{(\[‘“]/u', mb_substr ($title, max (0, $i-1), 3, 'UTF-8'))
                  ?	//convert first letter within wrapper to uppercase
                  mb_substr ($m, 0, 1, 'UTF-8').
                  mb_strtoupper (mb_substr ($m, 1, 1, 'UTF-8'), 'UTF-8').
                  mb_substr ($m, 2, mb_strlen ($m, 'UTF-8')-2, 'UTF-8')

                  //else:	do not uppercase these cases
                  : (	preg_match ('/[\])}]/', mb_substr ($title, max (0, $i-1), 3, 'UTF-8')) ||
                  preg_match ('/[A-Z]+|&|\w+[._]\w+/u', mb_substr ($m, 1, mb_strlen ($m, 'UTF-8')-1, 'UTF-8'))
                      ?	$m
                      //if all else fails, then no more fringe-cases; uppercase the word
                      :	mb_strtoupper (mb_substr ($m, 0, 1, 'UTF-8'), 'UTF-8').
                      mb_substr ($m, 1, mb_strlen ($m, 'UTF-8'), 'UTF-8')
                  ));

          //resplice the title with the change (`substr_replace` is not multi-byte aware)
          $title = mb_substr ($title, 0, $i, 'UTF-8').$m.
              mb_substr ($title, $i+mb_strlen ($m, 'UTF-8'), mb_strlen ($title, 'UTF-8'), 'UTF-8')
          ;
      }

      //restore the HTML
      foreach ($html[0] as &$tag) $title = substr_replace ($title, $tag[0], $tag[1], 0);
      return $title;

  }// end: titleify

  //-----------------------------------------------------------

  /**
	 * slugify
	 *
	 * @access      public
	 * @return      string/int
	 */
  public function slugify($value, $parameters=array())
  {
      $separator = array_get($parameters, 0, '-');

      return Slug::make($value, array('separator' => $separator));

  }// end: slugify


}// end: class
