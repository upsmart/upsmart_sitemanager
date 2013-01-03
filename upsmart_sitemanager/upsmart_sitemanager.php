<?php
/**
 * @package UpSmart_SiteManager
 */
/*
Plugin Name: UpSmart Site Manager
Plugin URI: http://upsmart.com
Description: 
Author: T.J. Lipscomb/Aaron Tobias
Version: 0.1
Author URI: http://tjl.co/
*/

if(!function_exists('ellistr')) {
 //Copyright 2006 T.J. Lipscomb
function ellistr($s,$n) {
	for ( $x = 0; $x < strlen($s); $x++ ) {
		$o = ($n+$x >= strlen($s) ? $s : ($s{$n+$x} == " " ? substr($s,0,$n+$x) . "..." : ""));
		if ( $o != "" ) { return $o; }
	}
}
}

//Bind to various actions needed to hijack the request.
add_action( 'parse_request', 'upsmart_parse_request' );
add_action( 'the_posts', 'upsmart_the_posts' );
add_action( 'wp_enqueue_scripts', 'upsmart_enqueue_scripts' );

require_once dirname(__FILE__).'/create.php';
require_once dirname(__FILE__).'/profiles.php';
require_once dirname(__FILE__).'/templates.php';

//Fix post formatting by removing wpautop
remove_filter('the_content','wpautop');
remove_filter('the_content','wptexturize');
add_filter('the_content','upsmart_filter_content');

function upsmart_filter_content($content){
	if(get_post_type()=='upsmart')
		return $content;//no autop/texturize
	else
		return wptexturize(wpautop($content));
}

function upsmart_enqueue_scripts() {
	if(defined('upsmart_handle_page')) {
		wp_register_style('upsmart_page_'.upsmart_handle_page,plugins_url('css/'.upsmart_handle_page.'.css',__FILE__));
		wp_enqueue_style('upsmart_page_'.upsmart_handle_page);
	}
}

function upsmart_parse_request( &$wp ) {
	//If this plugin has a function to handle the currently-requested page, then
	//take over processing from wordpress.
	if (isset($wp->query_vars['pagename']) && preg_match("#^[a-z]+$#i",$wp->query_vars['pagename']) && function_exists('upsmart_page_'.$wp->query_vars['pagename'])) {
		define('upsmart_handle_page',$wp->query_vars['pagename']);
		define('upsmart_handle_page_info',$wp->query_vars['page']);
	}
	
	return false;
}

function upsmart_the_posts($posts) {
	//If we determined earlier that we're taking over the page, here is where that
	//happens.
	if(!defined('upsmart_handle_page')) return $posts;
	
	//Disable canonical redirects for this one page load.
	remove_filter('template_redirect', 'redirect_canonical');
	
	//Create a dummy post
	$post = new stdClass();
	$post->ID=-9999;
	$post->post_type = "upsmart";
	$post->comment_status = 'closed';
	$post->ping_status = 'closed';
	$post->comment_count = -1;
	$post->post_date = 0;
	$post->post_status = 'published';
	$post->post_author = 0;
	$post->post_parent = 0;
	
	//Call the function that will actually handle the request.
	$function = 'upsmart_page_'.upsmart_handle_page;
	if(function_exists($function)) $post = $function($post);
	else $post->post_content = "404";
	
	$post->post_name = $post->post_title;
	
	return array(0 => $post);
}