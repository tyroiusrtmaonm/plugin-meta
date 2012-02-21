<?php
/**
 * Meta Plugin: Sets metadata for the current page
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_meta extends DokuWiki_Syntax_Plugin {

    /**
     * return some info
     */
    function getInfo() {
        return array(
                'author' => 'Esther Brunner',
                'email'  => 'wikidesign@gmail.com',
                'date'   => '2006-04-15',
                'name'   => 'Meta Plugin',
                'desc'   => 'Sets metadata for the current page',
                'url'    => 'http://wiki.splitbrain.org/plugin:meta',
                );
    }

    function getType() { return 'substition'; }
    function getSort() { return 99; }
    function connectTo($mode) { $this->Lexer->addSpecialPattern('~~META:.*?~~',$mode,'plugin_meta');}

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {
        $match = substr($match,7,-2); //strip ~~META: from start and ~~ from end

        return $match;
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if ($mode == 'xthml') {
            $renderer->doc .= getVersion();
            return true; // don't output anything
        }
        return false;
    }

    /**
     * converts YYYY-MM-DD[ hh:mm:ss][ -> [YYYY-MM-DD ]hh:mm:ss] to PHP timestamps
     */
    function _convertDate($date) {
        list($start, $end) = explode('->', $date, 2);

        // single date
        if (!$end) {
            list($date, $time) = explode(' ', trim($start), 2);
            if (!preg_match('/\d{4}\-\d{2}\-\d{2}/', $date)) return false;
            $time = $this->_autocompleteTime($time);
            return strtotime($date.' '.$time);

            // duration
        } else {

            // start
            list($startdate, $starttime) = explode(' ', trim($start), 2);
            $startdate = $this->_autocompleteDate($startdate);
            if (!$startdate) return false;
            $starttime = $this->_autocompleteTime($starttime);

            // end
            list($enddate, $endtime) = explode(' ', trim($end), 2);
            if (!trim($endtime)) { // only time given
                $end_date = $this->_autocompleteDate($enddate, true);
                if (!$end_date) {
                    $endtime = $this->_autocompleteTime($enddate, true);
                    $enddate = $startdate;
                } else {            // only date given
                    $enddate = $end_date;
                    $endtime = '23:59:59';
                }
            } else {
                $enddate = $this->_autocompleteDate($enddate, true);
                if (!$enddate) $enddate = $startdate;
                $endtime = $this->_autocompleteTime($endtime, true);
            }

            $start = strtotime($startdate.' '.$starttime);
            $end   = strtotime($enddate.' '.$endtime);
            if (!$start || !$end) return false;
            return array('start' => $start, 'end' => $end);
        }
    }

    function _autocompleteDate($date, $end=false) {
        if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) {
            if (preg_match('/^\d{4}\-\d{2}$/', $date))
                // we don't know which month
                return ($end) ? $date.'-28' : $date.'-01';
            elseif (preg_match('/^\d{4}$/', $date))
                return ($end) ? $date.'-12-31' : $date.'-01-01';
            else return false;
        } else {
            return $date;
        }
    }

    function _autocompleteTime($time, $end=false) {
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            if (preg_match('/^\d{2}:\d{2}$/', $time))
                return ($end) ? $time.':59' : $time.':00';
            elseif (preg_match('/^\d{2}$/', $time))
                return ($end) ? $time.':59:59': $time.':00:00';
            else return ($end) ? '23:59:59' : '00:00:00';
        } else {
            return $time;
        }
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
