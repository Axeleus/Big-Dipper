<?php
/**
 * Author: Vitaly Kukin
 * Date: 23.04.2016
 * Time: 22:20
 */

/**
 * List of sidebar for custom metabox
 * @return array
 */
function bidi_list_menu() {

    return array(
        'general'   => array(
            'title' => 'Главная',
            'icon'  => 'dashicons-before dashicons-dashboard',
            'call'  => 'bidi_call_dash'
        ),
        'seo'       => array(
            'title' => 'SEO',
            'icon'  => 'dashicons-before dashicons-marker',
            'call'  => 'bidi_call_seo'
        )
    );
}

function bidi_metabox_post() {

    add_meta_box(
        'bidi_box',
        __('Big Dipper', 'bidi'),
        'bidi_controller',
        array('post', 'page'),
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'bidi_metabox_post' );

function bidi_save_product( $post_id ) {

    if ( !isset($_POST['bidi_post_nonce_field']) ||
        !wp_verify_nonce( $_POST['bidi_post_nonce_field'], 'bidi_meta_action') ) return;

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if ( !current_user_can( 'edit_page', $post_id ) ) return;

    bidi_save_admin_run( $post_id );
}
add_action( 'save_post', 'bidi_save_product', 10, 1 );

function bidi_controller(){

    global $post;

    $row = bidi_get_post_details( $post->ID );

    if( !empty($row) ){
        $post = (object) array_merge((array) $post, (array) $row);
    }

    $menu = bidi_list_menu();

    wp_nonce_field( 'bidi_meta_action', 'bidi_post_nonce_field' );

    ?>
    <div class="tabs-inner clearfix" id="bidi-tabs">
        <div class="tab-menu">
            <ul>
                <?php
                $i = 0;
                $calls = array();

                foreach ( $menu as $key => $val ) {

                    $i++;
                    $calls[ $key ] = $val[ 'call' ];
                    $active        = ( $i == 1 ) ? 'active' : '';

                    printf( '<li class="%s"><a href="#%s"><span class="%s"></span> %s</a></li>',
                        $active, $key, $val[ 'icon' ], $val[ 'title' ]
                    );
                }
                ?>
            </ul>
        </div>
        <div class="tab-content">
            <?php
            $i = 0;

            foreach ( $calls as $key => $val ) {

                $i++;
                $display = ( $i != 1 ) ? 'display:none' : '';

                echo '<div class="tab-item-content" style="' . $display . '" id="bidi-tab-item-' . $key . '">';
                call_user_func($val);
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function bidi_call_dash(){
    ?>
    <h2>Первая вкладка</h2>
    <p class="description">Перейдите на вкладку SEO чтобы отредактировать данные</p>
    <?php
}

function bidi_call_seo(){

    global $post;

    ?>

    <h2>SEO</h2>
    <p class="description">Мета данные заголовка и описания - скрытые елементы на странице которые используются поисковыми системами</p>

    <table class="form-table" id="bidi-seo">
        <tbody>

        <?php

        bidi_text_field(
            'seo_title',
            array(
                'label'       => 'Заголовок',
                'id'          => 'seo_title',
                'value'       => $post->seo_title,
                'description' => 'Большинство поисковых систем используют максимум 60 символов для названия веб-страницы.',
                'class'       => 'large-text',
                'maxlength'   => '60',
            )
        );

        bidi_textarea_field(
            'seo_description',
            array(
                'label'       => 'Описание',
                'id'          => 'seo_description',
                'value'       => $post->seo_description,
                'description' => 'Большинство поисковых систем используют максимум 160 символов для описания веб-страницы.',
                'class'       => 'large-text resize-vertical',
            )
        );

        bidi_text_field(
            'seo_keywords',
            array(
                'label'       => 'Ключевые слова',
                'id'          => 'seo_keywords',
                'value'       => $post->seo_description,
                'description' => 'Разделенные запятой',
                'class'       => 'large-text',
            )
        );

        ?>

        </tbody>
    </table>

    <?php
}

function bidi_default_fileds(){

    return array(
        'seo_title'         => array( 'call' => 'strip_tags' ),
        'seo_description'   => array( 'call' => 'strip_tags' ),
        'seo_keywords'      => array( 'call' => 'strip_tags' )
    );
}

function bidi_save_admin_run( $post_id ){

    $data = $_POST;
    if( empty($data) ) return false;

    $foo = array();

    $args = bidi_default_fileds();

    foreach ($args as $key => $val ) {

        if( isset($data[$key]) ) {
            $foo[$key] = call_user_func( $val['call'], $data[$key] );
        }
        else {
            $foo[$key] = '';
        }
    }

    if( !empty($foo) ) {

        bidi_update_post( $post_id, $foo );

        return true;
    }
    else
        return false;
}

function bidi_update_post( $post_id, $foo ) {

    global $wpdb;
    
    $details = bidi_get_post_details( $post_id );

    if( !empty($details) ){
        $wpdb->update(
            $wpdb->bidi_posts,
            $foo,
            array('post_id' => $post_id)
        );
    }
    else {
        $foo['post_id'] = $post_id;
        $wpdb->insert($wpdb->bidi_posts, $foo);
    }
}

function bidi_get_post_details( $post_id ){

    global $wpdb;

    return $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->bidi_posts} WHERE post_id = %d", $post_id ) );
}