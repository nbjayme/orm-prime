<?php

class OrmPrimeTable {

  private $connProfile = null;
  private $arrOptions =[
    'table' => '',
    'fields' => []
  ];

  private $ormFilter = null;
  private $ormSpeak = null;
  private $connection = null;
  private $fields = null;
  private $table = '';

  public function __construct($arrOptions, $connProfile)
  {
    $this->connProfile = $connProfile;
    if (is_string($connProfile))
    {
      $instance = OrmPrimeInstance::getInstance();
      $connProfile = $instance->getProfile($connProfile);
      $this->connProfile = $connProfile;
    }
    $this->connection = & $this->connProfile['connection'];
    $this->ormSpeak = & $this->connProfile['ormSpeak'];

    $this->arrOptions = $arrOptions;
    $this->table = & $this->arrOptions['table'];
    $this->fields = & $this->arrOptions['fields'];
    return $this;
  }

  public function OrmFilter($ormFilter = NULL)
  {
      if (!empty($ormFilter))
      {
        /* assign */
        $this->ormFilter = $ormFilter;
        return $this;
      }

      if (empty($this->ormFilter))
      {
        $this->ormFilter = new OrmPrimeFilter();
      }
      return $this->ormFilter;
  }

  public function ClearOrmFilter()
  {
    $this->ormFilter = null;
    return $this;
  }

  public function Model()
  {
    return $this->arrModel;
  }

  public function Where($condString)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Where($condString);
    return $this;
  }

  public function Fields($arrFields)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Fields($arrFields);
    return $this;
  }

  public function Group($arrFields)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Group($arrFields);
    return $this;
  }

  public function Having($condString)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Having($condString);
    return $this;
  }

  public function Limit($num)
  {
    $ormFilter = $this->ormFilter();
    $ormFilter->Limit($num);
    return $this;
  }

  public function Page($num)
  {
    $ormFilter = $this->ormFilter();
    $ormFilter->Page($num);
    return $this;
  }

  public function Rows($num)
  {
    $ormFilter = $this->ormFilter();
    $ormFilter->Rows($num);
    return $this;
  }

  public function Order($arrOrder)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Order($arrOrder);
    return $this;
  }

  public function Tokens($arrTokens)
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Tokens($arrTokens);
    return $this;
  }

  public function Insert($arrValues)
  {
    $opts = [
      'table' => $this->table,
      'fields' => $arrValues
    ];
    $cmd = $this->ormSpeak->Insert($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($arrValues);
    $this->ClearOrmFilter();
  }

  public function Update($arrValues)
  {
    $opts = $this->OrmFilter()->toArray();
    $opts['table'] = $this->table;
    $opts['fields'] = $arrValues;
    $cmd = $this->ormSpeak->Update($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($arrValues + $opts['tokens']);
    $this->ClearOrmFilter();
  }

  public function Select()
  {
    $opts = $this->OrmFilter()->toArray();
    $opts['table'] = $this->table;
    $cmd = $this->ormSpeak->Select($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($opts['tokens']);
    $colls = [];
    while ($rowData = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $records = (array) $rowData;
        $f = [];
        foreach($this->fields as $k => $v)
        {
          $f[$k] = $v;
          if (isset($records[$k]))
          {
            $f[$k]['value'] = $records[$k];
          }
        }
        $colls[] = new OrmPrimeRowset([
          'table' => $this->table,
          'fields' => $f,
        ], $this->connProfile);
    }
    $this->ClearOrmFilter();
    return (new OrmPrimeCollections($colls));
  }

  public function SelectOne()
  {
    $ormFilter = $this->OrmFilter();
    $ormFilter->Page('*'); // disable pagination
    $ormFilter->Limit(1);
    $colls = $this->Select();
    if($colls->Length() == 0)
    {
      return [];
    }
    $this->ClearOrmFilter();
    return $colls->getIndex(0);
  }

  public function Delete()
  {
    $opts = $this->OrmFilter()->toArray();
    $opts['table'] = $this->table;
    $cmd = $this->ormSpeak->Delete($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($opts['tokens']);
    $this->ClearOrmFilter();
  }

  public function Truncate()
  {
    $opts = [
      'table' => $this->table,
    ];
    $cmd = $this->ormSpeak->Truncate($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute();
    $this->ClearOrmFilter();
  }

  public function Count()
  {
    $opts = $this->OrmFilter()->toArray();
    $opts['fields'] = ['COUNT(*) AS cnt'];
    $opts['table'] = $this->table;
    $cmd = $this->ormSpeak->Count($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($opts['tokens']);
    $rowData = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->ClearOrmFilter();
    return $rowData['cnt'];
  }

}
