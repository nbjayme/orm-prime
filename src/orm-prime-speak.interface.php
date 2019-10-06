<?php
interface iOrmPrimeSpeak {
  public function Connect($opts);
  public function Create($table, $fieldDefs, $opts = []);
  public function Insert($table, $fieldVals);
  public function Update($table, $fieldVals, $filterOpts = []);
  public function Select($table, $fieldList, $filterOps = []);
  public function Delete($table, $filterOpts = []);
  public function Count($table, $filterOpts = []);
  public function Truncate($table);
}
