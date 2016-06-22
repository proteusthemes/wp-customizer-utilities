<?php

namespace ProteusThemes\CustomizerUtils\Control;

/**
 * LayoutBuilder Control Class
 *
 * @package
 * @subpackage Customize
 */
class LayoutBuilder extends \WP_Customize_Control {
	/**
	 * @access public
	 * @var string
	 */
	public $type = 'dynamic-layout';

	/**
	 * Constructor.
	 *
	 * @uses WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Enqueue scripts/styles for the range selector.
	 */
	public function enqueue() {
		global $wp_scripts;

		wp_enqueue_script( 'jquery-ui-slider' );

		// get registered script object for jquery-ui
		$ui = $wp_scripts->query( 'jquery-ui-core' );

		// tell WordPress to load the jQuery UI CSS from cloudflare CDN
		wp_enqueue_style( 'jquery-ui-core', sprintf( '//cdnjs.cloudflare.com/ajax/libs/jqueryui/%s/jquery-ui.min.css', $ui->ver ), false, null );
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		$val = esc_attr( $this->value() );

		$attrs = shortcode_atts( array(
			'min'     => 1,
			'max'     => 12,
			'step'    => 1,
			'maxCols' => 6,
		), $this->input_attrs );

		$widgets_num = sizeof( explode( ',', $val ) ) + 1;
		$widgets_num = min( intval( $attrs['maxCols'] ), $widgets_num );

		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<select id="js-widgets-num-<?php echo esc_attr( $this->id ); ?>" style="margin-bottom: 1em;">
		<?php for ( $i = 0; $i <= (int) $attrs['maxCols']; $i++ ) {
			printf( '<option value="%1$d"%2$s>%1$d</option>', $i, selected( $i, $widgets_num ) );
		} ?>
		</select>
		<div id="js-layout-<?php echo esc_attr( $this->id ); ?>"></div>
		<script type="text/javascript">
			jQuery( function( $ ) {
				'use strict';
				var saveJSON = function( val ) {
					if ( typeof val !== 'string' ) {
						val = JSON.stringify( val );
					}

					wp.customize( '<?php echo esc_js( $this->id ); ?>', function( obj ) {
						obj.set( val );
					} );
				};

				// jquery ui slider
				var setSlider = function ( values ) {
					$( '#js-layout-<?php echo sanitize_html_class( $this->id ); ?>' ).slider( {
						values: values,
						min:    <?php echo intval( $attrs['min'] ); ?>,
						max:    <?php echo intval( $attrs['max'] ); ?>,
						step:   <?php echo intval( $attrs['step'] ); ?>,
						change: function ( ev, ui ) {
							saveJSON( ui.values );
						}
					} );
				};
				setSlider( <?php echo esc_js( $val ); ?> );

				var distributeDividers = function ( numberCols, minCols, maxCols ) {
					var out = [],
						step = ( maxCols - minCols ) / ( numberCols  );

					for ( var i = step; i < maxCols; i += step ) {
						out.push( Math.round( i ) );
					};

					return out;
				};

				// listen to <select> change and rebuild the layout accordingly
				$( '#js-widgets-num-<?php echo esc_js( $this->id ); ?>' ).on( 'change', function ( ev ) {
					var $el = $( '#js-layout-<?php echo esc_js( $this->id ); ?>' );

					if ( undefined !== $el.slider( 'instance' ) ) {
						$el.slider( 'destroy' );
					}

					var selected = $( ev.currentTarget ).val();
					selected = parseInt( selected );

					if ( selected < 2 ) {
						saveJSON( selected );
						return;
					}

					var distributedArray = distributeDividers(
						parseInt( selected ),
						<?php echo intval( $attrs['min'] ); ?>,
						<?php echo intval( $attrs['max'] ); ?>
					);

					setSlider( distributedArray, <?php echo intval( $attrs['min'] ); ?>, <?php echo intval( $attrs['max'] ); ?> );
					saveJSON( distributedArray );
				} );
			} );
		</script>
		<style>
			.customize-control-<?php echo sanitize_html_class( $this->type ); ?> .ui-widget-content {
				background: #e4eff4;
				border-radius: 0;
				border-color: #bcccd2;
			}
			.customize-control-<?php echo sanitize_html_class( $this->type ); ?> .ui-slider-horizontal {
				height: 20px;
			}
			.customize-control-<?php echo sanitize_html_class( $this->type ); ?> .ui-widget-content .ui-state-default {
				height: 20px;
				width: 16px;
			}
			.customize-control-<?php echo sanitize_html_class( $this->type ); ?> .ui-slider-handle {
				top: -1px;
				margin-left: -8px;
				border-radius: 0;
				background: #e5f4fa;
				border-color: #e5f4fa #bcccd2;
				cursor: col-resize;
				transition: border-color 0.25s ease-in-out, background 0.25s ease-in-out;
			}
			.customize-control-<?php echo sanitize_html_class( $this->type ); ?> .ui-state-hover {
				background: #bfe4f3;
				border-top-color: #bfe4f3;
				border-bottom-color: #bfe4f3;
			}
		</style>
		<?php
	}
}
