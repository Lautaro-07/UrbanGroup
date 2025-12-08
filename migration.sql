-- SQL script to migrate the properties table for the new property_types system.
-- Run this in your phpMyAdmin SQL tab AFTER running property_types.sql

-- Step 1: Add the new column to store the foreign key.
-- It's nullable because we will lose the old data and it will need to be reassigned manually.
ALTER TABLE `properties` ADD `property_type_id` INT(11) NULL DEFAULT NULL AFTER `operation_type`;

-- Step 2: (Optional but recommended) Create an index on the new column for better performance.
CREATE INDEX `idx_property_type_id` ON `properties`(`property_type_id`);

-- Step 3: Add the foreign key constraint.
-- This ensures data integrity. It connects the properties table to the new property_types table.
-- Assumes your database engine is InnoDB.
ALTER TABLE `properties` ADD CONSTRAINT `fk_property_type`
  FOREIGN KEY (`property_type_id`)
  REFERENCES `property_types` (`id`)
  ON DELETE SET NULL -- If a property type is deleted, set the property's type to NULL
  ON UPDATE CASCADE; -- If a property type ID changes, update it here too

-- Step 4: Drop the old, now unused, property_type column.
-- WARNING: This step is irreversible and will delete all your old property type data.
-- Your existing properties will no longer have a type assigned until you edit them.
ALTER TABLE `properties` DROP COLUMN `property_type`;

