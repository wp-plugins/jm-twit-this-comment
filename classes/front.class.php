<?php
namespace TokenToMe\tweet_this;

class Front {


	public function __construct() {

		add_action( 'comment_post', array( __CLASS__, 'save_comment_meta_data' ) );
		add_filter( 'comment_reply_link', array( __CLASS__, 'insert_link' ) );
		add_filter( 'comment_text', array( __CLASS__, 'show_twit_account' ) );
		add_action( 'comment_form_after_fields', array( __CLASS__, 'add_field' ) );
	}

	/**
	 * @return mixed|string
	 */
	static function twit_this_comment() {
		$id        = get_comment_ID();
		$link      = esc_url( wp_get_shortlink() . '#comment-' . get_comment_ID() );

		$ttc = get_comment_text( stripslashes( trim( $id ) ) );
		$ttc = preg_replace( '/[\p{Z}\s]{2,}/u', ' ', $ttc );
		$author    = __( 'by ', 'jm-ttc' ) . get_comment_author();

		/* making things simplier was the solution */
		$totalChar = 117 - strlen( $author ) - strlen( $link );
		$ttc = '"' . substr( $ttc, 0, $totalChar ) . '..."' . ' ' . $author . ' ' . $link;


		return $ttc;
	}

	/**
	 * add field for non logged users
	 */
	static function add_field() {
		echo '<p class="comment-form-twitter">' .
		     '<label for="twitAccount">' . __( 'Twitter account (optional and without @)', 'jm-ttc' ) . '</label>' .
		     '<input id="twitAccount" name="twitAccount" type="text" size="30" /></p>';
	}

	/**
	 * @param $comment_id
	 */
	static function save_comment_meta_data( $comment_id ) {
		if ( ( isset( $_POST['twitAccount'] ) ) && '' !== $_POST['twitAccount'] ) {
			$twitAccount = wp_filter_nohtml_kses( $_POST['twitAccount'] );
		}
		add_comment_meta( $comment_id, 'twitAccount', $twitAccount );
	}

	/**
	 * @param $text
	 *
	 * @return string
	 */
	static function show_twit_account( $text ) {
		if ( $commenttwitter = get_comment_meta( get_comment_ID(), 'twitAccount', true ) ) {
			$commenttwitter = '<a rel="nofollow"  href="http://twitter.com/intent/user?screen_name=' . esc_html( Utilities::remove_at( $commenttwitter ) ) . '">' . __( 'On Twitter', 'jm-ttc' ) . '</a>';
			$text           = $text . wpautop( $commenttwitter );
		} elseif ( current_user_can( 'read' ) && $usertwitter = get_user_meta( get_current_user_id(), 'twitter', true ) ) {
			$usertwitter = '<a rel="nofollow"  href="http://twitter.com/intent/user?screen_name=' . esc_html( Utilities::remove_at( $usertwitter ) ) . '">' . __( 'On Twitter', 'jm-ttc' ) . '</a>';
			$text        = $text . wpautop( $usertwitter );
		}

		return $text;

	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	static function insert_link( $content ) {

		$content = $content . ' <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( self::twit_this_comment() ) . '" class="jm_ttc comment-reply-link">' . __( 'Tweet this comment', 'jm-ttc' ) . '&rarr;</a>';

		if ( $commenttwitter = get_comment_meta( get_comment_ID(), 'twitAccount', true ) ) {
			$content = $content . ' <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( self::twit_this_comment() ) . '&amp;via=' . esc_html( Utilities::remove_at( $commenttwitter ) ) . '" class="jm_ttc comment-reply-link">' . __( 'Tweet this comment', 'jm-ttc' ) . '&rarr;</a>';
		} elseif ( current_user_can( 'read' ) && ( $usertwitter = get_user_meta( get_current_user_id(), 'twitter', true ) ) ) {
			$content = $content . ' <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( self::twit_this_comment() ) . '&amp;via=' . esc_html( Utilities::remove_at( $usertwitter ) ) . '" class="jm_ttc comment-reply-link">' . __( 'Tweet this comment', 'jm-ttc' ) . '&rarr;</a>';
		}

		return $content;
	}

}