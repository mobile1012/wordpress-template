<?php

/**
 * @group block-supports
 *
 * @covers ::wp_render_elements_support
 */
class Tests_Block_Supports_WpRenderElementsSupport extends WP_UnitTestCase {
	/**
	 * @var string|null
	 */
	private $test_block_name;

	public function tear_down() {
		WP_Style_Engine_CSS_Rules_Store::remove_all_stores();
		unregister_block_type( $this->test_block_name );
		$this->test_block_name = null;
		parent::tear_down();
	}

	/**
	 * Tests that elements block support applies the correct classname.
	 *
	 * @ticket 59555
	 *
	 * @covers ::wp_render_elements_support
	 *
	 * @dataProvider data_elements_block_support_class
	 *
	 * @param array  $color_settings  The color block support settings used for elements support.
	 * @param array  $elements_styles The elements styles within the block attributes.
	 * @param string $block_markup    Original block markup.
	 * @param string $expected_markup Resulting markup after application of elements block support.
	 */
	public function test_elements_block_support_class( $color_settings, $elements_styles, $block_markup, $expected_markup ) {
		$this->test_block_name = 'test/element-block-supports';

		register_block_type(
			$this->test_block_name,
			array(
				'api_version' => 3,
				'attributes'  => array(
					'style' => array(
						'type' => 'object',
					),
				),
				'supports'    => array(
					'color' => $color_settings,
				),
			)
		);

		$block = array(
			'blockName' => $this->test_block_name,
			'attrs'     => array(
				'style' => array(
					'elements' => $elements_styles,
				),
			),
		);

		$actual = wp_render_elements_support( $block_markup, $block );

		$this->assertMatchesRegularExpression(
			$expected_markup,
			$actual,
			'Position block wrapper markup should be correct'
		);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_elements_block_support_class() {
		$color_styles = array(
			'text'       => 'var:preset|color|vivid-red',
			'background' => '#fff',
		);

		return array(
			'button element styles with serialization skipped' => array(
				'color_settings'  => array(
					'button'                          => true,
					'__experimentalSkipSerialization' => true,
				),
				'elements_styles' => array(
					'button' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p>Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'link element styles with serialization skipped' => array(
				'color_settings'  => array(
					'link'                            => true,
					'__experimentalSkipSerialization' => true,
				),
				'elements_styles' => array(
					'link' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p>Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'heading element styles with serialization skipped' => array(
				'color_settings'  => array(
					'heading'                         => true,
					'__experimentalSkipSerialization' => true,
				),
				'elements_styles' => array(
					'heading' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p>Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'button element styles apply class to wrapper' => array(
				'color_settings'  => array( 'button' => true ),
				'elements_styles' => array(
					'button' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p class="wp-elements-[a-f0-9]{32}">Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'link element styles apply class to wrapper'   => array(
				'color_settings'  => array( 'link' => true ),
				'elements_styles' => array(
					'link' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p class="wp-elements-[a-f0-9]{32}">Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'heading element styles apply class to wrapper' => array(
				'color_settings'  => array( 'heading' => true ),
				'elements_styles' => array(
					'heading' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p>Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p class="wp-elements-[a-f0-9]{32}">Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'element styles apply class to wrapper when it has other classes' => array(
				'color_settings'  => array( 'link' => true ),
				'elements_styles' => array(
					'link' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p class="has-dark-gray-background-color has-background">Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p class="has-dark-gray-background-color has-background wp-elements-[a-f0-9]{32}">Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
			'element styles apply class to wrapper when it has other attributes' => array(
				'color_settings'  => array( 'link' => true ),
				'elements_styles' => array(
					'link' => array( 'color' => $color_styles ),
				),
				'block_markup'    => '<p id="anchor">Hello <a href="http://www.wordpress.org/">WordPress</a>!</p>',
				'expected_markup' => '/^<p class="wp-elements-[a-f0-9]{32}" id="anchor">Hello <a href="http:\/\/www.wordpress.org\/">WordPress<\/a>!<\/p>$/',
			),
		);
	}
}
