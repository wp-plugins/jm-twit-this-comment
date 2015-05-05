<?php
namespace TokenToMe\tweet_this;

class Admin {

	public function __construct() {

		add_action( 'personal_options_update', array( __CLASS__, 'save_extra_user_profile_field' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_extra_user_profile_field' ) );
		add_filter( 'user_profile_update_errors', array( __CLASS__, 'check_arobase' ), 10, 3 );
		add_action( 'show_user_profile', array( __CLASS__, 'add_profile_field' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'add_profile_field' ) );

		add_action( 'edit_comment', array( __CLASS__, 'edit_metafields' ) );
		add_action( 'add_meta_boxes_comment', array( __CLASS__, 'add_meta_box' ) );
	}

	static function add_meta_box() {
		add_meta_box( 'title', __( 'Twitter account (Twit This Comment)', 'jm-ttc' ), 'jm_ttc_meta_box', 'comment', 'normal', 'high' );
	}

	/**
	 * @param $comment
	 */
	static function meta_box( $comment ) {
		$twitAccount = get_comment_meta( $comment->comment_ID, 'twitAccount', true );
		wp_nonce_field( 'jm_ttc_update', 'jm_ttc_update', false );
		?>
		<p>
			<label for="twitAccount"><?php esc_html_e( 'Twitter account', 'jm-ttc' ); ?></label>
			<input type="text" name="twitAccount" value="<?php echo esc_attr( $twitAccount ); ?>" class="widefat"/>
		</p>

	<?php
	}

	/**
	 * @param $comment_id
	 */
	static function edit_metafields( $comment_id ) {
		if ( ! isset( $_POST['jm_ttc_update'] ) || ! wp_verify_nonce( $_POST['jm_ttc_update'], 'jm_ttc_update' ) ) {
			return;
		}

		if ( ( isset( $_POST['twitAccount'] ) ) && ( $_POST['twitAccount'] != '' ) ) :
			$twitAccount = wp_filter_nohtml_kses( $_POST['twitAccount'] );
			update_comment_meta( $comment_id, 'twitAccount', $twitAccount );
		else :
			delete_comment_meta( $comment_id, 'twitAccount' );
		endif;
	}

	/**
	 * @param $user
	 */
	static function add_profile_field( $user ) {
		wp_nonce_field( 'twitter_field_update', 'twitter_field_update', false );
		?>
		<h3><?php esc_html_e( 'Twit This Comment !', 'jm-ttc' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="twitter"><?php esc_html_e( 'Twitter Account', 'jm-ttc' ); ?></label></th>
				<td>
					<input type="text" name="twitter" id="twitter"
					       value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>"
					       class="regular-text"/><br/>
					<span class="description"><?php esc_html_e( 'Please enter your Twitter Account (without @)', 'jm-ttc' ); ?></span>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * @param $user_id
	 *
	 * @return bool
	 */
	static function save_extra_user_profile_field( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['twitter_field_update'] ) || ! wp_verify_nonce( $_POST['twitter_field_update'], 'twitter_field_update' ) ) {
			return false;
		}
		$ttc_twit = wp_filter_nohtml_kses( $_POST['twitter'] );
		update_user_meta( $user_id, 'twitter', $ttc_twit );
	}

	/**
	 * @param $errors
	 * @param $update
	 * @param $user
	 */
	static function check_arobase( $errors, $update, $user ) {
		if ( $update ) {
			if ( preg_match( '/ +/', $_POST['twitter'] ) || preg_match( ' ', $_POST['twitter'] ) ) {
				$errors->add( 'twitter', __( 'Wait ! Do not leave spaces in your Twitter account please.', 'jm-ttc' ), array( 'form-field' => 'Twitter for Comments' ) );
			} else {
				update_user_meta( $user->ID, 'twitter', esc_html( Utilities::remove_at( $_POST['twitter'] ) ) );
			}
		}
	}


}