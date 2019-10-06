<?php
class OrmPrimeConnection {
  private $ormSpeak = null;
  private $connection = null;

  public function __construct($connection, $ormSpeak)
  {
    $this->connection = $connection;
    $this->ormSpeak = $ormSpeak;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function getOrmSpeak()
  {
    return $this->ormSpeak;
  }

  public function execute($sqlCmd, $sqlParams = [])
  {
    $stmt = $this->connection->prepare($sqlCmd);
    $stmt->execute($sqlParams);
    return $stmt;
  }

  public function Insert($table, $fieldVals)
  {
    $cmd = $this->ormSpeak->Insert($table, $fieldVals);
    $this->execute($cmd, $fieldVals);
  }

  public function Update($table, $fieldVals, $filterOpts = [])
  {
    $cmd = $this->ormSpeak->Update($table, $fieldVals, $filterOpts);
    $tokens = $fieldVals + $filterOpts['tokens'];
    return $this->execute($cmd, $tokens);
  }

  public function Select($table, $fieldList = ['*'], $filterOpts = [])
  {
    $cmd = $this->ormSpeak->Select($table, $fieldList, $filterOpts);
    return $this->execute($cmd, $filterOpts['tokens']);
  }

  public function Delete($table, $filterOpts = [])
  {
    $cmd = $this->ormSpeak->Delete($table, $filterOpts);
    return $this->execute($cmd, $filterOpts['tokens']);
  }

  public function Truncate($table)
  {
    $cmd = $this->ormSpeak->Truncate($table);
    return $this->execute($cmd);
  }

  public function Count($table, $filterOpts = [])
  {
    $cmd = $this->ormSpeak->Count($table, $filterOpts);
    $stmt = $this->execute($cmd, $filterOpts['tokens']);
    $rowData = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rowData['cnt'];
  }

  public function __destruct()
  {
    $this->connection = null;
    $this->ormSpeak = null;
  }
}
