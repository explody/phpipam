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
     * Tools class
     *
     * @var Tools object
     * @access private
     */
    private $Tools;
    
    /**
     * __construct function.
     *
     * @access public
     * @param version Optional version. Defaults to VERSION
     */
    public function __construct($tools, $version = MEDIA_VERSION) {
        $this->Tools = $tools;
        $this->version = $version;
    }
    
    /**
     * Returns a string appropriate for a display name for a custom field
     *
     * @access public
     * @param mixed $cf Custom field object
     * @return string Name for display
     */
    public static function custom_field_display_name($cf) {
        return empty(trim($cf->display_name)) ? $cf->name : $cf->display_name;
    }
    
    /**
     * Returns language aware strings for boolean values
     *
     * @access public
     * @param mixed $bool A value that will contain a truthy/falsey boolean
     * @return string Translated string value for the true/false
     */
    public static function boolean_display_value($bool) {
        return $bool ? _('Yes') : _('No');
    }
    
    /**
     *	Takes an array of objects and returns an array of <option> elements
     *  for use in <selects>.
     * 
     *  @access public
     *  @param array $objects List of objects on which to iterate 
     *  @param string $valprop Property of the objects to use for the <option> 
     *                         value
     *  @param string|array $contprop Single or list of property names from the 
     *                          objects to use for the content of the <option>. 
     *                          Defaults to the same as $valprop
     *  @param array $selected Array in {k=>v} where if object.k === v, we'll 
     *                         add 'selected'
     *  @param array $classes List of classes to add to the <option>
     *  @param array $data List of object attributes to add as 'data-*' attrs in the <option>
     */
     public static function generate_options($objects, 
                                             $valprop,
                                             $contprop = false, 
                                             $selected = false, 
                                             $classes = false,
                                             $data = []) {
                                                 
          $options = [];
          foreach ($objects as $obj) {
              $o = '<option value="' . $obj->$valprop . '"';
              if ($classes) {
                  $o = $o . 'class="' . implode(" ", $classes) . '"';
              }
              if ($data) {
                  foreach ($data as $attr) {
                      if (property_exists($obj, $attr)) {
                          $o .= " data-${attr}=\"" . $obj->$attr . "\"";
                      }
                  }
              }
              if ($selected) {
                  // This enables passing multiple possible values to check 
                  foreach ($selected as $k=>$v) {
                      if ($obj->$k === $v) {
                          $o = $o . ' selected';
                      }
                  }
              } 
              
              /* 
                 If $contprop is specified...
                 If it is an array, loop and ensure that $obj has each of the
                 named properties. If so, add the property values to an array.
                 If it is a string, ensure that $obj has that one property and 
                 add that single value to an array.
                 Once the values are compiled, implode them into a string.
              */
              $cprops = [];
              if ($contprop) {
                  if (is_array($contprop)) {
                        foreach($contprop as $cprop) {
                            if (property_exists($obj, $cprop)) {
                                $cprops[] = $obj->$cprop;
                            }
                        }
                  } elseif (is_string($contprop)) {
                      $cprops = (property_exists($obj, $contprop) ? [$obj->$contprop] : []);
                  }
                  
                  // array_filter strips out any pesky nulls/empties that make it in
                  $content = implode(' | ', array_filter($cprops));
                  
              } else {
                  $content = $obj->$valprop;
              }
              
              $o = $o . '>' . $content . '</option>';
              
              $options[] = $o;
          }
          return $options;
     }

     /**
      *  Takes an array of objects arranged in groups such as 
      *   [ grp => array(obj1,obj2,obj3) ]
      *  and generates an array of option groups and options such as
      *   [ '<optgroup' => array('<option>','<option>','<option>') ]
      *  WARN: does not generate or include a closing </optgroup> tag
      *  See generate_options() for param details
       * 
      *  @access public
      *  @param array $objects Array of objects as described above 
      *  @param array $gclasses List of classes to add to the <optgroup>
      *  @param array $oclasses List of classes to pass on to generate_options()
      */
      public static function generate_option_groups($objgroups, 
                                                    $valprop,
                                                    $contprop = false, 
                                                    $selected = false,
                                                    $gclasses = false,
                                                    $oclasses = false,
                                                    $data = []) {

           $optgroups = [];
           foreach ($objgroups as $grp=>$objects) {
               $options = self::generate_options($objects,$valprop,$contprop,$selected,$oclasses,$data);
               $og = '<optgroup label="' . $grp . '" ';
               if ($gclasses) {
                   $og = $og . 'class="' . implode(" ", $gclasses) . '" ';
               }
               $og = $og . '>';
               $optgroups[$og] = $options;
           }
           
           return $optgroups;
      }
      
      /**
       *  Render the inline script element necessary for a select2 dropdown
       *  TODO: have this cleanly support options for the method as well as options for the JS
       * 
       *  @access public
       *  @param array $selector Name of the jQuery selector of the <select>
       *  @param array $options Optional array of options to pass to select2()
       */
       public static function render_select2_js($selector,$options=[]) {
           $defoptions = array('return' => false,
                               'theme' => 'bootstrap', 
                               'width' => "", // intentional
                               'minimumResultsForSearch' => 15,
                               'templateResult' => '$(this).s2oneLine'
                         );
           $options = (object) array_merge($defoptions, $options);
           
           // this is dumb but I added 'return' later and it's not an option for the JS
           $return = $options->return;
           unset($options->return);
           
           $output = [];
           
           $output[] = "<script type=\"text/javascript\">";
           $output[] = "$('$selector').select2({";
               
           foreach ($options as $k=>$v) {
                   if ($k == "return") {  
                       continue;
                   }
                   $output[] = "   $k: " . ((is_int($v) || $k === 'templateResult') ? 
                                             $v : 
                                             "\"$v\"") . ",\n";
           }

           $output[] = "});";
           $output[] = "</script>";
           
           // If the 'return' option is true, just return the array of strings, otherwise print them.
           if ($return) {
               return $output;
           } 
           print implode("\n", $output);
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
    
    /**
     * Render options and option groups for use in <select>
     * 
     * @access public
     * @param array $objs Array of objects such as returned from 
     *               Database::getObjects(), in form {key1: obj1, key2: obj2}
     * @param string $valueField Name of the object property to use as the 
     *               <option> value
     * @param string|array $displayField Single property name or list of names to 
     *               use as the <option> content. Multiple properties will be 
     *               concatenated together with " | "
     * @param array $options An array of options for rendering <option> and 
     *               <optgroup> elements
     * @param bool   $options['return'] If true, will return an array of strings 
     *               rather than printing them inline.
     *               Default: false.  Prints by defaul
     * @param bool|string $options['sort'] Sort the objects. Either a property
     *               name on which to sort them, or 'true' to sort by name.
     *               Will apply to options under optgroups.
     *               Default: false.  SQL sorting the objects is faster.
     * @param bool $options['gsort'] For grouped options, sort the optgroups.
     *               Default: true
     * @param bool|string $options['group'] Arrange the options under <optgroup>  
     *               Requires $options['groupby'].
     *               Default: false
     * @param bool|string $options['groupby'] Object property name to use for the 
     *               content of <optgroup>. Required if $group is true.
     *               Default: false
     * @param bool|string $options['resolveGroupKey'] If set to a string, the 
     *               value of the $groupby field is presumed to be a numeric ID 
     *               referencing a row in a db table and we'll attempt to 
     *               fetch the object from that table based on the value of
     *               the property specified by $groupby, and replace the numeric
     *               array key for the grouping with a friendlier string from 
     *               the fetched object. Will attempt to use several strings, 
     *               in this order:
     *                * fetched_object->$resolveGroupKey
     *                * fetched_object->description
     *                * fetched_object->name
     *               Default: false 
     * @param array $options['extFields'] List of property names that contain IDs 
     *               referencing other tables, for resolving these IDs to 
     *               human friendly names.  In the form {propname: table}
     *               Default: [] 
     * @param array $options['oclasses'] Array of css classes to add to <option>
     *               Default: []
     * @param array $options['gclasses'] Array of css classes to add to <optgroup>
     *               Default: []
     * @param array $options['selected'] Array of [k=>v] where if the option 
     *               object->k === v, we'll add 'selected'
     *               Default: []
     * @param bool $options['dotIPs'] If true and displayField contains 'subnet' or 'ip_addr'
     *               and these attributes are in integer format, we'll convert them to dotted
     *               notation
     *               Default: false
     * @param array $options['data'] A list of object attributes to add as "data-*" fields to the options
     *               Default: false
     * @return void|array  Directly prints options or returns an array of strings.
     */
    public function render_options($objs, $valueField, $displayField, $options = []) {
        
        $defoptions = array('return' => false,
                            'group' => false, 
                            'groupby' => false,
                            'resolveGroupKey' => false,
                            'sort' => false,
                            'gsort' => true,
                            'dotIPs' => true,
                            'extFields' => [], 
                            'oclasses' => [], 
                            'gclasses' => [],
                            'selected' => [],
                            'data' => []
                      );
        $options = (object) array_merge($defoptions, $options);
        $output  = [];

        if ($options->dotIPs) {
            foreach ($objs as $id => $obj) {
                foreach(['ip_addr','subnet'] as $prop) {
                    if (property_exists($obj, $prop) && $this->Tools->identify_address_format($obj->{$prop}) == "decimal") {
                        $objs[$id]->{$prop} = $this->Tools->transform_to_dotted($obj->{$prop});
                    }
                }
            }
        }
        
        if ($options->group && $options->groupby) {
            
            // don't sort here since inside group_objects the keys are numeric IDs
            $objs = Tools::group_objects($objs, $options->groupby, false);
            
            if ($options->resolveGroupKey) {
                foreach(array_keys($objs) as $gid) {
                    if (array_key_exists($options->groupby, $options->extFields)) {
                        
                        $gobj = $this->Tools->fetch_object($options->extFields[$options->groupby], 'id', $gid);
                      
                        if ($gobj) {
                            $r = false;
                            
                            // Sort object list on the property given in $options->sort or
                            // just 'name' if $options->sort is true
                            if ($options->sort) {
                                $sk = (is_string($options->sort) ? $options->sort : 'name');
                                usort($objs[$gid], Tools::sort_objs($sk));
                            }
                            
                            if (!empty($gobj->{$options->resolveGroupKey})) {
                                // Preference the field specified by $resolveGroupKey
                                $objs[$gobj->{$options->resolveGroupKey}] = $objs[$gid];
                                $r = true;
                            } elseif (!empty($gobj->description)) {
                                // Then 'description'
                                $objs[$gobj->description] = $objs[$gid];
                                $r = true;
                            } elseif (!empty($gobj->name)) {
                                // Lastly 'name'
                                $objs[$gobj->name] = $objs[$gid];
                                $r = true;
                            }
                            if ($r) { unset($objs[$gid]); }
                          
                        } 
                    }
                }
            }
            
            if ($options->gsort) {
                ksort($objs);
            }
            
            // Render <optgroup>'s and <option>'s
            $ogs = self::generate_option_groups($objs, 
                                                $valueField, 
                                                $displayField, 
                                                $options->selected, 
                                                $options->gclasses, 
                                                $options->oclasses,
                                                $options->data);
            
            foreach ($ogs as $og=>$os) {
                $output[] = "$og";
                foreach ($os as $o) {
                    $output[] = "    $o";
                }
                $output[] = "</optgroup>";
            }
        
        // end $group && $groupby    
        } else {
            // Sort object list on the property given in $options->sort or
            // just 'name' if $options->sort is true
            if ($options->sort) {
                $sk = (is_string($options->sort) ? $options->sort : 'name');
                usort($objs, Tools::sort_objs($sk));
            }
            // render <option>'s
            $opts = self::generate_options($objs, 
                                           $valueField, 
                                           $displayField, 
                                           $options->selected,
                                           $options->oclasses,
                                           $options->data);
            foreach ($opts as $o) {
                $output[] = "$o";
            }
        }
        
        // If the 'return' option is true, just return the array of strings, otherwise print them.
        if ($options->return) {
            return $output;
        } 
        print implode("\n", $output);
        
    } // end render_options
    
    /**
     * Allows use of render_options given an 1D list of unique non-objects. 
     * supports only the most basic usage of render_options, no grouping, keys, etc.
     * Supports 'return' and 'selected' and applies 'sort'. Unsupported options will be ignored.
     *
     * @access public
     * @param mixed $list a list of strings
     * @return void
     */
    public function render_options_from_list($list, $options = []) {
        $array = [];
        foreach($list as $item) {
            $array[$item] = new stdClass();
            $array[$item]->name = $item;
            $array[$item]->value = $item;
        }
        
        $supported_options = ['sort' => true];
        
        if (array_key_exists('return', $options)) {
            $supported_options['return'] = $options['return'];
        }
        
        if (array_key_exists('selected', $options)) {
            $supported_options['selected'] = $options['selected'];
        }
        
        return $this->render_options($array, 'value', 'name', $supported_options);
    }
    
    /**
     * Creates form input field for custom fields.
     *
     * @access public
     * @param mixed $field
     * @param mixed $object
     * @param mixed $action
     * @param mixed $timepicker_index
     * @return array
     */
    public function render_custom_field_input($cf, $object, $action, $index) {
        
        // Old code will be passing arrays until it's all updated
        if(!is_object($object)) {
            $object = (object) $object;
        }
        
        // If $index is not provided, set the counters to 0
        if (!$index) {
            $index = [];
            foreach(['timepicker','boolean','select'] as $ikey) {
                $index[$ikey] = 0;
            } 
        }
        
        $actions = ['add','edit','delete'];
        foreach ($actions as $act) { $$act = false; }
        
        in_array($action,$actions) ? ${$action} = true : null;
        
        // field params are stored as json.
        $cf->params = json_decode((!empty($cf->params) ? $cf->params : ''));
        
        $html = array();

        # required
        $req_flag = $cf->required ? "*" : "";

        # set default value if adding new object
        if ($add) { 
            $object->{$cf->name} = $cf->default; 
        }

        //set, enum
        if($cf->type == "set" || $cf->type == "enum") {
            
            if($index['select'] == 0) {
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/select2.js"></script>';
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/common.plugins.js"></script>';
            }

            $html[] = "<select name='$cf->name' id='custom-select" . $index['select'] . "' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='" . self::custom_field_display_name($cf) . "'>";
            
            // if the field allows null, add an option for 'none'
            if ($cf->null) {
                $html[] = "<option value=\"\">None</option>";
            }
            
            $html = array_merge($html, $this->render_options_from_list($cf->params->values, ['return' => true, 
                                                                                             'selected' => 
                                                                                                ['value' => $object->{$cf->name}]
                                                                                            ])
                                                                                        );

            $html[] = "</select>";
            
            if($index['select'] == 0) {
                $html = array_merge($html, Components::render_select2_js('#custom-select' . $index['select'],
                                              ['return' => true, 'templateResult' => '$(this).s2oneLine']));
            }
            
            $index['select']++;
        }
        //date and time picker
        elseif(in_array($cf->type, ['date','datetime','time','timestamp'])) {

            
            if($cf->type == "date") { 
                $size = 10; 
                $format = "Y-MM-DD";
            } else if($cf->type == "time") { 
                $size = 10; 
                $format = "hh:mm:ss";
            } else {
                $size = 19; 
                $format = "Y-MM-DD hh:mm:ss";
            }
            
            // just for first
            if($index['timepicker'] == 0) {
                $html[] =  '<link rel="stylesheet" type="text/css" href="' . MEDIA . '/css/bootstrap.datetimepicker.css">';
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/moment.js"></script>';
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/bootstrap.datetimepicker.js"></script>';
            }
            
            $html[] = '<div class="'.$class.' input-group date">';
            //field
            if(!isset($object->{$cf->name}))	{ 
                $html[] = ' <input type="text" id="datetimepicker' . $timepicker_index . '" class="form-control input-sm input-w-auto" name="' . $cf->name . '" maxlength="' . $size . '" rel="tooltip" data-placement="right" title="' . self::custom_field_display_name($cf) . '"></input>'. "\n"; 
            } else {
                $html[] = ' <input type="text" id="datetimepicker' . $timepicker_index . '" class="form-control input-sm input-w-auto" name="'. $cf->name .'" maxlength="' . $size . '" value="' . $object->{$cf->name} . '" rel="tooltip" data-placement="right" title="' . self::custom_field_display_name($cf) . '"></input>'. "\n"; 
            }
            $html[] = '<span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>';
            $html[] = '</div>';
            
            $html[] =  '<script type="text/javascript">';
            $html[] =  '$(document).ready(function() {';
            $html[] =  '    $("#datetimepicker' . $timepicker_index . '").datetimepicker( { format: \'' . $format . '\', allowInputToggle: true });';
            $html[] =  '})';
            $html[] =  '</script>';

            $index['timepicker']++;
        }
        //boolean
        elseif($cf->type == "boolean") {
            
            if($index['boolean'] == 0) {
                $html[] = '<script type="text/javascript">';
                $html[] = '$(document).ready(function() { ';
                $html[] = 'var switch_options = { onText: "Yes", offText: "No", onColor: "default", offColor: "default", size: "mini", inverse: true };';
                // applying this to a class will allow it to trigger on all subsequent inputs
                $html[] = '$(".input-switch").bootstrapSwitch(switch_options);';
                $html[] = '});';
                $html[] = '</script>';
            }
            
            // This input will be overwritten by the checkbox, if the checkbox is clicked.  This ensures that we 
            // always get a valid back for this input, including "no/off/0", which checkboxes don't send if they are unchecked.
            $html[] =  "<input type='hidden' name='$cf->name' value='0'>";
            // This is a little ugly.  If the value of the custom field in the object is 1 (true/yes), set selected 
            // or, if the property is empty *and* the default value is 1 (true/yes), set selected
            $html[] =  "<input type='checkbox' class='input-switch' name='$cf->name' value='1'" . 
                         (($object->{$cf->name} || ( empty($object->{$cf->name}) && $cf->default == 1)) ? 
                         'checked>' : 
                         '>' );
            
            $index['boolean']++;
        }
        //text
        elseif($cf->type == "text") {
            $html[] = ' <textarea class="form-control input-sm" name="' . $cf->name . '" placeholder="'. ($cf->description ? $cf->description : $cf->display_name) .'" rowspan="3" rows="5" rel="tooltip" data-placement="right" title="'.$cf->display_name.'">'. $object->{$cf->name}. '</textarea>'. "\n";
        }
        //default - input field
        else {
            $html[] = ' <input type="text" class="form-control input-sm" name="' . $cf->name . '" placeholder="'. ($cf->description ? $cf->description : $cf->display_name) . '" value="'. $object->{$cf->name}. '" size="30" rel="tooltip" data-placement="right" title="' . $cf->display_name . '">'. "\n";
        }

        # result
        return array(
            "required" => $req_flag,
            "field" => implode("\n", $html),
            "index" => $index
        );
    }
    
    /**
     * Creates form input field for custom fields.
     *
     * @access public
     * @param mixed $field
     * @param mixed $object
     * @param mixed $action
     * @param mixed $timepicker_index
     * @return array
     */
    public static function render_custom_field_input($field, $object, $action, $timepicker_index) {
        
        $actions = ['add','edit','delete'];
        foreach ($actions as $act) { $$act = false; }
        
        in_array($action,$actions) ? ${$action} = true : null;
        
        // field params are stored as json.
        $field->params = json_decode($field->params);
        
        $html = array();

        # required
        $req_flag = $field->required ? "*" : "";

        # set default value if adding new object
        if ($add) { 
            $object->{$field->name} = $field->default; 
        }

        //set, enum
        if($field->type == "set" || $field->type == "enum") {

            $html[] = "<select name='$field->name' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$field->display_name'>";
            foreach($field->params->values as $v) {
                $html[] = '<option value="' . $v . '"' . ($v==$object->{$field->name} ? ' selected="selected">' : '>') . $v . '</option>';
            }
            $html[] = "</select>";
            
        }
        //date and time picker
        elseif(in_array($field->type, ['date','datetime','time','timestamp'])) {

            
            if($field->type == "date") { 
                $size = 10; 
                $format = "Y-MM-DD";
            } else if($field->type == "time") { 
                $size = 10; 
                $format = "hh:mm:ss";
            } else {
                $size = 19; 
                $format = "Y-MM-DD hh:mm:ss";
            }
            
            // just for first
            if($timepicker_index==0) {
                $html[] =  '<link rel="stylesheet" type="text/css" href="' . MEDIA . '/css/bootstrap.datetimepicker.css">';
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/moment.js"></script>';
                $html[] =  '<script type="text/javascript" src="' . MEDIA .'/js/bootstrap.datetimepicker.js"></script>';
            }
            
            $html[] = '<div class="'.$class.' input-group date">';
            //field
            if(!isset($object->{$field->name}))	{ 
                $html[] = ' <input type="text" id="datetimepicker' . $timepicker_index . '" class="form-control input-sm input-w-auto" name="' . $field->name . '" maxlength="' . $size . '" rel="tooltip" data-placement="right" title="' . $field->display_name . '"></input>'. "\n"; 
            } else {
                $html[] = ' <input type="text" id="datetimepicker' . $timepicker_index . '" class="form-control input-sm input-w-auto" name="'. $field->name .'" maxlength="' . $size . '" value="' . $object->{$field->name} . '" rel="tooltip" data-placement="right" title="' . $field->display_name . '"></input>'. "\n"; 
            }
            $html[] = '<span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>';
            $html[] = '</div>';
            
            $html[] =  '<script type="text/javascript">';
            $html[] =  '$(document).ready(function() {';
            $html[] =  '    $("#datetimepicker' . $timepicker_index . '").datetimepicker( { format: \'' . $format . '\', allowInputToggle: true });';
            $html[] =  '})';
            $html[] =  '</script>';

            $timepicker_index++;
        }
        //boolean
        elseif($field->type == "boolean") {
            $html[] =  "<select name='$field->name' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$field->display_name'>";
            $tmp = array(0=>"No",1=>"Yes");
            //null
            if($field->null) { $tmp[2] = ""; }

            foreach($tmp as $k=>$v) {
                if(strlen($object->{$field->name})==0 && $k==2) {
                    $html[] = "<option value='$k' selected='selected'>"._($v)."</option>";
                } elseif($k==$object->{$field->name}) {
                    $html[] = "<option value='$k' selected='selected'>"._($v)."</option>"; 
                } else {
                    $html[] = "<option value='$k'>"._($v)."</option>";
                }
            }
            $html[] = "</select>";
        }
        //text
        elseif($field->type == "text") {
            $html[] = ' <textarea class="form-control input-sm" name="' . $field->name . '" placeholder="'. ($field->description ? $field->description : $field->display_name) .'" rowspan=3 rel="tooltip" data-placement="right" title="'.$field->display_name.'">'. $object->{$field->name}. '</textarea>'. "\n";
        }
        //default - input field
        else {
            $html[] = ' <input type="text" class="form-control input-sm" name="' . $field->name . '" placeholder="'. ($field->description ? $field->description : $field->display_name) . '" value="'. $object->{$field->name}. '" size="30" rel="tooltip" data-placement="right" title="' . $field->display_name . '">'. "\n";
        }

        # result
        return array(
            "required" => $req_flag,
            "field" => implode("\n", $html),
            "timepicker_index" => $timepicker_index
        );
    }
    
}