<?php
/**
 * Author: Vitaly Kukin
 * Date: 23.04.2016
 * Time: 22:13
 */

function bidi_init_bd() {

    global $wpdb;

    $wpdb->bidi_posts = $wpdb->prefix . 'bidi_posts';
}
add_action( 'init', 'bidi_init_bd' );

function bidi_head(){

    global $post;

    if( isset($post->seo_description) ){
        echo $post->seo_description != '' ? '<meta name="description" content="' . $post->seo_description . '" />' . "\n" : '';
        echo $post->seo_keywords != '' ? '<meta name="keywords" content="' . $post->seo_keywords . '" />' . "\n" : '';
    }
}
add_action('wp_head', 'bidi_head');

function bidi_filter_wp_title( $title ) {

    global $post;

    if( isset($post->seo_title) && $post->seo_title != '' )
        $title['title'] = $post->seo_title;

    return $title;
}
add_filter( 'document_title_parts', 'bidi_filter_wp_title', 10, 1 );

function bidi_query_clauses_front( $pieces ){

    if( !is_singular() )
        return $pieces;

    global $wpdb;

    $pieces['join'] = $pieces['join'] . " LEFT JOIN `{$wpdb->bidi_posts}` p ON (`{$wpdb->posts}`.`ID` = p.`post_id`) ";
    $pieces['fields'] = $pieces['fields'] . ", p.* ";

    return $pieces;
}
add_filter( 'posts_clauses', 'bidi_query_clauses_front' );

function bidi_delete_product( $post_id ) {

    global $wpdb;

    $wpdb->delete( $wpdb->bidi_posts, array( 'post_id' => $post_id ), array( '%d' ) );
}
add_action( 'before_delete_post', 'bidi_delete_product' );

if( ! function_exists('pr') ){
    function pr( $any ) {
        echo '<pre>';
        print_r($any);
        echo '</pre>';
    }
}

function bidi_text_field( $name = '', $args = array(), $layout = true ) {

    $defaults = array(
        'label'       => '',
        'placeholder' => '',
        'id'          => '',
        'value'       => '',
        'description' => '',
        'readonly'    => false,
        'class'       => 'large-text',
        'maxlength'   => '',
    );

    $args = wp_parse_args( $args, $defaults );

    $readonly = $args['readonly'] ? 'readonly="readonly"' : '';

    $result = '<tr valign="top">
                <th scope="row">
                    <label for="' . $args[ 'id' ] . '">' . $args[ 'label' ] . ':</label>
                </th>
                <td>
                    <input id="' . $args[ 'id' ] . '" type="text" value="' . $args[ 'value' ] . '" name="' . $name . '" 
                    class="' . $args[ 'class' ] . '" ' . $readonly . '>
                    <p class="description">' . $args[ 'description' ] . '</p>
                </td>
            </tr>';

    if ( $layout )
        echo $result;

    return $result;
}

function bidi_textarea_field( $name = '', $args = array(), $layout = true ) {

    $defaults = array(
        'label'       => '',
        'id'          => '',
        'value'       => '',
        'description' => '',
        'readonly'    => false,
        'rows'        => 2,
        'class'       => 'large-text'
    );

    $args = wp_parse_args( $args, $defaults );

    $readonly = ( $args[ 'readonly' ] ) ? 'readonly="readonly"' : '';

    $result = '<tr valign="top">
                <th scope="row">
                    <label for="' . $args[ 'id' ] . '">' . $args[ 'label' ] . ':</label>
                </th>
                <td>
                    <textarea rows="' . $args[ 'rows' ] . '" id="' . $args[ 'id' ] . '" class="' . $args[ 'class' ] . '" 
                    name="' . $name . '" ' . $readonly . '>' . $args[ 'value' ] . '</textarea>
                    <p class="description">' . $args[ 'description' ] . '</p>
                </td>
            </tr>';

    if ( $layout )
        echo $result;

    return $result;
}