<?php

$hname = 'localhost';
$uname = 'root';
$pass  = '';
$db    = 'vinhlong_hotel';

$con = mysqli_connect($hname, $uname, $pass, $db);

if(!$con){
    die("Cannot Connect to Database: " . mysqli_connect_error());
}

// SET UTF-8 CHUẨN TIẾNG VIỆT
mysqli_set_charset($con, "utf8mb4");


// =============================
//  Lọc dữ liệu an toàn
// =============================
if(!function_exists('filteration')) {
    function filteration($data) {
        // Nếu là string, chỉ cần trim và htmlspecialchars
        if(is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        
        // Nếu là array, xử lý từng phần tử
        if(is_array($data)) {
            foreach ($data as $key => $value) {
                if(is_string($value)) {
                    $value = trim($value);
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    $data[$key] = $value;
                } elseif(is_array($value)) {
                    $data[$key] = filteration($value); // Recursive for nested arrays
                }
            }
        }
        
        return $data;
    }
}


// =============================
//  SELECT ALL
// =============================
if(!function_exists('selectAll')) {
    function selectAll($table){
        $con = $GLOBALS['con'];
        return mysqli_query($con, "SELECT * FROM `$table`");
    }
}


// =============================
//  SELECT (Prepared Statement)
// =============================
if(!function_exists('select')) {
    function select($sql, $values, $datatypes){
        $con = $GLOBALS['con'];

        if($stmt = mysqli_prepare($con, $sql)){
            // Chỉ bind param nếu có datatypes và values
            if(!empty($datatypes) && !empty($values) && count($values) > 0){
                mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
            }
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return $res;
        }

        return false;
    }
}


// =============================
//  INSERT
// =============================
if(!function_exists('insert')) {
    function insert($sql, $values, $datatypes){
        $con = $GLOBALS['con'];
        
        error_log("Insert - Starting. SQL: $sql");
        error_log("Insert - Values count: " . count($values) . ", Datatypes: $datatypes (length: " . strlen($datatypes) . ")");
        
        // Validate số lượng parameters
        if (strlen($datatypes) !== count($values)) {
            error_log("Insert ERROR: Datatypes length (" . strlen($datatypes) . ") doesn't match values count (" . count($values) . ")");
            return false;
        }

        // Thử prepare statement
        $stmt = mysqli_prepare($con, $sql);
        if(!$stmt) {
            // Prepare failed - log chi tiết
            $error = mysqli_error($con);
            $errno = mysqli_errno($con);
            error_log("Insert ERROR - Prepare returned false");
            error_log("Insert ERROR - MySQL Error #$errno: $error");
            error_log("Insert ERROR - SQL: $sql");
            error_log("Insert ERROR - Datatypes: '$datatypes' (length: " . strlen($datatypes) . ")");
            error_log("Insert ERROR - Values count: " . count($values));
            error_log("Insert ERROR - Values: " . print_r($values, true));
            return false;
        }
        
        // Prepare thành công
        error_log("Insert - Prepare successful");
        
        // Xử lý NULL values - cần dùng references
        $refs = [];
        foreach ($values as $key => $val) {
            $refs[$key] = &$values[$key];
        }
        
        $bind_result = mysqli_stmt_bind_param($stmt, $datatypes, ...$refs);
        if(!$bind_result) {
            $error = mysqli_stmt_error($stmt);
            $errno = mysqli_stmt_errno($stmt);
            error_log("Insert ERROR - Bind param failed #$errno: $error");
            error_log("Insert - Datatypes: '$datatypes', Values count: " . count($values));
            error_log("Insert - Values types: " . implode(', ', array_map('gettype', $values)));
            mysqli_stmt_close($stmt);
            return false;
        }
        error_log("Insert - Bind param successful");
        
        // ⚠️ QUAN TRỌNG: Kiểm tra lỗi từ execute
        if(mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            $insert_id = mysqli_insert_id($con); // Lấy ID của record vừa insert
            error_log("Insert - Execute successful. Affected rows: $affected, Insert ID: " . ($insert_id ?: 'NULL'));
            mysqli_stmt_close($stmt);
            
            // Log nếu affected rows = 0 (có thể là dấu hiệu của vấn đề)
            if($affected == 0) {
                error_log("Insert WARNING: No rows affected. SQL: $sql");
                error_log("Insert WARNING: Insert ID: " . ($insert_id ?: 'NULL'));
                // Nếu có insert_id nhưng affected = 0, có thể là duplicate hoặc trigger
                if($insert_id > 0) {
                    error_log("Insert INFO: Has insert_id but affected=0. Possible duplicate or trigger issue.");
                }
            }
            
            // Nếu có insert_id, trả về insert_id thay vì affected rows (để đảm bảo insert thành công)
            if($insert_id > 0 && $affected == 0) {
                error_log("Insert INFO: Returning insert_id ($insert_id) instead of affected rows (0)");
                return $insert_id; // Trả về insert_id để đảm bảo insert thành công
            }
            
            return $affected;
        } else {
            // Log lỗi MySQL từ execute
            $error = mysqli_stmt_error($stmt);
            $errno = mysqli_stmt_errno($stmt);
            error_log("Insert ERROR - Execute failed #$errno: $error");
            error_log("Insert SQL: $sql");
            error_log("Insert values: " . print_r($values, true));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}


// =============================
//  UPDATE
// =============================
if(!function_exists('update')) {
    function update($sql, $values, $datatypes){
        $con = $GLOBALS['con'];
        
        if($stmt = mysqli_prepare($con, $sql)){
            mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
            mysqli_stmt_execute($stmt);
            $affected = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $affected;
        }

        return false;
    }
}


// =============================
//  DELETE
// =============================
if(!function_exists('delete')) {
    function delete($sql, $values, $datatypes){
        $con = $GLOBALS['con'];

        if($stmt = mysqli_prepare($con, $sql)){
            mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
            mysqli_stmt_execute($stmt);
            $affected = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $affected;
        }

        return false;
    }
}

?>
