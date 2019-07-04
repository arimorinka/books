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


/**
 * Add a metabox to the right side of the screen
 */
function wpt_add_book_metaboxes() {
    add_meta_box(
        'wpt_books_isbn',
        'book isbn',
        'wpt_books_isbn',
        'books',
        'side',
        'default'
    );
}

/**
 * Output the HTML for the metabox.
 */
function wpt_books_isbn() {
    global $post;
    // Nonce field to validate form request came from current site
    wp_nonce_field( basename( __FILE__ ), 'book_fields' );
    // Get the isbn data if it's already been entered
    $isbn = get_post_meta( $post->ID, 'isbn', true );
    // Output the field
    echo '<input type="text" name="isbn" value="' . esc_textarea( $isbn )  . '" class="widefat">';
}


/**
 * Save the metabox data
 */
function wpt_save_books_meta( $post_id, $post ) {
    // Return if the user doesn't have edit permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    // Verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times.
    if ( ! isset( $_POST['isbn'] ) || ! wp_verify_nonce( $_POST['book_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // Now that we're authenticated, time to save the data.
    // This sanitizes the data from the field and saves it into an array $books_meta.
    $books_meta['isbn'] = esc_textarea( $_POST['isbn'] );
    // Cycle through the $books_meta array.
    // Note, in this example we just have one item, but this is helpful if you have multiple.
    foreach ( $books_meta as $key => $value ) :
        // Don't store custom data twice
        if ( 'revision' === $post->post_type ) {
            return;
        }
        if ( get_post_meta( $post_id, $key, false ) ) {
            // If the custom field already has a value, update it.

            update_post_meta( $post_id, $key, $value );

        } else {
            // If the custom field doesn't have a value, add it.
            add_post_meta( $post_id, $key, $value);

        }
        if ( ! $value ) {
            // Delete the meta key if there's no value
            delete_post_meta( $post_id, $key );
        }
    endforeach;

}
add_action( 'save_post', 'wpt_save_books_meta', 1, 2 );

