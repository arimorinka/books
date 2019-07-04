<?php 
    /*
    Plugin Name: Books Info
    Plugin URI: https://zagros.pro
    Description: Books Info
    Author: Soma Moradi
    Version: 1.0
    Author URI: https://zagros.pro
    */

/**
* create and update db
**/
function installer(){
    include('installer.php');
}
register_activation_hook( __file__, 'installer' );

include 'functions.php';

function add_book_stylesheet() 
{

    // or
    //Register the script like this for a theme:
     wp_enqueue_script( 'jquery', get_template_directory_uri() . '/interface/javascripts/jquery.min.js', array(), false , true );
            // Register the script like this for a plugin:
    wp_enqueue_script( 'table', plugins_url( '/js/table.js', __FILE__ ), array(), false, true );
            // Register the script like this for a plugin:
    wp_enqueue_script( 'scripts', plugins_url( '/js/scripts.js', __FILE__ ), array( 'jquery'), false, true );
    wp_enqueue_style('table', plugins_url( '/css/table.css', __FILE__ ));
    wp_enqueue_style('style', plugins_url( '/css/style.css', __FILE__ ));
}
add_action('admin_print_styles', 'add_book_stylesheet');


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Books_List_Table extends WP_List_Table {
    var $books_data = array(
            array( 'ID' => 1,'id' => 'Quarter Share', 'author' => 'Nathan Lowell', 
                   'isbn' => '978-0982514542' ),
            array( 'ID' => 2, 'id' => '7th Son: Descent','author' => 'J. C. Hutchins',
                   'isbn' => '0312384378' ),
            array( 'ID' => 3, 'id' => 'Shadowmagic', 'author' => 'John Lenahan',
                   'isbn' => '978-1905548927' ),
            array( 'ID' => 4, 'id' => 'The Crown Conspiracy', 'author' => 'Michael J. Sullivan',
                   'isbn' => '978-0979621130' ),
            array( 'ID' => 5, 'id'     => 'Max Quick: The Pocket and the Pendant', 'author'    => 'Mark Jeffrey',
                   'isbn' => '978-0061988929' ),
            array('ID' => 6, 'id' => 'Jack Wakes Up: A Novel', 'author' => 'Seth Harwood',
                  'isbn' => '978-0307454355' )
        );
    function __construct(){
    global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'book', 'bookslist' ),     //singular name of the listed records
            'plural'    => __( 'books', 'bookslist' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
    ) );
    add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }
  function admin_header() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'books_list' != $page )
    return;
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-id { width: 40%; }';
    echo '.wp-list-table .column-author { width: 35%; }';
    echo '.wp-list-table .column-isbn { width: 20%;}';
    echo '</style>';
  }
  function no_items() {
    _e( 'No books found, dude.' );
  }
  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'id':
        case 'author':
        case 'isbn':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }
function get_sortable_columns() {
  $sortable_columns = array(
    'id'  => array('id',false),
    'author' => array('author',false),
    'isbn'   => array('isbn',false)
  );
  return $sortable_columns;
}
function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'id' => __( 'Number', 'bookslist' ),
            'author'    => __( 'Post ID', 'bookslist' ),
            'isbn'      => __( 'ISBN', 'bookslist' )
        );
         return $columns;
    }
function usort_reorder( $a, $b ) {
  // If no sort, default to title
  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
  // If no order, default to asc
  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
  // Determine sort order
  $result = strcmp( $a[$orderby], $b[$orderby] );
  // Send final sort direction to usort
  return ( $order === 'asc' ) ? $result : -$result;
}
function column_id($item){
  $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&book=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
  return sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions) );
}
function get_bulk_actions() {
  $actions = array(
    'delete'    => 'Delete'
  );
  return $actions;
}
function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s" />', $item['ID']
        );    
    }
function prepare_items() {
  $columns  = $this->get_columns();
  $hidden   = array();
  $sortable = $this->get_sortable_columns();
  $this->_column_headers = array( $columns, $hidden, $sortable );
  usort( $this->books_data, array( &$this, 'usort_reorder' ) );
  
  $per_page = 5;
  $current_page = $this->get_pagenum();
  $total_items = count( $this->books_data );
  // only ncessary because we have sample data
  $this->found_data = array_slice( $this->books_data,( ( $current_page-1 )* $per_page ), $per_page );
  $this->set_pagination_args( array(
    'total_items' => $total_items,                  //WE have to calculate the total number of items
    'per_page'    => $per_page                     //WE have to determine how many items to show on a page
  ) );
  $this->items = $this->found_data;
}
} //class
function book_add_menu_items(){
  $hook = add_menu_page( 'List of Books', 'Books List', 'activate_plugins', 'books_list', 'book_render_list_page' );
  add_action( "load-$hook", 'add_options' );
}
function add_options() {
  global $bookslist;
  $option = 'per_page';
  $args = array(
         'label' => 'Books',
         'default' => 10,
         'option' => 'books_per_page'
         );
  add_screen_option( $option, $args );
  $bookslist = new Books_List_Table();
}
add_action( 'admin_menu', 'book_add_menu_items' );
function book_render_list_page(){
  global $bookslist;
  echo '</pre><div class="wrap"><h2>List of Books</h2>'; 
  $bookslist->prepare_items(); 
?>
  <form method="post">
    <input type="hidden" name="page" value="ttest_list_table">
    <?php
    $bookslist->search_box( 'search', 'search_id' );
  $bookslist->display(); 
  echo '</form></div>'; 
}
?>