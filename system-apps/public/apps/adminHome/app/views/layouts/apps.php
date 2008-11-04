
<script>jQuery.noConflict();</script>

<script type="text/javascript" charset="utf-8">
<?php include dirname(dirname(dirname(dirname(__FILE__)))) . "/public/javascripts/ajaxMenu.js"; ?>
<?php include dirname(dirname(dirname(dirname(__FILE__)))) . "/public/javascripts/communities.js"; ?>
</script>

<?php include dirname(dirname(dirname(dirname(__FILE__)))) . "/public/stylesheets/style.php"; ?>



<% yield %>

<script type="text/javascript">
  loadMenus();
</script>
