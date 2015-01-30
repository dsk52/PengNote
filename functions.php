<?php
// カスタムメニューを有効化
add_theme_support( 'menus' );
register_nav_menu( 'place_global', 'グローバルナビゲーション' );

// アイキャッチ画像
add_theme_support( 'post-thumbnails' );
add_image_size( 'archive', 450, 280, true );
add_image_size( 'large', 660, 460, false );

// ウィジェットの有効化
function footer_widgets_init() {
  register_sidebar(array(
   'name' => 'フッターウィジェット',
   'description' => 'フッターに置くウィジェット',
   'id' => 'footer-widget',
   'before_widget' => '<div class="l-footer-widget">',
   'after_widget' => '</div>',
  ));
}
add_action( 'widgets_init', 'footer_widgets_init' );

// ページャー
function pagerNavi($maxNum,$pCur){
  global $wp_rewrite;
  $paginate_base = get_pagenum_link(1);
  if(strpos($paginate_base, '?') || ! $wp_rewrite->using_permalinks()){
      $paginate_format = '';
      $paginate_base = add_query_arg('paged','%#%');
  }
  else{
      $paginate_format = (substr($paginate_base,-1,1) == '/' ? '' : '/') .
      user_trailingslashit('page/%#%/','paged');;
      $paginate_base .= '%_%';
  }
  echo paginate_links(array(
      'base' => $paginate_base,
      'format' => $paginate_format,
      'total' => $maxNum,
      'mid_size' => 4,
      'current' => ($pCur ? $pCur : 1),
      'prev_text' => '«',
      'next_text' => '»',
  ));
}

// 個別ページのナビゲーションの設定
function Custom_previous_post_link($maxlen = -1, $format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
  Custom_adjacent_post_link($maxlen, $format, $link, $in_same_cat, $excluded_categories, true, $maxlen);
}
function Custom_next_post_link($maxlen = -1, $format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
  Custom_adjacent_post_link($maxlen, $format, $link, $in_same_cat, $excluded_categories, false);
}

function Custom_adjacent_post_link($maxlen = -1, $format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '', $previous = true) {

  if ( $previous && is_attachment() )
    $post = & get_post($GLOBALS['post']->post_parent);
  else
    $post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

  if ( !$post )
    return;

  $tCnt = mb_strlen( $post->post_title, get_bloginfo('charset') );
  
  if(($maxlen > 0)&&($tCnt > $maxlen)) {
    $title = mb_substr( $post->post_title, 0, $maxlen, get_bloginfo('charset') ) . '…';
  } else {
    $title = $post->post_title;
  }

  if ( empty($post->post_title) )
    $title = $previous ? __('Previous Post') : __('Next Post');

  $title = apply_filters('the_title', $title, $post->ID);
  $date = mysql2date(get_option('date_format'), $post->post_date);
  $rel = $previous ? 'prev' : 'next';

  $string = '<a href="'.get_permalink($post).'" rel="'.$rel.'">';
  $link = str_replace('%title', $title, $link);
  $link = str_replace('%date', $date, $link);
  $link = $string . $link . '</a>';

  $format = str_replace('%link', $link, $format);
  echo $format;
}