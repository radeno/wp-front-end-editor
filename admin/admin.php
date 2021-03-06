<?php

class FEE_Admin extends scbBoxesPage {

	function setup() {
		$this->textdomain = 'front-end-editor';

		$this->args = array(
			'page_title' => __( 'Front-end Editor', $this->textdomain ),
			'page_slug' => 'front-end-editor'
		);

		$this->boxes = array(
			array( 'fields', __( 'Fields', $this->textdomain ), 'normal' ),
			array( 'settings', __( 'Settings', $this->textdomain ), 'side' ),
		);
	}

	function page_head() {
		wp_enqueue_style( 'fee-admin', $this->plugin_url . "admin/admin.css", array(), '2.0-alpha' );
	}

	protected function checklist_wrap( $title, $tbody ) {
		$thead =
		html( 'tr',
			 html( 'th scope="col" class="check-column"', '<input type="checkbox" />' )
			.html( 'th scope="col"', $title )
		);

		$table =
		html( 'table class="checklist widefat"',
			 html( 'thead', $thead )
			.html( 'tbody', $tbody )
		);

		return $table;
	}


	function fields_handler() {
		if ( !isset( $_POST['manage_fields'] ) )
			return;

		$disabled = array();
		foreach ( array_keys( FEE_Core::get_fields() ) as $field )
			if ( !isset( $_POST[$field] ) )
				$disabled[] = $field;

		$this->options->disabled = $disabled;

		$this->admin_msg();
	}

	function fields_box() {
		// Separate fields
		$post_fields = $other_fields = array();
		foreach ( FEE_Core::get_fields() as $field => $args )
			if ( 'post' == call_user_func( array( $args['class'], 'get_object_type' ) ) )
				$post_fields[$field] = $args;
			else
				$other_fields[$field] = $args;

		echo html( 'p', __( 'Enable or disable editable fields:', $this->textdomain ) );

		$tables  = $this->fields_table( __( 'Post fields', $this->textdomain ), $post_fields );
		$tables .= $this->fields_table( __( 'Other fields', $this->textdomain ), $other_fields );

		echo $this->form_wrap( $tables, '', 'manage_fields' );
	}

	private function fields_table( $title, $fields ) {
		$tbody = '';
		foreach ( $fields as $field => $args ) {
			if ( empty( $args['title'] ) )
				continue;

			$tbody .=
			html( 'tr',
				html( 'th scope="row" class="check-column"',
					$this->input( array(
						'type' => 'checkbox',
						'name' => $field,
						'checked' => !in_array( $field, (array) $this->options->disabled )
					) )
				)
				.html( 'td', $args['title'] )
			);
		}

		return $this->checklist_wrap( $title, $tbody );
	}

	function settings_handler() {
		if ( !isset( $_POST['save_settings'] ) )
			return;

		$this->options->rich = isset( $_POST['rich'] );

		if ( isset( $_POST['taxonomy_ui'] ) && in_array( $_POST['taxonomy_ui'], array( 'termselect', 'terminput' ) ) )
			$this->options->taxonomy_ui = $_POST['taxonomy_ui'];

		$this->admin_msg();
	}

	function settings_box() {

		$out = html( 'p', $this->input( array(
			'name' => 'rich',
			'type' => 'checkbox',
			'desc' => __( 'Enable the WYSIWYG editor.', $this->textdomain ),
		) ) );

		$out .= html( 'p',
			__( 'To edit categories, use a:', $this->textdomain ),
			' ',
			$this->input( array(
				'name' => 'taxonomy_ui',
				'type' => 'radio',
				'values' => array(
					'termselect',
					'terminput',
				),
				'desc' => array(
					__( 'dropdown', $this->textdomain ),
					__( 'text field', $this->textdomain ),
				)
			) )
		);

		echo $this->form_wrap( $out, '', 'save_settings' );
	}
}

