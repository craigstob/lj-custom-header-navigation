<?php
/**
 * Plugin Name: Local Jungle Custom Header Nav
 * Version: 1.0.0
 * Requires at least: 5.5
 * Requires PHP: 7.2
 * Description: Set up a custom navigation the LJ way.
 * Author: Local Jungle
 * Author URI: https://www.localjungle.com
 * Text Domain: ljhn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// [theme-menu id="342"]
add_shortcode( 'theme-menu', function ( $atts ) {
	$atts = shortcode_atts( [
		'id' => '',
	], $atts );

	$post_id = intval( $atts['id'] );
	if ( ! $post_id ) {
		return 'No menu ID provided.';
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'wp_navigation' ) {
		return 'Menu not found.';
	}

	$blocks = parse_blocks( $post->post_content );

	// Dig into navigation block wrapper
	if ( isset( $blocks[0] ) && $blocks[0]['blockName'] === 'core/navigation' && ! empty( $blocks[0]['innerBlocks'] ) ) {
		$blocks = $blocks[0]['innerBlocks'];
	}

	$menu_html = build_custom_menu_from_blocks( $blocks );

	return '<nav class="theme-navigation" hidden><ul class="main-wrapper">' . $menu_html . '</ul></nav>';
} );

function build_custom_menu_from_blocks( $blocks ) {
	$output = '';

	// Loop through each block to construct the menu
	foreach ( $blocks as $block ) {
		$block_name = $block['blockName'] ?? '';

		// Handle the menu links
		if ( $block_name === 'core/navigation-link' ) {
			$attrs = $block['attrs'] ?? [];
			$label = esc_html( $attrs['label'] ?? 'Menu Item' );
			$url   = esc_url( $attrs['url'] ?? '#' );

			$output .= '<li class="menu-item">';
			$output .= '<a href="' . $url . '">' . $label . '</a>';
			$output .= '</li>';
		} // Handle the navigation submenu
        elseif ( $block_name === 'core/navigation-submenu' ) {
			$attrs = $block['attrs'] ?? [];
			$label = esc_html( $attrs['label'] ?? 'Submenu' );
			$url   = esc_url( $attrs['url'] ?? '#' );
			$open  = '<span class="open-sub"><svg width="25" height="25" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>arrow-down-circle</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-412.000000, -1087.000000)" fill="#007cba"> <path d="M428,1117 C420.268,1117 414,1110.73 414,1103 C414,1095.27 420.268,1089 428,1089 C435.732,1089 442,1095.27 442,1103 C442,1110.73 435.732,1117 428,1117 L428,1117 Z M428,1087 C419.163,1087 412,1094.16 412,1103 C412,1111.84 419.163,1119 428,1119 C436.837,1119 444,1111.84 444,1103 C444,1094.16 436.837,1087 428,1087 L428,1087 Z M433.121,1102.46 L429,1106.59 L429,1096 C429,1095.45 428.553,1095 428,1095 C427.448,1095 427,1095.45 427,1096 L427,1106.59 L422.879,1102.46 C422.488,1102.07 421.855,1102.07 421.465,1102.46 C421.074,1102.86 421.074,1103.49 421.465,1103.88 L427.121,1109.54 C427.361,1109.78 427.689,1109.85 428,1109.79 C428.311,1109.85 428.639,1109.78 428.879,1109.54 L434.535,1103.88 C434.926,1103.49 434.926,1102.86 434.535,1102.46 C434.146,1102.07 433.512,1102.07 433.121,1102.46 L433.121,1102.46 Z" id="arrow-down-circle" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg></span>';

			$output .= '<li class="menu-item menu-item-has-children">';
			$output .= '<a href="' . $url . '">' . $label . '</a> ' . $open . '';

			if ( ! empty( $block['innerBlocks'] ) ) {
				$output .= '<ul class="sub-menu">'; // Open the submenu list
				$output .= build_custom_menu_from_blocks( $block['innerBlocks'] );  // Recursive call to render the submenu items
				$output .= '</ul>';  // Close the submenu list
			}

			$output .= '</li>';
		} // Handle recursion for inner blocks (other types of blocks that may contain inner blocks)
        elseif ( ! empty( $block['innerBlocks'] ) ) {
			$output .= build_custom_menu_from_blocks( $block['innerBlocks'] );
		}
	}

	return $output;
}

add_shortcode( 'lj-menu-toggle', function ( $atts ) {
	return '<div class="hamburger" id="hamburger"><div></div><div></div><div></div></div>';
} );

add_action( 'wp_header', function () {
	?>
    <style>
        .main_menu_container {
            position: relative;
        }

        /*.main_menu_container,*/
        /*.main_menu_container * {*/
        /*    display: block !important;*/
        /*}*/

        /* Make the hamburger menu container clickable */
        .hamburger {
            cursor: pointer; /* Make the entire container clickable */
            width: 40px; /* Adjust to your desired width */
            height: 30px; /* Adjust to your desired height */
            position: relative; /* Important for precise positioning of spans */
            /*border: 1px solid red;*/
        }

        /* Style the hamburger lines */
        .hamburger div {
            width: 30px; /* Ensure the span width matches the container's width */
            height: 4px; /* Adjust thickness of lines */
            background-color: #fff; /* Set the bars to white */
            transition: all 0.3s ease; /* Smooth transition for transform and positioning */
            position: absolute; /* Absolute position for precise control */
        }

        /* Position the spans vertically within the container */
        .hamburger div:nth-child(1) {
            top: 0; /* First span at the top */
        }

        .hamburger div:nth-child(2) {
            top: 50%; /* Middle span in the center */
            transform: translateY(-50%); /* Ensure it is vertically centered */
        }

        .hamburger div:nth-child(3) {
            bottom: 0; /* Third span at the bottom */
        }

        /* When the menu is active (crossed lines) */
        .hamburger.active div:nth-child(1) {
            transform: translateY(18px) rotate(45deg); /* Move the first line down and rotate */
        }

        .hamburger.active div:nth-child(2) {
            opacity: 0; /* Hide the middle line */
        }

        .hamburger.active div:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg); /* Move the third line up and rotate */
        }


        /* Optional: hide menu by default for mobile */
        /* Base styles for the menu */
        .theme-navigation {
            list-style: none;
            padding: 0 0 20px;
            margin: 0;
            position: absolute;
            z-index: 9;
            left: 0;
            width: 100%;
            background: #1a2c56;
            /*background: #fff;*/
            /*top: -9999em;*/
            font-weight: 700;
            /*visibility: hidden;*/
            /*height: 0;*/
            /*opacity: 0;*/
            /*min-height: 70px;*/
        }

        .theme-navigation a {
            text-decoration: none;
            display: block;
            padding: 10px 0;
            color: #c2ad77;
        }

        .theme-navigation.active {
            display: block;
        }

        .theme-navigation .menu-item {
            position: relative;
        }

        .theme-navigation .sub-menu {
            padding: 0;
            margin: 0;
            display: none;
            background: #007cba;
        }

        .theme-navigation .main-wrapper {
            padding: 0 1.2rem;
            margin: 0;
        }

        .theme-navigation li:first-child {
            border-top: 1px solid #e6b743;
        }

        .theme-navigation li {
            list-style: none;
            border-bottom: 1px solid #e6b743;
        }

        .theme-navigation .open-sub {
            width: 40px;
            height: 40px;
            position: absolute;
            top: 10px;
            right: 0;
            cursor: pointer;
        }

        .theme-navigation .sub-menu li:last-child {
            border-bottom: none;
        }
    </style>
	<?php
} );