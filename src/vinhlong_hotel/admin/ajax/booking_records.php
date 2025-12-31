<?php
require('../inc/db_config.php');
require('../inc/essentials.php');

if(isset($_POST['get_bookings']))
{
    $search = $_POST['search'] ?? '';
    $page = $_POST['page'] ?? 1;

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $search_sql = "";
    if($search != ''){
        $search_sql = "AND (bd.user_name LIKE '%$search%' 
                        OR bd.phonenum LIKE '%$search%'
                        OR bo.order_id LIKE '%$search%')";
    }

    // COUNT
    $count_query = "
        SELECT COUNT(*) AS total 
        FROM booking_order bo 
        INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE 1 $search_sql
    ";
    $total_records = mysqli_fetch_assoc(mysqli_query($con, $count_query))['total'];
    $total_pages = ceil($total_records / $limit);

    // MAIN QUERY
    $query = "
        SELECT bo.*, 
               bd.user_name, bd.phonenum, bd.address, bd.room_no, bd.total_pay,
               r.name AS room_name, r.price AS room_price
        FROM booking_order bo
        INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE 1 $search_sql
        ORDER BY bo.booking_id DESC
        LIMIT $offset, $limit
    ";

    $res = mysqli_query($con, $query);
    $table_data = "";

    if(mysqli_num_rows($res) > 0)
    {
        $i = $offset + 1;

        while($row = mysqli_fetch_assoc($res))
        {
            $ngay_nhan = date("d/m/Y", strtotime($row['check_in']));
            $ngay_tra  = date("d/m/Y", strtotime($row['check_out']));
            $ngay_dat  = date("d/m/Y H:i", strtotime($row['datentime']));
            $gia_phong = number_format($row['room_price'],0,',','.');
            $tong_tien = number_format($row['total_pay'],0,',','.');

            /* ===============================
               TRẠNG THÁI BOOKING – TIẾNG VIỆT 
               =============================== */
            $status_txt = "";
            $badge = "";
            $icon = "";

            switch($row['booking_status']){

                case "pending":
                    $status_txt = "Đang chờ xử lý";
                    $badge = "warning";
                    $icon = "bi-hourglass-split";
                    break;

                case "booked":
                    $status_txt = "Đã đặt thành công";
                    $badge = "success";
                    $icon = "bi-check-circle-fill";
                    break;

                case "cancelled":
                    $status_txt = "Đã hủy";
                    $badge = "danger";
                    $icon = "bi-x-circle-fill";
                    break;

                default:
                    $status_txt = "Không xác định";
                    $badge = "secondary";
                    $icon = "bi-question-circle";
                    break;
            }

            $table_data .= "
                <tr>
                    <td>$i</td>

                    <td>
                        <b>{$row['user_name']}</b><br>
                        <i class='bi bi-telephone'></i> {$row['phonenum']}<br>
                        <i class='bi bi-geo-alt'></i> {$row['address']}<br>
                        <span class='badge bg-primary'>Mã đơn: {$row['order_id']}</span>
                    </td>

                    <td>
                        <b>{$row['room_name']}</b><br>
                        <i class='bi bi-hash'></i> Số phòng: {$row['room_no']}<br>
                        <i class='bi bi-cash'></i> Giá: {$gia_phong} VND<br>
                        <i class='bi bi-wallet2'></i> Tổng: {$tong_tien} VND
                    </td>

                    <td>
                        <i class='bi bi-calendar-check text-success'></i> Nhận: $ngay_nhan<br>
                        <i class='bi bi-calendar-x text-danger'></i> Trả: $ngay_tra<br>
                        <i class='bi bi-clock text-primary'></i> Đặt: $ngay_dat
                    </td>

                    <td class='text-center'>
                        <span class='badge bg-$badge'>
                            <i class='bi $icon'></i> $status_txt
                        </span>
                    </td>

                    <td>
                        <button onclick=\"download('{$row['booking_id']}')\" 
                                class='btn btn-sm btn-outline-danger'>
                            <i class='bi bi-file-earmark-pdf-fill'></i>
                        </button>
                    </td>
                </tr>
            ";
            $i++;
        }
    }
    else {
        $table_data = "<tr><td colspan='6' class='text-center'>Không có dữ liệu</td></tr>";
    }

    // PAGINATION
    $pagination = "";
    for($i=1;$i<=$total_pages;$i++){
        $active = ($i==$page) ? "active":""; 
        $pagination .= "
            <li class='page-item $active'>
                <button class='page-link' onclick='change_page($i)'>$i</button>
            </li>
        ";
    }

    echo json_encode([
        'table_data' => $table_data,
        'pagination' => $pagination
    ]);
}
?>
