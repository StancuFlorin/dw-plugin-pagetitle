<?php
/**
 * DokuWiki plugin PageTitle Breadcrums; Syntax component
 * Macro to set the short title of the page in metadata
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Satoshi Sahara <sahara.satoshi@gmail.com>
 */

if (!defined('DOKU_INC')) die();

class syntax_plugin_pagetitle_breadcrumb extends DokuWiki_Syntax_Plugin {

    protected $special_pattern = '~~ShortTitle:.*?~~';
    protected $check = array(); // ensure first matched pattern only effective

    function __construct() {
        $this->pluginMode = substr(get_class($this), 7); // drop 'syntax_' from class name
    }

    public function getType() { return 'substition'; }
    public function getPType(){ return 'normal'; }
    public function getSort() { return 990; }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->special_pattern, $mode, $this->pluginMode);
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        global $ID;

        if ($this->check[$ID]++) {
            return false; // ignroe second match in a page
        }

        list($key, $value) = explode(':', substr($match, 2, -2), 2);
        $short_title = trim($value);
        return array($state, $short_title, $ID);
    }

    public function render($format, Doku_Renderer $renderer, $data) {
        global $ID;

        list($state, $short_title, $id) = $data;

        if ($format == 'metadata') {
             if (strcmp($id, $ID) !== 0) return false;
             $renderer->meta['shorttitle'] = $short_title;
        }
        return true;
    }

}
