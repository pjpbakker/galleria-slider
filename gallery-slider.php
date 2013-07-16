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
			'interval' => 5000,
			'sortby' => 'id',
			'show_info' => 'true',
			'show_desc' => 'true',
			'theme' => 'classic',
		), $attrs
	));
	global $wpdb;
	
	$orderby_clause = " order by p.ID";
  if ($sortby == "name") {
		$orderby_clause = " order by p.post_name";
  }
	$query ="SELECT * FROM wp_posts as p, wp_term_relationships as tr, wp_terms as t where p.post_type = 'attachment' AND p.ID = tr.object_id AND tr.term_taxonomy_id = t.term_id AND t.name = '" . $tag . "'" . $orderby_clause;
	$results = $wpdb->get_results($query);

	$widget_content = '<div class="galleria">';
	if ($results) {
	    foreach ($results as $attachment) {
	    	setup_postdata($attachment);
        $url = get_attachment_link($attachment->ID);      
        $img = wp_get_attachment_url($attachment->ID);
      	$default_attr = array(
					'alt'   => trim(strip_tags( get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) )),
					'title' => $attachment->post_excerpt,
				);
        
        $widget_content .= '<a href="'.$img.'">'.wp_get_attachment_image($attachment->ID, 'thumbnail', false, $default_attr).'</a>';
	    }   
	}
  $widget_content .= "</div>\n";
	$galleria_data = array(
		"interval" => $interval, 
		"show_info" => $show_info,
		"show_desc" => $show_desc,
		"theme" => $theme,
	);
	
	$widget_content .= add_galleria_initializer($galleria_data);
	return $widget_content;
}

add_shortcode( 'galleria', 'get_tagged_images' );

function 	log_message($message) {
	error_log(print_r($message, true));
}

function load_galleria_scripts() {
	wp_register_style("play_style", plugins_url( '/css/style.css' , __FILE__ ), null, "1.0.3");
	wp_enqueue_style("play_style");
	
	wp_enqueue_script(
		'galleria-1.2.9.min',
		plugins_url( '/js/galleria/galleria-1.2.9.min.js' , __FILE__ ),
		array( 'jquery' )
	);
}

add_action( 'wp_enqueue_scripts', 'load_galleria_scripts' );

function add_galleria_initializer($data) {
	$theme = $data['theme'];
	$theme_url = plugins_url( "/css/galleria/themes/$theme/galleria.$theme.min.js" , __FILE__ );
	$interval = $data['interval'];
	$show_info = $data['show_info'];
	$description_activate = '';
	if (parseBool($data['show_desc'])) {
		$description_activate = '$(".galleria-info-link").click();';
	}
	$escaped_theme_url = str_replace(" ", "%20", $theme_url);
	$galleria_js = <<<EOD
	<script>
		(function($) {    Galleria.loadTheme('$escaped_theme_url');
	    Galleria.run('.galleria', {
	       autoplay: $interval,
	       showInfo: $show_info,
	       height: 500,
	       transition: "fade",
	       transitionSpeed: 400,
	       lightbox: true,
	       extend: function() {
	            var gallery = this;
	            $('#togglePlay').click(function() {
	                gallery.playToggle();
	            });
	       },
	    });
	    Galleria.ready(function() {
	    	this.bind("play", function(e) {
	    		$("#togglePlay").attr("class", "playing");
	    	});
	    	this.bind("pause", function(e) {
	    		$("#togglePlay").attr("class", "pausing");
	    	});
	    	$(".galleria-info").after('<div id="togglePlay">&nbsp;</div>');
	    	$("#togglePlay").attr("class", "playing");
	    	$description_activate
	    });
		})(jQuery);
</script>
EOD;
	return $galleria_js;
}

function parseBool($value) {
   if ($value && strtolower($value) !== "false") {
      return true;
   } else {
      return false;
   }
}

?>