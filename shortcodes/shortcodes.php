<?php
// functions to support shortcodes (ie. [myshortcode]) to incorporate
// the output of custom php functions in wordpress blog messages and
// pages.

// upstream javascript and css files from
// https://github.com/asciinema/asciinema-player
$asciinemaplayer_reldir = "upstream";
$asciinemaplayer_cssfile = "asciinema-player.css";
$asciinemaplayer_jsfile = "asciinema-player.js";
$asciinemaplayer_dir = '/' . dirname( plugin_basename( __FILE__ )) . '/' . $asciinemaplayer_reldir;

wp_register_style(
    'asciinema',
    $asciinemaplayer_dir . '/' . $asciinemaplayer_cssfile
);
wp_register_script(
    'asciinema',
    $asciinemaplayer_dir . '/' . $asciinemaplayer_jsfile
);
wp_enqueue_style('asciinema');
wp_enqueue_script( 'asciinema');

function lacocinawordpressplugin_init()
{
    function lacocinawordpressplugin_display_commentscount($atts)
    {
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
    };

    function lacocinawordpressplugin_display_githubissuescount($atts)
    {
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
    };

    function lacocinawordpressplugin_return_asciinemaelement($atts = [], $content = null, $tag = '')
    {
        // returns a asciinema html element
        // needs 
        $def_theme = "solarized-dark";
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);
        $shortcode_attrs = shortcode_atts([
            'autoplay' => '',
            'cols' => '',
            'loop' => '',
            'poster' => '1:00',
            'preload' => '',
            'rows' => '',
            'speed' => '1',  
            'src' => '',
            'startat' => '',
            'theme' => $def_theme,
            'time' => '',
            'title' => '',
        ], $atts, $tag);
        $element = '<!-- asciinema-shortcodes: empty src attribute supplied! -->';
        if ( !$shortcode_attrs['src'] )
            return $element;
        
        $element = '<asciinema-player';
        $element .= add_html_attr('src', $shortcode_attrs['src']);
        if ( $shortcode_attrs['poster'] )
            $element .= add_html_attr(
                'poster',
                'npt:' .
                str_replace(
                    'npt:', '', $shortcode_attrs['poster']
                )
            );
        if ( $shortcode_attrs['theme'] )
            $element .= add_html_attr('theme', $shortcode_attrs['theme']);
        if ( $shortcode_attrs['speed'] )
            $element .= add_html_attr('speed', $shortcode_attrs['speed']);
        if ( $shortcode_attrs['cols'] )
            $element .= add_html_attr('cols', $shortcode_attrs['cols']);
        if ( $shortcode_attrs['rows'] )
            $element .= add_html_attr('rows', $shortcode_attrs['rows']);
        if ( $shortcode_attrs['preload'] )
            $element .= add_html_attr('preload', $shortcode_attrs['preload']);
        if ( $shortcode_attrs['loop'] )
            $element .= add_html_attr('loop', $shortcode_attrs['loop']);
        if ( $shortcode_attrs['autoplay'] )
            $element .= add_html_attr('autoplay', $shortcode_attrs['autoplay']);
        if ( $shortcode_attrs['time'] )
            $element .= add_html_attr('time', $shortcode_attrs['time']);
        if ( $shortcode_attrs['startat'] )
            $element .= add_html_attr('start-at', $shortcode_attrs['startat']);
        $element .= '></asciinema-player>';
        // $element .= '<script src="/asciinema-player.js"></script>';
        return $element;
    };

    add_shortcode( 'githubissuescount', 'lacocinawordpressplugin_display_githubissuescount' );
    add_shortcode( 'commentscount', 'lacocinawordpressplugin_display_commentscount' );
    add_shortcode( 'asciinema', 'lacocinawordpressplugin_return_asciinemaelement' );
}


function add_html_attr($attribute, $raw_value)
{
    // returns html attribute with html escaped raw_value
    if ( !$raw_value)
        return '';
    $val = esc_html__($raw_value);
    $html = ' ' . $attribute . '="' . $val . '"';
    return $html;
}

add_action('init', 'lacocinawordpressplugin_init');

?>