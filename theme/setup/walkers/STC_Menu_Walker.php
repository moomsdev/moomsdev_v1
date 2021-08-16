<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The menu walker.  This is just the methods from `Walker_Nav_Menu` with
 * all of the whitespace generation (eg. `$indent` remove) as well as
 * some restrictions on the CSS classes that are added. Menu item IDs are also
 * removed.
 * Most of the filters here are preserved so it should be backwards
 * compatible.
 *
 * @since   0.1
 */
class STC_Menu_Walker extends Walker_Nav_Menu
{

    /**
     * {@inheritdoc}
     */
    function start_lvl(&$output, $depth = 0, $args = [])
    {
        if ($args->walker->has_children) {
            $output .= '<ul class="nav__dropdown-menu">';
        } else {
            $output .= '<ul class="nav__dropdown-menu">';
        }
    }

    /**
     * {@inheritdoc}
     */
    function end_lvl(&$output, $depth = 0, $args = [])
    {
        if ($args->walker->has_children) {
            $output .= '</ul>';
        } else {
            $output .= '</ul>';
        }
    }

    /**
     * {@inheritdoc}
     */
    function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $classes   = empty($item->classes) ? [] : (array)$item->classes;
        $classes[] = 'nav__dropdown menu-item-' . $item->ID;
        if ($args->walker->has_children) {
            $classes[] = 'menu-item-has-children';
        }


        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="menu-line ' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . str_replace('menu-item-has-children', '', $class_names) . '>';

        /* Chèn icon vào trước menu link */
        //            $icon = carbon_get_nav_menu_item_meta($item->ID, 'icon');
        //            $output .= '<img src="' . wp_get_attachment_image_url($icon) . '">';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        // if ($args->walker->has_children) {
        //     $item_output .= '<span class="caret"></span>';
        // }
        $item_output .= '</a>';


        $item_output .= $args->after;
        $output      .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * {@inheritdoc}
     */
    function end_el(&$output, $item, $depth = 0, $args = [])
    {
        $output .= "</li>";
    }
}
