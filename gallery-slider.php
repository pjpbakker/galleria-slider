<?php
/*
Plugin Name: Galleria Slider
Plugin URI: http://www.vierdeweg.nl/galleria-slider
Description: Displays a gallery of images from Wordpress' Media library
Version: 1.0.0
Author: Paul Bakker
Author URI: http://www.vierdeweg.nl
License: GPL2

Copyright 2013  Paul Bakker

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

function get_tagged_images($attrs) {
	
	extract(shortcode_atts(
		array(
			'tag' => 'emtpy_tag',
		), $attrs
	));
	global $wpdb;
	
	$query ="SELECT * FROM wp_posts, wp_term_relationships, wp_terms where post_type = 'attachment' AND ID = object_id AND term_taxonomy_id = term_id AND name = '" . $tag . "'";
	$results = $wpdb->get_results($query);
	
	$widget_content = '<div id="galleria">';
	if ($results) {
	    foreach ($results as $attachment) {
	    	setup_postdata($attachment);
		    $widget_content .= "<div class=\"post photo col3\">";
        $url = get_attachment_link($attachment->ID);      
        $img = wp_get_attachment_url($attachment->ID);
        $title = get_the_title($attachment->post_parent);//extraigo titulo
        $widget_content .= '<a href="'.$url.'"><img title="'.$title.'" src="'.$img.'"></a>';
        $widget_content .= "</div>";
	    }   
	}
  $widget_content .= "</div>";
	return $widget_content;
}

add_shortcode( 'galleria', 'get_tagged_images' );

function load_galleria_scripts() {
	wp_enqueue_script(
		'galleria-1.2.9.min',
		plugins_url( '/js/galleria/galleria-1.2.9.min.js' , __FILE__ ),
		array( 'jquery' )
	);
}

add_action( 'wp_enqueue_scripts', 'load_galleria_scripts' );

?>