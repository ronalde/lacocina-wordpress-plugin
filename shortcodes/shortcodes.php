<?php
// functions to support shortcodes (ie. [myshortcode]) to incorporate
// the output of custom php functions in wordpress blog messages and
// pages.

function lacocinawordpressplugin_display_commentscount($atts) {
    // return the number of comments on the current page

    // provide optional attributes:
    // [commentscount nocomments="nothing found" unavailable="not enabled"]
    $atts = shortcode_atts( array(
		'nocomments' => '(none)',
		'unavailable' => '(unavailable)'
	), $atts, 'lc_commentscount' );
    
    $msg_no_comments_found = "{$atts['nocomments']}";
    $msg_no_comments_available = "{$atts['unavailable']}";
    // get_comments_number returns only a numeric value
    $num_comments = get_comments_number(); 
    if ( comments_open() ) {
        if ( $num_comments == 0 ) {
            $comments = __($msg_no_comments_found);
        } else {
            $comments = $num_comments;
        }
        $write_comments = $comments;
    } else {
        $write_comments =  __($msg_no_comments_available);
    }
    return $write_comments;
}


function lacocinawordpressplugin_display_githubissuescount($atts) {
    // return the number of open issues on a github project using it's API

    // provide optional attributes:
    // [commentscount nocomments="nothing found" unavailable="not enabled"]
    $atts = shortcode_atts( array(
		'user' => 'ronalde',
		'project' => 'lacocina-wordpress-plugin',
		'issuelabel' => 'bug'        
	), $atts, 'githubissuecount' );
    
    $gh_api_url ='https://api.github.com';
    $gh_user = "{$atts['user']}";
    $gh_repo = "{$atts['project']}";
    $gh_issuelabel = "{$atts['issuelabel']}";
    $gh_url = $gh_api_url . '/repos/' . $gh_user . '/' . $gh_repo;
    //$gh_url = $gh_api_url . '/repos/' . $gh_user . '/' . $gh_repo . '/labels/' . $gh_issuetype ;
    //$gh_url = $gh_api_url . '/users/ronalde';
    $gh_response = wp_remote_get( $gh_url );
    $gh_jsonbody = wp_remote_retrieve_body( $gh_response );
    $gh_jsonobj = json_decode( $gh_jsonbody, true );
     //return var_dump( $gh_jsonobj );
    return $gh_jsonobj['open_issues_count'];
}


add_shortcode( 'githubissuescount', 'lacocinawordpressplugin_display_githubissuescount' );
add_shortcode( 'commentscount', 'lacocinawordpressplugin_display_commentscount' );
?>