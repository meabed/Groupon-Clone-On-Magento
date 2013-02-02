$.noConflict();
jQuery(document).ready(function($) {
  jQuery('img.lazy').jail({
    event: 'load+scroll',
    placeholder : "/skin/frontend/default/site/images/loader.gif"
  });
});