<?php

namespace Swef;

class SwefContent extends \Swef\Bespoke\Plugin {

    public $error;
    public $permits;
    public $triggered;

/*
    EVENT HANDLER SECTION
*/

    public function __construct ($page) {
        // Get definitions
        require_once SWEF_CONFIG_PATH.'/Swef/SwefContent.define.php';
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($page,'\Swef\SwefContent');
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ( );
    }

    public function _on_pageIdentifyBefore ( ) {
        // MATCH AND SERVE CONTENT
        if (preg_match(swefcontent_shortcut_preg_match,$this->page->requestURI)) {
            $this->page->diagnosticAdd ('Serving content');
            $this->triggered = SWEF_BOOL_TRUE;
            $f = SWEF_STR__DOT.$this->page->requestURI;
            if (is_dir($f)) {
                $this->error = SWEF_HTTP_STATUS_CODE_403;
            }
            elseif (!is_readable($f)) {
                $this->error = SWEF_HTTP_STATUS_CODE_404;
            }
            else {
                // Load content permits and match
                $this->error = SWEF_HTTP_STATUS_CODE_403;
                $this->permits = $this->page->swef->db->dbCall (
                    swefcontent_call_permitsload
                   ,$this->page->swef->context[SWEF_COL_CONTEXT]
                   ,$this->page->requestURI
                );
                if (is_array($this->permits)) {
                    foreach ($this->permits as $p) {
                        if ($p[swefcontent_col_read]) {
                            if ($this->page->swef->user->inUserGroup($p[SWEF_COL_USERGROUP])) {
                                $this->error = null;
                            }
                        }
                    }
                }
            }
            if ($this->error==SWEF_HTTP_STATUS_CODE_403) {
                header (SWEF_HTTP_STATUS_CODE_403.SWEF_STR__SPACE.sweferror_403);
                header (SWEF_STR_CONTENTTYPE.swefcontent_error_contenttype);
                echo SWEF_HTTP_STATUS_CODE_403.SWEF_STR__SPACE.sweferror_403;
            }
            elseif ($this->error==SWEF_HTTP_STATUS_CODE_404) {
                header (SWEF_HTTP_STATUS_CODE_404.SWEF_STR__SPACE.sweferror_404);
                header (SWEF_STR_CONTENTTYPE.swefcontent_error_contenttype);
                echo SWEF_HTTP_STATUS_CODE_404.SWEF_STR__SPACE.sweferror_404;
            }
            else {
                header (SWEF_HTTP_STATUS_MSG_200);
                header (SWEF_STR_CONTENTTYPE.mime_content_type($f));
                echo file_get_contents ($f);
            }
            return SWEF_BOOL_FALSE;
        }
        return SWEF_BOOL_TRUE;
    }

    public function _on_pushBefore ( ) {
        if ($this->triggered) {
            $this->page->diagnosticAdd ('Serving content');
            return SWEF_BOOL_FALSE;
        }
        return SWEF_BOOL_TRUE;
    }


/*
    DASHBOARD SECTION
*/


    public function _dashboard ( ) {
        // Process inputs
        if (array_key_exists('user-input',$_POST)) {
            $this->exampleMethod ();
        }
        // Include the template
        require_once swefcontent_file_dash;
    }

    public function _info ( ) {
        $info   = __FILE__.SWEF_STR__CRLF;
        $info  .= SWEF_COL_CONTEXT.SWEF_STR__EQUALS;
        $info  .= $this->page->swef->context[SWEF_COL_CONTEXT];
        return $info;
    }

}

?>
