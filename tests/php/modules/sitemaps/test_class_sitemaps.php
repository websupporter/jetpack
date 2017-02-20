<?php

require dirname( __FILE__ ) . '/../../../../modules/sitemaps.php';

class WP_Test_Jetpack_Sitemaps extends WP_UnitTestCase {
	protected static $attachment_id;

	public function tearDown() {
		parent::tearDown();

		wp_reset_postdata();
	}

	public static function wpSetUpBeforeClass( $factory ) {
		$post_id             = $factory->post->create();
		$file                = DIR_TESTDATA . '/images/canola.jpg';
		self::$attachment_id = $factory->attachment->create_upload_object(
			$file, $post_id, array(
				'post_mime_type' => 'image/jpeg',
			)
		);
	}

	/**
	 * Verify that counted images in a post with a single image in content is correct.
	 *
	 * @since  4.7.0
	 */
	public function test_content() {
		$post_id = $this->factory->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Post title',
				'post_content' => 'Post content with an image.
				<img src="https://jetpack.com/image.jpg" />',
			)
		);

		// Set as current post. Necessary for Jetpack_PostImages::get_images
		setup_postdata( $post_id );
		the_post();

		$images = jetpack_sitemap_get_images( $post_id );
		$this->assertEquals( 1, count( $images ) );
	}

	/**
	 * Verify that counted images in a post with an image in content and another in featured image is correct.
	 *
	 * @since  4.7.0
	 */
	public function test_featured_content() {
		// Get the same image that is set as featured image
		$image = wp_get_attachment_image_src( self::$attachment_id, 'full' );

		$post_id = $this->factory->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Post title',
				'post_content' => 'Post content with an image in content and a featured image.'
					. "<img src='{ $image[0] }' />",
			)
		);
		set_post_thumbnail( $post_id, self::$attachment_id );

		// Set as current post. Necessary for Jetpack_PostImages::get_images
		setup_postdata( $post_id );
		the_post();

		$images = jetpack_sitemap_get_images( $post_id );
		$this->assertEquals( 2, count( $images ) );
	}

	/**
	 * Verify that counted images in post content, featured image and gallery are correct.
	 *
	 * @since  4.7.0
	 */
	public function test_featured_content_gallery() {
		// Get the same image that is set as featured image
		$image = wp_get_attachment_image_src( self::$attachment_id, 'full' );

		$post_id = $this->factory->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Post title',
				'post_content' => 'Post content with an image in content, a featured image and one in the gallery.'
					. "<img src='{ $image[0] }' />"
					. "\n"
					. '[gallery ids="' . self::$attachment_id . '"]',
			)
		);

		set_post_thumbnail( $post_id, self::$attachment_id );

		// Set as current post. Necessary for Jetpack_PostImages::get_images
		setup_postdata( $post_id );
		the_post();

		$images = jetpack_sitemap_get_images( $post_id );
		$this->assertEquals( 2, count( $images ) );
	}
}