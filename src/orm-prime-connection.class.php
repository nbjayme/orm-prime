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

  public function Insert($opts, $tokens)
  {
    $cmd = $this->ormSpeak->Insert($opts);
    $this->execute($cmd, $tokens);
  }

  public function Update($opts, $tokens)
  {
    $cmd = $this->ormSpeak->Update($opts);
    return $this->execute($cmd, $tokens);
  }

  public function Select($opts, $tokens)
  {
    $cmd = $this->ormSpeak->Select($opts);
    return $this->execute($cmd, $tokens);
  }

  public function Delete($opts, $tokens)
  {
    $cmd = $this->ormSpeak->Delete($opts);
    return $this->execute($cmd, $tokens);
  }

  public function Truncate($opts)
  {
    $cmd = $this->ormSpeak->Truncate($opts);
    return $this->execute($cmd);
  }

  public function Count($opts, $tokens = [])
  {
    $cmd = $this->ormSpeak->Count($opts);
    $stmt = $this->execute($cmd, $tokens);
    $rowData = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rowData['cnt'];
  }

  public function __destruct()
  {
    $this->connection = null;
    $this->ormSpeak = null;
  }
}
