<?php

class Date {
  private $date = null;
  const FMT_DA = "%d-%m-%Y";
  const FMT_MYSQL = "%Y-%m-%d";
  const FMT_YMD = "%Y%m%d";

  public function __construct($date = "", $fmt = self::FMT_DA) {
    if (!$date || !$fmt) {
      return;
    }
    if (is_object($date)) {
      if ($date instanceof Date) {
        $this->date = new DateTime($date->toString());
      }
      else if ($date instanceof DateTime) {
        $this->date = $date;
      }
    }
    else {
      if ($this->isMysqlDate($date)) {
        $fmt = self::FMT_MYSQL;
        if ($date == "0000-00-00") {
          return;
        }
      }
      $tmp = strptime($date, $fmt);
      if (!$tmp) {
        throw new IllegalArgumentException("date: $date", __FILE__, __LINE__);
      }
      $this->date = new DateTime();
      $this->date->setDate(1900+$tmp["tm_year"], $tmp["tm_mon"]+1, $tmp["tm_mday"]);
      $this->date->setTime($tmp["tm_hour"], $tmp["tm_min"], $tmp["tm_sec"]);
    }
  }

  public function isNull() {
    return is_null($this->date);
  }
  
  public function __toString() {
    return $this->toString();
  }
  
  public function toString() {
    return $this->format(self::FMT_MYSQL);
  }

  public function format($fmt) {
    $fmt = str_replace("%","", $fmt);
    return ($this->date != null ? $this->date->format($fmt) : "");
  }

  public function __get($name) {
    // This needs to be expanded when the need arises
    $fmt = "";
    switch ($name) {
      case "year" :
        $fmt = "Y";
        break;
      case "month" :
        $fmt = "m";
        break;
      case "day" :
        $fmt = "d";
        break;
      case "date" :
        $fmt = "Ymd";
        break;
      case "time" :
        $fmt = "His";
        break;
      case "datetime" :
        $fmt = "Ymd His";
        break;
      default:
        throw new IllegalArgumentException($name, __FILE__, __LINE__);
    }
    return intval($this->format($fmt));    
  }

  public function __set($name, $value) {
    $day = $this->day;
    $year = $this->year;
    $month = $this->month;
    switch ($name) {
      case "day":
        $this->date->setDate($year, $month, intval($value));
        break;
      case "month":
        $this->date->setDate($year, intval($value), $day);
        break;
      case "year":
        $this->date->setDate(intval($value), $month, $day);
        break;
      default:
        throw new IllegalArgumentException($name, __FILE__, __LINE__);
    }
  }
  
  public function equals(Date $other) {
    return ($this->format("Ymd") - $other->format("Ymd")) == 0;
  }
  
  public function isAfter(Date $other) {
    return ($this->format("Ymd") - $other->format("Ymd")) > 0;
  }

  public function isBefore(Date $other) {
    return ($this->format("Ymd") - $other->format("Ymd")) < 0;
  }

  public function diff(Date $other) {
    $days = $this->date->diff($other->date)->days;
    return ($this->isAfter($other) ? $days : -$days);
  }
  
  private static function isMysqlDate($date) {
    //Mysql date is on the form yyyy-mm-dd
    if ($date[4] == '-' && $date[7] == '-') {
      return true;
    }
    return false;
  }
}
