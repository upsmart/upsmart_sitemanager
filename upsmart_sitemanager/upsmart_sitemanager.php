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

//Bind to various actions needed to hijack the request.
add_action( 'parse_request', 'upsmart_parse_request' );
add_action( 'the_posts', 'upsmart_the_posts' );

require_once dirname(__FILE__).'/create.php';

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
	
	//Create a dummy post
	$post = new stdClass();
	$post->ID = -9999;
	
	//Call the function that will actually handle the request.
	$function = 'upsmart_page_'.upsmart_handle_page;
	if(function_exists($function)) $post = $function($post);
	else $post->post_content = "404";
	
	return array(0 => $post);
}