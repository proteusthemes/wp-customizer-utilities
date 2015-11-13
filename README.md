# WP Customizer Utilities

Advanced WordPress customizer controls and settings for better user experience.

## Installation

Install it via [Composer](https://getcomposer.org/) and [Packagist](https://packagist.org/packages/proteusthemes/wp-customizer-utilities):

```shell
$ composer require proteusthemes/wp-customizer-utilities
```

Start using the classes and they will be autoloaded (PSR-4).

## Documentation

### Controls

- #### Layout Builder

  [jQuery UI slider](https://jqueryui.com/slider/) with an option how many handles you want to control in that slider. Useful for creating dynamic layouts, for example for the footer, where user can congigure how many columns they want and how wide each of these columns will be.

- #### Gradient

  Control for the CSS gradient (WP has only support for solid colors by default).

### Settings

- #### Dynamic CSS

  Create Dynamic CSS by declaring the selectors (for example for background colors etc.) when defining a setting. Has build-in support for modifiers of that color and media queries.

  ##### Public methods

  - `get_css_props()`

    Get entire css_props property of the class.

  - `get_single_css_prop( string $name, DynamicCSS\ModInterface|callable $modifier )`

    Return all variants of the CSS propery with selectors. Optionally filtered with the modifier.

  - `render_css()`

    Render the entire CSS for this setting in the inline style (each group of selectors in its own line). Useful for caching the output of the setting and echoing it on the WP frontend.

  ##### Modifier Interface `ModInterface`

  The modifer classes must implement the `DynamicCSS\ModInterface`. It has only one method `modify( $in )` which takes input value and returns the modified value.

  Example:

  ```php
  class MyModClass implements \ProteusThemes\CustomizerUtils\Setting\DynamicCSS\ModInterface {
    public function modify( $in ) {
      return your_modify_function( $in );
    }
  }
  ```

  ##### Included modifiers

  Some modifiers are already included with the package and you can use them stright away.

  - `ModDarken( $amount )` - darken hexdec color for `$amount` (using [phpColors](https://github.com/mexitek/phpColors#available-methods))
  - `ModLighten( $amount )` - lighten hexdec color for `$amount` (using [phpColors](https://github.com/mexitek/phpColors#available-methods))
  - `ModLinearGradient( ModInterface $modifier, $orientation = 'to bottom' )` - creates CSS linear-gradient. First color is instact, second color is modified using `$modifier`. `$orientation` can be any valid [CSS orientation](https://developer.mozilla.org/en-US/docs/Web/CSS/linear-gradient#Syntax).
  - `ModPrependAppend( $prefix = '', $suffix = '' )` - adds the prefix or suffix (or both) to the value. Useful for adding ` !import` or `url('value')`.

  ##### Use

  Example of the code you would most likely attach to the action `customize_register`:

  ```php
  function your_func( $wp_customize ) {
    $darken5  = new \ProteusThemes\CustomizerUtils\Setting\DynamicCSS\ModDarken( 5 );

    $wp_customize->add_setting( new \ProteusThemes\CustomizerUtils\Setting\DynamicCSS( $wp_customize, 'nav_bg', array(
      'default'   => '#bada55',
      'css_props' => array( // list of all css properties this setting controls
        array( // each property in it's own array
          'name'      => 'background-color', // this is actual CSS property
          'selectors' => array(
            'noop' => array( // regular selectors in the key 'noop'
              'body',
              '.selector2',
            ),
            '@media (min-width: 900px)' => array( // selectors which should be in MQ
              '.selector3',
            ),
          ),
          'modifier'  => $darken5, // optional. Separate data type, with the modify() method (via implemented interface) which takes value and returns modified value OR callable function with 1 argument
        ),
      ),
    ) ) );
  }
  add_action( 'customize_register', 'your_func' );
  ```

  You must also enqueue the JS file which handles the live preview changes via `postMessage`:

  ```php
  function enqueue_customizer_js() {
    wp_enqueue_script(
      'mytheme-live-customize',
      get_template_directory_uri() . '/vendor/proteusthemes/wp-customizer-utilities/assets/live-customize.js',
      array( 'jquery', 'customize-preview' ),
      false,
      true
    );

    wp_localize_script( 'mytheme-live-customize', 'ptCustomizerDynamicCSS', array(
      array(
        'settingID' => 'nav_bg',
        'selectors' => 'body, .selector1, .selector2',
        'cssProp'   => 'background-color',
      )
    ) );
  }
  add_action( 'customize_preview_init', 'enqueue_customizer_js', 31 );
  ```
