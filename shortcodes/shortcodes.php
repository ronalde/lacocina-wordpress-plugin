<?php

function lacocinawordpressplugin_display_commentscount() {
    // return the number of comments on the current page

    $msg_no_comments_found = '(none)';
    $msg_no_comments_available = '(unavailable)';
    $num_comments = get_comments_number(); // get_comments_number returns only a numeric value

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


function lacocinawordpressplugin_display_githubissuescount() {
    // return the number of open issues on a github project using it's API

    $gh_api_url ='https://api.github.com';
    $gh_user = 'ronalde';
    $gh_repo = 'mpd-configure';
    $gh_issuetype = 'bug';
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