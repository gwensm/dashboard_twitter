<?php


if(isset($_POST['key'])){
	$wpdb->insert( 
	'wp_test', 
	array( 
		'matable_description' => $_POST['key']
	)
);
}

