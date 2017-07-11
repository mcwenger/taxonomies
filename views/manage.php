<div class="container">
    <form action="/admin.php/<?php print $route; ?>/update" method="post" class="primary-form" data-validate="parsley">
    <div class="section content">

        <h1 style="margin-bottom:30px;"><?php echo Localization::fetch('nav_title_taxonomies'); ?></h1>

        <div class="input-block input-replicator" id="taxonomy-editor">
          <?php
            print Fieldtype::render_fieldtype('replicator', 'taxonomy', $field_config, $taxonomy_groups, tabindex(), '[yaml]', null, $errors);
            ?>
        </div>

    </div>
    <div id="publish-action" class="footer-controls push-down">
      <input type="submit" class="btn" value="Save &amp; Publish" id="publish-submit">
    </div>
    </form>
</div>

<?php
function tabindex()
{
  static $count = 1;
  return $count++;
}
?>
