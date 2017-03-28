<?php

class Jetpack_JSON_API_Get_Term_Taxonomy_Backup_Endpoint extends Jetpack_JSON_API_Endpoint {
	// /sites/%s/term-taxonomies/backup      -> $blog_id

	protected $needed_capabilities = array(); // This endpoint is only accessible using a site token
	protected $taxonomy_name;

	function validate_input( $object ) {
		$query_args = $this->query_args();		

		if ( empty( $query_args['name'] ) ) {
			return new WP_Error( 'taxonomy_name_not_specified', __( 'You must specify a taxonomy name', 'jetpack' ), 400 );
		}

		$this->taxonomy_name = $query_args['name'];

		return true;
	}

	protected function result() {
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		if ( empty( $taxonomy ) ) {
			return new WP_Error( 'taxonomy_not_found', __( 'Taxonomy not found', 'jetpack' ), 404 );
		}

		return array( 'taxonomy' => $taxonomy );
	}

}
