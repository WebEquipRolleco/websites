# websites
Multiboutique Prestashop 1.7

# Installation des remotes GIT
git remote add preprod root@ns336802.cubeo.fr:/var/www/devrolleco/rolleco.git

# Modification de la BDD 
ALTER TABLE `ps_order_detail` ADD `week` INT(2) NULL AFTER `original_wholesale_price`;
ALTER TABLE `ps_order_detail` ADD `day` DATE NULL AFTER `week`;
ALTER TABLE `ps_order_detail` ADD `comment` TEXT NULL AFTER `day`;
ALTER TABLE `ps_order_detail` ADD `id_supplier` TEXT NULL AFTER `product_attribute_id`;

ALTER TABLE `ps_shop` ADD `reference_prefix` VARCHAR(10) NULL AFTER `name`;
ALTER TABLE `ps_shop` ADD `reference_length` INT NOT NULL DEFAULT '5' AFTER `reference_prefix`;

ALTER TABLE `ps_customer` ADD `id_account_type` INT(11) NULL AFTER `id_risk`;
ALTER TABLE `ps_customer` ADD `id_customer_state` INT(11) NULL AFTER `id_account_type`;
ALTER TABLE `ps_customer` ADD `comment` TEXT NULL AFTER `id_customer_state`;
ALTER TABLE `ps_customer` ADD `reference` VARCHAR(255) NULL AFTER `id_risk`;
ALTER TABLE `ps_customer` ADD `chorus` VARCHAR(255) NULL AFTER `reference`;
ALTER TABLE `ps_customer` ADD `tva` VARCHAR(255) NULL AFTER `chorus`;
ALTER TABLE `ps_customer` ADD `funding` TINYINT(1) DEFAULT '1' AFTER `tva`;
ALTER TABLE `ps_customer` ADD `date_funding` DATE AFTER `funding`;
ALTER TABLE `ps_customer` ADD `email_invoice` VARCHAR(255) NULL AFTER `email`;
ALTER TABLE `ps_customer` ADD `email_tracking` VARCHAR(255) NULL AFTER `email_invoice`;

ALTER TABLE `ps_supplier` ADD `emails` VARCHAR(255) NULL AFTER `name`;
ALTER TABLE `ps_supplier` ADD `BC` VARCHAR(255) NULL AFTER `active`;
ALTER TABLE `ps_supplier` ADD `BL` VARCHAR(255) NULL AFTER `BC`;

ALTER TABLE `ps_orders` ADD `internal_reference` VARCHAR(255) NULL AFTER `reference`;
ALTER TABLE `ps_orders` ADD `delivery_information` TEXT NULL AFTER `internal_reference`;
ALTER TABLE `ps_orders` ADD `supplier_information` TEXT NULL AFTER `delivery_information`;
ALTER TABLE `ps_orders` ADD `no_recall` TINYINT DEFAULT '0' AFTER `valid`;
ALTER TABLE `ps_orders` ADD `display_with_taxes` TINYINT DEFAULT '0' AFTER `no_recall`;

CREATE TABLE IF NOT EXISTS `ps_product_icon` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NULL, 
    `title` TEXT NULL, 
    `url` VARCHAR(255) NULL, 
    `extension` VARCHAR(5) NULL, 
    `height` INT(11) NULL, 
    `width` INT(11) NULL, 
    `white_list` VARCHAR(255) NULL, 
    `black_list` VARCHAR(255) NULL, 
    `position` INT(11) DEFAULT 1, 
    `active` TINYINT DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_product_icon_shop` (
    `id_product_icon` INT NOT NULL,
    `id_shop` INT NOT NULL
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_daily_objective` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `date` DATE NOT NULL, 
    `value` FLOAT NOT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `ps_newsletter` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_shop` INTEGER(1) NOT NULL, 
    `id_shop_group` INTEGER(1) NOT NULL, 
    `email` VARCHAR(255) NOT NULL, 
    `ip` VARCHAR(16) NULL, 
    `date_add` DATE NULL DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_customer_state` (
    `id_customer_state` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NULL, 
    `color` VARCHAR(255) NULL, 
    `light_text` TINYINT DEFAULT 0, 
    `show_customer` TINYINT DEFAULT 1, 
    PRIMARY KEY (`id_customer_state`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_oa` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_order` INT NOT NULL, 
    `id_supplier` INT NOT NULL, 
    `code` VARCHAR(255) NULL, 
    `date_BC` DATETIME DEFAULT NULL, 
    `date_BL` DATETIME DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_option` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NOT NULL, 
    `description` TEXT NULL,
    `type` INT(1) NOT NULL, 
    `value` FLOAT NULL, 
    `white_list` TEXT NULL,
    `black_list` TEXT NULL,
    `active` TINYINT DEFAULT 0, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_option_cart` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_option` INT NOT NULL, 
    `id_cart` INT NOT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_option_history` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_order` INT NOT NULL, 
    `name` VARCHAR(255) NOT NULL, 
    `description` TEXT NULL,
    `value` FLOAT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_account_type` (
    `id_account_type` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NOT NULL, 
    `extra_information` TINYINT DEFAULT 0,
    `tva` TINYINT DEFAULT 0, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_webequip_quotations` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(255) NULL, 
    `status` INTEGER(1) NOT NULL, 
    `id_customer` INTEGER(11) NULL, 
    `origin` INT(1) NULL, 
    `email` VARCHAR(255) NULL, 
    `hidden_emails` VARCHAR(255) NULL,
    `date_add` DATETIME NULL,
    `date_begin` DATE NULL,
    `date_end` DATE NULL,
    `date_recall` DATE NULL,
    `phone` VARCHAR(255) NULL, 
    `fax` VARCHAR(255) NULL, 
    `comment` TEXT NULL, 
    `details` TEXT NULL, 
    `id_employee` INTEGER(11) NULL,
    `active` TINYINT(1) NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_webequip_quotation_lines` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(255) NULL, 
    `name` VARCHAR(255) NULL, 
    `information` TEXT NULL, 
    `comment` TEXT NULL, 
    `buying_price` FLOAT(11) NULL, 
    `selling_price` FLOAT(11) NULL, 
    `quantity` INTEGER(11) NULL,
    `position` INTEGER(11) NULL,
    `id_quotation` INTEGER(11) NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_webequip_quotation_associations` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_line` INTEGER(11) NULL,
    `id_cart` INTEGER(11) NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_after_sale` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `number` VARCHAR(255) NULL,
    `firstname` VARCHAR(255) NULL, 
    `lastname` VARCHAR(255) NULL, 
    `company` VARCHAR(255) NULL, 
    `phone` VARCHAR(255) NULL, 
    `email` VARCHAR(255) NULL, 
    `city` VARCHAR(255) NULL, 
    `content` TEXT NULL, 
    `id_customer` INTEGER(11) NULL, 
    `date_add` DATE NULL DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;