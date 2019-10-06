<?php

class OrmPrimeSpeakMysql implements iOrmPrimeSpeak {
  private $dialect = 'mysql';

  public function __construct()
  {
  }

  public function getDialect()
  {
    return $this->dialect;
  }

  public function Connect($opts=[])
  {
      $optDefaults = [
        'host' => '',
        'database' => '',
      ];
      $opts = array_replace($optDefaults,
        array_intersect_key($opts, $optDefaults));

      return "mysql:host=" . $opts['host'] . ';'
        . "dbname=" . $opts['database'];
  }

  public function Create($table, $fieldDefs, $opts = [])
  {

    $optDefaults = [
      'primarykeys' => [],
      'engine' => 'myisam',
      'charset' => 'utf8'
    ];

    $opts = array_replace($optDefaults,
      array_intersect_key($opts, $optDefaults));

    $fieldStructs = [];
    foreach($fieldDefs as $k => $v)
    {
      $pKey = "";
      if (isset($v['is_key']))
      {
        $pKey = $k;
      }
      if ($pKey !== "")
      {
        $opt['primarykeys'][] = $pKey;
      }
      $fieldDef = "`" . $k . "` " . $v['type'] . "(" . $v['length'] . ") ";

      $defValue = "''";
      if (is_numeric($v['default_value']))
      {
        $defValue = '0';
      }
      if (is_float($v['default_value']))
      {
        $defValue = '0.00';
      }
      if(!empty($v['default_value']))
      {
        $defValue = $v['default_value'];
      }
      $fieldDef .= "DEFAULT " . $defValue;

      $fieldStructs[] = $fieldDef;
    }

    $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` "
      . "(" . implode(', ', $fieldStructs) . ") "
      . "ENGINE=" . $opts['engine'] . " "
      . "DEFAULT CHARSET=" . $opts['charset'] . " ";

    if (!empty($opts['primarykeys']))
    {
      $sql .=  implode(',', $opts['primarykeys']);
    }
    $sql .= ";";

    return $sql;
  }

  public function Insert($table, $fieldVals)
  {
    $keys = [];
    $tKeys = [];
    foreach($fieldVals as $k => $v)
    {
      $keys[] = $k;
      $tKeys[] = ':' . $k;
    }
    $sql =  'INSERT INTO ' . $table
       . '(' . implode(', ', $keys) . ')'
       . ' VALUES '
       . '(' . implode(', ', $tKeys) . ');';
     return $sql;
  }

  public function Update($table, $fieldVals, $filterOpts = [])
  {
    $optDefaults = [
      'where' => ''
    ];
    $opts = array_replace($optDefaults,
      array_intersect_key($filterOpts, $optDefaults));

    $keys = [];
    foreach($fieldVals as $k => $v)
    {
      $keys[] = $k . ' = :' . $k;
    }

    $sql =  'UPDATE ' . $table . ' '
      . 'SET '
      . implode(', ', $keys) . ' ';
    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' . $opts['where'] . ' ';
    }
    $sql .= ';';

    return $sql;
  }

  public function Select($table, $fieldList = ['*'], $filterOpts = [])
  {
    $optDefaults = [
      'where' => '',
      'group' => [],
      'having' => '',
      'order' => [],
      'page' => '*',
      'rows' => 50,
      'limit' => '*',
    ];

    $opts = array_replace($optDefaults,
      array_intersect_key($filterOpts, $optDefaults));

    $sql = 'SELECT '  . implode(', ', $fieldList) . ' '
      . 'FROM ' . $table . ' ';

    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' .  $opts['where'] . ' ';
    }
    if (!empty($opts['group']))
    {
      $sql .= 'GROUP BY ' . implode(', ', $opts['group']) . ' ';
    }
    if ($opts['having'] !== '')
    {
      $sql .= 'HAVING ' . $opts['having'] . ' ';
    }
    if(!empty($opts['order']))
    {
      $sql .= 'ORDER BY ' . implode(', ', $opts['order']) . ' ';
    }
    if($opts['page'] !== '*')
    {
      $pg = $opts['page'];
      $pg --;
      if ($pg < 0)
      {
        $pg = 0;
      }
      $startRow =$pg * $opts['rows'];
      $sql .= 'LIMIT ' . $startRow . ', ' . $opts['rows'];
    }else if ($opts['limit'] !== '*')
    {
      $sql .= 'LIMIT ' . $opts['limit'];
    }
    return $sql . ';';
  }

  public function Delete($table, $filterOpts= [])
  {
    $optDefaults = [
      'where' => '',
      'limit' => '*',
    ];
    $opts = array_replace($optDefaults,
      array_intersect_key($filterOpts, $optDefaults));

    $sql =  'DELETE FROM ' . $table . ' ';
    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' . $opts['where'] . ' ';
    }
    if ($opts['limit'] !== '*')
    {
      $sql .= 'LIMIT ' . $opts['limit'] . ' ';
    }
    $sql .= ';';
    return $sql;
  }

  public function Truncate($table)
  {
    $optDefaults = [
      'table' => ''
    ];

    $sql =  'TRUNCATE ' . $table . ';';
    return $sql;
  }

  public function Count($table, $filterOpts = [])
  {
    $optDefaults = [
      'where' => '',
    ];

    $opts = array_replace($optDefaults,
      array_intersect_key($filterOpts, $optDefaults));

    $sql = "SELECT " .  'COUNT(*) AS cnt' . " "
      . "FROM " . $table . " ";
    if($opts['where'] !== '')
    {
      $sql .= "WHERE " . $opts['where'];
    }
    $sql .= ";";
    return $sql;
  }

};
