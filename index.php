<?php
/*
 * Plugin Name:       CPT: Reviews
 * Plugin URI:        https://red-eagle-berlin.de/
 * Description:       Custom Post-Type: Reviews
 * Version:           1.2.0
 * Author:            Marvin Borisch
 * Author URI:        https://marvinpoo.dev/
 * Text Domain:       cpt-review
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Copyright since 4th December 2018
 */

 /* Copyright 2019  Marvin Borisch für RED Eagle

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

 function reviews() {

	$labels = array(
		'name'                  => _x( 'Bewertungen', 'Post Type General Name', 'cpt-review' ),
		'singular_name'         => _x( 'Bewertung', 'Post Type Singular Name', 'cpt-review' ),
		'menu_name'             => __( 'Bewertungen', 'cpt-review' ),
		'name_admin_bar'        => __( 'Bewertungen', 'cpt-review' ),
		'archives'              => __( 'Bewertungs Archiv', 'cpt-review' ),
		'attributes'            => __( 'Attribute', 'cpt-review' ),
		'parent_item_colon'     => __( 'Parent Item:', 'cpt-review' ),
		'all_items'             => __( 'Alle Bewertungen', 'cpt-review' ),
		'add_new_item'          => __( 'Neue Bewertung', 'cpt-review' ),
		'add_new'               => __( 'Neue Bewertung', 'cpt-review' ),
		'new_item'              => __( 'Neue Bewertung', 'cpt-review' ),
		'edit_item'             => __( 'Bewertung bearbeiten', 'cpt-review' ),
		'update_item'           => __( 'Bewertung aktualisieren', 'cpt-review' ),
		'view_item'             => __( 'Bewertung ansehen', 'cpt-review' ),
		'view_items'            => __( 'Bewertungen ansehen', 'cpt-review' ),
		'search_items'          => __( 'Bewertung suche', 'cpt-review' ),
		'not_found'             => __( 'Nicht gefunden', 'cpt-review' ),
		'not_found_in_trash'    => __( 'Nichts im Papierkorb gefunden', 'cpt-review' ),
		'featured_image'        => __( 'Anschaubild', 'cpt-review' ),
		'set_featured_image'    => __( 'Anschaubild setzen', 'cpt-review' ),
		'remove_featured_image' => __( 'Anschaubild entfernen', 'cpt-review' ),
		'use_featured_image'    => __( 'Als Anschaubild nutzen', 'cpt-review' ),
		'insert_into_item'      => __( 'In Bewertung einführen', 'cpt-review' ),
		'uploaded_to_this_item' => __( 'An Bewertung anhängen', 'cpt-review' ),
		'items_list'            => __( 'Bewertungslistee', 'cpt-review' ),
		'items_list_navigation' => __( 'Bewertungsnavigation', 'cpt-review' ),
		'filter_items_list'     => __( 'Bewertungen filtern', 'cpt-review' ),
	);
	$args = array(
		'label'                 => __( 'Bewertung', 'cpt-review' ),
		'description'           => __( 'Die besten Bewertungen', 'cpt-review' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-star-half',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'review', $args );

}
add_action( 'init', 'reviews', 0 );

function bewerter_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function bewerter_add_meta_box() {
	add_meta_box(
		'bewerter-bewerter',
		__( 'Bewerter', 'bewerter' ),
		'bewerter_html',
		'review',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'bewerter_add_meta_box' );

function bewerter_html( $post) {
	wp_nonce_field( '_bewerter_nonce', 'bewerter_nonce' ); ?>

	<p>Hier den Namen der bewertenden Person eintragen.</p>

	<p>
		<label for="bewerter_name"><?php _e( 'Name', 'bewerter' ); ?></label><br>
		<input type="text" name="bewerter_name" id="bewerter_name" value="<?php echo bewerter_get_meta( 'bewerter_name' ); ?>">
	</p><?php
}

function bewerter_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['bewerter_nonce'] ) || ! wp_verify_nonce( $_POST['bewerter_nonce'], '_bewerter_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['bewerter_name'] ) )
		update_post_meta( $post_id, 'bewerter_name', esc_attr( $_POST['bewerter_name'] ) );
}
add_action( 'save_post', 'bewerter_save' );

/*
	Usage: bewerter_get_meta( 'bewerter_name' )
*/

?>

<?php function reviewslide_function() {
	$output .= '<!-- Review Injection v1.2.0 --><div id="rev">';
	$args = array(
		'post_type' => 'review',
		'posts_per_page' => 5,
		'order' => 'DESC',
	);
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
	$output .= '<div id="reviews" class="p-full review_indicator" style="background-image: url('.$image[0].')" >
		<div class="section_head">
			<span class="eyebrow">Sie über uns</span>
			<h2>Kundenstimmen</h2>
		</div>
		<div class="stars">
			<span class="material-icons">grade</span>
			<span class="material-icons">grade</span>
			<span class="material-icons">grade</span>
			<span class="material-icons">grade</span>
			<span class="material-icons">grade</span>
		</div>
		<div id="reviewbox">
		<div class="review">'.
		get_the_content().
		'<span class="review_author">'. 
		bewerter_get_meta( "bewerter_name" ).
		'</span></div></div></div>';
	endwhile;
	wp_reset_query();
	$output .= '
	</div>
		<script>jQuery("#rev > div:gt(0)").hide();

		function sliderheight(){
			var paddingResolution = window.getComputedStyle(document.getElementById("reviews"), null);
			var padT = paddingResolution.getPropertyValue("padding-top");
			var padB = paddingResolution.getPropertyValue("padding-bottom");
			var padnum = parseInt(padT, 10) + parseInt(padB, 10);
			divHeight = jQuery(".review_indicator").height();
			jQuery("#rev").css({"height" : divHeight + padnum});
		}
		sliderheight();
		
			setInterval(function() {
				jQuery("#rev > div:first")
				.fadeOut(1800)
				.next()
				.fadeIn(2000)
				.end()
				.appendTo("#rev");
			},  12000);
		</script>
		<script>
		jQuery(".review_indicator").each(function() {
			var $divs = jQuery(this).add(jQuery(this).prev(".review_indicator"));
			var tallestHeight = $divs.map(function(i, el) {
				return jQuery(el).height();
			}).get();
			$divs.height(Math.max.apply(this, tallestHeight));
		});
		</script>
		<div class="reviewbtn btn btn_blue arrow_r nosmooth">
			<a href="https://www.google.com/search?q=feuchteklinik&oq=feuchteklinik&aqs=chrome..69i57j69i60l2.2708j0j4&sourceid=chrome&ie=UTF-8#lrd=0x47a8486a43085f5d:0x7b49e6ae37a0d5a0,1,,," target="_blank"><span>Alle Bewertungen</span></a>
		</div>
	<!-- Review Exit -->';
	return $output;
}
add_shortcode('reviews', 'reviewslide_function'); ?>