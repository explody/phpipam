<?php

use PHPUnit\Framework\TestCase;

$t = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
$t->prop1 = "thing";
$t->prop2 = "stuff";

$GLOBALS['test1'] = [ 
           "key1" => "val1",
           "ignore" => (object) [1,2,3],
           "traversable" => $t,
           "array" => ['aval1','aval2'],
           "deep" => ["1lev" => [ "2lev" => [ "3lev" => "3val" ]]],
          ];
           

class ImmutableConfigTest extends TestCase
{
    protected function setUp() {
        ImmutableConfig::init($GLOBALS['test1']);
        $this->c = ImmutableConfig::get();
    }
    
    public function testBasic()
    {
        $this->assertEquals("val1", $this->c->key1);
        $this->assertTrue(!property_exists($this->c, 'ignore'));
    }
}
?>
