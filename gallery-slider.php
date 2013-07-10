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

function replace_letters($content) {
	return $content;
}

add_filter('the_content', 'replace_letters');

//[foobar]
function foobar_func( $atts ){
	$widget_content = "<div style='color: red;'>mijn tekst</div>";
	return $widget_content;
	// return plugins_url();
	}

add_shortcode( 'foobar', 'foobar_func' );

function get_all_images() {
	$args = array (
    'post_type' => 'attachment',
    'post_status' => 'published',
    'posts_per_page' => 25,
    'numberposts' => null,
	);
	
	$attachments = get_posts($args);
	
	$post_count = count($attachments);
	
	$widget_content = '<div class="galleria">';
	if ($attachments) {
	    foreach ($attachments as $attachment) {
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

function get_tagged_images($atts) {
	
	extract(shortcode_atts(
		array(
			'tag' => 'emtpy_tag',
		), $atts
	));
	global $wpdb;
	
	$query ="SELECT * FROM wp_posts, wp_term_relationships, wp_terms where post_type = 'attachment' AND ID = object_id AND term_taxonomy_id = term_id AND name = '" . $tag . "'";
	$results = $wpdb->get_results($query);
	
	$widget_content = '<div class="galleria">';
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

?>