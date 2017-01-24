<?php

namespace Ipam\Migration;

use Phinx\Db\Adapter\MysqlAdapter;

class TransientAdapter extends MysqlAdapter
{

    /**
     * Sets the connection but nullifies creation of the schema table
     *
     * @param \PDO $connection Connection
     * @return AdapterInterface
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
        return $this;
    }
    
}