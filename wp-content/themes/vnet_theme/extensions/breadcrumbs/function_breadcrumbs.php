<?php

function vnet_breadcrumbs ( &$post = false ) {

  $front_id = get_option('page_on_front');

  $exclude_terms = ['useful_articles'];

  $text['home']     = get_the_title( $front_id );
  $text['category'] = '%s';
  $text['search']   = 'Результаты поиска по запросу "%s"';
  $text['tag']      = 'Записи с тегом "%s"';
  $text['author']   = 'Статьи автора %s';
  $text['404']      = 'Ошибка 404';
  $text['page']     = 'Страница %s';
  $text['cpage']    = 'Страница комментариев %s';

  $wrap_before      = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
  $wrap_after       = '</div><!-- .breadcrumbs -->';
  $sep              = '<span class="breadcrumbs__separator">|</span>';
  $before           = '<span class="breadcrumbs__current">';
  $after            = '</span>';

  $show_on_home     = 0;
  $show_home_link   = 1;
  $show_current     = 1;
  $show_last_sep    = 1;

  if (!$post) {
    global $post;
    $arg_post = false;
  } else {
    $arg_post = true;
  }

  $home_url = home_url('/');
  $link = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $link .= '<a class="breadcrumbs__link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>';
  $link .= '<meta itemprop="position" content="%3$s" />';
  $link .= '</span>';
  $parent_id = ( $post ) ? $post->post_parent : '';
  $home_link =  sprintf( $link, $home_url, $text['home'], 1 );

  $front_id = get_option( 'page_on_front' );

  $blog_id  = get_option('page_for_posts');

  $this_id  = $post->ID;

  if ( $this_id === $blog_id || $this_id === $front_id ) {

    if ( $show_on_home ) echo $wrap_before . $home_link . $wrap_after;

  } else {

    $position = 0;

    echo $wrap_before;

    if ( $show_home_link ) {
      $position += 1;
      echo $home_link;
    }


    if ( is_category( $this_id ) ) {
      $parents = get_ancestors( get_query_var('cat'), 'category' );
      foreach ( array_reverse( $parents ) as $cat ) {
        $position += 1;
        if ( $position > 1 ) echo $sep;
        echo sprintf ( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
      }
      if ( get_query_var( 'paged' ) ) {
        $position += 1;
        $cat = get_query_var('cat');
        echo $sep . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
        echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
      } else {

        if ( $show_current ) {
          if ( $position >= 1 ) echo $sep;
          echo $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after;
        } elseif ( $show_last_sep ) echo $sep;
      }

    } elseif ( is_search () && !$arg_post ) {
      if ( get_query_var( 'paged' ) ) {
        $position += 1;
        if ( $show_home_link ) echo $sep;
        echo sprintf( $link, $home_url . '?s=' . get_search_query(), sprintf( $text['search'], get_search_query() ), $position );
        echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
      } else {
        if ( $show_current ) {
          if ( $position >= 1 ) echo $sep;
          echo $before . sprintf( $text['search'], get_search_query() ) . $after;
        } elseif ( $show_last_sep ) echo $sep;
      }

    } elseif ( is_year() && !$arg_post ) {
      if ( $show_home_link && $show_current ) echo $sep;
      if ( $show_current ) echo $before . get_the_time('Y') . $after;
      elseif ( $show_home_link && $show_last_sep ) echo $sep;

    } elseif ( is_month() && !$arg_post ) {
      if ( $show_home_link ) echo $sep;
      $position += 1;
      echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position );
      if ( $show_current ) echo $sep . $before . get_the_time('F') . $after;
      elseif ( $show_last_sep ) echo $sep;

    } elseif ( is_day() && !$arg_post ) {
      if ( $show_home_link ) echo $sep;
      $position += 1;
      echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position ) . $sep;
      $position += 1;
      echo sprintf( $link, get_month_link( get_the_time('Y'), get_the_time('m') ), get_the_time('F'), $position );
      if ( $show_current ) echo $sep . $before . get_the_time('d') . $after;
      elseif ( $show_last_sep ) echo $sep;

    } elseif ( is_single ( $this_id ) && ! is_attachment ( $this_id ) ) {

      if ( get_post_type( $this_id ) != 'post' ) {
        $position += 1;
        $post_type = get_post_type_object( get_post_type( $this_id ) );
        if ( $position > 1 ) echo $sep;
        echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->labels->name, $position );
        if ( $show_current ) echo $sep . $before . get_the_title() . $after;
        elseif ( $show_last_sep ) echo $sep;
      } else {
        $cat       = get_the_category( $this_id );
        $catID     = $cat[0]->cat_ID;
        $parents   = get_ancestors( $catID, 'category' );
        $parents   = array_reverse( $parents );
        $parents[] = $catID;
        foreach ( $parents as $cat ) {
          $position += 1;
          if ( $position > 1 ) echo $sep;
          echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
        }
        if ( get_query_var( 'cpage' ) ) {
          $position += 1;
          echo $sep . sprintf( $link, get_permalink(), get_the_title(), $position );
          echo $sep . $before . sprintf( $text['cpage'], get_query_var( 'cpage' ) ) . $after;
        } else {
          if ( $show_current ) echo $sep . $before . get_the_title() . $after;
          elseif ( $show_last_sep ) echo $sep;
        }
      }

    } elseif ( is_post_type_archive ( $this_id ) ) {
      $post_type = get_post_type_object( get_post_type( $this_id ) );
      if ( get_query_var ( 'paged' ) ) {
        $position += 1;
        if ( $position > 1 ) echo $sep;
        echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->label, $position );
        echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
      } else {
        if ( $show_home_link && $show_current ) echo $sep;
        if ( $show_current ) echo $before . $post_type->label . $after;
        elseif ( $show_home_link && $show_last_sep ) echo $sep;
      }

    } elseif ( is_attachment( $this_id ) ) {
      $parent    = get_post( $parent_id );
      $cat       = get_the_category( $parent->ID );
      $catID     = $cat[0]->cat_ID;
      $parents   = get_ancestors( $catID, 'category' );
      $parents   = array_reverse( $parents );
      $parents[] = $catID;
      foreach ( $parents as $cat ) {
        $position += 1;
        if ( $position > 1 ) echo $sep;
        echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
      }
      $position += 1;
      echo $sep . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position );
      if ( $show_current ) echo $sep . $before . get_the_title() . $after;
      elseif ( $show_last_sep ) echo $sep;

    } elseif ( get_post_type( $this_id ) === 'page' && ! $parent_id ) {

      if ( $show_home_link && $show_current ) echo  $sep ;
      if ( $show_current ) echo  $before . get_the_title() . $after;
      elseif ( $show_home_link && $show_last_sep ) echo $sep;

    } elseif ( is_page ( $this_id ) && $parent_id ) {
      $parents = get_post_ancestors( get_the_ID() );
      foreach ( array_reverse( $parents ) as $pageID ) {
        $position += 1;
        if ( $position > 1 ) echo $sep;
        echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position );
      }
      if ( $show_current ) echo $sep . $before . get_the_title() . $after;
      elseif ( $show_last_sep ) echo $sep;

    } elseif ( is_tag( $this_id ) ) {
      if ( get_query_var( 'paged' ) ) {
        $position += 1;
        $tagID = get_query_var( 'tag_id' );
        echo $sep . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position );
        echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
      } else {
        if ( $show_home_link && $show_current ) echo $sep;
        if ( $show_current ) echo $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;
        elseif ( $show_home_link && $show_last_sep ) echo $sep;
      }

    } elseif ( is_author( $this_id ) ) {
      $author = get_userdata( get_query_var( 'author' ) );
      if ( get_query_var( 'paged' ) ) {
        $position += 1;
        echo $sep . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text['author'], $author->display_name ), $position );
        echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
      } else {
        if ( $show_home_link && $show_current ) echo $sep;
        if ( $show_current ) echo $before . sprintf( $text['author'], $author->display_name ) . $after;
        elseif ( $show_home_link && $show_last_sep ) echo $sep;
      }

    } elseif ( is_404 ( $this_id ) ) {
      if ( $show_home_link && $show_current ) echo $sep;
      if ( $show_current ) echo $before . $text['404'] . $after;
      elseif ( $show_last_sep ) echo $sep;

    } elseif ( has_post_format( $this_id ) && ! is_singular( $this_id ) ) {
      // print_r(get_post_type( $this_id ));

      if ( $show_home_link && $show_current ) echo $sep;
      echo get_post_format_string( get_post_format( $this_id ) );
    } else {
      if ($post->post_type) {
        if ($post->post_type === 'product') {
          $shop_id = wc_get_page_id('shop');
          $shop_post = get_post($shop_id);
          $label = $shop_post->post_title;
          $archive_link = get_permalink( $shop_id );
          $term_list = wp_get_post_terms( $this_id, 'product_cat' );
        } else {
          $archive_link = get_post_type_archive_link ( $post->post_type );
          $object       = get_post_type_object ( $post->post_type );
          $label        = $object->label;
          $term_list = wp_get_post_terms( $this_id, 'category' );
        }
        echo  $sep . sprintf( $link, $archive_link, $label, 2 );
        $all_terms = [];
        foreach ($term_list as $term) {
          do {
            $tax       = $term->taxonomy;
            $parent_id = $term->parent;
            if (!in_array($term->slug, $exclude_terms)) {
              array_push ($all_terms, ['link' => get_term_link ($term->term_id), 'name' => $term->name]);
            }
            if ($parent_id) {
              $term = get_term_by ('term_id', $parent_id, $tax);
            }
          } while ( $parent_id );
        }
        $position = 2;
        foreach ( array_reverse($all_terms) as $term ) {
          $position++;
          echo $sep . sprintf( $link, $term['link'], $term['name'], $position );
        }
        echo $sep . $before . $post->post_title . $after;
      }
    }

    echo $wrap_after;

  }
}

//
//
// function vnet_breadcrumbs () {
//
// 	/* === ОПЦИИ === */
// 	$text['home']     = 'Главная'; // текст ссылки "Главная"
// 	$text['category'] = '%s'; // текст для страницы рубрики
// 	$text['search']   = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
// 	$text['tag']      = 'Записи с тегом "%s"'; // текст для страницы тега
// 	$text['author']   = 'Статьи автора %s'; // текст для страницы автора
// 	$text['404']      = 'Ошибка 404'; // текст для страницы 404
// 	$text['page']     = 'Страница %s'; // текст 'Страница N'
// 	$text['cpage']    = 'Страница комментариев %s'; // текст 'Страница комментариев N'
//
// 	$wrap_before    = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // открывающий тег обертки
// 	$wrap_after     = '</div><!-- .breadcrumbs -->'; // закрывающий тег обертки
// 	$sep            = '<span class="breadcrumbs__separator"> › </span>'; // разделитель между "крошками"
// 	$before         = '<span class="breadcrumbs__current">'; // тег перед текущей "крошкой"
// 	$after          = '</span>'; // тег после текущей "крошки"
//
// 	$show_on_home   = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
// 	$show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
// 	$show_current   = 1; // 1 - показывать название текущей страницы, 0 - не показывать
// 	$show_last_sep  = 1; // 1 - показывать последний разделитель, когда название текущей страницы не отображается, 0 - не показывать
// 	/* === КОНЕЦ ОПЦИЙ === */
//
// 	global $post;
// 	$home_url       = home_url('/');
// 	$link           = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
// 	$link          .= '<a class="breadcrumbs__link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>';
// 	$link          .= '<meta itemprop="position" content="%3$s" />';
// 	$link          .= '</span>';
// 	$parent_id      = ( $post ) ? $post->post_parent : '';
// 	$home_link      = sprintf( $link, $home_url, $text['home'], 1 );
//
// 	if ( is_home() || is_front_page() ) {
//
// 		if ( $show_on_home ) echo $wrap_before . $home_link . $wrap_after;
//
// 	} else {
//
// 		$position = 0;
//
// 		echo $wrap_before;
//
// 		if ( $show_home_link ) {
// 			$position += 1;
// 			echo $home_link;
// 		}
//
// 		if ( is_category() ) {
// 			$parents = get_ancestors( get_query_var('cat'), 'category' );
// 			foreach ( array_reverse( $parents ) as $cat ) {
// 				$position += 1;
// 				if ( $position > 1 ) echo $sep;
// 				echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
// 			}
// 			if ( get_query_var( 'paged' ) ) {
// 				$position += 1;
// 				$cat = get_query_var('cat');
// 				echo $sep . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
// 				echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
// 			} else {
// 				if ( $show_current ) {
// 					if ( $position >= 1 ) echo $sep;
// 					echo $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after;
// 				} elseif ( $show_last_sep ) echo $sep;
// 			}
//
// 		} elseif ( is_search() ) {
// 			if ( get_query_var( 'paged' ) ) {
// 				$position += 1;
// 				if ( $show_home_link ) echo $sep;
// 				echo sprintf( $link, $home_url . '?s=' . get_search_query(), sprintf( $text['search'], get_search_query() ), $position );
// 				echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
// 			} else {
// 				if ( $show_current ) {
// 					if ( $position >= 1 ) echo $sep;
// 					echo $before . sprintf( $text['search'], get_search_query() ) . $after;
// 				} elseif ( $show_last_sep ) echo $sep;
// 			}
//
// 		} elseif ( is_year() ) {
// 			if ( $show_home_link && $show_current ) echo $sep;
// 			if ( $show_current ) echo $before . get_the_time('Y') . $after;
// 			elseif ( $show_home_link && $show_last_sep ) echo $sep;
//
// 		} elseif ( is_month() ) {
// 			if ( $show_home_link ) echo $sep;
// 			$position += 1;
// 			echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position );
// 			if ( $show_current ) echo $sep . $before . get_the_time('F') . $after;
// 			elseif ( $show_last_sep ) echo $sep;
//
// 		} elseif ( is_day() ) {
// 			if ( $show_home_link ) echo $sep;
// 			$position += 1;
// 			echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position ) . $sep;
// 			$position += 1;
// 			echo sprintf( $link, get_month_link( get_the_time('Y'), get_the_time('m') ), get_the_time('F'), $position );
// 			if ( $show_current ) echo $sep . $before . get_the_time('d') . $after;
// 			elseif ( $show_last_sep ) echo $sep;
//
// 		} elseif ( is_single() && ! is_attachment() ) {
// 			if ( get_post_type() != 'post' ) {
// 				$position += 1;
// 				$post_type = get_post_type_object( get_post_type() );
// 				if ( $position > 1 ) echo $sep;
// 				echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->labels->name, $position );
// 				if ( $show_current ) echo $sep . $before . get_the_title() . $after;
// 				elseif ( $show_last_sep ) echo $sep;
// 			} else {
// 				$cat = get_the_category(); $catID = $cat[0]->cat_ID;
// 				$parents = get_ancestors( $catID, 'category' );
// 				$parents = array_reverse( $parents );
// 				$parents[] = $catID;
// 				foreach ( $parents as $cat ) {
// 					$position += 1;
// 					if ( $position > 1 ) echo $sep;
// 					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
// 				}
// 				if ( get_query_var( 'cpage' ) ) {
// 					$position += 1;
// 					echo $sep . sprintf( $link, get_permalink(), get_the_title(), $position );
// 					echo $sep . $before . sprintf( $text['cpage'], get_query_var( 'cpage' ) ) . $after;
// 				} else {
// 					if ( $show_current ) echo $sep . $before . get_the_title() . $after;
// 					elseif ( $show_last_sep ) echo $sep;
// 				}
// 			}
//
// 		} elseif ( is_post_type_archive() ) {
// 			$post_type = get_post_type_object( get_post_type() );
// 			if ( get_query_var( 'paged' ) ) {
// 				$position += 1;
// 				if ( $position > 1 ) echo $sep;
// 				echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->label, $position );
// 				echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
// 			} else {
// 				if ( $show_home_link && $show_current ) echo $sep;
// 				if ( $show_current ) echo $before . $post_type->label . $after;
// 				elseif ( $show_home_link && $show_last_sep ) echo $sep;
// 			}
//
// 		} elseif ( is_attachment() ) {
// 			$parent = get_post( $parent_id );
// 			$cat = get_the_category( $parent->ID ); $catID = $cat[0]->cat_ID;
// 			$parents = get_ancestors( $catID, 'category' );
// 			$parents = array_reverse( $parents );
// 			$parents[] = $catID;
// 			foreach ( $parents as $cat ) {
// 				$position += 1;
// 				if ( $position > 1 ) echo $sep;
// 				echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
// 			}
// 			$position += 1;
// 			echo $sep . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position );
// 			if ( $show_current ) echo $sep . $before . get_the_title() . $after;
// 			elseif ( $show_last_sep ) echo $sep;
//
// 		} elseif ( is_page() && ! $parent_id ) {
// 			if ( $show_home_link && $show_current ) echo $sep;
// 			if ( $show_current ) echo $before . get_the_title() . $after;
// 			elseif ( $show_home_link && $show_last_sep ) echo $sep;
//
// 		} elseif ( is_page() && $parent_id ) {
// 			$parents = get_post_ancestors( get_the_ID() );
// 			foreach ( array_reverse( $parents ) as $pageID ) {
// 				$position += 1;
// 				if ( $position > 1 ) echo $sep;
// 				echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position );
// 			}
// 			if ( $show_current ) echo $sep . $before . get_the_title() . $after;
// 			elseif ( $show_last_sep ) echo $sep;
//
// 		} elseif ( is_tag() ) {
// 			if ( get_query_var( 'paged' ) ) {
// 				$position += 1;
// 				$tagID = get_query_var( 'tag_id' );
// 				echo $sep . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position );
// 				echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
// 			} else {
// 				if ( $show_home_link && $show_current ) echo $sep;
// 				if ( $show_current ) echo $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;
// 				elseif ( $show_home_link && $show_last_sep ) echo $sep;
// 			}
//
// 		} elseif ( is_author() ) {
// 			$author = get_userdata( get_query_var( 'author' ) );
// 			if ( get_query_var( 'paged' ) ) {
// 				$position += 1;
// 				echo $sep . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text['author'], $author->display_name ), $position );
// 				echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
// 			} else {
// 				if ( $show_home_link && $show_current ) echo $sep;
// 				if ( $show_current ) echo $before . sprintf( $text['author'], $author->display_name ) . $after;
// 				elseif ( $show_home_link && $show_last_sep ) echo $sep;
// 			}
//
// 		} elseif ( is_404() ) {
// 			if ( $show_home_link && $show_current ) echo $sep;
// 			if ( $show_current ) echo $before . $text['404'] . $after;
// 			elseif ( $show_last_sep ) echo $sep;
//
// 		} elseif ( has_post_format() && ! is_singular() ) {
// 			if ( $show_home_link && $show_current ) echo $sep;
// 			echo get_post_format_string( get_post_format() );
// 		}
//
// 		echo $wrap_after;
//
// 	}
// }
