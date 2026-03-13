<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Category_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_categories`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `product_categories` (
 `id` int(8) primary key auto_increment,
 `id_parent` int(8) NULL default NULL,
 `name` varchar(255) ,
 `color` char(10) ,
 `short_description` text ,
 `long_description` text ,
 `photo_1` varchar(255) ,
 `photo_2` varchar(255) ,
 `photo_3` varchar(255) ,
 `photo_4` varchar(255) ,
 `photo_5` varchar(255) ,
 `url_slug` varchar(255) ,
 index `id` (`id`),
 index `id_parent` (`id_parent`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_categories`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_categories`
          ADD CONSTRAINT `fk_bbf73e7a081d6ea9c35c236ba3aff2da`
          FOREIGN KEY (`id_parent`)
          REFERENCES `product_categories` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_categories`
          DROP FOREIGN KEY `fk_bbf73e7a081d6ea9c35c236ba3aff2da`;");
  }
}