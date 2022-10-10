CREATE TABLE products.`groups` (
                          `id` INT NOT NULL AUTO_INCREMENT,
                          `name` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`id`)
);
CREATE TABLE products.`attributes` (
                          `id` INT NOT NULL AUTO_INCREMENT,
                          `name` VARCHAR(255) NOT NULL,
                          PRIMARY KEY (`id`)
);
CREATE TABLE `groups_to_attributes` (
                                        `group_id` INT NOT NULL,
                                        `attribute_id` INT NOT NULL,
                                        PRIMARY KEY (`group_id`,`attribute_id`)
);
alter table products.groups_to_attributes
    add constraint fk_groups_to_attributes_group_id_to_groups_id
        foreign key (group_id) references products.`groups` (id);
alter table products.groups_to_attributes
    add constraint fk_groups_to_attributes_attribute_id_to_attributes_id
        foreign key (attribute_id) references products.`attributes` (id);