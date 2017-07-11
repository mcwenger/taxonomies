
/**
 *
 * Enable Taxonomies editor functionality
 *
 */
var TaxonomiesEditor = function()
{
  //-----------------------------------
  // Private vars
  //-----------------------------------
  var editor = '#taxonomy-editor',
      groupSlugTrigger = 'taxonomy_group_slug',
      nameTrigger = 'taxonomy_name',
      slugTrigger = 'taxonomy_slug',
      groupSlugField = 'input[name*="[' + groupSlugTrigger + ']"]',
      nameField = 'input[name*="[' + nameTrigger + ']"]',
      slugField = 'input[name*="[' + slugTrigger + ']"]',
      storageAttr = 'data-storage',
      _event = 'blur',
      allowSlugUpdate = false;

  //-----------------------------------
  // Private methods
  //-----------------------------------
	_bind = function(obj)
	{
    if (!obj) obj = $(editor);

    $(nameField, obj).each(function(){

      var $field = $(this),
          val = $field.val();

      $field.attr( storageAttr, _slugify(val) );
    });

    // group/taxonomy slug
    $(obj).on(_event, groupSlugField + ', ' + slugField, function(e)
    {
      var $field = $(this),
          val = $field.val();

      $field.val( _slugify(val) );
    });

    // name field
    $(obj).on(_event, nameField, function(e)
    {
      var $field = $(this),
          val = $field.val(),
          slugVal = _slugify(val),
          $parent = $field.closest('tr'),
          $slug = $(slugField, $parent),
          storage = $field.attr(storageAttr);

      // only update if the name field has updated
      if (storage != slugVal)
      {
        if (allowSlugUpdate || !allowSlugUpdate && $slug.val() === '')
        {
          $slug.val( slugVal );
          $field.attr( storageAttr, slugVal );
        }
      }
    });
  }

  // destroy
	_destroy = function(obj)
	{
    $(obj).off(_event, groupSlugField + ', ' + slugField);
    $(obj).off(_event, nameField);
    $(nameField, obj).each(function(){
      $(this).removeAttr(storageAttr);
    });
  }

  // slugify
  _slugify = function(str)
  {
    return str
              .toString()
              .toLowerCase()
              .replace(/\s+/g, '-')
              .replace(/[^\w\-]+/g, '')
              .replace(/\-\-+/g, '-')
              .replace(/^-+/, '')
              .replace(/-+$/, '');
  }

  //-----------------------------------
  // Public access/methods
  //-----------------------------------
  return {

    init: function(obj)
    {
      _bind(obj);
      this.active = 1;
    },
    destroy: function(obj)
    {
      _destroy(obj);
      this.active = 0;
    },
    active: 0,
  };
}

$(document).ready(function(){

  var TaxonomyEditor = new TaxonomiesEditor();
  TaxonomyEditor.init();

});
