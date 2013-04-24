<?php
/*
Plugin Name: JM Twit This Comment
Plugin URI: http://tweetpress.fr/
Description:  Sometimes you read amazing comments that are even worthier than entire post so tweet it ^^
Author: JUlien Maury
Author URI: http://tweetpress.fr
Version: 1.2.0
License: GPL2++

Copyright 2013 Julien Maury

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// TINYURL
function jm_ttc_getTinyUrl($url) { 
 $tiny = 'http://tinyurl.com/api-create.php?url=';
    $tinyhandle = fopen($tiny.urlencode(trim($url)), "r");
    $tinyurl = fread($tinyhandle, 26);
    fclose($tinyhandle);
    return $tinyurl;
}

// GRABS DATA FROM COMMENT
function jm_twit_this_comment(){
$jm_tc_ttc = "";
$jm_ttc_id = get_comment_ID();
$jm_ttc_link = get_comment_link();
$jm_ttc_data = get_comment($jm_ttc_id , ARRAY_A);
$jm_tc_ttc = get_comment_text(stripslashes(trim($jm_ttc_id)));
$jm_ttc_author = get_comment_author();

$jm_ttc_tinyUrl =  jm_ttc_getTinyUrl($jm_ttc_link);

if(strlen($jm_tc_ttc)<116){
$jm_tc_ttc = '"'.$jm_tc_ttc . '" ' . $jm_ttc_tinyUrl;
} elseif('' === get_comment_meta( get_comment_ID(), 'twitAccount', true)) {
$jm_ttc_author = __('by ','jm-ttc') . $jm_ttc_author;
$totalChar = 113 - strlen($jm_ttc_author) - strlen($jm_tc_ttc) - strlen($jm_ttc_tinyUrl);
$jm_tc_ttc = '"'. substr($jm_tc_ttc,0,$totalChar).'..." '. $jm_ttc_author .' '. $jm_ttc_tinyUrl;
} else {
$jm_tc_ttc = '"'. substr($jm_tc_ttc,0,111) . '... "' . $jm_ttc_tinyUrl;
}

return $jm_tc_ttc;
}

// ADD A FIELD FOR TWITTER TO YOUR COMMENT FORM
add_action( 'comment_form_after_fields', 'jm_ttc_add_field' );

function jm_ttc_add_field () {
echo '<p class="comment-form-twitter">'.
'<label for="twitAccount">' . __( 'Twitter account (optional and without @)','jm-ttc' ) . '</label>'.
'<input id="twitAccount" name="twitAccount" type="text" size="30" /></p>';
}

// SAVE OUR COMMENT DATA
add_action( 'comment_post', 'jm_ttc_save_comment_meta_data' );
function jm_ttc_save_comment_meta_data( $comment_id ) {
if ( ( isset( $_POST['twitAccount'] ) ) && ( $_POST['twitAccount'] != '') )
$twitAccount = wp_filter_nohtml_kses($_POST['twitAccount']);
add_comment_meta( $comment_id, 'twitAccount', $twitAccount );
}


// ADD TWIT ACCOUNT TO COMMENT
add_filter( 'comment_text', 'jm_ttc_show_twit_account');
function jm_ttc_show_twit_account( $text ){
if( $commenttwitter = get_comment_meta( get_comment_ID(), 'twitAccount', true ) ) {
$commenttwitter = '<p class="twitAccount"><a href="http://twitter.com/intent/user?screen_name=' . esc_attr( $commenttwitter ) . '">'.__('On Twitter','jm-ttc').'</a><p>';
$text = $text .  $commenttwitter ;
return $text;
} else {
return $text;
}

}

// ADD FIELD IN EDIT SCREEN (THINK ABOUT MODERATION)
add_action( 'add_meta_boxes_comment', 'jm_ttc_add_meta_box' );
function jm_ttc_add_meta_box() {
add_meta_box( 'title', __( 'Twitter account (Jwit This Comment)','jm-ttc' ), 'jm_ttc_meta_box', 'comment', 'normal', 'high' );
}

function jm_ttc_meta_box ( $comment ) {
$twitAccount = get_comment_meta( $comment->comment_ID, 'twitAccount', true );
wp_nonce_field( 'jm_ttc_update', 'jm_ttc_update', false );
?>
<p>
<label for="twitAccount"><?php _e( 'Twitter account','jm-ttc' ); ?></label>
<input type="text" name="twitAccount" value="<?php echo esc_attr( $twitAccount ); ?>" class="widefat" />
</p>

<?php
}

// UPDATE DATAS FROM EDIT SCREEN
add_action( 'edit_comment', 'jm_ttc_edit_metafields' );

function jm_ttc_edit_metafields( $comment_id ) {
if( ! isset( $_POST['jm_ttc_update'] ) || ! wp_verify_nonce( $_POST['jm_ttc_update'], 'jm_ttc_update' ) ) return;

if ( ( isset( $_POST['twitAccount'] ) ) && ( $_POST['twitAccount'] != '') ) :
$twitAccount = wp_filter_nohtml_kses($_POST['twitAccount']);
update_comment_meta( $comment_id, 'twitAccount', $twitAccount );
else :
delete_comment_meta( $comment_id, 'twitAccount');
endif;
}

// ADD OUR TWIT LINK BESIDE REPLY LINK
function jm_ttc_insert_link($content) {
if( $commenttwitter = get_comment_meta( get_comment_ID(), 'twitAccount', true ) ) {
$content = $content . '| <a href="http://twitter.com/intent/tweet?text=' . urlencode( jm_twit_this_comment() ) . '&amp;via='. $commenttwitter .'" class="jm_ttc comment-reply-link">'. __('Tweet this comment','jm-ttc'). '&rarr;</a>';	
} else {
$content = $content . '| <a href="http://twitter.com/intent/tweet?text=' . urlencode( jm_twit_this_comment() ) . '" class="jm_ttc comment-reply-link">'. __('Tweet this comment','jm-ttc'). '&rarr;</a>';
}
return $content;
}
add_filter('comment_reply_link', 'jm_ttc_insert_link');


// LANGUAGE SUPPORT
add_action( 'init', 'jm_ttc_lang_init' );
function jm_ttc_lang_init() {
load_plugin_textdomain( 'jm-ttc', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
