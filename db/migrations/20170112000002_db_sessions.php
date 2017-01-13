<?php

use Phinx\Migration\AbstractMigration;

class DbSessions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('settings')->addColumn('dbSessions', 'set', array('after'=>'subnetView','values'=>array('Yes','No'), 'null'=>false, 'default'=>'No'))->update();
        $this->table('session_data', array('id'=>false, 'primary_key'=>array('session_id')))
                ->addColumn('session_id', 'string', array('length'=>32,'default'=>''))
                ->addColumn('hash', 'string', array('length'=>32,'default'=>''))
                ->addColumn('session_data', 'blob')
                ->addColumn('session_expire', 'integer', array('default'=>0))
                ->create();
    }
}
