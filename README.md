# websites
Multiboutique Prestashop 1.7

# Installation des remotes GIT
git remote add preprod root@ns336802.cubeo.fr:/var/www/devrolleco/rolleco.git
git remote add prod root@ns336802.cubeo.fr:/var/www/dev2rolleco/webequip.git

# Modification de la BDD 
ALTER TABLE `ps_order_detail` ADD `delivery_fees` FLOAT DEFAULT '0' AFTER `product_price`;
ALTER TABLE `ps_order_detail` ADD `week` INT(2) NULL AFTER `original_wholesale_price`;
ALTER TABLE `ps_order_detail` ADD `day` DATE NULL AFTER `week`;
ALTER TABLE `ps_order_detail` ADD `comment` TEXT NULL AFTER `day`;
ALTER TABLE `ps_order_detail` ADD `comment_product_1` TEXT NULL AFTER `comment`;
ALTER TABLE `ps_order_detail` ADD `comment_product_2` TEXT NULL AFTER `comment_product_1`;
ALTER TABLE `ps_order_detail` ADD `notification_sent` TINYINT DEFAULT '0' AFTER `comment_product_2`;
ALTER TABLE `ps_order_detail` ADD `prevent_notification` TINYINT DEFAULT '0' AFTER `notification_sent`;
ALTER TABLE `ps_order_detail` ADD `id_quotation_line` TEXT NULL AFTER `product_attribute_id`;
ALTER TABLE `ps_order_detail` ADD `id_product_supplier` INT NULL AFTER `id_quotation_line`;

ALTER TABLE `ps_order_state` ADD `id_m3` TINYINT DEFAULT NULL AFTER `id_order_state`;
ALTER TABLE `ps_order_state` ADD `term_of_use` TINYINT DEFAULT '0' AFTER `pdf_delivery`;
ALTER TABLE `ps_order_state` ADD `proforma` TINYINT DEFAULT '0' AFTER `paid`;
ALTER TABLE `ps_order_state` ADD `rollcash` TINYINT DEFAULT '0' AFTER `proforma`;

ALTER TABLE `ps_shop` ADD `reference_prefix` VARCHAR(10) NULL AFTER `name`;
ALTER TABLE `ps_shop` ADD `reference_length` INT NOT NULL DEFAULT '5' AFTER `reference_prefix`;
ALTER TABLE `ps_shop` ADD `quotation_prefix` VARCHAR(10) NULL AFTER `reference_length`;
ALTER TABLE `ps_shop` ADD `quotation_number` INT NOT NULL DEFAULT '0' AFTER `quotation_prefix`;
ALTER TABLE `ps_shop` ADD `color` VARCHAR(30) NULL AFTER `theme_name`;

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
ALTER TABLE `ps_customer` ADD `rollcash` FLOAT DEFAULT '0' AFTER `email_tracking`; 
ALTER TABLE `ps_customer` ADD `quotation` INT DEFAULT '0' AFTER `rollcash`; 

ALTER TABLE `ps_supplier` ADD `reference` VARCHAR(30) NULL AFTER `id_supplier`;
ALTER TABLE `ps_supplier` ADD `emails` VARCHAR(255) NULL AFTER `name`;
ALTER TABLE `ps_supplier` ADD `email_sav` VARCHAR(255) NULL AFTER `emails`;
ALTER TABLE `ps_supplier` ADD `BC` VARCHAR(255) NULL AFTER `active`;
ALTER TABLE `ps_supplier` ADD `BL` VARCHAR(255) NULL AFTER `BC`;

ALTER TABLE `ps_orders` ADD `internal_reference` VARCHAR(255) NULL AFTER `reference`;
ALTER TABLE `ps_orders` ADD `m3_reference` TEXT NULL AFTER `internal_reference`;
ALTER TABLE `ps_orders` ADD `delivery_information` TEXT NULL AFTER `m3_reference`;
ALTER TABLE `ps_orders` ADD `supplier_information` TEXT NULL AFTER `delivery_information`;
ALTER TABLE `ps_orders` ADD `no_recall` TINYINT DEFAULT '0' AFTER `valid`;
ALTER TABLE `ps_orders` ADD `exported` INT(1) DEFAULT '0' AFTER `no_recall`;
ALTER TABLE `ps_orders` ADD `display_with_taxes` TINYINT DEFAULT '0' AFTER `exported`;
ALTER TABLE `ps_orders` ADD `invoice_comment` TEXT NULL AFTER `invoice_number`;

ALTER TABLE `ps_category_lang` ADD `bottom_description` TEXT NULL AFTER `description`;

ALTER TABLE `ps_product` ADD `custom_ecotax` FLOAT DEFAULT '0' AFTER `ecotax`;
ALTER TABLE `ps_product` ADD `rollcash` FLOAT DEFAULT '0' AFTER `state`;
ALTER TABLE `ps_product` ADD `destocking` TINYINT DEFAULT '0' AFTER `rollcash`;
ALTER TABLE `ps_product` ADD `comment_1` TEXT DEFAULT NULL AFTER `destocking`;
ALTER TABLE `ps_product` ADD `comment_2` TEXT DEFAULT NULL AFTER `comment_1`;
ALTER TABLE `ps_product` ADD `batch` INT DEFAULT '1' AFTER `comment_2`;

ALTER TABLE `ps_product_shop` ADD `custom_ecotax` FLOAT DEFAULT '0' AFTER `ecotax`;
ALTER TABLE `ps_product_shop` ADD `delivery_fees` FLOAT DEFAULT '0' AFTER `pack_stock_type`;
ALTER TABLE `ps_product_shop` ADD `comment_1` TEXT DEFAULT NULL AFTER `delivery_fees`;
ALTER TABLE `ps_product_shop` ADD `comment_2` TEXT DEFAULT NULL AFTER `comment_1`;
ALTER TABLE `ps_product_shop` ADD `batch` INT DEFAULT NULL AFTER `comment_2`;

ALTER TABLE `ps_product_attribute` ADD `custom_ecotax` FLOAT DEFAULT '0' AFTER `ecotax`;
ALTER TABLE `ps_product_attribute` ADD `rollcash` FLOAT DEFAULT '0' AFTER `price`;
ALTER TABLE `ps_product_attribute` ADD `position` FLOAT DEFAULT '0' AFTER `rollcash`;
ALTER TABLE `ps_product_attribute` ADD `batch` INT DEFAULT '1' AFTER `position`;
ALTER TABLE `ps_product_attribute` ADD `comment_1` TEXT DEFAULT NULL AFTER `batch`;
ALTER TABLE `ps_product_attribute` ADD `comment_2` TEXT DEFAULT NULL AFTER `comment_1`;

ALTER TABLE `ps_product_attribute_shop` ADD `custom_ecotax` FLOAT DEFAULT '0' AFTER `ecotax`;
ALTER TABLE `ps_product_attribute_shop` ADD `delivery_fees` FLOAT DEFAULT '0' AFTER `price`;
ALTER TABLE `ps_product_attribute_shop` ADD `rollcash` FLOAT DEFAULT '0' AFTER `delivery_fees`;
ALTER TABLE `ps_product_attribute_shop` ADD `position` FLOAT DEFAULT '0' AFTER `rollcash`;
ALTER TABLE `ps_product_attribute_shop` ADD `batch` INT DEFAULT NULL AFTER `position`;
ALTER TABLE `ps_product_attribute_shop` ADD `comment_1` TEXT DEFAULT NULL AFTER `batch`;
ALTER TABLE `ps_product_attribute_shop` ADD `comment_2` TEXT DEFAULT NULL AFTER `comment_1`;

ALTER TABLE `ps_specific_price` ADD `full_price` FLOAT NULL AFTER `price`;
ALTER TABLE `ps_specific_price` ADD `buying_price` FLOAT NULL AFTER `to`;
ALTER TABLE `ps_specific_price` ADD `delivery_fees` FLOAT NULL AFTER `buying_price`;
ALTER TABLE `ps_specific_price` ADD `comment_1` TEXT NULL AFTER `delivery_fees`;
ALTER TABLE `ps_specific_price` ADD `comment_2` TEXT NULL AFTER `comment_1`;

ALTER TABLE `ps_employee` ADD `sav` TINYINT DEFAULT '0' AFTER `active`;

ALTER TABLE `ps_attribute` ADD `reference` VARCHAR(10) DEFAULT NULL AFTER `id_attribute`;

ALTER TABLE `ps_attribute_group` ADD `reference` VARCHAR(10) DEFAULT NULL AFTER `id_attribute_group`;
ALTER TABLE `ps_attribute_group` ADD `quotation` TINYINT DEFAULT '1' AFTER `is_color_group`;
ALTER TABLE `ps_attribute_group` ADD `column` INT(2) DEFAULT NULL AFTER `position`;

ALTER TABLE `ps_cms` ADD `display_raw` TINYINT DEFAULT '0' AFTER `active`;

ALTER TABLE `ps_cms_lang` ADD `description` TEXT DEFAULT NULL AFTER `meta_keywords`;

ALTER TABLE `ps_feature` ADD `reference` VARCHAR(10) DEFAULT NULL AFTER `id_feature`;
ALTER TABLE `ps_feature` ADD `column` INT(2) DEFAULT NULL AFTER `position`;

ALTER TABLE `ps_feature_value` ADD `reference` VARCHAR(10) DEFAULT NULL AFTER `id_feature_value`;

ALTER TABLE `ps_feature_lang` ADD `public_name` VARCHAR(255) DEFAULT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `ps_product_matching` (
    `id_product_matching` INT NOT NULL AUTO_INCREMENT,
    `id_product` INT(11) DEFAULT NULL, 
    `id_combination` INT(11) DEFAULT NULL, 
    PRIMARY KEY (`id_product_matching`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_product_icon` (
    `id_product_icon` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT NULL, 
    `title` TEXT DEFAULT NULL, 
    `url` VARCHAR(255) DEFAULT NULL, 
    `extension` VARCHAR(5) DEFAULT NULL, 
    `height` INT(11) DEFAULT NULL, 
    `width` INT(11) DEFAULT NULL, 
    `white_list` VARCHAR(255) DEFAULT NULL, 
    `black_list` VARCHAR(255) DEFAULT NULL, 
    `position` INT(2) DEFAULT 1, 
    `location` INT(2) DEFAULT 1, 
    `active` TINYINT DEFAULT 1,
    PRIMARY KEY (`id_product_icon`)
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
    `ip` VARCHAR(16) DEFAULT NULL, 
    `date_add` DATE NULL DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_customer_state` (
    `id_customer_state` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) DEFAULT NULL, 
    `color` VARCHAR(255) DEFAULT NULL, 
    `light_text` TINYINT DEFAULT 0, 
    `show_customer` TINYINT DEFAULT 1,
    `risk_level` INT(1) DEFAULT 0, 
    PRIMARY KEY (`id_customer_state`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_oa` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `id_order` INT NOT NULL, 
    `id_supplier` INT NOT NULL, 
    `code` VARCHAR(255) DEFAULT NULL, 
    `date_BC` DATETIME DEFAULT NULL, 
    `date_BL` DATETIME DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_state_rule` (
    `id_order_state_rule` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NOT NULL, 
    `description` TEXT NULL,
    `ids` TEXT DEFAULT NULL,
    `target_id` INT NOT NULL,
    `active` TINYINT DEFAULT 0, 
    PRIMARY KEY (`id_order_state_rule`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_option` (
    `id_order_option` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(30) NULL, 
    `name` VARCHAR(255) NOT NULL, 
    `description` TEXT DEFAULT NULL,
    `warning` TEXT DEFAULT NULL,
    `type` INT(1) NOT NULL, 
    `value` FLOAT DEFAULT NULL, 
    `white_list` TEXT DEFAULT NULL,
    `black_list` TEXT DEFAULT NULL,
    `active` TINYINT DEFAULT 0, 
    PRIMARY KEY (`id_order_option`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_order_option_shop` (
    `id_order_option` INT NOT NULL, 
    `id_shop` INT NOT NULL, 
    `active` TINYINT DEFAULT 0
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
    `name` VARCHAR(255) DEFAULT NULL, 
    `description` TEXT DEFAULT NULL,
    `value` FLOAT DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_account_type` (
    `id_account_type` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NOT NULL, 
    `company` TINYINT DEFAULT 0,
    `siret` TINYINT DEFAULT 0,
    `chorus` TINYINT DEFAULT 0,
    `tva` TINYINT DEFAULT 0, 
    `default_value` TINYINT DEFAULT 0, 
    PRIMARY KEY (`id_account_type`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_quotation` (
    `id_quotation` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(255) DEFAULT NULL, 
    `status` INTEGER(1) NOT NULL, 
    `id_customer` INTEGER(11) DEFAULT NULL, 
    `origin` INT(1) DEFAULT NULL, 
    `source` INT(1) DEFAULT NULL, 
    `email` VARCHAR(255) DEFAULT NULL, 
    `phone` VARCHAR(255) DEFAULT NULL, 
    `fax` VARCHAR(255) DEFAULT NULL, 
    `date_add` DATETIME DEFAULT NULL,
    `date_begin` DATE DEFAULT NULL,
    `date_end` DATE DEFAULT NULL,
    `date_recall` DATE DEFAULT NULL,
    `comment` TEXT DEFAULT NULL, 
    `details` TEXT DEFAULT NULL, 
    `id_employee` INTEGER(11) DEFAULT NULL,
    `active` TINYINT(1) DEFAULT NULL,
    `new` TINYINT(1) DEFAULT NULL,
    `hightlight` TINYINT(1) DEFAULT NULL,
    `option_ids` VARCHAR(255) DEFAULT NULL, 
    `document_ids` VARCHAR(255) DEFAULT NULL, 
    `id_shop` INT(1) DEFAULT NULL,
    `secure_key` VARCHAR(255) DEFAULT NULL, 
    `mail_sent` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id_quotation`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_quotation_line` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(255) DEFAULT NULL, 
    `reference_supplier` VARCHAR(255) DEFAULT NULL, 
    `name` VARCHAR(255) DEFAULT NULL, 
    `properties` TEXT DEFAULT NULL, 
    `information` TEXT DEFAULT NULL, 
    `comment` TEXT DEFAULT NULL, 
    `buying_price` FLOAT(11) DEFAULT NULL, 
    `buying_fees` FLOAT(11) DEFAULT NULL, 
    `selling_price` FLOAT(11) DEFAULT NULL, 
    `eco_tax` FLOAT(11) DEFAULT NULL, 
    `quantity` INTEGER(11) DEFAULT NULL,
    `min_quantity` INTEGER(11) DEFAULT NULL,
    `position` INTEGER(11) DEFAULT NULL,
    `id_quotation` INTEGER(11) DEFAULT NULL,
    `id_supplier` INTEGER(11) DEFAULT NULL,
    `id_product` INTEGER(11) DEFAULT NULL,
    `id_combination` INTEGER(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_quotation_association` (
    `id_quotation_association` INT NOT NULL AUTO_INCREMENT, 
    `id_quotation` INTEGER(11) DEFAULT NULL,
    `id_cart` INTEGER(11) DEFAULT NULL,
    PRIMARY KEY (`id_quotation_association`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_after_sale` (
    `id_after_sale` INT NOT NULL AUTO_INCREMENT, 
    `reference` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL, 
    `id_customer` INTEGER(11) DEFAULT NULL, 
    `id_order` INTEGER(11) DEFAULT NULL, 
    `ids_detail` VARCHAR(255) DEFAULT NULL,
    `status` INTEGER(1) DEFAULT NULL, 
    `condition` VARCHAR(255) DEFAULT NULL,
    `notice_on_delivery` TINYINT(1) DEFAULT '0',
    `date_add` DATETIME NULL DEFAULT NULL, 
    `date_upd` DATETIME NULL DEFAULT NULL, 
    PRIMARY KEY (`id_after_sale`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_after_sale_history` (
    `id_after_sale_history` INT NOT NULL AUTO_INCREMENT, 
    `id_after_sale` INTEGER(11) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL, 
    `id_employee` INTEGER(11) DEFAULT NULL, 
    `date_add` DATETIME DEFAULT NULL, 
    PRIMARY KEY (`id_after_sale_history`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ps_after_sale_message` (
    `id_after_sale_message` INT NOT NULL AUTO_INCREMENT, 
    `id_after_sale` INTEGER(11) DEFAULT NULL, 
    `id_customer` INTEGER(11) DEFAULT NULL, 
    `id_employee` INTEGER(11) DEFAULT NULL, 
    `id_supplier` INTEGER(11) DEFAULT NULL, 
    `message` TEXT DEFAULT NULL,
    `display` INTEGER(1) DEFAULT NULL, 
    `new` INTEGER(1) DEFAULT NULL, 
    `date_add` DATETIME DEFAULT NULL, 
    PRIMARY KEY (`id_after_sale_message`)
) ENGINE = InnoDB;