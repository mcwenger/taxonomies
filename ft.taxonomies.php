<?php

/**
 * Taxonomy: Fieldtype
 * @ author: Mike Wenger, Q Digital Studio
 * @ version: 1.0.0
 * @author_url: http://qdigitalstudio.com
 */

class Fieldtype_taxonomies extends Fieldtype
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

    $this->pkg        = $this->core->pkg;
    $this->key        = $this->core->key;
    $this->taxonomies = isset($this->core->getTaxonomies()[$this->key])
                        ? $this->core->getTaxonomies()[$this->key]
                        : array();

  }// end: __construct

  //-----------------------------------------------------------

  /**
   * render
   *
   * @access      public
   * @return      string
   */
    public function render()
    {
      $html       = '';
      $field_data = array();

      // field_config
      $max_items   = array_get($this->field_config, 'max_items', 'null');
      $multiple    = array_get($this->field_config, 'multiple', TRUE);
      $allow_blank = array_get($this->field_config, 'allow_blank', TRUE);
      $group_slug  = array_get($this->field_config, 'group_slug', 'null');
      $field_ui    = array_get($this->field_config, 'field_ui', 'suggest');

      // revert field data array
      // if (!empty($this->field_data))
      // {
      //   foreach($this->field_data as $key => $data)
      //   {
      //     $field_data[] = $data['value'];
      //   }
      // }
      $field_data = $this->field_data;

      /*
      field_id
      field_config
      field_error
      field_data
      has_error
      field
      fieldname
      fieldnameremove
      tabindex
      is_required
      */

      if (!$group_slug) return '<br><small>' . Localization::fetch('no_taxonomies_taxonomies') . '</small>';

      $taxonomies = $this->core->getTaxonomiesBySlug($group_slug);

      // -------------------------------------
      //	Checkboxes UI
      // -------------------------------------
      if ($field_ui === 'checkboxes')
      {
        $html .= '<div class="input-block input-checkboxes">';
        $html .= '<input type="hidden" name="page[yaml][' . $this->field . ']" value="false">';
        $i = 1;
        foreach($taxonomies as $value => $label)
        {
          $html .= '<div class="checkbox-block"><input type="checkbox" name="' . $this->fieldname . '[]" id="' . $this->field_id . '_' . $i . '" class="checkbox" tabindex="' . $this->tabindex . '" value="' . $value . '"' . (is_array($field_data) && in_array($value, $field_data) ? ' checked' : '') . '><label for="' . $this->field_id . '_' . $i . '">' . $label . '</label></div>';
          $i++;
        }
        $html .= '</div>';

      // -------------------------------------
      //	Suggest UI
      // -------------------------------------
      }
      else
      {
        $suggest_field_config = array(
          'max_items' => $max_items,
          'force_list' => FALSE,
          'multiple' => $multiple,
          'allow_blank' => $allow_blank,
          'placeholder' => FALSE,
          'create' => FALSE,
          'hide_selected' => TRUE,
          'persist' => TRUE,
          'options' => $taxonomies
        );

        $fieldname = rtrim(preg_replace('/page\[yaml\]\[/', '', $this->fieldname), ']');

        $required_str = ($this->is_required) ? 'required' : '';
        $html .= Fieldtype::render_fieldtype($field_ui, $fieldname, $suggest_field_config, $field_data, $this->tabindex);
      }

      $html .= '<a class="taxonomy-edit-link" href="/admin.php/' . $this->pkg . '" class="small">' . Localization::fetch('edit_taxonomies') . '</a>';

      return $html;

    }// end: render

    //-----------------------------------------------------------

    /**
     * render
     *
     * @access      public
     * @return      object
     */
    public function process($settings)
    {
      // If empty, save as null
      if ($this->field_data === '') {
          return null;
      }

      // save the value: label pair to the content file
      // (until we find a good hook/method from the F/E)
      // also, preserve tag var namespacing

      // $return = array();
      // $slug   = isset($settings['group_slug'])
      //         ? $settings['group_slug']
      //         : FALSE;
      //
      // // get taxonomies
      // if ($slug)
      // {
      //   $taxonomies = $this->core->getTaxonomiesBySlug($slug, $this->taxonomies);
      // }
      //
      // // associate key/value
      // foreach($this->field_data as $key => $value)
      // {
      //   $taxonomy_slug = array_search($taxonomies[$value], $taxonomies);
      //   $taxonomy_name = $taxonomies[$value];
      //
      //   $return[] = array(
      //     'value'      => $taxonomy_slug,
      //     'name'       => $taxonomy_name
      //   );
      // }
      // return $return;

      return $this->field_data;

    }// end: process

}// end: class
