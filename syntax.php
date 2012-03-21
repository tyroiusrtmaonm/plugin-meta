<?php
/**
 * Plugin Meta: Inserts meta data
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tristan Rasmussen
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
        return array('author' => 'Tristan Rasmussen',
                     'email'  => 'tristanr93@gmail.com',
                     'date'   => '2012-02-21',
                     'name'   => 'Meta Data Plugin',
                     'desc'   => 'Include various meta data fields',
                     'url'    => '');
    }
 
    function getType() { return 'substition'; }
    function getSort() { return 32; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~META:[a-zA-z/-/_1-9 ]*~~',$mode,'plugin_meta');
    }
 
    function handle($match, $state, $pos, &$handler) {
        $match = substr($match,7,-2); //strip ~~META: from start and ~~ from end
        $result = explode(' ', $match); //explode on spaces
        return $result;
    }
 
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $META = p_get_metadata($data[0], $data[1]);
            if($data[1]=='date') {
                $renderer->doc .= date("F j, Y, g:i a", $META[$data[2]]);
            }
            elseif($data[1]=='last_change') {
                $renderer->doc .= $META[$data[2]];
            }
            else {
                $renderer->doc .= $META;
            }
            return true;
        }
        return false;
    }
}
?>
