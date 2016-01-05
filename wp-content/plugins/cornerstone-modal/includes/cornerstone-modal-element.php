<?php

class Cornerstone_Modal extends Cornerstone_Element_Base {

	public function data() {
		return array(
			'name'        => 'cornerstone-modal',
			'title'       => __( 'Modal', csl18n() ),
			'section'     => 'content',
			'description' => __( 'Adds a modal box to your content.', csl18n() ),
			'supports'    => array( 'id', 'class', 'style' ),
		);
	}

	public function controls() {

		$this->addControl(
			'modalcontent',
			'editor',
			__( 'Modal Content', csl18n() ),
			__( 'Content that will display in modal popup.', csl18n() ),
			''
		);

		$this->addControl(
			'display_on',
			'select',
			__( 'Display Modal On', csl18n() ),
			__( 'When should the popup be initiated.', csl18n() ),
			'button',
			array(
				'choices' => array(
					array( 'value' => 'button',  'label' => __( 'Button', csl18n() ) ),
					array( 'value' => 'element', 'label' => __( 'Element Click', csl18n() ) ),
					array( 'value' => 'load', 'label' => __( 'Page Load', csl18n() ) )
				)
			)
		);

		// Element

		$this->addControl(
			'identifier',
			'text',
			__( 'Element Identifier', csl18n() ),
			__( 'Enter the CSS class or ID of the page element that will trigger the modal.', csl18n() ),
			'',
			array(
				'condition' => array(
					'display_on' => 'element'
				)
			)
		);

		// Button

		$this->addControl(
			'button_size',
			'select',
			__( 'Button Size', csl18n() ),
			__( 'How big of a button would you like?', csl18n() ),
			'default',
			array(
				'choices' => array(
					array( 'value' => 'default',  'label' => __( 'Theme Default', csl18n() ) ),
					array( 'value' => 'x-btn-small', 'label' => __( 'Small', csl18n() ) ),
					array( 'value' => 'x-btn-medium', 'label' => __( 'Medium', csl18n() ) ),
					array( 'value' => 'x-btn-large', 'label' => __( 'Large', csl18n() ) ),
				),
				'condition' => array(
					'display_on' => 'button'
				)
			)
		);

		$this->addControl(
			'button_text',
			'text',
			__( 'Button Text', csl18n() ),
			__( 'Provide a title for this button.', csl18n() ),
			'Click Me',
			array(
				'condition' => array(
					'display_on' => 'button'
				)
			)
		);

		// Page load

	    $this->addControl(
	      'delay',
	      'number',
	      __( 'Delay', csl18n() ),
	      __( 'Time delay before modal popup on page load (in seconds).', csl18n() ),
	      '2',
			array(
				'condition' => array(
					'display_on' => 'load'
				)
			)
	    );

	}

	public function render( $atts ) {

		extract( $atts );

		$shortcode = "[cornerstone_modal display_on='{$display_on}' btn_size='{$button_size}' btn_txt='{$button_text}' identifier='{$identifier}' delay='{$delay}' {$extra}]{$modalcontent}[/cornerstone_modal]";

		return $shortcode;

	}

}