
CREATE SCHEMA libdb_test DEFAULT CHARACTER SET utf8 COLLATE utf8_danish_ci;

CREATE  TABLE `libdb_test`.`sample` (
  `uid` INT NOT NULL AUTO_INCREMENT ,
  `case_number` INT NULL ,
  `date_value` DATE NULL ,
  `cpr` INT NULL ,
  `int_value` INT NULL ,
  `string_value` VARCHAR(45) NULL ,
  `decimal_value` DECIMAL NULL ,
  `boolean_value` INT NULL ,
  PRIMARY KEY (`uid`) )
ENGINE = InnoDB;
