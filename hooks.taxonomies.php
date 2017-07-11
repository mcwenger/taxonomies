<?php

/**
 * Taxonomies: Hooks
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

 // https://v1.statamic.com/learn/documentation/hooks

class Hooks_taxonomies extends Hooks {

  // class properties
  private $pkg;
  public $key;

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

  }// end: __construct

  //-----------------------------------------------------------

  /**
   * control_panel__add_routes hook
   *
   * @access      public
   * @return      void
   */
  public function control_panel__add_routes()
  {
    $app = \Slim\Slim::getInstance();

    // -------------------------------------
		//	View(s)
		// -------------------------------------
    $app->get('/' . $this->pkg, function() use ($app) {
      authenticateForRole('admin');
      doStatamicVersionCheck($app);

      Statamic_View::set_templates(array('manage'), __DIR__ . '/views');

      // fetch errors
      $errors = $this->flash->get($this->pkg . '_errors');

      // set view data
      $data                 = $this->core->getTaxonomies();
      $data['field_config'] = $this->core->getRelicatorConfig();
      $data['pkg']          = $this->pkg;
      $data['fieldname']    = 'taxonomy';
      $data['errors']       = $errors ? implode('<br>', $errors) : '';

      // render
      $app->render(null, array('route' => $this->pkg, 'app' => $app) + $data);

    })->name( $this->pkg );

    // -------------------------------------
		//	Update
		// -------------------------------------
    $app->post('/' . $this->pkg . '/update', function() use ($app) {
        authenticateForRole('admin');
        doStatamicVersionCheck($app);

        $errors = array();

        // fetch the yaml array (yes, this isn't cleansed - yet)
        $taxonomies[$this->key] = $_POST['page']['yaml']['taxonomy'];

        // add the type for replicator
        foreach($taxonomies[$this->key] as $gi => $group)
        {
          $taxonomy_group_slug_name = 'taxonomy_group_slug';
          $taxonomies_name          = 'taxonomies';
          $taxonomy_name_name       = 'taxonomy_name';
          $taxonomy_slug_name       = 'taxonomy_slug';

          if (!isset($group[$taxonomy_group_slug_name]) OR empty($group[$taxonomy_group_slug_name]))
          {
            $errors[$taxonomy_group_slug_name . '_display_taxonomies'] = Localization::fetch($taxonomy_group_slug_name . '_display_taxonomies') . ': taxonomy group slugs may not be empty.';
          }

          // group data assurance
          $taxonomies[$this->key][$gi][$taxonomy_group_slug_name] = $this->core->slugify($group[$taxonomy_group_slug_name]);
          $taxonomies[$this->key][$gi]['type'] = 'taxonomy_group';

          // loop each taxonomy
          foreach($group[$taxonomies_name] as $ti => $taxonomy)
          {
            if (!isset($taxonomy[$taxonomy_name_name]) OR empty($taxonomy[$taxonomy_name_name]))
            {
              $errors[$taxonomy_name_name . '_display_taxonomies'] = Localization::fetch($taxonomy_name_name . '_display_taxonomies') . ': taxonomy values may not be empty.';
            }
            if (!isset($taxonomy[$taxonomy_slug_name]) OR empty($taxonomy[$taxonomy_slug_name]))
            {
              $errors[$taxonomy_slug_name . '_display_taxonomies'] = Localization::fetch($taxonomy_slug_name . '_display_taxonomies') . ': taxonomy values may not be empty.';
            }

            // taxonomy data assurance
            $taxonomies[$this->key][$gi][$taxonomies_name][$ti][$taxonomy_slug_name] = $this->core->slugify($taxonomy[$taxonomy_slug_name]);
          }
        }

        // echo '<pre>';
        // print_r($taxonomies);
        // exit;

        // set flash errors
        $this->flash->set($this->pkg . '_errors', (!empty($errors) ? $errors : FALSE));

        // only save if we do not have errors
        if (empty($errors))
        {
          // dump it
          File::put($this->core->getTaxonomiesPath(), YAML::dump($taxonomies, 1));

          $app->flash('success', Localization::fetch('success_taxonomies'));
        }

        $app->redirect($app->urlFor( $this->pkg ));

        // $this->field_data = Helper::ensureArray($this->field_data);
        // foreach ($taxonomies as $key => $value) {
        //     $taxonomies[$key] = strtolower($value);
        // }

    });

  }// end: control_panel__add_routes

  //-----------------------------------------------------------

  /**
   * control_panel__add_to_head hook
   *
   * @access      public
   * @return      string
   */
  public function control_panel__add_to_head()
  {
    $locations = array(
      '/publish',
      '/' . $this->pkg
    );

    if (in_array(URL::getCurrent(false), $locations))
    {
      $return = '';

      $css = array(
        'taxonomies.css'
      );
      foreach($css as $file)
      {
        $return .= $this->css->link($file)."\n";
      }
      return $return;
    }
  }// end: control_panel__add_to_head

  //-----------------------------------------------------------

  /**
   * control_panel__add_to_foot hook
   *
   * @access      public
   * @return      string
   */
  public function control_panel__add_to_foot()
  {
    $locations = array(
      '/' . $this->pkg
    );

    if (in_array(URL::getCurrent(false), $locations))
    {
      $return = '';

      $js = array(
        'taxonomies.js'
      );
      foreach($js as $file)
      {
        $return .= $this->js->link($file)."\n";
      }
      return $return;
    }
  }// end: control_panel__add_to_foot

}// end: class
