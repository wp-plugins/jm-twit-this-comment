<?php
/*
Plugin Name: JM Twit This Comment
Plugin URI: http://tweetpress.fr/
Description:  Sometimes you read amazing comments that are even worthier than entire post so tweet it ^^
Author: JUlien Maury
Author URI: http://tweetpress.fr
Version: 1.3.4
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


// Remove any @ from input value
function jm_ttc_remove_at($at) { 
$noat = str_replace('@','',$at);
return $noat;
}


// GRABS DATA FROM COMMENT
function jm_twit_this_comment(){
$jm_tc_ttc = '';
$jm_ttc_id = get_comment_ID();
$jm_ttc_link = get_comment_link();
$jm_ttc_data = get_comment($jm_ttc_id , ARRAY_A);

$jm_tc_ttc = get_comment_text(stripslashes(trim($jm_ttc_id)));
$jm_tc_ttc = preg_replace('/[\p{Z}\s]{2,}/u', ' ', $jm_tc_ttc );//remove all spaces cf http://tweetpress.fr/codewp/enlever-espaces-doubles-wordpress/
$jm_ttc_author = __('by ','jm-ttc') . get_comment_author();

/* making things simplier was the solution */
$totalChar = 117 - strlen($jm_ttc_author) - strlen($jm_ttc_link); //118 is the new limit for tweets with URL
$jm_tc_ttc = '"'. substr($jm_tc_ttc,0,$totalChar) . '..."' .' '. $jm_ttc_author .' '. $jm_ttc_link;


return $jm_tc_ttc;
}

// ADD A FIELD FOR TWITTER TO YOUR COMMENT FORM
add_action( 'comment_form_after_fields', 'jm_ttc_add_field' );

function jm_ttc_add_field () {
echo '<p class="comment-form-twitter">'.
'<label for="twitAccount">' . __( 'Twitter account (optional and without @)','jm-ttc' ) . '</label>'.
'<input id="twitAccount" name="twitAccount" type="text" size="30" /></p>';
}

// ADD FIELD IN PROFILES FOR REGISTERED USERS IN CASE YOUR BLOG HAS MEMBERS
add_action( 'show_user_profile', 'jm_ttc_add_profile_field' );
add_action( 'edit_user_profile', 'jm_ttc_add_profile_field' );

function jm_ttc_add_profile_field($user) {
wp_nonce_field( 'jm_ttc_twitter_field_update', 'jm_ttc_twitter_field_update', false );
?>
<h3><?php _e("Twit This Comment !","jm-ttc");?></h3>	
<table class="form-table">
<tr>
<th><label for="jm_ttc_twitter"><?php _e("Twitter Account", "jm-ttc"); ?></label></th>
<td>
<input type="text" name="jm_ttc_twitter" id="jm_ttc_twitter" value="<?php echo esc_attr( get_the_author_meta( 'jm_ttc_twitter', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your Twitter Account (without @)", "jm-ttc"); ?></span>
</td>
</tr>
</table>
<?php
}

// SAVE OUR DATAS IN USER PROFILE
add_action( 'personal_options_update', 'jm_ttc_save_extra_user_profile_field', 10,1 );
add_action( 'edit_user_profile_update', 'jm_ttc_save_extra_user_profile_field',10,1 );

function jm_ttc_save_extra_user_profile_field( $user_id ) {
if( !current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['jm_ttc_twitter_field_update'] ) || ! wp_verify_nonce( $_POST['jm_ttc_twitter_field_update'], 'jm_ttc_twitter_field_update' ) ) { return false; }
$ttc_twit = wp_filter_nohtml_kses($_POST['jm_ttc_twitter']);
update_user_meta( $user_id, 'jm_ttc_twitter', $ttc_twit );
}

// APPLY A FILTER ON INPUT TO DELETE ANY @ 
add_filter('user_profile_update_errors','jm_ttc_check_arobase', 10, 3); // wp-admin/includes/users.php, thanks Greglone for this great hint
function jm_ttc_check_arobase($errors, $update, $user)  {
if($update) {  
// do the error handling here
if( preg_match('/ +/',$_POST['jm_ttc_twitter'] ) || preg_match(' ',$_POST['jm_ttc_twitter'] ) ) {
$errors->add('jm_ttc_twitter', __('Wait ! Do not leave spaces in your Twitter account please.','jm-ttc'), array('form-field' => 'Twitter for Comments'));
}
else {
//let's save it but in case there's a @ just remove it before saving
update_user_meta($user->ID, 'jm_ttc_twitter', jm_ttc_remove_at($_POST['jm_ttc_twitter']) );
}
}
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
$commenttwitter = '<p class="twitAccount"><a rel="nofollow"  href="http://twitter.com/intent/user?screen_name=' . esc_html(jm_ttc_remove_at($commenttwitter))  . '">'.__('On Twitter','jm-ttc').'</a></p>';
$text = $text .  $commenttwitter ;
return $text;
} elseif( current_user_can( 'read') && $usertwitter = get_user_meta( get_current_user_id(), 'jm_ttc_twitter', true ) ) {
$usertwitter = '<p class="twitAccount"><a rel="nofollow"  href="http://twitter.com/intent/user?screen_name=' . esc_html(jm_ttc_remove_at($usertwitter)). '">'.__('On Twitter','jm-ttc').'</a></p>';
$text = $text .  $usertwitter ;
return $text;
} else {
return $text;
}

}


// ADD FIELD IN EDIT SCREEN (THINK ABOUT MODERATION)
add_action( 'add_meta_boxes_comment', 'jm_ttc_add_meta_box' );
function jm_ttc_add_meta_box() {
add_meta_box( 'title', __( 'Twitter account (Twit This Comment)','jm-ttc' ), 'jm_ttc_meta_box', 'comment', 'normal', 'high' );
}

function jm_ttc_meta_box ( $comment ) {
$twitAccount = get_comment_meta( $comment->comment_ID, 'twitAccount', true );
wp_nonce_field( 'jm_ttc_update', 'jm_ttc_update', false );
?>
<p>
<label for="twitAccount"><?php _e( 'Twitter account','jm-ttc' ); ?></label>
<input type="text" name="twitAccount" value="<?php echo esc_attr($twitAccount); ?>" class="widefat" />
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

// ADD OUR TWIT LINK BESIDE REPLY LINK (I know code can be shorter but this is for lisibility and perhaps customization)
function jm_ttc_insert_link($content) {
if( $commenttwitter = get_comment_meta( get_comment_ID(), 'twitAccount', true ) ) {
$content = $content . '| <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( jm_twit_this_comment() ) . '&amp;via='. esc_html(jm_ttc_remove_at($commenttwitter)) .'" class="jm_ttc comment-reply-link">'. __('Tweet this comment','jm-ttc'). '&rarr;</a>';	
} elseif ( current_user_can('read') && ($usertwitter = get_user_meta( get_current_user_id() , 'jm_ttc_twitter', true )) )   { 
$content = $content . '| <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( jm_twit_this_comment() ) . '&amp;via='. esc_html(jm_ttc_remove_at($usertwitter)) .'" class="jm_ttc comment-reply-link">'. __('Tweet this comment','jm-ttc'). '&rarr;</a>';
}
else {
$content = $content . '| <a rel="nofollow" href="http://twitter.com/intent/tweet?text=' . urlencode( jm_twit_this_comment() ) . '" class="jm_ttc comment-reply-link">'. __('Tweet this comment','jm-ttc'). '&rarr;</a>';
}
return $content;
}
add_filter('comment_reply_link', 'jm_ttc_insert_link');


// LANGUAGE SUPPORT
add_action( 'init', 'jm_ttc_lang_init' );
function jm_ttc_lang_init() {
load_plugin_textdomain( 'jm-ttc', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
