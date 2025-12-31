-- ============================================================
-- THÊM TÍNH NĂNG PHÊ DUYỆT PHÒNG
-- Chạy file này để thêm cột approved vào bảng rooms
-- ============================================================

-- Thêm cột approved vào bảng rooms
ALTER TABLE `rooms` 
ADD COLUMN `approved` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=chờ duyệt, 1=đã duyệt' 
AFTER `status`;

-- Cập nhật các phòng hiện có thành đã duyệt (nếu muốn)
-- UPDATE `rooms` SET `approved` = 1 WHERE `status` = 1 AND `removed` = 0;

