<?php
function wpb_postsbycategory() {
// the query
$the_query = new WP_Query( array( 
    'category_name' => 'Media & News', 
	'order' => 'DESC',
    'posts_per_page' => -1,
) ); 
   
// The Loop
if ( $the_query->have_posts() ) {
    $string .= '<ul class="postsbycategory widget_recent_entries">';
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
            // if no featured image is found
            $string .= '<li><a href="' . get_the_permalink() .'" rel="bookmark">' . the_title() .'</a></li>';
            $string .= '<li>' . the_content() .'</li>';
            }
    } else {
    // no posts found
 $string .= '<li>No Posts Found</li>';
}
$string .= '</ul>';
   
return $string;
   
/* Restore original Post Data */
//wp_reset_postdata();
	wp_die();
}


// Add a shortcode
add_shortcode('categoryposts', 'wpb_postsbycategory');

function wp_custom_archive($args = '') {
    global $wpdb, $wp_locale;
	
$catObj = get_category_by_slug('media-news'); 
$catName = $catObj->term_id;
	
$output = '';

$year_prev = null;
$months = $wpdb->get_results(	"SELECT DISTINCT MONTH( post_date ) AS month ,
								YEAR( post_date ) AS year,
								COUNT( id ) as post_count FROM $wpdb->posts
								WHERE post_status = 'publish' and post_date <= now( )
								and post_type = 'post'
								GROUP BY month , year								ORDER BY post_date DESC");
foreach($months as $month) : 
	$year_current = $month->year;
	if ($year_current != $year_prev){
		if ($year_prev != null){
			$output .= '</ul>';
		}
        $output .= '<h4 class="accordion">' . $month->year. '</h4>';
        $output .= '<ul class="archive-list panel">';
		}
        $output .= '<li>';
		 $output .= '<a class="arc_click" href="#" data-cat='.$catName.'  data-year='.$month->year.' data-month_nub = '.$month->month.' data-month_str= '.date("F", mktime(0, 0, 0, $month->month, 1, $month->year)).'>';
         $output .= '<span class="archive-month" style="color:#000">' . date("F", mktime(0, 0, 0, $month->month, 1, $month->year)) .'(' .$month->post_count .')' . '</span>';
		$output .= '</a>';
        $output .= '</li>';
		
	$year_prev = $year_current;
 endforeach; 
	$output .= '</ul>';
return $output;
}

/* Add a shortcode */
add_shortcode('archive_data', 'wp_custom_archive');

/* arc filter call */
add_action( 'wp_ajax_tab_filter', 'tab_filter' );
add_action( 'wp_ajax_nopriv_tab_filter', 'tab_filter' );
function tab_filter()
{
	$cat_id = $_POST['cat_id'];
	$year = $_POST['year'];
	$month_nub = $_POST['month_nub'];
	$mmonth_str = $_POST['month_str'];

	// the query
	$the_query = new WP_Query( array( 
		'post_type' => 'post',
		'cat' => $cat_id,
		'order' => 'DESC',
		'posts_per_page' => -1 ,
		'monthnum' => $month_nub,
		'year' => $year
	) ); 
  // The Loop
	if ( $the_query->have_posts() ) {
		$string .= '<ul class="postsbycategory widget_recent_entries">';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			// if no featured image is found
			$string .= '<li><a href="' . get_the_permalink() .'" rel="bookmark">' . the_title() .'</a></li>';
			$string .= '<li>' . the_content() .'</li>';
		}
	} else {
		// no posts found
		$string .= '<li>No Posts Found</li>';
	}
	$string .= '</ul>';

	echo $string;
/* Restore original Post Data */
wp_reset_postdata();
	wp_die();
}

/*Get RSS blof from read.rehsmandi */

function wpb_getrss() {
$posts = json_decode(file_get_contents('https://read.reshamandi.com/wp-json/wp/v2/posts?per_page=4&filter[limit]=4&filter[orderby]=date'));
	$rss .= '<div class="rss_cards">';
foreach ( $posts as $post ) {
	$rss .= '<article class="rss_recent_blog_article">';
	$rss .= '<div class="entry_img"><img src="'.$post->yoast_head_json->og_image[0]->url.'" class="rss_recent_blog_img"></img></div>';
	$rss .= '<div class="entry_wrapper">';
    $rss .= '<a href="'.$post->link.'">'.$post->title->rendered.'</a><br>';
	$rss .= '</div>';
	$rss .= '<div class="btn_rss_div">';
	$rss .= '<a href="'.$post->link.'" class="rss_readmore">Read More</a><br>';
	$rss .= '</div>';
	$rss .= '</article>';
}
$rss .= '</div>';
	return $rss;
}

// Add a shortcode
add_shortcode('getrss', 'wpb_getrss');
