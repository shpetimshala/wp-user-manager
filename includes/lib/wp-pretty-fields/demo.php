<?php
/**
 * Demo Metabox for Pretty_Fields
 */

if (is_admin()) {

  	/* 
	 * configure your meta box
	 */
	$config = array(
		'id'    => 'demo_meta_box',
		'title' => 'Demo Fields',
		'pages' => array('page'),
		'fields' => array(
			array(
				'id'   => 'text',
				'name' => __( 'Text Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'text'
			),
			array(
				'id'   => 'textarea',
				'name' => __( 'Textarea Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'textarea'
			),
			array(
				'id'   => 'url',
				'name' => __( 'URL Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'url'
			),
			array(
				'id'   => 'number',
				'name' => __( 'Number Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'number'
			),
			array(
				'id'   => 'email',
				'name' => __( 'Email Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'email'
			),
			array(
				'id'   => 'button',
				'name' => __( 'Button Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'url' => 'http://google.com',
				'type' => 'button'
			),
			array(
				'id'   => 'color',
				'name' => __( 'Color Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'color'
			),
			array(
				'id'   => 'image',
				'name' => __( 'Image Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'image'
			),
			array(
				'id'   => 'select',
				'name' => __( 'Select Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'select',
				'options' => array('value' => 'label', 'value1' => 'Another label')
			),
			array(
				'id'   => 'radio',
				'name' => __( 'Radio Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'radio',
				'options' => array('value' => 'label', 'value1' => 'Another label')
			),
			array(
				'id'   => 'multiselect',
				'name' => __( 'Multiselect Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'multiselect',
				'options' => array('value' => 'label', 'value1' => 'Another label')
			),
			array(
				'id'   => 'checkbox_list',
				'name' => __( 'Checkbox list Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'checkbox_list',
				'options' => array('value' => 'label', 'value1' => 'Another label')
			),
			array(
				'id'   => 'checkbox',
				'name' => __( 'Checkbox Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'checkbox'
			),
			array(
				'id'   => 'editor',
				'name' => __( 'Editor Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'editor'
			),
			array(
				'id'   => 'gallery',
				'name' => __( 'Gallery Field' ),
				'sub' => __( 'Description goes here' ),
				'desc' => __( 'Field Description goes here' ),
				'type' => 'gallery'
			),
		),
	);

	/*
	 * Initiate your meta box
	 */
  	$demo_meta_box =  new Pretty_Metabox($config);

}