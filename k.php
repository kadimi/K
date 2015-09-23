<?php
/**
 * The k framework
 * @author Nabil Kadimi <nabil@kadimi.com>
 * @version 1.0.5
 * @package K
 */

if ( ! class_exists( 'K' ) ) {

	/**
	 * The K class for HTML generation.
	 */
	class K {

		/**
		 * Returns a variable or an associative array value.
		 *
		 * The function search the global scope if no array (`$array`) is given.
		 *
		 * @param  string $name    The name of the variable or the associative array key.
		 * @param  array  $array   [optional] If this parameter is provided, the function will look for `$array[$name]`.
		 * @param  string $default The default value to use if this variable has never been set.
		 * @return mixed           The variable or associative array value.
		 */
		public static function get_var( $name, $array = null, $default = null ) {

			if ( is_null( $array ) ) {
				$array = $GLOBALS;
			}
			if ( is_array( $array ) && array_key_exists( $name, $array ) ) {
				return $array[ $name ];
			} else {
				return $default;
			}
		}

		/**
		 * Returns or prints the code for an input element.
		 *
		 * Here are a few examples.
		 * 
		 * ### Basic Example
		 * 
		 * This example demonstrates how to print a text input field whose name attribute value is "my_txt".
		 * 
		 * <code>
		 * K::input('my_txt'); // Outputs <input type="text" name="my_txt"/>
		 * </code>
         *
		 * ### Example with `$params` & `$args` Attributes
		 *
		 * This example demonstrates how to generate (without printing) the code for a text input field with an id attribute.
		 *
		 * <code>
		 * $params = array( 'id' => 'my_txt_1' );
		 * $args = array( 'return' => true );
		 * $code = K::input( 'my_txt', $params, $args ); // $code is now <input id="my_txt_1" type="text" name="my_txt"/>
		 * </code>
		 * 
		 * @param  string $name   The name attribute for the generated element.
		 * @param  array  $params Associative array designating the element attributes and their values ('name' is always overriden).
		 * @param  array  $args   Additional arguments, currently supported arguments are:
		 *
		 * - **`format`**: [string, default=`:input`] The input field template, these replacement patterns are available:
         *   - **`:input`**: The input field HTML code
         *   - **`:name`**: The name attribute value
         *   - **`:id`**: The id attribute value
         *   - **`:value`**: The value attribute value
		 * - **`colorpicker`**: [wordpress specific] If true, the field will be turned into a color-picker
		 * - **`return`**: If true, field will be retured instead of being printed
		 * @return string|null    The input HTML code or null if printing (i.e., when **`$args['return']`** is false).
		 */
		public static function input( $name, $params = null, $args = null ) {

			// Make sure $params is an array
			if ( empty( $params ) ) {
				$params = array();
			}

			// Load default parameters.
			$params += array(
				'type' => 'text',
				'id' => '',
				'value' => '',
			);

			// Set or override name.
			$params['name'] = $name;

			// Make sure $args is an array
			if ( empty( $args ) ) {
				$args = array();
			}

			// Build the input field html
			$input = sprintf( '<input %s>', K::params_str( $params ) );

			// Format
			if ( ! empty( $args['format'] ) ) {
				$input = str_replace(
					array( ':input', ':name', ':id', ':value' ),
					array( $input, $name, $params['id'], $params['value'] ),
					$args['format']
				);
			}

			// Add default color picker.
			if ( 0
				|| ! empty( $args['colorpicker'] )
				|| ( 1
					&& 'text' === $params['type']
					&& preg_match( '/_color\]?$/', $name )
					&& empty( $args['nocolorpicker'] )
				)
			) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				ob_start();
				?>
					<script>
						jQuery( 'document' ).ready( function($) {
							$( '[name="<?php echo $name; ?>"]' ).wpColorPicker();
						});
					</script>
				<?php
				$input .= ob_get_clean();
			}

			// Print or return the input field HTML
			if ( ! empty( $args['return'] ) ) {
				return $input;
			} else {
				echo $input;
			}
		}

		/**
		 * Returns or prints the code for a textarea element.
		 *
		 * This method is very similar to `K::input()`, one important difference is that K::textarea() expects a value to be passed in `$args` instead of `$params`, the reason for this is that the textarea element doesn't recognize a value attribute.
		 * 
		 * @param  string $name   The name attribute for the generated element.
		 * @param  array  $params Associative array designating the element attributes and their values ('name' is always overriden).
		 * @param  array  $args   Additional arguments, currently supported arguments are:
		 *
		 * - **`value`**: [string] The initial text area content
		 * - **`format`**: [string, default=`:textarea`] The input field template, these replacement patterns are available:
         *   - **`:textarea`**: The textarea element HTML code
         *   - **`:name`**: The name attribute value
         *   - **`:id`**: The id attribute value
		 * - **`editor`**: [wordpress specific] If true, the text area will be turned into WordPress editor
         * - **`editor_height`**: [wordpress specific] If the WordPress editor is used, i.e, when `$args['editor']` is true, this parameter value is passed to the WordPress function [wp_editor()](https://codex.wordpress.org/Function_Reference/wp_editor) that is used to generate the editor
         * - **`media_buttons`**: [wordpress specific] If the WordPress editor is used, i.e, when `$args['editor']` is true, this parameter value is passed to the WordPress function [wp_editor()](https://codex.wordpress.org/Function_Reference/wp_editor) that is used to generate the editor
         * - **`teeny`**: [wordpress specific] If the WordPress editor is used, i.e, when `$args['editor']` is true, this parameter value is passed to the WordPress function [wp_editor()](https://codex.wordpress.org/Function_Reference/wp_editor) that is used to generate the editor
         * - **`textarea_name`**: [wordpress specific] If the WordPress editor is used, i.e, when `$args['editor']` is true, this parameter value is passed to the WordPress function [wp_editor()](https://codex.wordpress.org/Function_Reference/wp_editor) that is used to generate the editor
         * - **`textarea_rows`**: [wordpress specific] If the WordPress editor is used, i.e, when `$args['editor']` is true, this parameter value is passed to the WordPress function [wp_editor()](https://codex.wordpress.org/Function_Reference/wp_editor) that is used to generate the editor
		 * - **`return`**: If true, field will be retured instead of being printed
		 * @return string|null    The input HTML code or null if printing (i.e., when **`$args['return']`** is false).
		 */
		public static function textarea( $name, $params = null, $args = null ) {

			// Make sure $params is an array
			if ( empty( $params ) ) {
				$params = array();
			}

			// Load default parameters.
			$params += array(
				'id' => '',
			);

			// Set or override name.
			$params['name'] = $name;

			// Make sure $args is an array
			if ( empty( $args ) ) {
				$args = array();
			}

			// Set $value.
			$value = K::get_var( 'value', $args, '' );

			// Build textarea HTML code.
			if ( K::get_var( 'editor', $args ) ) {
				// Remove the name since it's attached to the editor
				$params_for_editor = $params;
				unset( $params_for_editor['name'] );
				// Build
				ob_start();
				wp_editor(
					$value,
					str_replace( array( '[', ']' ), '_', $name ) . mt_rand( 100, 999 ),
					array(
					'editor_height' => K::get_var( 'editor_height', $args ),
					'media_buttons' => K::get_var( 'media_buttons', $args, true ),
					'teeny' => K::get_var( 'teeny', $args ),
					'textarea_name' => $name,
					'textarea_rows' => K::get_var( 'textarea_rows', $args, 20 ),
					)
				);
				$textarea = ob_get_clean();
				$textarea = sprintf( '<div %s>%s</div>', K::params_str( $params_for_editor ), $textarea );
			} else {
				$textarea = sprintf( '<textarea %s>%s</textarea>', K::params_str( $params ), $value );
			}

			// Format.
			if ( ! empty( $args['format'] ) ) {
				$textarea = str_replace(
					array( ':textarea', ':value', ':name', ':id' ),
					array( $textarea, $value, $name, $params['id'] ),
					$args['format']
				);
			}

			// Print or return the textarea field HTML.
			if ( ! empty( $args['return'] ) ) {
				return $textarea;
			} else {
				echo $textarea;
			}
		}

		/**
		 * Prints or returns an dropdown select
		 */
		static function select( $name ) {

			// $params
			if ( func_num_args() > 1 ) {
				$params = func_get_arg( 1 );
			}
			if ( empty( $params ) ) {
				$params = array();
			}
			// Load defaults
			$params += array(
			'id' => '',
			);

			// Sanitize $params[multiple], and Add brackets if the former is true
			if ( ! empty( $params['multiple'] ) ) {
				$params['multiple'] = 'multiple';
				$name .= '[]';
			}

			// Add name
			$params['name'] = $name;

			// $args
			if ( func_num_args() > 2 ) {
				$args = func_get_arg( 2 );
			}
			if ( empty( $args ) ) {
				$args = array();
			}
			$args += array(
			'default' => '',
			'options' => array(),
			'html_before' => '',
			'html_after' => '',
			'selected' => '',
			);

			// Make 'selected' an array
			if ( $selected = $args['selected'] ) {
				if ( ! is_array( $selected ) ) {
					$selected = array( $selected );
				}
			}

			// Use 'default' is 'selected' is empty
			if ( ! $selected ) {
				$selected = array( $args['default'] );
			}

			// Build options
			$options = '';
			foreach ( $args['options'] as $value => $label ) {
				$options .= K::wrap(
					$label
					, array(
					'value' => $value,
					'selected' => ( in_array( $value, $selected ) )
						? 'selected'
						: NULL,
					)
					, array(
					'in' => 'option',
					'return' => true,
					)
				);
			}

			// Build the input field html
			$select = sprintf( '%s<select %s>%s</select>%s', $args['html_before'], K::params_str( $params ), $options, $args['html_after'] );

			// Format
			if ( ! empty( $args['format'] ) ) {
				$select = str_replace(
					array( ':select', ':name', ':id' ),
					array( $select, $name, $params['id'] ),
					$args['format']
				);
			}

			// Print or return the input field HTML
			if ( ! empty( $args['return'] ) ) {
				return $select;
			} else {
				echo $select;
			}
		}

		/**
		 * Prints or returns an input field
		 *
		 * @param array $controls The array of controls
		 */
		static function fieldset( $legend, $controls = array() ) {

			// $params.
			if ( func_num_args() > 2 ) {
				$params = func_get_arg( 2 );
			}
			if ( empty( $params ) ) {
				$params = array();
			}

			// $args.
			if ( func_num_args() > 3 ) {
				$args = func_get_arg( 3 );
			}
			if ( empty( $args ) ) {
				$args = array();
			}

			// Inner HTML placeholder.
			$innerHTML = '';

			// Put controls in placehoder.
			foreach ( $controls as $control ) {

				// Fill params if needed.
				$control[2] = ! empty( $control[2] ) ? $control[2] : array();

				// Set $args['return'] to false.
				if ( empty( $control[3] ) ) {
					$control[3] = array();
				}
				$control[3]['return'] = true;

				// Get control HTML.
				$innerHTML .= call_user_func(
					/* Callback */     'K::' . $control[0],
					/* Name/content */ $control[1],
					/* Params*/        $control[2],
					/* Args */         $control[3]
				);
			}

			// Prepare HTML.
			$HTML = str_replace(
				array( ':legend', ':controls', ':parameters' ),
				array(
				! empty( $legend ) ? $legend : '',
				$innerHTML,
				K::params_str( $params ),
				),
				'<fieldset :parameters><legend>:legend</legend>:controls</fieldset>'
			);

			// Print or return the input field HTML.
			if ( ! empty( $args['return'] ) ) {
				return $HTML;
			} else {
				echo $HTML;
			}
		}

		/**
		 * Wraps given input in an html tag.
		 * @param  string $content The content to wrap.
		 * @return string          The HTML
		 */
		static function wrap( $content = '' ) {

			// $params
			if ( func_num_args() > 1 ) {
				$params = func_get_arg( 1 );
			}
			if ( empty( $params ) ) {
				$params = array();
			}

			// $args
			if ( func_num_args() > 2 ) {
				$args = func_get_arg( 2 );
			}
			if ( empty( $args ) ) {
				$args = array();
			}
			$args += array(
			'in' => 'div',
			'html_before' => '',
			'html_after' => '',
			);

			// Build the input field html.
			$html = sprintf( '%s<%s %s>%s</%s>%s',
				$args['html_before'],
				$args['in'],
				K::params_str( $params ),
				$content,
				$args['in'],
				$args['html_after']
			);

			// Print or return the input field HTML.
			if ( ! empty( $args['return'] ) ) {
				return $html;
			} else {
				echo $html;
			}
		}

		/**
		 * Prepares an array of params and their values to be added to an HTML element.
		 *
		 * @param  array $params Associative array of parameter names and their values.
		 * @return string HTML   tag parameters as a string.
		 */
		static function params_str( $params ) {
			ksort( $params );
			$params_str = '';
			foreach ( $params as $parameter => $value ) {
				if ( strlen( $value ) ) {
					$params_str .= sprintf( ' %s="%s"', $parameter, $value );
				}
			}
			$params_str = trim( $params_str );
			return $params_str;
		}
	}
}
