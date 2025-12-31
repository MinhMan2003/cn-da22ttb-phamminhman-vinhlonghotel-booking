<?php
/**
 * Helper functions for destinations feature
 */

// Check and create destination_images table if not exists
if(!function_exists('ensureDestinationImagesTable')) {
    function ensureDestinationImagesTable($con) {
        // Check if table exists
        $check = mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
        if($check && mysqli_num_rows($check) > 0) {
            return; // Table exists
        }
        
        // Create table
        $create_table = "CREATE TABLE IF NOT EXISTS `destination_images` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `destination_id` INT(11) NOT NULL,
          `image` VARCHAR(255) NOT NULL COMMENT 'Tên file ảnh',
          `is_primary` TINYINT(1) DEFAULT 0 COMMENT '1=ảnh chính, 0=ảnh phụ',
          `sort_order` INT(11) DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_destination_id` (`destination_id`),
          KEY `idx_is_primary` (`is_primary`),
          CONSTRAINT `fk_destination_images_destination` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        mysqli_query($con, $create_table);
        
        // Migrate existing images from destinations.image to destination_images
        // Only migrate if destinations table exists
        $dest_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destinations'");
        if($dest_table_check && mysqli_num_rows($dest_table_check) > 0){
          $migrate_query = "INSERT INTO `destination_images` (`destination_id`, `image`, `is_primary`, `sort_order`)
                           SELECT `id`, `image`, 1, 1
                           FROM `destinations`
                           WHERE `image` IS NOT NULL AND `image` != ''
                           AND NOT EXISTS (
                             SELECT 1 FROM `destination_images` WHERE `destination_images`.`destination_id` = `destinations`.`id`
                           )";
          @mysqli_query($con, $migrate_query);
        }
    }
}

