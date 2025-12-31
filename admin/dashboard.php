<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Lý</title>
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<?php 
require('inc/header.php'); 

$is_shutdown = mysqli_fetch_assoc(mysqli_query($con,"SELECT `shutdown` FROM `settings`"));

$current_bookings = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT 
       COUNT(CASE WHEN bo.booking_status='pending' 
                   OR (bo.booking_status='booked' AND COALESCE(bo.arrival,0)=0) THEN 1 END) AS new_bookings,
       COUNT(CASE WHEN bo.booking_status='cancelled' AND COALESCE(bo.refund,0)=0 THEN 1 END) AS refund_bookings
    FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    WHERE r.owner_id IS NULL
")); 

$unread_queries = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(sr_no) AS `count` FROM `user_queries` WHERE `seen`=0"
));

$unread_reviews = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(sr_no) AS `count` FROM `rating_review` WHERE `seen`=0"
));

$current_users = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT 
        COUNT(id) AS `total`,
        COUNT(CASE WHEN `status`=1 THEN 1 END) AS `active`,
        COUNT(CASE WHEN `status`=0 THEN 1 END) AS `inactive`,
        COUNT(CASE WHEN `is_verified`=0 THEN 1 END) AS `unverified`
    FROM `user_cred`
"));
?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4 overflow-hidden">

    <!-- ================= TITLE ================= -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3>BẢNG ĐIỀU KHIỂN</h3>
        <?php 
        if($is_shutdown['shutdown']){
            echo '<h6 class="badge bg-danger py-2 px-3 rounded">Chế độ bảo trì đang hoạt động!</h6>';
        }
        ?>
    </div>

    <!-- ================= TOP 4 STAT CARDS ================= -->
    <div class="stat-row">

        <a class="stat-card green-card" href="new_bookings.php">
            <i class="bi bi-calendar-plus"></i>
            <h6>Lượt đặt phòng mới</h6>
            <h1 id="new_bookings"><?php echo $current_bookings['new_bookings'] ?></h1>
        </a>

        <a class="stat-card yellow-card" href="refund_bookings.php">
            <i class="bi bi-arrow-counterclockwise"></i>
            <h6>Lượt hoàn tiền</h6>
            <h1 id="refunds"><?php echo $current_bookings['refund_bookings'] ?></h1>
        </a>

        <a class="stat-card blue-card" href="user_queries.php">
            <i class="bi bi-chat-dots"></i>
            <h6>Số tin nhắn</h6>
            <h1><?php echo $unread_queries['count'] ?></h1>
        </a>

        <a class="stat-card star-card" href="rate_review.php">
            <i class="bi bi-star-half"></i>
            <h6>Lượt đánh giá</h6>
            <h1><?php echo $unread_reviews['count'] ?></h1>
        </a>

    </div>

    <!-- ================= BOOKING ANALYTICS ================= -->
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h5>Phân tích đặt phòng</h5>
        <select class="form-select shadow-none w-auto" onchange="booking_analytics(this.value)">
            <option value="1">30 ngày gần đây</option>
            <option value="2">90 ngày gần đây</option>
            <option value="3">1 năm gần đây</option>
            <option value="4">Tất cả thời gian</option>
        </select>
    </div>

    <div class="stat-row">
        <div class="stat-card ocean-card">
            <i class="bi bi-list-check"></i>
            <h6>Tổng số đặt chỗ</h6>
            <h1 id="total_bookings">0</h1>
            <h4 id="total_amt">0 VND</h4>
        </div>

        <div class="stat-card green-card">
            <i class="bi bi-check2-circle"></i>
            <h6>Đặt chỗ đang hoạt động</h6>
            <h1 id="active_bookings">0</h1>
            <h4 id="active_amt">0 VND</h4>
        </div>

        <div class="stat-card red-card">
            <i class="bi bi-x-circle"></i>
            <h6>Đặt chỗ đã bị hủy</h6>
            <h1 id="cancelled_bookings">0</h1>
            <h4 id="cancelled_amt">0 VND</h4>
        </div>
    </div>

    <!-- ================= USER ANALYTICS ================= -->
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h5>Người dùng, Truy vấn, Phân tích đánh giá</h5>
        <select class="form-select shadow-none w-auto" onchange="user_analytics(this.value)">
            <option value="1">30 ngày gần đây</option>
            <option value="2">90 ngày gần đây</option>
            <option value="3">1 năm gần đây</option>
            <option value="4">Tất cả thời gian</option>
        </select>
    </div>

    <div class="stat-row">
        <div class="stat-card green-card">
            <i class="bi bi-person-plus"></i>
            <h6>Đăng ký mới</h6>
            <h1 id="total_new_reg">0</h1>
        </div>

        <div class="stat-card blue-card">
            <i class="bi bi-envelope-open"></i>
            <h6>Câu hỏi</h6>
            <h1 id="total_queries">0</h1>
        </div>

        <div class="stat-card star-card">
            <i class="bi bi-star"></i>
            <h6>Đánh giá</h6>
            <h1 id="total_reviews">0</h1>
        </div>
    </div>

    <!-- ================= USERS ================= -->
    <h5 class="mt-4">Người dùng</h5>

    <div class="stat-row">
        <div class="stat-card blue-card">
            <i class="bi bi-people"></i>
            <h6>Tổng cộng</h6>
            <h1><?php echo $current_users['total'] ?></h1>
        </div>

        <div class="stat-card green-card">
            <i class="bi bi-person-check"></i>
            <h6>Đang hoạt động</h6>
            <h1><?php echo $current_users['active'] ?></h1>
        </div>

        <div class="stat-card yellow-card">
            <i class="bi bi-person-x"></i>
            <h6>Không hoạt động</h6>
            <h1><?php echo $current_users['inactive'] ?></h1>
        </div>

        <div class="stat-card red-card">
            <i class="bi bi-shield-exclamation"></i>
            <h6>Chưa được xác minh</h6>
            <h1><?php echo $current_users['unverified'] ?></h1>
        </div>
    </div>

    <!-- ===================== BẢNG XẾP HẠNG ===================== -->
    <div class="row mt-4">
        <!-- Bảng xếp hạng người dùng -->
        <div class="col-md-6 mb-4">
            <div class="card chart-card p-3">
                <h6 class="text-info mb-3">
                    <i class="bi bi-trophy-fill"></i> Top người dùng đặt phòng nhiều nhất
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tên người dùng</th>
                                <th class="text-end">Số lượt đặt</th>
                                <th class="text-end">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $top_users_query = "SELECT 
                                uc.id,
                                uc.name,
                                uc.email,
                                COUNT(bo.booking_id) AS booking_count,
                                COALESCE(SUM(COALESCE(bo.trans_amt, bd.total_pay, bd.price)), 0) AS total_amount
                            FROM user_cred uc
                            INNER JOIN booking_order bo ON uc.id = bo.user_id
                            LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
                            WHERE bo.booking_status IN ('booked', 'pending')
                            GROUP BY uc.id, uc.name, uc.email
                            ORDER BY booking_count DESC, total_amount DESC
                            LIMIT 10";
                            
                            $top_users_result = mysqli_query($con, $top_users_query);
                            $rank = 1;
                            if($top_users_result && mysqli_num_rows($top_users_result) > 0):
                                while($user = mysqli_fetch_assoc($top_users_result)):
                                    $medal_class = '';
                                    $medal_icon = '';
                                    if($rank == 1) {
                                        $medal_class = 'text-warning';
                                        $medal_icon = '<i class="bi bi-trophy-fill"></i>';
                                    } elseif($rank == 2) {
                                        $medal_class = 'text-secondary';
                                        $medal_icon = '<i class="bi bi-award-fill"></i>';
                                    } elseif($rank == 3) {
                                        $medal_class = 'text-danger';
                                        $medal_icon = '<i class="bi bi-award"></i>';
                                    } else {
                                        $medal_icon = '<span class="badge bg-dark">' . $rank . '</span>';
                                    }
                                    $user_name = htmlspecialchars($user['name'] ?? 'Khách', ENT_QUOTES, 'UTF-8');
                                    $booking_count = (int)$user['booking_count'];
                            ?>
                            <tr>
                                <td class="<?php echo $medal_class; ?>">
                                    <?php echo $medal_icon; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo $user_name; ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-primary"><?php echo $booking_count; ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="fw-bold text-success">
                                            <i class="bi bi-currency-dollar me-1"></i><?php echo number_format((int)$user['total_amount'], 0, ',', '.'); ?> VND
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                    $rank++;
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i> Chưa có dữ liệu
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bảng xếp hạng chủ khách sạn -->
        <div class="col-md-6 mb-4">
            <div class="card chart-card p-3">
                <h6 class="text-info mb-3">
                    <i class="bi bi-building"></i> Top chủ khách sạn có lượt đặt cao nhất
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tên khách sạn / Chủ</th>
                                <th class="text-end">Số lượt đặt</th>
                                <th class="text-end">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $top_owners_query = "SELECT 
                                ho.id,
                                ho.name AS owner_name,
                                ho.hotel_name,
                                ho.email,
                                COUNT(bo.booking_id) AS booking_count,
                                COALESCE(SUM(COALESCE(bo.trans_amt, bd.total_pay, bd.price)), 0) AS total_amount
                            FROM hotel_owners ho
                            INNER JOIN rooms r ON ho.id = r.owner_id
                            INNER JOIN booking_order bo ON r.id = bo.room_id
                            LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
                            WHERE bo.booking_status IN ('booked', 'pending')
                            AND ho.status = 1
                            GROUP BY ho.id, ho.name, ho.hotel_name, ho.email
                            ORDER BY booking_count DESC, total_amount DESC
                            LIMIT 10";
                            
                            $top_owners_result = mysqli_query($con, $top_owners_query);
                            $rank = 1;
                            if($top_owners_result && mysqli_num_rows($top_owners_result) > 0):
                                while($owner = mysqli_fetch_assoc($top_owners_result)):
                                    $medal_class = '';
                                    $medal_icon = '';
                                    if($rank == 1) {
                                        $medal_class = 'text-warning';
                                        $medal_icon = '<i class="bi bi-trophy-fill"></i>';
                                    } elseif($rank == 2) {
                                        $medal_class = 'text-secondary';
                                        $medal_icon = '<i class="bi bi-award-fill"></i>';
                                    } elseif($rank == 3) {
                                        $medal_class = 'text-danger';
                                        $medal_icon = '<i class="bi bi-award"></i>';
                                    } else {
                                        $medal_icon = '<span class="badge bg-dark">' . $rank . '</span>';
                                    }
                                    $display_name = !empty($owner['hotel_name']) 
                                        ? htmlspecialchars($owner['hotel_name'], ENT_QUOTES, 'UTF-8')
                                        : htmlspecialchars($owner['owner_name'], ENT_QUOTES, 'UTF-8');
                                    $booking_count = (int)$owner['booking_count'];
                            ?>
                            <tr>
                                <td class="<?php echo $medal_class; ?>">
                                    <?php echo $medal_icon; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo $display_name; ?></div>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($owner['owner_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success"><?php echo $booking_count; ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="fw-bold text-success">
                                            <i class="bi bi-currency-dollar me-1"></i><?php echo number_format((int)$owner['total_amount'], 0, ',', '.'); ?> VND
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                    $rank++;
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i> Chưa có dữ liệu
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== BIỂU ĐỒ THỐNG KÊ ===================== -->
<h5 class="mt-4 mb-3">Biểu đồ thống kê</h5>

<div class="row">

    <!-- BIỂU ĐỒ 1: BAR CHART -->
    <div class="col-md-6 mb-4">
        <div class="card chart-card p-3">
            <h6 class="text-info mb-3">
                <i class="bi bi-bar-chart-line"></i> Tổng số đặt chỗ theo tháng
            </h6>
            <canvas id="bookingBarChart"></canvas>
        </div>
    </div>

    <!-- BIỂU ĐỒ 2: DONUT CHART -->
    <div class="col-md-6 mb-4">
        <div class="card chart-card p-3">
            <h6 class="text-info mb-3">
                <i class="bi bi-pie-chart"></i> Tỷ lệ loại đặt chỗ
            </h6>
            <canvas id="bookingPieChart"></canvas>
        </div>
    </div>

</div>

<div class="row">

    <!-- BIỂU ĐỒ 3: LINE CHART -->
    <div class="col-md-6 mb-4">
        <div class="card chart-card p-3">
            <h6 class="text-info mb-3">
                <i class="bi bi-graph-up-arrow"></i> Doanh thu theo tháng
            </h6>
            <canvas id="revenueLineChart"></canvas>
        </div>
    </div>

    <!-- BIỂU ĐỒ 4: RADAR CHART -->
    <div class="col-md-6 mb-4">
        <div class="card chart-card p-3">
            <h6 class="text-info mb-3">
                <i class="bi bi-ui-radios"></i> Phân tích người dùng
            </h6>
            <canvas id="userRadarChart"></canvas>
        </div>
    </div>

</div>


</div>
</div>
</div>

<!-- CSS & JS sẽ được thêm ở PHẦN 2 & 3 -->
<?php require('inc/scripts.php'); ?>
<script src="scripts/dashboard.js"></script>

</body>
</html>
<style>
  /* ============================================
   🌙 NEON OCEAN DARK MODE PRO
   ============================================ */

/* GLOBAL */
body.bg-light {
    background: #09101c !important;
    color: #d6e6ff !important;
    font-family: 'Segoe UI', sans-serif;
}

#main-content {
    background: #09101c !important;
}

/* NAVBAR + SIDEBAR */
.navbar, #dashboard-menu {
    background: #0b1624 !important;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

#dashboard-menu a {
    color: #bcd4ff !important;
    transition: .25s;
}

#dashboard-menu a:hover {
    background: #152845 !important;
    color: #50b4ff !important;
}

/* TITLES */
h3, h5, h6 {
    color: #50b4ff !important;
    text-shadow: 0 0 12px rgba(80,180,255,0.55);
    font-weight: 700;
    letter-spacing: .5px;
}

/* ============================================
     ⚡ STAT CARDS – GỌN – ICON 32px – PREMIUM
   ============================================ */
.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 18px;
    margin-bottom: 18px;
}

.stat-card {
    background: linear-gradient(145deg,#0c141f,#112033) !important;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 18px;
    padding: 18px 20px !important;
    text-align: center;
    box-shadow:
        0 10px 22px rgba(0,0,0,0.45),
        inset 0 0 12px rgba(80,180,255,0.12);
    transition: .25s ease-in-out;
}

/* Làm cho thẻ thống kê hiển thị con trỏ chuột và đảm bảo các thẻ kiểu neo không bị định dạng liên kết */
.stat-card { cursor: pointer; }
.stat-card[href] { text-decoration: none; color: inherit; display: block; }

/* Hover mạnh hơn 15% */
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow:
        0 16px 32px rgba(0,0,0,0.55),
        inset 0 0 18px rgba(80,180,255,0.22);
}

/* ICON SIZE */
.stat-card i {
    font-size: 32px !important;
    margin-bottom: 5px;
    transition: .3s ease;
}

/* ICON Hover */
.stat-card:hover i {
    transform: scale(1.15);
    filter: drop-shadow(0 0 7px rgba(255,255,255,0.25));
}

/* TEXT */
.stat-card h6 {
    font-size: 14px;
    margin-bottom: 6px;
    opacity: .9;
}
.stat-card h1 {
    font-size: 26px !important;
    margin: 0;
}

/* COLOR THEMES */
.green-card i { color: #68ffb3 !important; }
.yellow-card i { color: #ffd96a !important; }
.blue-card  i { color: #58b8ff !important; }
.red-card   i { color: #ff7b7b !important; }
.star-card  i { color: #94aaff !important; }
.ocean-card i { color: #4dd7ff !important; }

/* ============================================
     📊 BIỂU ĐỒ – GỌN – MÀU OCEAN PRESET
   ============================================ */
.chart-card {
    padding: 18px;
    background: linear-gradient(145deg,#0c141f,#112033) !important;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.10);
    box-shadow: 
        0 8px 20px rgba(0,0,0,0.35),
        inset 0 0 10px rgba(80,180,255,0.12);
}

.chart-card h6 {
    font-size: 15px;
    color: #58b8ff !important;
    margin-bottom: 12px;
}

canvas {
    max-height: 260px !important;
}

/* SELECT DROPDOWN */
.form-select {
    background: #0b1624 !important;
    color: #dce7ff !important;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
}

.form-select:focus {
    border-color: #50b4ff !important;
    box-shadow: 0 0 8px rgba(80,180,255,0.4);
}

/* TABLE STYLE */
.table { 
    color: #dce7ff !important; 
}

thead {
    background: #122131 !important;
}

thead th {
    color: #50b4ff !important;
    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

tbody tr {
    background: #0b1624 !important;
    transition: all 0.3s ease;
}
tbody tr:hover {
    background: #152845 !important;
    transform: translateX(3px);
}

/* Ranking Table Specific */
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
    overflow-x: auto;
}

.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #0b1624;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #50b4ff;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #68c4ff;
}

/* Responsive for mobile */
@media (max-width: 768px) {
    .table-responsive {
        max-height: 400px;
    }
    
    .table th, .table td {
        font-size: 12px;
        padding: 8px 4px;
    }
    
    .table th {
        white-space: nowrap;
    }
}

/* Medal Icons */
.text-warning {
    color: #ffd700 !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}

.text-secondary {
    color: #c0c0c0 !important;
    text-shadow: 0 0 8px rgba(192, 192, 192, 0.4);
}

.text-danger {
    color: #cd7f32 !important;
    text-shadow: 0 0 8px rgba(205, 127, 50, 0.4);
}

/* Badge styling */
.badge {
    font-size: 12px;
    padding: 6px 12px;
    font-weight: 600;
    border-radius: 8px;
}

/* Prevent text disappearing */
*, *:hover {
    -webkit-text-fill-color: inherit !important;
}

</style>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       1. BAR CHART — Đặt phòng theo tháng
    ===================================================== */
    const barCtx = document.getElementById('bookingBarChart').getContext('2d');

    new Chart(barCtx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{
                label: 'Đặt phòng',
                data: [12, 19, 13, 15, 22, 30, 28, 26, 25, 18, 14, 20],
                backgroundColor: 'rgba(0,150,255,0.55)',
                borderColor: '#0096ff',
                borderWidth: 2,
                borderRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },

                datalabels: {
                    color: '#fff',
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => value,
                    font: { size: 12, weight: 'bold' }
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` Đặt phòng: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#dce7ff' },
                    grid: { color: '#1d2635' }
                },
                x: {
                    ticks: { color: '#dce7ff' },
                    grid: { display: false }
                }
            }
        }
    });



    /* =====================================================
       2. DOUGHNUT CHART — Tỷ lệ loại đặt chỗ
    ===================================================== */
    const pieCtx = document.getElementById('bookingPieChart').getContext('2d');

    const bookingData = [35, 9, 1];
    const totalBooking = bookingData.reduce((a,b)=>a+b,0);

    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hoạt động', 'Đã hủy', 'Đang chờ'],
            datasets: [{
                data: bookingData,
                backgroundColor: ['#00e676','#ff5252','#40c4ff'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: "55%",
            plugins: {
                legend: {
                    labels: { color: '#dce7ff', font: { size: 14 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context){
                            let value = context.raw;
                            let percent = ((value/totalBooking)*100).toFixed(1);
                            return `${context.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });



    /* =====================================================
       3. LINE CHART — Doanh thu theo tháng
    ===================================================== */
    const lineCtx = document.getElementById('revenueLineChart').getContext('2d');

    new Chart(lineCtx, {
        type: 'line',
        plugins: [ChartDataLabels],
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{
                label: 'Doanh thu (triệu VND)',
                data: [8,10,11,12,14,16,17,20,19,18,15,17],
                backgroundColor: 'rgba(0,140,255,0.22)',
                borderColor: '#0096ff',
                fill: true,
                borderWidth: 3,
                tension: 0.35,
                pointBackgroundColor: '#58b8ff',
                pointRadius: 5
            }]
        },
        options: {
            plugins: {
                legend: { labels: { color:'#dce7ff' }},

                datalabels: {
                    color: '#fff',
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => value + "M",
                    font: { weight: "bold", size: 12 }
                },

                tooltip: {
                    callbacks: {
                        label: (context)=> `Doanh thu: ${context.raw} triệu`
                    }
                }
            },
            scales: {
                y: { ticks: { color:'#dce7ff' }, grid: { color:'#1d2635' }},
                x: { ticks: { color:'#dce7ff' }, grid: { display:false }},
            }
        }
    });



    /* =====================================================
       4. HORIZONTAL BAR — Phân tích người dùng (THAY RADAR)
    ===================================================== */
    const userBarCtx = document.getElementById('userRadarChart').getContext('2d');

    new Chart(userBarCtx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: ['Hoạt động','Không hoạt động','Chưa xác minh'],
            datasets: [{
                label: 'Số lượng',
                data: [
                    <?= $current_users['active'] ?>,
                    <?= $current_users['inactive'] ?>,
                    <?= $current_users['unverified'] ?>
                ],
                backgroundColor: [
                    'rgba(76, 175, 80, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(33, 150, 243, 0.7)'
                ],
                borderColor: [
                    '#4caf50',
                    '#ffc107',
                    '#2196f3'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { labels: { color: '#dce7ff', font:{size:14} }},

                datalabels: {
                    color: '#fff',
                    anchor: 'end',
                    align: 'right',
                    formatter: (v)=> v,
                    font: { size: 14, weight: 'bold' }
                }
            },
            scales: {
                y: {
                    ticks: { color:'#dce7ff', font:{size:14} },
                    grid: { display:false }
                },
                x: {
                    ticks: { color:'#dce7ff' },
                    grid: { color:'#1d2635' }
                }
            }
        }
    });

});
</script>
