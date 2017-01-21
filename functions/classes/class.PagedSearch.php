<?php

class PagedSearch extends Common_functions {
    
    /* Needs docs */
    
    public $page = 1;
    
    public $pages = 1;
    
    public $limit = 50; 
    
    public $offset = 0;
    
    public $search_term = "";
    
    public $all_obj_count;
    
    public $search_query = '';
    
    public $found_objects;
    
    public $custom;
    
    public function __construct(Database_PDO $Database, 
                                $table_name, 
                                $search_fields,
                                $sortby,
                                $search_custom = false) {
        
        $this->Tools        = new Tools ($Database);
        $this->Database     = $Database;
        
        $this->custom = $this->Tools->fetch_custom_fields($table_name);
        
        if (array_key_exists('table-page-size', $_COOKIE)) { 
            $this->limit = $_COOKIE['table-page-size']; 
        } 
        
        if (isset($_GET['l'])) {
            if (is_numeric($_GET['l']) && $_GET['l'] > 0) {
                $this->limit = $_GET['l'];    
            }
        }

        if (isset($_GET['p'])) {
            if (is_numeric($_GET['p']) && $_GET['p'] > 0) {
                $this->page = $_GET['p'];    
            } 
        }
        
        $this->offset = ($this->page > 1 ? ($this->page - 1) * $this->limit : 0);
        
        if (array_key_exists('search', $_GET)) {
            
            $this->search_term = $_GET['search'];
            
            $this->base_query = "SELECT * from " . $table_name . " where ";
            
            if ($search_custom === true) {
                # Search all custom fields
                $this->custom_fields = array_keys($this->custom);
                
                # Merge default fields with custom fields
                $search_fields = array_merge($this->custom_fields, $search_fields);
            }
            
            list($extended_query, 
                 $query_params) = $this->Database->constructSearch($search_fields,
                                                                   $this->search_term);
            
            # Put together with the base query
            $this->search_query = $this->base_query . $extended_query;
            
            # Search query
            $this->found_objects = $this->Database->getObjectsQuery($this->search_query, 
                                                        $query_params, 
                                                        $sortby, 
                                                        true, 
                                                        $this->limit, 
                                                        $this->offset);
            
            $this->all_obj_count = $this->Database->numObjectsConditional($table_name, 
                                                                    $extended_query, 
                                                                    $query_params);
            
        } else {
            
            $this->found_objects = $this->fetch_objects($table_name, 
                                                        $sortby, 
                                                        true, 
                                                        $this->limit, 
                                                        $this->offset);
                                                        
            $this->all_obj_count = $this->Database->numObjects($table_name);
            
        }
        
        $this->pages = ceil($this->all_obj_count / $this->limit);
        
    }
    
    public function count() {
        return count($this->found_objects);
    }
    
}


?>
