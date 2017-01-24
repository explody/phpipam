<?php

// this is why we need an autoloader
require_once(dirname(__FILE__) . '/../../paths.php');
require_once(FUNCTIONS . '/functions.php');
require_once(FUNCTIONS . '/classes/class.Legacy.php');
require_once(FUNCTIONS . '/classes/Migration/class.RepeatableMigration.php');

class CustomFieldConversion extends Ipam\Migration\RepeatableMigration
{
    public $type_conversion = [
        'int'       => 'integer',
        'tinyint'   => 'integer',
        'smallint'  => 'integer',
        'mediumint' => 'integer',
        'bigint'    => 'biginteger',
        'float'     => 'float',
        'double'    => 'integer',
        'decimal'   => 'decimal',
        'varchar'   => 'string',
        'date'      => 'date',
        'datetime'  => 'datetime',
        'time'      => 'time',
        'timestamp' => 'timestamp',
        'year'      => 'year',
        'char'      => 'char',
        'varchar'   => 'string',
        'blob'      => 'blob',
        'text'      => 'text',
        'enum'      => 'enum',
        'set'       => 'set'
    ];
    
    // Column names from prior migrations that are not actually custom fields
    // This could probably be handled by reordering the migrations instead.
    public $exclude = [
        'dbSessions',
        'devicegrouping',
        'devicegroupfield',
        'id',
        'name'
    ];
    
    public function change()
    {
        $d = new Database_PDO;
        $l = new Legacy($d);
        
        foreach ($l->fetch_standard_tables() as $st) {
            $cf = $l->fetch_custom_fields($st);
            if (sizeof($cf)) {
                $ct = $this->table($st);
                $this_table_count = 1;
                
                foreach($cf as $fname => $fdata) {
                    print_r($fdata);
                    $cfield = (object) [];
                    $cfield->name = $fname;
                    
                    if (in_array($fname, $this->exclude)) {
                        continue;
                    }
                    
                    $rtype = str_replace(['(',')'], ' ', $fdata['type']);

                    list($type,$typedata) = explode(' ', $rtype, 2);
                    
                    if(!array_key_exists($type, $this->type_conversion)) {
                        print "WARNING: Cannot convert custom field $fname of type $type.\n";
                        continue;
                    } else {
                        $cfield->type = $this->type_conversion[$type];
                    }

                    // values for a set come in as a string with quotes around values
                    // strip the quotes and parse as a CSV string.
                    $values = null;
                    if ($type === 'set' || $type === 'enum') {
                        $values = str_getcsv(str_replace("'", "", $typedata));
                    } else {
                        $limit = explode(' ', $typedata)[0];
                    }
                    
                    $params = [];
                    $values ? $params['values'] = $values : null;

                    !empty($fdata['Default']) ? $cfield->default = $fdata['Default'] : null;
                    !empty($fdata['Comment']) ? $cfield->description = $fdata['Comment'] : null;
                    $fdata['Null'] === 'NO' ? $cfield->null = 0 : $cfield->null = 1;
                    
                    $limit ? $cfield->limit = $limit : 128; // limit must be something
                    
                    $cfield->table = $st;
                    $cfield->display_name = $cfield->name;  // No corresonding data so copy name
                    
                    $cfield->order = $this_table_count;     // We'll record column order as found.
                    
                    // Check the customFields table for existing records.
                    $where = "`table`=? and `name`=?";
                    $count = $d->numObjectsConditional('customFields', $where, [$st,$fname]);
                    
                    // addColumn from RepeatableMigration handles add/update. Even though the columns must exist 
                    // at this point, run the migration to ensure consistency.
                    $ct->addColumn($cfield->name, $cfield->type, $params)->save();
                    
                    // Entry does not exist in customFields, so add it.
                    if ($count == 0) {
                        $cfield->params = json_encode($params);    
                        $d->insertObject('customFields', $cfield);
                    }
                    
                    $this_table_count += 1;
                    
                }
            }
        }
    }
}

?>
