<?php

/**
 *	Simple class for including static JS/CSS/etc media
 */

class Components {
    
    # Path relative to web server root. Do not include trailing slash.
    # Will be appended with the type and version, e.g. '$media_path/css/1.0.0'
    # empty string means server root or "/"
    private $media_path = "static";
    
    # Path relative to this class file  Do not include trailing slash.
    private $media_dir  = "../../static";
    
    /**
	 * __construct function.
	 *
	 * @access public
	 * @param version Optional version. Defaults to VERSION
	 */
	public function __construct($version = MEDIA_VERSION) {
        $this->version = $version;
    }
    
    /**
	 * _server_path function. Returns the server path to the component
     * 
	 * @access private
     * @param type String the type of component
	 * @param component String filename minus the file extension
	 */
    private function _server_path($type, $component) {
        return $this->media_path . '/' . $this->version . '/' . $type . '/' . $component . '.' . $type;
    }
    
    /**
	 * _file_path function. Returns the local file path to the component
     * 
	 * @access private
     * @param type String the type of component
	 * @param component String filename minus the file extension
	 */
    private function _file_path($type, $component) {
        
        if (substr($this->media_dir, 0, 1) === "/") {
            # if media_dir is an absolute path
            $base = $this->media_dir;
        } else {
            # otherwise relative to this class file
            $base = dirname(__FILE__) . "/" . $this->media_dir;
        }
        return $base . '/' . 
               $type . '/' . 
               $this->version . '/' . 
               $component . '.' . 
               $type;
    }
    
    /**
	 * _js_tag function. Outputs an html <script> tag for a js file
     * 
	 * @access private
	 * @param component String filename minus the file extension
	 */
    private function _js_tag($component, $remote = false) {
        $path = ($remote ? $component : $this->_server_path('js',$component));
        return '<script type="text/javascript" src="' . $path . '"></script>';
    }
    
    /**
     * _css_tag function. Outputs an html <link> tag for a css file
     * 
     *
     * @access private
     * @param component String filename minus the file extension
     */
    private function _css_tag($component, $remote = false) {
        $path = ($remote ? $component : $this->_server_path('css',$component));
        return '<link rel="stylesheet" type="text/css" href="' . $path . '" />';
    }
    
    /**
	 * _render_tag function.
	 *
	 * @access private
	 * @param type String indicating type of component to include. 'js' or 'css'
	 */
    private function _render_tag ($type, $components) {
        $f = "_" . $type . "_tag";
        
        if (is_string($components)) {
            $components = array($components);
        } 
        
        foreach ($components as $c) {
            if ($this->_file_path($type, $c)) {
                print $this->$f($c) . "\n";
            } elseif (substr($c, 0, 4) === 'http') {
                print $this->$f($c, true) . "\n";
            }
        }

    }
    
    /**
	 * js function.
	 *
	 * @access public
	 * @param components String or array of strings of js filenames, not including the file extension
	 */
    public function js ($components) {
        $this->_render_tag('js',$components);
    }
    
    /**
     * css function.
     *
     * @access public
     * @param components String or array of strings of css filenames, not including the file extension
     */
    public function css ($components) {
        $this->_render_tag('css',$components);
    }
    
}