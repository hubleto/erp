<?php

namespace Hubleto\App\Community\PriceList\Models\Migrations;

class History_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_history`;
      create table `price_lists_history` (
      `id` int(8) primary key auto_increment,
      `id_price_list` int(8) NULL default NULL,
      `id_item` int(8) NULL default NULL,
      `id_product` int(8) NULL default NULL,
      `event_type` int(255) ,
      `old_value` decimal(14, 4) ,
      `new_value` decimal(14, 4) ,
      `short_description` varchar(255) ,
      `date_time` datetime ,
      `id_user` int(8) NULL default NULL,
      `source` varchar(255) ,
      index `id` (`id`),
      index `id_price_list` (`id_price_list`),
      index `id_item` (`id_item`),
      index `id_product` (`id_product`),
      index `event_type` (`event_type`),
      index `date_time` (`date_time`),
      index `id_user` (`id_user`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_history`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_history` ADD CONSTRAINT `fk_7dffaa59a0ba3509021907c9d1fd4853` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_history` ADD CONSTRAINT `fk_85c8a347660d64fa1e19675e089ec4ee` FOREIGN KEY (`id_item`) REFERENCES `price_lists_items` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_history` ADD CONSTRAINT `fk_14f27c4f87058992e68e6c4d69c67f99` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_history` ADD CONSTRAINT `fk_a4e60091cf06e5a14403bdf6feaf337c` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_history` DROP FOREIGN KEY `fk_7dffaa59a0ba3509021907c9d1fd4853`;
      ALTER TABLE `price_lists_history` DROP FOREIGN KEY `fk_85c8a347660d64fa1e19675e089ec4ee`;
      ALTER TABLE `price_lists_history` DROP FOREIGN KEY `fk_14f27c4f87058992e68e6c4d69c67f99`;
      ALTER TABLE `price_lists_history` DROP FOREIGN KEY `fk_a4e60091cf06e5a14403bdf6feaf337c`;
    ");
  }
}
