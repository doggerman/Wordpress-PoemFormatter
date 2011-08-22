<?php
// vim: set ts=4 et nu ai syntax=php indentexpr= ff=unix :vim
/*
Plugin Name: poem formatter
# Plugin URI: http://wordpress.org/extend/plugins/gpx2chart/
Description: poem formatter - a WP-Plugin that allows to format poems properly
Version: 0.0.1
Author: Walter Werther
Author URI: http://wwerther.de/
# Update Server: http://downloads.wordpress.org/plugin
Min WP Version: 3.2.0
 */


/* require_once(dirname(__FILE__).'/ww_gpx.php'); */

if (! defined('POEMFORMATTER_SHORTCODE')) define('POEMFORMATTER_SHORTCODE','poem');

class POEMFORMATTER {
   
    static $debug=1;

    static function debug ($text,$headline='') {
       if (self::$debug) {
            return "\n<!-- poemformatter $headline\n$text\n poemformatter -->\n";
       }
        return '';
    }
 
	public static function init() {
		add_shortcode(POEMFORMATTER_SHORTCODE, array(__CLASS__, 'handle_shortcode'));

        if (self::$debug) {
#            wp_register_script('excanvas', plugins_url('/js/flot/excanvas.js',__FILE__), array('jquery'), '2.1.4', false);
        } else {
#            wp_register_script('strftime', "http://hacks.bluesmoon.info/strftime/strftime.js",__FILE__) ;
        }
        wp_enqueue_style('POEMFORMATTER', plugins_url('css/poem_formatter.css',__FILE__), false, '1.0.0', 'screen');
        wp_enqueue_style('POEMFORMATTER2', plugins_url('poem_formatter/css/poem_formatter.css'), false, '1.0.0', 'screen');
	}


	public static function formattime($value) {
            return strftime('%H:%M:%S',$value);
	}

/*
 * Our shortcode-Handler for Poems
 * It provides support for the necessary parameters that are defined in
 * http://codex.wordpress.org/Shortcode_API
 */
	public static function handle_shortcode( $atts, $content=null, $code="" ) {
        // $atts    ::= array of attributes
        // $content ::= text within enclosing form of shortcode element
        // $code    ::= the shortcode found, when == callback name
        //           [gpx2chart href="<GPX-Source>" (maxelem="51") (debug) (width="90%") (metadata="heartrate cadence distance speed") (display="heartrate cadence elevation speed")]

        /* Check if we are in "debug mode". Create a more verbose output then */
        self::$debug=self::$debug ? self::$debug : in_array('debug',$atts);

        /* 
         * Evaluate optional attributes 
         */
        $title=array_key_exists('title',$atts) ? $atts['title'] : 'no title';
        $indent=array_key_exists('indent',$atts) ? intval($atts['indent']) : 50;
        $align=array_key_exists('align',$atts) ? $atts['align'] : "center";

        /*
         * Preprocess the content
         */
        $content=preg_replace("/<br \/>/","\n",$content);   # Replace linebreaks (html-style) by \n
        $content=strip_tags($content);                      # Remove all HTML-tags
        $content=preg_replace("/^\n+/","",$content);        # Strip all starting and trailing new-lines before the content
        $content=preg_replace("/\n+$/","",$content);
        $lines=split("\n",$content);                        # Split the text to an array

        $linecount=count($lines);                           # Count the lines

        $lineheight=intval(430/$linecount);                 # Calculate the lineheight

        $directcontent.=self::debug(var_export ($atts,true),"Attributes");
        $directcontent.=self::debug($content,"Content");
        $directcontent.=self::debug("Count:$linecount Height:$lineheight","Lines");

#        include (dirname(__FILE__)."/templates/bg_1.php");

$directcontent.= <<<EOC
		<div class="poem" style="background:url(http://wwerther.de/wp-content/gallery/poetry/bg_2.jpg); width:800px; height:532px">
		<div class="poem_box poem_title" style="left:20px;top:10px;width:390px;height:50px; line-height:50px; color:#FF0000">$title</div>
		<div class="poem_box" style="left:20px;top:40px;width:390px;height:430px;line-height:${lineheight}px">
EOC;
#			<div class="poem_left">Zeile1</div>
#			<div class="poem_left" style="margin-left:50px">Zeile2</div>
#			<div class="poem_empty"><br/></div>
#			<div class="poem_right">Zeile3</div>
#			<div class="poem_left">Zeile4</div>

foreach ($lines as $line) {

    if (preg_match('/#c#/',$line)) {
        $align="center";
    } elseif (preg_match('/#r#/',$line)) {
        $align="right";
    } elseif (preg_match('/#l#/',$line)) {
        $align="left";
    }
    $line=preg_replace("/#.#/",'',$line);


    $add_style='';
    if (preg_match('/^(\++)/',$line,$matches)) {
        $style='style="left:'.strlen($matches[0])*$indent.'px"';
        
        $line=preg_replace("/^\++/",'',$line);
    }

    if (preg_match("/^$/",$line)) {
        $style='style="line-height:'.$lineheight.'px"';
        $directcontent.= '<div class="poem_empty"'.$style.'><br/></div>'."\n";
    } else {
        $directcontent.= '<div class="poem_'.$align.'"'.$style.'>'.$line."</div>\n";
    }
}
$directcontent.= <<<EOC
		</div>
		</div>
EOC;

        return $directcontent;

    }

}
 
/*
 * I just define a small test-scenario, wether or not the add_shortcode function 
 * already exists. This allows me to do a compilation test of this file
 * without the full overhead of wordpress
 * This is used when I do a git commit to guarantee, that the code will compile
 * properly
 */
if (! function_exists('add_shortcode')) {
        function wp_register_script($name, $plugin, $deps, $vers, $switch) {
            print "REGISTER: $name, $plugin\n";
        }
        function plugins_url($module, $file) {
            print "PLUGINS_URL: $module, $file \n";
            return $module;
        }
        function add_action($hook, $action) {
            print "ADD_ACTION: $hook, $action[1]\n";
        }
        function wp_print_scripts($script) {
            print "WP_PRINT_SCRIPT: $script\n";
        }
        function add_shortcode ($shortcode,$function) {
                echo "Only Test-Case: $shortcode: $function";

                print POEMFORMATTER::handle_shortcode(array('href'=>'http://sonne/cadence.gpx','maxelem'=>0),null,'');
                print POEMFORMATTER::add_script();
        };
}


POEMFORMATTER::init();

?>
