<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */
 
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();
 
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_meta extends DokuWiki_Syntax_Plugin {
 
    function getInfo() {
        return array('author' => 'me',
                     'email'  => 'me@someplace.com',
                     'date'   => '2005-07-28',
                     'name'   => 'Now Plugin',
                     'desc'   => 'Include the current date and time',
                     'url'    => 'http://www.dokuwiki.org/plugin:tutorial');
    }
 
    function getType() { return 'substition'; }
    function getSort() { return 32; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~META:.*~~',$mode,'plugin_meta');
    }
 
    function handle($match, $state, $pos, &$handler) {
        return array($match, $state, $pos);
    }
 
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $META = p_get_metadata('start', 'date'); 
            $renderer->doc .= date("F j, Y, g:i a", $META['modified']);
            return true;
        }
        return false;
    }
}
?>
