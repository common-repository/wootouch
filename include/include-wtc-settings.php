<?php
class WTC_Settings {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_Settings
	 */
	 public $options;
	 
	public static function get() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->hooks();
		
	}

	/**
	 * Hooks
	 */
	private function hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'wp_ajax_wtc_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_stylesheet'));
	}
	
	public function add_stylesheet(){
	wp_enqueue_style( 'wtc-admin', plugin_dir_url( WTC_PLUGIN_FILE ) . 'assets/css/style.css');
	}
	/**
	 * Add menu pages
	 */
	public function add_menu_pages() {
		add_menu_page( 'Overview', 'Wootouch', 'manage_options', 'wtc', array( $this, 'screen_main' ));
	}
		 
	/**
	 * Save settings via AJAX
	 *
	 */
		public function save_settings() {

		// Security check
		check_ajax_referer( 'wtc-ajax-security', 'ajax_nonce' );
		
		// Permission check
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '0';
			exit;
		}

		// Get options
		$options = WooTouch::get()->get_options();

		// Setup variables
		$fields = explode( ',', $_POST['fields'] );
		$custom = explode( ',', $_POST['custom'] );

		// Update options
		$options['get_posts'][$_POST['post_type']] = array(
			'enabled' => $_POST['enabled'],
			'fields'  => $fields,
			'custom'  => $custom
			
		);
		
		// Save webservice
		WooTouch::get()->save_options( $options );

		exit;
	}
	/************************/


	/**
	 * The main screen
	 */
	public function screen_main() {
		
		if(function_exists( 'wp_enqueue_media' )){
    	wp_enqueue_media();
		}else{
		
		    wp_enqueue_style('thickbox');
		    wp_enqueue_script('media-upload');
		    wp_enqueue_script('thickbox');
		    
		}
?>
<script>
    jQuery(document).ready(function($) {
		
        $('.screen_logo_upload').click(function(e) {
            e.preventDefault();

            var custom_uploader = wp.media({
                title: 'Custom Image',
                button: {
                    text: 'Upload Image'
                },
                multiple: false  // Set this to true to allow multiple files to be selected
            })
            .on('select', function() {
			$('#loadingmessage').show();
			$('.after').hide();
            var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.screen_logo').attr('src', attachment.url).load(function() {
			$('#loadingmessage').hide(); $('.after').show(); });
            //$('.screen_logo').attr('src', attachment.url);
                $('.screen_logo_url').val(attachment.url);

            })
            .open();
        });
    });
</script>
		<?php
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'wtc_settings'; 
  if (!empty($_POST['screen_logo'])) {
    $image_url = $_POST['screen_logo'];
   	$sql = $wpdb->prepare("UPDATE $table_name SET `image_url`='".$image_url."' Where `id`=1");
	$wpdb->query($sql);
  }
  
  if (!empty( $_POST['mv_cr_section_color'])) {
    $back_color = $_POST['mv_cr_section_color'];
    $sql1 = $wpdb->prepare("UPDATE $table_name SET `back_color`='".$back_color."' Where `id`=1");
	$wpdb->query($sql1);
  }
  if (!empty( $_POST['font_color'])) {
    $font_color = $_POST['font_color'];
	$sql2 = $wpdb->prepare("UPDATE $table_name SET `font_color`='".$font_color."' Where `id`=1");
	$wpdb->query($sql2);
  } 
  if (!empty( $_POST['editor-terms'])) {
    $editor_terms = $_POST['editor-terms'];
	$sql3 = $wpdb->prepare("UPDATE $table_name SET `terms`='".$editor_terms."' Where `id`=1");
	$wpdb->query($sql3);
  }
  if (!empty( $_POST['editor-contact'])) {
    $editor_contact = $_POST['editor-contact'];
	$sql4 = $wpdb->prepare("UPDATE $table_name SET `contact`='".$editor_contact."' Where `id`=1");
	$wpdb->query($sql4);
  }
  
	$sql_all = "SELECT * FROM $table_name Where id=1";
	$results = $wpdb->get_results($sql_all); 
	
    foreach( $results as $result ) {
		
        $bk_color =  $result->back_color;
		$img_url =  $result->image_url;
        $fnt_color =  $result->font_color;
        $terms = $result->terms;
		$contact = $result->contact;
	}
?>
		<div class="nowrap" id="wws-wrap">
			<?php $default_url =  plugin_dir_url( WTC_PLUGIN_FILE ) . 'images/logo.png'; ?>
			<div class="screen">
			<?php
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
            if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
            ?>
				<h2 class="nav-tab-wrapper">
				  <a  href="?page=wtc&amp;tab=overview" class="nav-tab <?php echo $active_tab == 'overview' ? 'nav-tab-active' : ''; ?>" id="nav-tab1">Overview</a>
				  <a  href="?page=wtc&amp;tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>" id="nav-tab2">Settings</a>
				  <a  href="?page=wtc&amp;tab=terms" class="nav-tab <?php echo $active_tab == 'terms' ? 'nav-tab-active' : ''; ?>" id="nav-tab3">Terms & conditions</a>
				  <a  href="?page=wtc&amp;tab=contact" class="nav-tab <?php echo $active_tab == 'contact' ? 'nav-tab-active' : ''; ?>" id="nav-tab4">Contact</a>
				</h2>
			<form method="post">
				<?php if($active_tab == 'overview') { ?>
				<div id="Overview" class="w3-container overview">
					<p>Wootouch is Woocommerce plugin. It create the bridge between the mobile Application and Website. Whatever changes done from Woocommere Admin Interface ,those changes will be display immediatly on Applicaiton.</p>
				 <h4>Wootouch FEATURES</h4>
				<ul class="feature">
					<li>Flattering Design for the Woocommerce</li>
					<li>Change the UI of Application.</li>
					<li>It Covers All by default features of Woocommerce.</li>
					<li>Categries, Sub categires, Product , Product Details</li>
					<li>Checkout and Payment method.</li>
					<li>Order List & Order Details</li>
					<li>Always Allow to upgrade</li>
					<li>Native applications</li>
				</ul>
				</div>
				<?php } ?>
				<?php  if($active_tab == 'settings') { ?>
				<div id="Settings" class="w3-container overview">
				<script>
					jQuery(document).ready(function($) {   
						$('#mv_cr_section_color').wpColorPicker({palettes: false, width: 200});
						$('#font_color').wpColorPicker({palettes: false, width: 200});
					});
				</script>
				<form action="#" name="splash_screen" method="post">
				<h2>All Settings:</h2>
               <div class="show_txt">
				<p><b>Upload Logo :</b></p>
				<input class="screen_logo_url" type="text" name="screen_logo" size="25" value="<?php if(get_option('screen_logo')){echo get_option('screen_logo'); }elseif($img_url != ''){ echo $img_url; }else{ echo $default_url; }?>">
				 <a href="#" class="screen_logo_upload button-primary">Upload Image</a><p><label><span class="description">Upload your Image from here.</span></label></p>
				</div>
				<div class="show_upload">
                <div id='loadingmessage' style='display:none'>
					<img class="load" src='<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>/images/loding_image.gif' height="100" width="100" />
				</div>
				<div class="after">
					<img class="screen_logo" src="<?php if(get_option('screen_logo')){echo get_option('screen_logo'); }elseif($img_url != ''){ echo $img_url; }else{ echo $default_url; }?>" height="100" width="100"/>
                </div>
                </div>
                
                <?php
					wp_enqueue_script('wp-color-picker');
					wp_enqueue_style( 'wp-color-picker' );
				?>				
                <div class="show_back_color">
				<p><b>Background Color :</b></p>
				<input name="mv_cr_section_color" type="text" id="mv_cr_section_color" value="<?php if(!empty($bk_color)){ echo $bk_color;} else{ echo "#96588a"; } ?>" data-default-color="#96588a">
				</div>
				<div class="show_font_color">
				<p><b>Font Color :</b></p>
				<input name="font_color" type="text" id="font_color" value="<?php if(!empty($fnt_color)){ echo $fnt_color;}else{ echo "#ffffff"; } ?>" data-default-color="#ffffff">
				</div>
				
				<?php echo submit_button( __( 'Save Setting', 'WTC' ) ); ?>
				<?php do_action( 'wtc_general_settings' ); ?>
				</form>
				</div>
				<?php } ?>
				<?php  if($active_tab == 'terms') { ?>
				<div id="Terms" class="w3-container overview">
					<form action="#" name="terms_desc" method="post">
					<?php
					$edit_id = 'editor-terms';
					$setting = array( 'media_buttons' => false,'editor_height' => '200px','textarea_rows' => 20 );
					$contentone = $terms;
					wp_editor( $contentone, $edit_id, $setting );
					?>
					<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?> 
					</form>
					
				</div>
				<?php } ?>
				<?php  if($active_tab == 'contact') { ?>
				<div id="Contact" class="w3-container overview">
				<form action="#" name="contact_info" method="post">
				<?php
					$editor_id = 'editor-contact';
					$settings = array( 'media_buttons' => false,'editor_height' => '200px' );
					$content = $contact;
					wp_editor( $content, $editor_id, $settings );
				?>
				<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?> 
				</form>
				</div>
				<?php } ?>
			</div>
			</form>
		</div>
	<?php
	WooTouch::get();
	}
}
