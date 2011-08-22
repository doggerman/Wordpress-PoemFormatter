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
    static $align="center";

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
        $title=array_key_exists('title',$atts) ? $atts['title'] : null;
        $author=array_key_exists('author',$atts) ? $atts['author'] : 'Walter Werther';
        $date=array_key_exists('date',$atts) ? $atts['date'] : ' ';
        $indent=array_key_exists('indent',$atts) ? intval($atts['indent']) : 50;
        $align=array_key_exists('align',$atts) ? $atts['align'] : "center";

        /*
         * Preprocess the content
         */
    
        $content=preg_replace("/<br \/>/","\n",$content);   # Replace linebreaks (html-style) by \n
        $content=strip_tags($content);                      # Remove all HTML-tags
        $content=preg_replace("/^\n+/","",$content);        # Strip all starting and trailing new-lines before the content
        $content=preg_replace("/\n+$/","",$content);


        $content.=<<<EOCONTENT

#2##r#Lorem ipsum dolor sit amet,
consetetur sadipscing elitr,
sed diam nonumy eirmod tempor 
invidunt ut labore et 
dolore magna aliquyam erat,
sed diam voluptua. 

At vero eos et accusam et justo 
duo dolores et ea rebum. Stet clita 
kasd gubergren, no sea takimata
sanctus est Lorem ipsum dolor sit amet.

Lorem ipsum dolor sit amet, consetetur 
sadipscing elitr, sed diam nonumy 
eirmod tempor invidunt ut labore 
et dolore magna aliquyam erat, 
sed diam voluptua. 

At vero eos et accusam et justo 
duo dolores et ea rebum. Stet clita 
kasd gubergren, no sea takimata sanctus 
est 

Lorem ipsum dolor sit amet.
EOCONTENT;



$lines=split("\n",$content);                        # Split the text to an array

        $linecount=count($lines);                           # Count the lines




        $lineheight=intval(430/$linecount);                 # Calculate the lineheight

        $directcontent.=self::debug(var_export ($atts,true),"Attributes");
        $directcontent.=self::debug($content,"Content");
        $directcontent.=self::debug("Count:$linecount Height:$lineheight","Lines");

#        include (dirname(__FILE__)."/templates/bg_1.php");
#
        $template=<<<EOTEMPLATE
		<div class="poem" style="background:url(http://wwerther.de/wp-content/gallery/poetry/bg_2.jpg); width:800px; height:532px">
     		<div class="poem_box poem_title" style="left:20px;top:10px;width:390px;height:50px; line-height:50px; color:#FF0000">{title}</div>
	    	<div class="poem_box" style="left:20px;top:40px;width:390px;height:430px;line-height:{box1lineheight}px"> <!--box1-->
                {box1content}
    		</div>
	    	<div class="poem_box" style="left:480px;top:-390px;width:250px;"> <!--box2-->
                {box2content}
    		</div>
       		<div class="poem_box" style="top:30px;position:relative;padding:0px;right:0px;left:600px; width:200px; color:#FFF">(c) {date} {author}</div>

            
		</div>
EOTEMPLATE;


        if ($title) { $template=preg_replace('/{title}/',$title,$template); }
        if ($author) { $template=preg_replace('/{author}/',$author,$template); }
        if ($date) { $template=preg_replace('/{date}/',$date,$template); }

        $blockno=1;
        $blockdata=array();
        foreach ($lines as $line) {
            if (preg_match('/#(\d)#/',$line,$matches)) {            # Evaluate the block-number
                $blockno=$matches[1];
                next;
            }
            if (! $blockdata[$blockno]) { $blockdata[$blockno]=array(); }
            array_push($blockdata[$blockno],self::render_line($line,$lineheight,$indent));
        }

        foreach ($blockdata as $blockno=>$block) {
            $directcontent.=self::debug("Blockno: $blockno","Next block");
            $blockheight=null;
            $blocklineheight='';
            if (preg_match('/height:(\d+)px;.*box'.$blockno.'/',$template,$matches)) {
                $blockheight=$matches[1];
                $blockline=count($block);
                $blocklineheight=intval($blockheight/$blockline);
            };
            $data=join("\n",$block);
            $directcontent.=self::debug("total lines: $linecount\nblock lines:$blockline\n Blockheight: $blockheight\n Blocklineheight: $blocklineheight","Metadata");
            $directcontent.=self::debug($data,"data");
            $template=preg_replace('/{box'.$blockno.'lineheight}/',$blocklineheight,$template);
            $template=preg_replace('/{box'.$blockno.'content}/',$data,$template);
        }

        $directcontent.= $template;
        return $directcontent;

    }

    public static function render_box () {
    }


    public static function render_line ($line,$lineheight,$indent) {

        if (preg_match('/#c#/',$line)) {            # Change to center-align
            self::$align="center";
        } elseif (preg_match('/#r#/',$line)) {      # Change to right-align
            self::$align="right";
        } elseif (preg_match('/#l#/',$line)) {      # Change to left-align
            self::$align="left";
        }
        $line=preg_replace("/#.#/",'',$line);       # Remove all meta-tags from the line

    
        $add_style='';
        if (preg_match('/^(\++)/',$line,$matches)) {
            $style='style="left:'.strlen($matches[0])*$indent.'px"';
        
            $line=preg_replace("/^\++/",'',$line);
        }

        if (preg_match("/^$/",$line)) {
#            $style='style="line-height:'.$lineheight.'px"';
            return '<div class="poem_empty"'.$style.'><br/></div>'."\n";
        } else {
            return '<div class="poem_'.self::$align.'"'.$style.'>'.$line."</div>\n";
        }
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
