<?php 
/**
* create and update db
**/
function books_info_insert_into_db() {
    global $wpdb;
    // creates books_info in database if not exists
    $table = $wpdb->prefix . "books_info"; 
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `post_id` text NOT NULL,
        `isbn` text NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`post_id`),
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


/**
* Create Custom Post type
**/
function create_books()
{
   $labels = array(
    'name'               => _x( 'books', 'post type general name', 'stacy' ),
    'singular_name'      => _x( 'books', 'post type singular name', 'stacy' ),
    'menu_name'          => _x( 'books', 'admin menu', 'stacy' ),
    'name_admin_bar'     => _x( 'books', 'add new on admin bar', 'stacy' ),
    'add_new'            => _x( 'Add New', 'books', 'stacy' ),
    'add_new_item'       => __( 'Add New books', 'stacy' ),
    'new_item'           => __( 'New books', 'stacy' ),
    'edit_item'          => __( 'Edit books', 'stacy' ),
    'view_item'          => __( 'View books', 'stacy' ),
    'all_items'          => __( 'All books', 'stacy' ),
    'search_items'       => __( 'Search books', 'stacy' ),
    'not_found'          => __( 'No books found.', 'stacy' ),
    'not_found_in_trash' => __( 'No books found in Trash.', 'stacy' )
    );
   $supports = array(
        'title',
        'editor',
        'thumbnail',
        'comments',
        'revisions',
    );

       $args = array(
        'labels'             => $labels,
        'supports'             => $supports,
        'capability_type'      => 'post',
        'description'        => __( 'Description.', 'Add New books on stacy' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'books' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 3,
        'menu_icon'          =>'dashicons-book',
        'taxonomies'         => array('authors','publisher'),
        'register_meta_box_cb' => 'wpt_add_book_metaboxes'
    );
        register_post_type( 'books', $args );
     }
     add_action( 'init', 'create_books' );




    function save_books_custom_fields(){
         global $post;

         if ( $post )
         {
           update_post_meta($post->ID, "short_description", @$_POST["short_description"]);
          update_post_meta($post->ID, "price", @$_POST["price"]);
          update_post_meta($post->ID, "length", @$_POST["length"]);
               update_post_meta($post->ID,'books_ship_lead_days',@$_POST['ship_lead_days']);
         update_post_meta($post->ID,'commision_broker',@$_POST['commision_broker']);
          }
        }
    add_action( 'save_post', 'save_books_custom_fields' );


    // add authors taxonomy
    add_action( 'init', 'authors', 0 );
    function authors() {
    $labels = array(
        'name'              => _x( 'Authors', 'taxonomy general name' ),
        'singular_name'     => _x( 'authors', 'taxonomy singular name' ),
        'search_items'      => __( 'Search authors' ),
        'all_items'         => __( 'All authors' ),
        'parent_item'       => __( 'Parent authors' ),
        'parent_item_colon' => __( 'Parent authors:' ),
        'edit_item'         => __( 'Edit authors' ),
        'update_item'       => __( 'Update authors' ),
        'add_new_item'      => __( 'Add New authors' ),
        'new_item_name'     => __( 'New authors Name' ),
        'menu_name'         => __( 'authors' ),
      );

       $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'authors' ),
    );
    register_taxonomy( 'authors', array( 'books' ), $args );

    // add publisher taxonomy
    add_action( 'init', 'publisher', 1 );
    function publisher() {
    $labels = array(
        'name'              => _x( 'publisher', 'taxonomy general name' ),
        'singular_name'     => _x( 'publisher', 'taxonomy singular name' ),
        'search_items'      => __( 'Search publisher' ),
        'all_items'         => __( 'All publisher' ),
        'parent_item'       => __( 'Parent publisher' ),
        'parent_item_colon' => __( 'Parent publisher:' ),
        'edit_item'         => __( 'Edit publisher' ),
        'update_item'       => __( 'Update publisher' ),
        'add_new_item'      => __( 'Add New publisher' ),
        'new_item_name'     => __( 'New publisher Name' ),
        'menu_name'         => __( 'publisher' ),
      );

       $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'publisher' ),
    );
    register_taxonomy( 'publisher', array( 'books' ), $args );
    } 

}
