<?php
/**
 * AJAX endpoint for search suggestions/autocomplete
 * Supports both destinations and rooms search
 */

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode(['suggestions' => []]);
    exit;
}

$query = trim($_GET['q']);
$type = $_GET['type'] ?? 'all'; // 'destinations', 'rooms', 'all'
$limit = min(10, max(1, (int)($_GET['limit'] ?? 10)));

$suggestions = [];

// Function to remove Vietnamese accents for better matching
function vn_str_filter($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $unicode = [
        'a' => 'áàảãạăắằẳẵặâấầẩẫậ',
        'd' => 'đ',
        'e' => 'éèẻẽẹêếềểễệ',
        'i' => 'íìỉĩị',
        'o' => 'óòỏõọôốồổỗộơớờởỡợ',
        'u' => 'úùủũụưứừửữự',
        'y' => 'ýỳỷỹỵ'
    ];
    foreach ($unicode as $ascii => $chars) {
        $chars_array = preg_split('//u', $chars, -1, PREG_SPLIT_NO_EMPTY);
        $str = str_replace($chars_array, $ascii, $str);
    }
    $str = preg_replace('/[^a-z0-9\s]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

$query_normalized = vn_str_filter($query);
$query_escaped = mysqli_real_escape_string($con, $query);
$query_normalized_escaped = mysqli_real_escape_string($con, $query_normalized);

// Search Destinations
if($type === 'all' || $type === 'destinations') {
    $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destinations'");
    if($table_check && mysqli_num_rows($table_check) > 0) {
        $dest_query = "SELECT id, name, location, category 
                       FROM destinations 
                       WHERE active = 1 
                       AND (
                           name LIKE '%{$query_escaped}%' 
                           OR location LIKE '%{$query_escaped}%'
                           OR description LIKE '%{$query_escaped}%'
                       )
                       ORDER BY 
                           CASE 
                               WHEN name LIKE '{$query_escaped}%' THEN 1
                               WHEN name LIKE '%{$query_escaped}%' THEN 2
                               WHEN location LIKE '%{$query_escaped}%' THEN 3
                               ELSE 4
                           END,
                           rating DESC,
                           review_count DESC
                       LIMIT {$limit}";
        
        $dest_result = @mysqli_query($con, $dest_query);
        if($dest_result) {
            while($row = mysqli_fetch_assoc($dest_result)) {
                $suggestions[] = [
                    'type' => 'destination',
                    'id' => (int)$row['id'],
                    'title' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
                    'subtitle' => htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8'),
                    'category' => htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'),
                    'url' => 'destination_details.php?id=' . $row['id'],
                    'icon' => 'bi-geo-alt-fill'
                ];
            }
        }
    }
}

// Search Rooms
if($type === 'all' || $type === 'rooms') {
    $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'rooms'");
    if($table_check && mysqli_num_rows($table_check) > 0) {
        // Check if approved column exists
        $has_approved = false;
        $col_check = @mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
        if($col_check && mysqli_num_rows($col_check) > 0) {
            $has_approved = true;
        }
        $approved_condition = $has_approved ? " AND approved = 1" : "";
        
        $room_query = "SELECT r.id, r.name, r.location, r.price,
                       (SELECT ROUND(AVG(rating)) FROM rating_review WHERE room_id = r.id) AS avg_rating
                       FROM rooms r
                       LEFT JOIN hotel_owners ho ON r.owner_id = ho.id
                       WHERE r.status = 1 AND r.removed = 0{$approved_condition}
                       AND (r.owner_id IS NULL OR ho.status = 1)
                       AND (
                           r.name LIKE '%{$query_escaped}%' 
                           OR r.location LIKE '%{$query_escaped}%'
                           OR r.description LIKE '%{$query_escaped}%'
                       )
                       ORDER BY 
                           CASE 
                               WHEN r.name LIKE '{$query_escaped}%' THEN 1
                               WHEN r.name LIKE '%{$query_escaped}%' THEN 2
                               WHEN r.location LIKE '%{$query_escaped}%' THEN 3
                               ELSE 4
                           END,
                           avg_rating DESC
                       LIMIT {$limit}";
        
        $room_result = @mysqli_query($con, $room_query);
        if($room_result) {
            while($row = mysqli_fetch_assoc($room_result)) {
                $price = number_format((int)$row['price'], 0, ',', '.');
                $suggestions[] = [
                    'type' => 'room',
                    'id' => (int)$row['id'],
                    'title' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
                    'subtitle' => htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8') . ' • ' . $price . ' đ/đêm',
                    'price' => (int)$row['price'],
                    'rating' => $row['avg_rating'] ? (float)$row['avg_rating'] : null,
                    'url' => 'room_details.php?id=' . $row['id'],
                    'icon' => 'bi-door-open'
                ];
            }
        }
    }
}

// Remove duplicates and limit results
$unique_suggestions = [];
$seen_ids = [];
foreach($suggestions as $suggestion) {
    $key = $suggestion['type'] . '_' . $suggestion['id'];
    if(!in_array($key, $seen_ids)) {
        $unique_suggestions[] = $suggestion;
        $seen_ids[] = $key;
    }
}

// Limit total suggestions
$unique_suggestions = array_slice($unique_suggestions, 0, $limit);

echo json_encode([
    'suggestions' => $unique_suggestions,
    'query' => $query
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>




