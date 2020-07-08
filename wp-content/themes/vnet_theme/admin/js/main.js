if (!$) $ = jQuery;

VN = {};
VN.fn = {};


VN.add_active_menu = function (item) {
  $(item).addClass('active');
}





VN.fn.change_system_tag_style = function () {
  $('.taxonomy-page_tags').each(function (i, item) {
    let href = $(this).find('a').attr('href');
    if (!href) return;
    if (href.includes('page_tags=system_page')) {
      item.parentNode.classList.add('system-tag-page');
    }
  })
}





VN.fn.active_menu_item = function () {
  if (window.location.href.includes('post.php?post=' + back_dates.block_post + '&action')) {
    VN.add_active_menu('#toplevel_page_post-post-' + back_dates.block_post + '-action-edit');
  }
  if (window.location.href.includes('post.php?post=' + back_dates.about_post + '&action')) {
    VN.add_active_menu('#toplevel_page_post-post-' + back_dates.about_post + '-action-edit');
  }
}



VN.fn.init_fancybox_template_preview = function () {
  $('.template-link-preview').fancybox();
  // let btn = document.querySelector('.acf-actions .acf-button[data-name=add-layout]');
}





$(document).ready(function () {
  for (let key in VN.fn) {
    VN.fn[key]();
  }
});
