<?php
/*
Plugin Name: WP Direction Detector
Description: This plugin auto dectects and apply the right direction (RTL or LTR) on post's titles, bodies and comments.
Version: 1.2.1 beta
Author: Fayçal Tirich
Author URI: http://faycaltirich.blogspot.com
Plugin URI: http://faycaltirich.blogspot.com/1979/01/wp-direction-detector.html
*/

Class WP_Direction_Detector {
    function  checkWordDirection($content) {
        $ltrCharchters = 'A-Za-z\x{00C0}-\x{00D6}\x{00D8}-\x{00F6}\x{00F8}-\x{02B8}\x{0300}-\x{0590}\x{0800}-\x{1FFF}\x{2C00}-\x{FB1C}\x{FDFE}-\x{FE6F}\x{FEFD}-\x{FFFF}';
        $rtlCharchters = '\x{0591}-\x{07FF}\x{FB1D}-\x{FDFD}\x{FE70}-\x{FEFC}';
        $pMarks = '\p{P}';
        $space = '\s';
        if (!preg_match("/[^".$space.$pMarks.$rtlCharchters."]/u" , $content)) {
            return 'rtl' ;
        } elseif (!preg_match("/[^".$space.$pMarks.$ltrCharchters."]/u" , $content)) {
            return 'ltr';
        } else {
            return '0 ';
        }
    }
    function getContentDirection($content) {
        $words = explode(' ', $content);
        $level = 0.5;
        $dir = 'ltr';
        $rtlCount = 0;
        foreach ($words as $word) {
            $direction = $this->checkWordDirection($word);
            if($direction=='rtl') {
                $rtlCount++;
            }
        }
        if(($rtlCount/sizeof($words))>$level) {
             $dir  = 'rtl';
        }
        return $dir;
    }
    function processContent($content){
        $new_content = '';
        if(preg_match_all("/(<([\w]+)([^>]*)>)(.*?)(<\/\\2>)/i", $content, $matches, PREG_SET_ORDER)){
            foreach ($matches as $val) {
                if (preg_match("/p|div/i",$val[2])) {
                    $dir = $this->getContentDirection($val[4]);
                    $new_content .= '<'.$val[2]. (($dir=='rtl')?' style="float: right; font-family: tahoma"':'').' dir="'.$this->getContentDirection($val[4]).'" '.$val[3].'>'.$val[4].$val[5].'<div style="clear:both"></div>';
                }
            }
        } else {
            $dir = $this->getContentDirection($content);
            $new_content .= '<div '.(($dir=='rtl')?'style="float: right; font-family: tahoma"':'').' dir="'.$this->getContentDirection($content).'">'.$content.'</div><div style="clear:both"></div>';
        }
        return  $new_content;
    }

    function options_page() {
        $msg = '';
        if ( isset($_POST['wpdd_submit']) ) {
                update_option('wp-direction-detector_titles', stripslashes_deep($_POST['wp-direction-detector_titles']));
                update_option('wp-direction-detector_bodies', stripslashes_deep($_POST['wp-direction-detector_bodies']));
                update_option('wp-direction-detector_comments', stripslashes_deep($_POST['wp-direction-detector_comments']));
                $msg = '<span style="color:green">'.__('Options updated').'</span><br />';
        }
        if(!empty($msg)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$msg.'</p></div>'; }
        ?>
        <div class="wrap">
        <?php screen_icon(); ?>
        <h2>WP Direction Detector</h2>
        <br /><br />
        <form method="post" action="">
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Enable</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="checkbox" name="wp-direction-detector_titles" value="yes" <?php if(get_option('wp-direction-detector_titles')=='yes') echo ' checked="checked"'; ?> />&nbsp;<?php _e(' for Titles'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="wp-direction-detector_bodies" value="yes" <?php if(get_option('wp-direction-detector_bodies')=='yes') echo ' checked="checked"'; ?> />&nbsp;<?php _e(' for Bodies'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="wp-direction-detector_comments" value="yes" <?php if(get_option('wp-direction-detector_comments')=='yes') echo ' checked="checked"'; ?> />&nbsp;<?php _e(' for Comments'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="submit">
                                <input class="button-primary" type="submit" name="wpdd_submit" class="button" value="<?php _e('Save Changes'); ?>" />
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p>
                NB: When your theme is using functions the_title() and get_the_title() inside html attributes (eg: &lt;img alt="&lt;php? the_title(); ?&gt; ...), please don't enable this plugin for titles and use instead the_directed_title() and get_the_directed_title() to display titles.
            </p>
        </form>
        </div>
    <?php
    }
    function add_admin_menu() {
            if (function_exists('add_options_page')) {
                    if( current_user_can('manage_options') ) {
                            add_options_page('Emails Encoder', 'Emails Encoder', 'manage_options', 'fay-emails-encoder', array('Fay_Emails_Encoder', 'options_page')) ;
                    }
            }
    }
}
$wp_dir_detector = new WP_Direction_Detector();

if(get_option('wp-direction-detector_titles')=='yes') {
    add_filter('the_title', array($wp_dir_detector,processContent),99);
}
if(get_option('wp-direction-detector_bodies')=='yes') {
    add_filter('the_content', array($wp_dir_detector,processContent),99);
}
if(get_option('wp-direction-detector_comments')=='yes') {
    add_filter('comment_text', array($wp_dir_detector,processContent),99);
}

function wp_direction_detector_menu() {
    global $wp_dir_detector;
    if (function_exists('add_options_page')) {
        if( current_user_can('manage_options') ) {
            add_options_page('WP Direction Detector', 'WP Direction Detector', 'manage_options', __FILE__, array($wp_dir_detector, options_page)) ;
        }
    }
}

add_action('admin_menu', 'wp_direction_detector_menu');

function get_the_directed_title()
{
    global $wp_dir_detector;
    return $wp_dir_detector->processContent(get_the_title());
}

function the_directed_title()
{
    echo get_the_directed_title();
}

?>