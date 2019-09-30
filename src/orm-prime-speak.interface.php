<?php
interface iOrmPrimeSpeak {
  public function Connect($opts);
  public function Insert($opts);
  public function Update($opts);
  public function Select($opts);
  public function Delete($opts);
  public function Truncate($opts);
  public function Count($opts);
}
