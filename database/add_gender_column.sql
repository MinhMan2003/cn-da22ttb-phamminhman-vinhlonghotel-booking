-- Thêm cột gender vào bảng user_cred
ALTER TABLE `user_cred` 
ADD COLUMN `gender` ENUM('male', 'female') DEFAULT 'male' AFTER `dob`;

