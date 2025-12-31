<?php
  $current_lang = 'vi';
  $lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
  if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
    setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    $_COOKIE['lang'] = $lang_from_url;
    $current_lang = $lang_from_url;
  } else {
    setcookie('lang', 'vi', time() + (365 * 24 * 60 * 60), '/', '', false, true);
    $_COOKIE['lang'] = 'vi';
  }
  $html_lang = $current_lang;
?>
<!DOCTYPE html>

<html lang="<?php echo $html_lang; ?>">



<head>



  <meta charset="UTF-8">



  <meta http-equiv="X-UA-Compatible" content="IE=edge">



  <meta name="viewport" content="width=device-width, initial-scale=1.0">



  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">



  <?php require('inc/links.php'); ?>



  <?php require_once('admin/inc/promos_helper.php'); ?>



  <title><?php echo $settings_r['site_title']; ?> - Trang chủ</title>







  <style>



    :root{



      --ink:#0f172a;



      --muted:#6b7280;



      --border:#e5e7eb;



      --primary:#0ea5e9;



      --primary-dark:#0b7eb2;



      --bg-soft:#f8fafc;



    }



    body{background:#f6f8fb;}



    .availability-form{margin-top:-50px;z-index:2;position:relative;overflow:visible;}



    @media screen and (max-width:575px){.availability-form{margin-top:25px;padding:0 35px;}}





    /* HERO */



    .hero-box{
      position:relative;
      margin-top:20px;
      margin-bottom:30px;
      z-index:1;
      text-align:center;
      overflow:visible;
    }
    
    @media (max-width: 768px) {
      .hero-box {
        margin-top: 12px;
        margin-bottom: 20px;
        padding: 0 12px;
      }
    }
    
    @media (max-width: 576px) {
      .hero-box {
        margin-top: 8px;
        margin-bottom: 16px;
        padding: 0 8px;
      }
    }
    
    .hero-overlay{
      display:inline-block;
      background:linear-gradient(135deg, rgba(15,23,42,0.9), rgba(11,74,102,0.85));
      backdrop-filter:blur(10px);
      padding:32px 50px;
      border-radius:20px;
      margin-bottom:30px;
      position:relative;
      z-index:1;
      box-shadow:0 12px 40px rgba(0,0,0,0.2);
      max-width:900px;
    }

    .hero-title{
      color:#fff;
      font-size:36px;
      font-weight:800;
      letter-spacing:.3px;
      margin-bottom:12px;
      text-shadow:0 2px 10px rgba(0,0,0,0.3);
      line-height:1.3;
    }

    .hero-sub{
      color:rgba(255,255,255,0.9);
      font-size:17px;
      margin:0;
      text-shadow:0 1px 4px rgba(0,0,0,0.2);
      line-height:1.5;
    }

    .traveloka-search-box{
      background:#ffffff;
      width:100%;
      max-width:1400px;
      margin:0 auto;
      padding:0;
      border-radius:32px;
      box-shadow:0 16px 48px rgba(0,61,92,0.25), 0 8px 24px rgba(0,0,0,0.15), 0 0 0 2px #003d5c, 0 0 0 4px rgba(0,61,92,0.1);
      border:2px solid #003d5c;
      position:relative;
      transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display:flex;
      align-items:stretch;
      overflow:visible;
      z-index:10;
    }
    
    .hero-box{
      position:relative;
      margin-top:40px;
      z-index:1;
      text-align:center;
    }


    .traveloka-search-box:hover{
      box-shadow:0 20px 60px rgba(0,61,92,0.35), 0 12px 32px rgba(0,0,0,0.2), 0 0 0 3px #004d6b, 0 0 0 6px rgba(0,61,92,0.15);
      border-color:#004d6b;
      transform:translateY(-4px);
    }

    .traveloka-search-box .row{
      width:100%;
      margin:0;
      display:flex;
      align-items:stretch;
      flex-wrap:nowrap;
    }
    
    /* Responsive cho search box trên mobile */
    @media (max-width: 992px) {
      .traveloka-search-box .row {
        flex-wrap: wrap;
      }
      
      .traveloka-search-box .row > div {
        flex: 1 1 100%;
        border-right: none !important;
        border-bottom: 1px solid #d1d5db;
        padding: 16px 20px;
        min-height: auto;
        border-radius: 0 !important;
      }
      
      .traveloka-search-box .row > div:first-child {
        border-top-left-radius: 20px !important;
        border-top-right-radius: 20px !important;
      }
      
      .traveloka-search-box .row > div:last-child {
        border-bottom: none;
        border-bottom-left-radius: 20px !important;
        border-bottom-right-radius: 20px !important;
      }
      
      /* Nút tìm kiếm full width trên mobile */
      .traveloka-search-box .row > div:last-child button {
        width: 100%;
        padding: 12px;
      }
    }
    
    @media (max-width: 768px) {
      .traveloka-search-box {
        border-radius: 16px;
        max-width: 100%;
        margin: 0 16px;
      }
      
      .traveloka-search-box .row > div {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
      }
      
      .traveloka-search-box .row > div:last-child {
        border-bottom: none;
      }
      
      .search-label {
        font-size: 13px;
        margin-bottom: 8px;
        text-align: left;
        width: 100%;
        justify-content: flex-start;
      }
      
      .traveloka-input,
      .traveloka-select {
        font-size: 15px;
        width: 100%;
        text-align: left;
        padding: 10px 12px;
      }
      
      .traveloka-input::placeholder {
        font-size: 14px;
      }
      
      /* Căn trái cho input trên mobile */
      .traveloka-search-box .row > div {
        align-items: flex-start;
        text-align: left;
      }
    }
    
    @media (max-width: 576px) {
      .traveloka-search-box {
        margin: 0 auto;
        border-radius: 12px;
        border-width: 1px;
      }
      
      .traveloka-search-box .row > div {
        padding: 12px 14px;
        min-height: 65px;
      }
      
      .traveloka-search-box .row > div:last-child {
        padding: 10px;
      }
      
      .search-label {
        font-size: 11px;
        margin-bottom: 5px;
        font-weight: 600;
      }
      
      .traveloka-input,
      .traveloka-select {
        font-size: 13px;
        padding: 7px 8px;
      }
      
      .traveloka-input::placeholder {
        font-size: 12px;
      }
      
      .traveloka-btn {
        height: 44px;
        font-size: 15px;
      }
    }

    .traveloka-search-box .row > div{
      flex:1;
      padding:24px 28px;
      border-right:1px solid #d1d5db;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      text-align:center;
      position:relative;
      background:#ffffff;
      transition:background 0.3s ease;
      overflow:visible;
      min-height:80px;
    }

    .traveloka-search-box .row > div:first-child{
      border-top-left-radius:32px;
      border-bottom-left-radius:32px;
    }

    .traveloka-search-box .row > div:last-of-type{
      border-top-right-radius:32px;
      border-bottom-right-radius:32px;
      border-right:none;
    }

    .traveloka-search-box .row > div:hover{
      background:#fafbfc;
    }

    /* Đảm bảo các section không che sticky search box */

    @media (max-width: 992px){
      .hero-box{
        margin-top:30px;
      }
      .hero-overlay{
        padding:24px 32px;
        margin-bottom:24px;
        max-width:95%;
      }
      .hero-title{
        font-size:28px;
      }
      .hero-sub{
        font-size:16px;
      }
    }

    @media (max-width: 768px){
      .hero-box{
        margin-top:20px;
      }
      .hero-overlay{
        padding:20px 24px;
        margin-bottom:20px;
        border-radius:16px;
      }
      .hero-title{
        font-size:24px;
        margin-bottom:10px;
      }
      .hero-sub{
        font-size:14px;
      }
    }



    .search-label{
      white-space: nowrap;
      overflow: visible;
      text-overflow: clip;
      font-size:16px;
      font-weight:900;
      margin-bottom:8px;
      color:#1f2937;
      letter-spacing:0.8px;
      text-transform:uppercase;
      text-align:center;
      width:100%;
      height:20px;
      display:flex;
      align-items:center;
      justify-content:center;
      line-height:1.2;
      white-space:nowrap;
      gap:6px;
    }
    
    .search-label i{
      font-size:16px;
      color:#003d5c;
    }



    .traveloka-input{
      border:none;
      border-radius:0;
      padding:0;
      width:100%;
      height:auto;
      min-height:32px;
      transition:all 0.2s ease;
      background:transparent;
      font-size:16px;
      line-height:1.5;
      color:#111827;
      position:relative;
      z-index:1;
      box-sizing:border-box;
      display:flex;
      align-items:center;
      justify-content:center;
      margin:0;
      font-weight:600;
      text-align:center;
      white-space: nowrap;
      overflow: visible;
      text-overflow: clip;
    }
    

    .traveloka-input:hover{
      color:#003d5c;
    }
    
    .traveloka-select:hover{
      color:#003d5c;
    }

    .traveloka-input::placeholder{
      color:#6b7280;
      font-weight:400;
    }

    .traveloka-input:focus,.traveloka-select:focus{
      outline:none;
      color:#003d5c;
      font-weight:500;
    }

    .traveloka-select{
      border:none;
      border-radius:0;
      padding:4px 24px 4px 8px;
      width:100%;
      height:auto;
      min-height:32px;
      transition:all 0.2s ease;
      background:transparent;
      font-size:15px;
      line-height:1.5;
      white-space: nowrap;
      overflow: visible;
      text-overflow: clip;
      color:#111827;
      position:relative;
      z-index:1;
      box-sizing:border-box;
      display:block;
      cursor:pointer;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      margin:0;
      font-weight:500;
      text-align:left;
      text-align-last:left;
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 14 14'%3E%3Cpath fill='%236b7280' d='M7 10L2 5h10z'/%3E%3C/svg%3E");
      background-repeat:no-repeat;
      background-position:right 8px center;
      background-size:16px 16px;
    }
    
    .traveloka-select option{
      font-size:16px;
      color:#111827;
      background:#ffffff;
      padding:12px;
      text-align:center;
    }

    .traveloka-select::-ms-expand{
      display:none;
    }

    .traveloka-select::-webkit-select-arrow{
      display:none;
    }

    input[type="date"].traveloka-input{
      position:relative;
    }

    input[type="date"].traveloka-input::-webkit-calendar-picker-indicator{
      opacity:0.6;
      cursor:pointer;
    }

    /* Date Range Picker Styles */
    .date-range-wrapper{
      position:relative;
      z-index:100;
      overflow:visible;
    }

    #date-range-input{
      position:relative;
      padding-right:45px;
    }


      .date-range-calendar{
      position:absolute;
      top:calc(100% + 8px);
      left:0;
      right:0;
      background:#ffffff;
      border-radius:16px;
      box-shadow:0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.1);
      border:1px solid #e5e7eb;
      z-index:9999 !important;
      padding:24px;
      min-width:680px;
      max-width:100%;
      display:none;
    }
    
    @media (max-width: 768px) {
      .date-range-calendar {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%);
        width: calc(100% - 32px);
        max-width: 500px;
        min-width: auto;
        max-height: 90vh;
        overflow-y: auto;
        padding: 16px;
        border-radius: 12px;
      }
      
      .calendar-container {
        flex-direction: column;
        gap: 16px;
      }
      
      .calendar-month-year {
        font-size: 18px;
      }
      
      .calendar-grid {
        gap: 3px;
      }
    }
    
    @media (max-width: 576px) {
      .date-range-calendar {
        width: calc(100% - 16px);
        padding: 12px;
        max-height: 85vh;
      }
      
      .calendar-month-year {
        font-size: 16px;
      }
      
      .calendar-nav-btn {
        width: 32px;
        height: 32px;
        font-size: 16px;
      }
      
      .calendar-day {
        font-size: 12px;
        padding: 4px;
      }
      
      .calendar-day-header {
        font-size: 10px;
        padding: 6px 2px;
      }
    }
    
    /* Guests Popup Responsive */
    @media (max-width: 768px) {
      .guests-popup {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%);
        width: calc(100% - 32px);
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
      }
    }
    
    @media (max-width: 576px) {
      .guests-popup {
        width: calc(100% - 16px);
        max-width: 350px;
        padding: 16px;
      }
      
      .guests-item {
        padding: 12px 0;
      }
      
      .guests-item-label {
        font-size: 14px;
      }
      
      .guests-item-desc {
        font-size: 12px;
      }
      
      .counter-value {
        font-size: 16px;
      }
      
      .counter-btn {
        width: 32px;
        height: 32px;
        font-size: 14px;
      }
    }

    .date-range-wrapper{
      position:relative;
      z-index:100;
    }

    .calendar-container{
      display:flex;
      gap:24px;
      margin-bottom:20px;
    }

    .calendar-month{
      flex:1;
    }

    .calendar-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      margin-bottom:16px;
    }

    .calendar-month-year{
      font-size:22px;
      font-weight:900;
      color:#003d5c;
      text-align:center;
      flex:1;
      letter-spacing:0.5px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:8px;
    }
    
    .calendar-month-year i{
      font-size:20px;
    }

    .calendar-nav-btn{
      background:none;
      border:none;
      color:#003d5c;
      font-size:18px;
      cursor:pointer;
      padding:8px;
      border-radius:8px;
      transition:all 0.2s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      width:36px;
      height:36px;
    }

    .calendar-nav-btn:hover{
      background:#f3f4f6;
      color:#004d6b;
    }

    .calendar-grid{
      display:grid;
      grid-template-columns:repeat(7,1fr);
      gap:4px;
    }

    .calendar-day-header{
      text-align:center;
      font-size:12px;
      font-weight:600;
      color:#6b7280;
      padding:8px 4px;
      text-transform:uppercase;
    }

    .calendar-day{
      aspect-ratio:1;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:8px;
      cursor:pointer;
      font-size:14px;
      font-weight:500;
      transition:all 0.2s ease;
      position:relative;
      color:#111827;
      overflow:visible;
      z-index:1;
    }
    
    .calendar-day > * {
      position:relative;
      z-index:2;
    }

    .calendar-day:hover:not(.disabled):not(.selected-start):not(.selected-end){
      background:#f3f4f6;
    }

    .calendar-day.disabled{
      color:#d1d5db;
      cursor:not-allowed;
      background:#f9fafb;
    }

    .calendar-day.in-range{
      background:#e0f2fe;
      color:#003d5c;
    }

    .calendar-day.selected-start,
    .calendar-day.selected-end{
      background:#003d5c;
      color:#ffffff;
      font-weight:700;
    }

    .calendar-day.today{
      position:relative;
      font-weight:600;
    }
    
    .calendar-day.today::after{
      content:'';
      position:absolute;
      bottom:4px;
      left:50%;
      transform:translateX(-50%);
      width:6px;
      height:6px;
      background:#003d5c;
      border-radius:50%;
      z-index:1;
    }
    
    .calendar-day.today:not(.selected-start):not(.selected-end):not(.disabled){
      color:#003d5c;
    }
    
    /* Highlight ngày trả phòng khi đang chọn - làm đậm khi hover */
    .calendar-day[data-selectable-end="true"]:hover{
      background:#003d5c !important;
      color:#ffffff !important;
      font-weight:700;
      transform:scale(1.12);
      box-shadow:0 4px 12px rgba(0,61,92,0.4);
      border:2px solid #002e42;
      z-index:100;
      position:relative;
      overflow:visible;
    }
    
    .calendar-day[data-selectable-end="true"]:hover * {
      color:#ffffff !important;
      z-index:101;
      position:relative;
      text-shadow:0 1px 2px rgba(0,0,0,0.2);
    }
    
    /* Highlight các ngày có thể chọn khi đang chọn ngày trả */
    .calendar-day[data-selectable-end="true"]:not(.disabled):not(.selected-start){
      position:relative;
      overflow:visible;
    }
    
    .calendar-day[data-selectable-end="true"]:not(.disabled):not(.selected-start)::before{
      content:'';
      position:absolute;
      inset:0;
      background:rgba(0,61,92,0.08);
      border-radius:8px;
      pointer-events:none;
      z-index:0;
    }
    
    /* Làm đậm ngày đã chọn (start và end) */
    .calendar-day.selected-start,
    .calendar-day.selected-end{
      transform:scale(1.08);
      box-shadow:0 3px 10px rgba(0,61,92,0.35);
    }

    .calendar-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding-top:16px;
      border-top:1px solid #e5e7eb;
    }

    .btn-clear-dates,
    .btn-apply-dates{
      padding:10px 20px;
      border-radius:10px;
      font-weight:600;
      font-size:14px;
      border:none;
      cursor:pointer;
      transition:all 0.2s ease;
    }

    .btn-clear-dates{
      background:transparent;
      color:#6b7280;
    }

    .btn-clear-dates:hover{
      background:#f3f4f6;
      color:#111827;
    }

    .btn-apply-dates{
      background:#003d5c;
      color:#ffffff;
    }

    .btn-apply-dates:hover:not(:disabled){
      background:#004d6b;
      transform:translateY(-1px);
      box-shadow:0 4px 12px rgba(0,61,92,0.25);
    }

    .btn-apply-dates:disabled{
      background:#d1d5db;
      color:#9ca3af;
      cursor:not-allowed;
      opacity:0.6;
    }

    .date-picker-hint{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:13px;
      color:#ef4444;
      padding:8px 12px;
      background:#fef2f2;
      border-radius:8px;
      border:1px solid #fecaca;
      margin-top:12px;
      width:100%;
    }

    .date-picker-hint i{
      font-size:16px;
    }


    @media (max-width: 992px){
      .date-range-calendar{
        min-width:100%;
        left:0;
        right:0;
      }
      
      .calendar-container {
        flex-direction: column;
        gap: 16px;
      }
      
      .calendar-month {
        width: 100%;
      }
    }
    
    @media (max-width: 768px) {
      .date-range-calendar {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%);
        min-width: calc(100vw - 32px);
        max-width: calc(100vw - 32px);
        max-height: calc(100vh - 32px);
        overflow-y: auto;
        padding: 16px;
        border-radius: 16px;
      }
      
      .calendar-container {
        flex-direction: column;
        gap: 12px;
      }
      
      .calendar-month {
        width: 100%;
      }
      
      .calendar-grid {
        gap: 4px;
      }
      
      .calendar-day {
        font-size: 13px;
        padding: 8px 4px;
      }
    }
      .calendar-container{
        flex-direction:column;
        gap:16px;
      }
    }

    /* Guests Picker Styles */
    .guests-wrapper{
      position:relative;
    }

    #guests-input{
      position:relative;
      padding-right:45px;
    }


    .guests-popup{
      position:absolute;
      top:calc(100% + 8px);
      left:0;
      right:0;
      background:#ffffff;
      border-radius:16px;
      box-shadow:0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.1);
      border:1px solid #e5e7eb;
      z-index:9999 !important;
      padding:20px;
      min-width:320px;
      max-width:100%;
      display:none;
    }
    
    @media (max-width: 768px) {
      .guests-popup {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%);
        min-width: calc(100vw - 32px);
        max-width: calc(100vw - 32px);
        max-height: calc(100vh - 32px);
        overflow-y: auto;
        padding: 16px;
        border-radius: 16px;
      }
    }

    .guests-wrapper{
      position:relative;
      z-index:100;
      overflow:visible;
    }

    .guests-section{
      margin-bottom:16px;
    }

    .guests-item{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:16px 0;
      border-bottom:1px solid #f3f4f6;
    }

    .guests-item:last-child{
      border-bottom:none;
    }

    .guests-item-info{
      flex:1;
    }

    .guests-item-label{
      font-size:15px;
      font-weight:600;
      color:#111827;
      margin-bottom:4px;
    }

    .guests-item-desc{
      font-size:13px;
      color:#6b7280;
    }

    .guests-counter{
      display:flex;
      align-items:center;
      gap:16px;
    }

    .counter-btn{
      width:36px;
      height:36px;
      border-radius:8px;
      border:1.5px solid #e5e7eb;
      background:#ffffff;
      color:#003d5c;
      cursor:pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      transition:all 0.2s ease;
      font-size:18px;
      padding:0;
    }

    .counter-btn:hover:not(:disabled){
      background:#f3f4f6;
      border-color:#003d5c;
    }

    .counter-btn:disabled{
      opacity:0.4;
      cursor:not-allowed;
    }

    .counter-value{
      font-size:16px;
      font-weight:600;
      color:#111827;
      min-width:30px;
      text-align:center;
    }

    .guests-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding-top:16px;
      border-top:1px solid #e5e7eb;
    }

    .btn-clear-guests,
    .btn-apply-guests{
      padding:10px 20px;
      border-radius:10px;
      font-weight:600;
      font-size:14px;
      border:none;
      cursor:pointer;
      transition:all 0.2s ease;
    }

    .btn-clear-guests{
      background:transparent;
      color:#6b7280;
    }

    .btn-clear-guests:hover{
      background:#f3f4f6;
      color:#111827;
    }

    .btn-apply-guests{
      background:#003d5c;
      color:#ffffff;
    }

    .btn-apply-guests:hover:not(:disabled){
      background:#004d6b;
      transform:translateY(-1px);
      box-shadow:0 4px 12px rgba(0,61,92,0.25);
    }

    .btn-apply-guests:disabled{
      background:#d1d5db;
      color:#9ca3af;
      cursor:not-allowed;
      opacity:0.6;
    }

    .guests-picker-hint{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:13px;
      color:#ef4444;
      padding:8px 12px;
      background:#fef2f2;
      border-radius:8px;
      border:1px solid #fecaca;
      margin-top:12px;
      width:100%;
    }

    .guests-picker-hint i{
      font-size:16px;
    }

    .traveloka-btn{
      background:#003d5c;
      color:#fff;
      font-weight:600;
      font-size:12px;
      border-radius:8px;
      padding:0;
      width:36px;
      height:36px;
      min-width:36px;
      min-height:36px;
      border:none;
      transition:all 0.2s ease;
      box-shadow:0 2px 8px rgba(0,61,92,0.2);
      position:relative;
      z-index:1;
      display:flex;
      align-items:center;
      justify-content:center;
      margin:0;
      flex-shrink:0;
    }

    .traveloka-btn span,
    .traveloka-btn i{
      position:relative;
      z-index:1;
    }

    .traveloka-btn:hover{
      background:#004d6b;
      box-shadow:0 4px 12px rgba(0,61,92,0.3);
      transform:translateY(-1px);
    }

    .traveloka-btn:active{
      background:#003d5c;
      transform:translateY(0);
      box-shadow:0 1px 4px rgba(0,61,92,0.2);
    }

    .traveloka-btn i{
      font-size:14px;
    }

    .traveloka-btn span{
      display:none;
    }







    /* ROOM CARDS (home) */



    .home-room-card{
      border-radius:16px;
      border:1px solid var(--border);
      background:#fff;
      box-shadow:0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.04);
      position:relative;
      transition:all 0.2s ease;
      display:flex;
      flex-direction:column;
      height:100%;
    }

    .home-room-card:hover{
      transform:translateY(-2px);
      box-shadow:0 4px 16px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.06);
      border-color:var(--gray-300);
    }



    .home-room-thumb img{width:100%;height:380px;object-fit:cover;display:block;}

    .home-room-card .p-3{
      padding:0.75rem !important;
    }

    .home-room-card .mb-2{
      margin-bottom:0.5rem !important;
    }

    .home-room-card .mb-1{
      margin-bottom:0.125rem !important;
    }

    .home-room-meta{font-size:13px;color:var(--muted);}



    .home-tag{font-size:12px;border:1px solid var(--border);border-radius:999px;padding:6px 10px;margin:2px;display:inline-block;background:#f8fafc;}



    .home-room-actions{display:flex;gap:10px;align-items:center;border-top:1px solid var(--border);padding-top:2px !important;margin-top:2px !important;}



    .home-room-actions .btn{flex:1 1 0;}



    .home-fav-btn{position:absolute;top:10px;right:10px;width:40px;height:40px;border:none;border-radius:50%;background:#fff;box-shadow:0 6px 18px rgba(0,0,0,0.12);display:flex;align-items:center;justify-content:center;color:#ef4444;}



    .home-fav-btn.active{background:#ffeae6;color:#e11d48;}



    .badge-soft{background:#eef2ff;color:#4338ca;border-radius:999px;padding:4px 10px;font-weight:600;font-size:12px;}



    .home-price-row{display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;}



    .home-rating{font-weight:700;color:#f59e0b;font-size:14px;}



    .home-price-row .text-primary{color:#0d4ed8!important;}



    .home-section-title{
      font-size:32px;
      font-weight:700;
      color:var(--gray-900);
      letter-spacing:-0.5px;
      line-height:1.2;
    }

    /* View All Rooms Button */
    .btn-view-all-rooms{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:14px 32px;
      font-size:16px;
      font-weight:600;
      color:#0ea5e9;
      background:#ffffff;
      border:2px solid #0ea5e9;
      border-radius:12px;
      text-decoration:none;
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow:0 2px 8px rgba(14,165,233,0.1);
      position:relative;
      overflow:hidden;
    }

    .btn-view-all-rooms::before{
      content:'';
      position:absolute;
      top:0;
      left:-100%;
      width:100%;
      height:100%;
      background:linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition:left 0.5s ease;
    }

    .btn-view-all-rooms:hover{
      color:#ffffff;
      background:#0ea5e9;
      border-color:#0ea5e9;
      transform:translateY(-2px);
      box-shadow:0 8px 24px rgba(14,165,233,0.25);
    }

    .btn-view-all-rooms:hover::before{
      left:100%;
    }

    .btn-view-all-rooms:active{
      transform:translateY(0);
      box-shadow:0 4px 12px rgba(14,165,233,0.2);
    }

    .btn-view-all-rooms i{
      font-size:18px;
      transition:transform 0.3s ease;
    }

    .btn-view-all-rooms:hover i{
      transform:scale(1.1);
    }
    .room-badge-popular{
      position:absolute;
      top:10px;
      left:10px;
      background:linear-gradient(135deg,#f59e0b,#ef4444);
      color:#fff;
      padding:6px 12px;
      border-radius:999px;
      font-size:12px;
      font-weight:700;
      z-index:2;
      box-shadow:0 4px 12px rgba(245,158,11,0.4);
    }
    .room-badge-discount{
      position:absolute;
      top:10px;
      left:10px;
      background:linear-gradient(135deg,#ef4444,#dc2626);
      color:#fff;
      padding:6px 12px;
      border-radius:999px;
      font-size:12px;
      font-weight:700;
      z-index:2;
      box-shadow:0 4px 12px rgba(239,68,68,0.4);
    }
    .room-thumb-overlay{
      position:absolute;
      inset:0;
      background:rgba(0,0,0,0.4);
      display:flex;
      align-items:center;
      justify-content:center;
      opacity:0;
      transition:all .3s;
      border-radius:18px 18px 0 0;
    }
    .home-room-thumb:hover .room-thumb-overlay{
      opacity:1;
    }
    .home-room-thumb{
      overflow:hidden;
      border-radius:18px 18px 0 0;
    }
    .home-room-thumb img{
      transition:transform .4s;
    }
    .home-room-card:hover .home-room-thumb img{
      transform:scale(1.08);
    }

    .facility-card{
      border-radius:16px;
      border:1px solid var(--border);
      background:#ffffff;
      padding:28px 20px;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:flex-start;
      gap:12px;
      height:100%;
      min-height:220px;
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position:relative;
      overflow:hidden;
      box-shadow:0 1px 3px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
    }

    .facility-card::before{
      content:'';
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:4px;
      background:linear-gradient(90deg, #0ea5e9, #3b82f6);
      transform:scaleX(0);
      transition:transform 0.3s ease;
    }



    .facility-card:hover{



      transform:translateY(-6px);



      box-shadow:0 8px 24px rgba(14,165,233,0.12), 0 2px 8px rgba(0,0,0,0.08);
      border-color:#0ea5e9;
    }

    .facility-card:hover::before{
      transform:scaleX(1);
    }

    .facility-card img{
      max-width:72px;
      max-height:72px;
      width:auto;
      height:auto;
      object-fit:contain;
      filter:drop-shadow(0 2px 8px rgba(14,165,233,0.15));
      transition:all 0.3s ease;
    }

    .facility-card:hover img{
      transform:scale(1.1);
      filter:drop-shadow(0 4px 12px rgba(14,165,233,0.25));
    }

    .facility-card .fw-semibold{
      font-size:15px;
      font-weight:600;
      color:var(--gray-900);
      text-align:center;
      line-height:1.4;
      margin-top:4px;
      margin-bottom:0;
      transition:color 0.3s ease;
      flex-shrink:0;
    }

    .facility-card:hover .fw-semibold{
      color:#0ea5e9;
    }

    .facility-card .text-muted{
      font-size:13px;
      line-height:1.5;
      color:var(--gray-600);
      text-align:center;
      margin-bottom:0;
      flex-grow:1;
      display:flex;
      align-items:flex-start;
      justify-content:center;
    }

    .facility-card img{
      flex-shrink:0;
    }

    /* Review Like Count */
    .review-like-count{
      padding:4px 10px;
      background:rgba(14,165,233,0.08);
      border-radius:12px;
      color:#0ea5e9;
      font-size:13px;
      transition:all 0.2s ease;
    }

    .review-like-count:hover{
      background:rgba(14,165,233,0.12);
    }

    .review-like-count i{
      font-size:14px;
    }

    .testimonial-card{
      background:#fff;
      border-radius:16px;
      border:1px solid var(--border);
      padding:24px;
      min-height:280px;
      display:flex;
      flex-direction:column;
      gap:12px;
      box-shadow:0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.04);
      transition:all 0.3s ease;
    }

    .testimonial-card:hover{
      transform:translateY(-2px);
      box-shadow:0 4px 16px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.06);
      border-color:var(--gray-300);
    }



    .testimonial-card p{



      color:var(--muted);



      line-height:1.5;



    }



    .testimonial-card .rating-row i{



      font-size:16px;



    }



    .swiper-testimonials .swiper-slide{



      display:flex;



      justify-content:center;



    }



    .swiper-testimonials .swiper-slide .testimonial-card{
      max-width:360px;
    }

    /* Reviews */
    .review-card{
      background:#fff;
      border:1px solid var(--border);
      border-radius:16px;
      box-shadow:0 1px 3px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.04);
      padding:24px;
      min-height:300px;
      display:flex;
      flex-direction:column;
      gap:16px;
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor:grab;
      position:relative;
      overflow:hidden;
    }

    .review-card::before{
      content:'';
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:4px;
      background:linear-gradient(90deg, #0ea5e9, #3b82f6);
      transform:scaleX(0);
      transition:transform 0.3s ease;
    }

    .review-card:hover{
      transform:translateY(-4px);
      box-shadow:0 8px 24px rgba(14,165,233,0.12), 0 2px 8px rgba(0,0,0,0.08);
      border-color:#0ea5e9;
    }

    .review-card:hover::before{
      transform:scaleX(1);
    }
    .review-card:active{
      cursor:grabbing;
    }
    .review-text{
      color:var(--gray-700);
      line-height:1.6;
      margin:0;
      flex:1 1 auto;
      font-size:14px;
    }

    .review-badge{
      background:linear-gradient(135deg,#0ea5e9,#3b82f6);
      color:#fff;
      font-weight:600;
      padding:6px 12px;
      border-radius:8px;
      display:inline-flex;
      align-items:center;
      gap:4px;
      font-size:13px;
      box-shadow:0 2px 8px rgba(14,165,233,0.2);
    }
    .review-stat-card{
      background:#ffffff;
      border:1px solid var(--border);
      border-radius:16px;
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow:0 1px 3px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
      position:relative;
      overflow:hidden;
    }

    .review-stat-card::before{
      content:'';
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:4px;
      background:linear-gradient(90deg, #0ea5e9, #3b82f6);
      opacity:0;
      transition:opacity 0.3s ease;
    }

    .review-stat-card:hover{
      transform:translateY(-3px);
      box-shadow:0 8px 24px rgba(14,165,233,0.12), 0 2px 8px rgba(0,0,0,0.08);
      border-color:#0ea5e9;
    }

    .review-stat-card:hover::before{
      opacity:1;
    }

    .review-stat-number{
      font-size:36px;
      font-weight:700;
      color:var(--gray-900);
      line-height:1.2;
      margin-bottom:8px;
    }

    .review-stat-label{
      font-size:14px;
      color:var(--gray-600);
      font-weight:500;
    }
    .review-stat-label{
      font-size:13px;
      color:#6b7280;
      margin-top:4px;
      font-weight:500;
    }
    .review-avatar{
      border:3px solid #e5e7eb;
      transition:all .3s;
    }
    .review-card:hover .review-avatar{
      border-color:#0ea5e9;
      transform:scale(1.05);
    }
    .verified-badge{
      position:absolute;
      bottom:-2px;
      right:-2px;
      background:#fff;
      border-radius:50%;
      padding:2px;
      box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }
    .verified-badge i{
      font-size:18px;
    }
    .reviews-prev,.reviews-next{
      width:44px;
      height:44px;
      background:#fff;
      border:1px solid #e5e7eb;
      border-radius:50%;
      color:#0f172a;
      box-shadow:0 8px 20px rgba(15,23,42,0.1);
      transition:all .3s;
    }
    .reviews-prev:hover,.reviews-next:hover{
      background:#0ea5e9;
      color:#fff;
      border-color:#0ea5e9;
      transform:scale(1.1);
    }
    .reviews-prev::after,.reviews-next::after{
      font-size:18px;
      font-weight:700;
    }
    .reviews-pagination{
      position:relative;
      margin-top:24px;
    }
    .reviews-pagination .swiper-pagination-bullet{
      width:10px;
      height:10px;
      background:#0ea5e9;
      opacity:0.3;
      transition:all .3s;
    }
    .reviews-pagination .swiper-pagination-bullet-active{
      opacity:1;
      width:24px;
      border-radius:999px;
    }
    .review-room-link{
      display:block;
      transition:all .3s;
    }
    .review-room-link:hover{
      transform:translateX(4px);
    }
    .review-room-info{
      background:linear-gradient(135deg,#f8fafc,#ffffff);
      border:1px solid #e5e7eb;
      http://localhost/vinhlong_hotel/contact.php      transition:all .3s;
    }
    .review-room-link:hover .review-room-info{
      background:linear-gradient(135deg,#e8f2ff,#ffffff);
      border-color:#0ea5e9;
      box-shadow:0 4px 12px rgba(14,165,233,0.1);
    }
    .review-room-thumb{
      border-radius:10px;
      object-fit:cover;
      border:2px solid #e5e7eb;
      transition:all .3s;
    }
    .review-room-link:hover .review-room-thumb{
      border-color:#0ea5e9;
      transform:scale(1.05);
    }

    /* Contact */
    .contact-section{
      margin-top:70px;
      margin-bottom:60px;
    }
    .contact-card{
      background:#fff;
      border:1px solid var(--border);
      border-radius:20px;
      box-shadow:0 10px 28px rgba(15,23,42,0.08);
      position:relative;
      overflow:hidden;
      transition:all .4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .contact-card:hover{
      transform:translateY(-4px);
      box-shadow:0 20px 40px rgba(15,23,42,0.12);
      border-color:#0d6efd;
    }
    .contact-cta{
      display:inline-flex;
      align-items:center;
      gap:6px;
      font-weight:700;
      color:#0c66c4;
      text-decoration:none;
      border:1px solid #cbd5e1;
      padding:10px 14px;
      border-radius:12px;
      transition:.2s;
    }
    .contact-cta:hover{
      background:#e8f2ff;
      border-color:#94a3b8;
      color:#0a4f9a;
    }
    .contact-pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      background:#f8fafc;
      border:1px solid #e2e8f0;
      border-radius:12px;
      padding:10px 12px;
      font-weight:600;
      margin-right:8px;
      margin-bottom:10px;
    }
    .contact-meta{color:#64748b;}
    .contact-note{
      background:#f8fafc;
      border:1px dashed #cbd5e1;
      border-radius:12px;
      padding:10px 12px;
      color:#475569;
      font-size:14px;
    }
    .contact-map-top{
      position:relative;
      background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);
      border-bottom:2px solid #e5e7eb;
      z-index:2;
      backdrop-filter:blur(10px);
    }
    .badge-soft{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:8px 14px;
      background:linear-gradient(135deg,#e8f2ff 0%,#dbeafe 100%);
      color:#0c66c4;
      border-radius:999px;
      font-weight:700;
      font-size:13px;
      border:1px solid rgba(13,110,253,0.2);
      box-shadow:0 2px 8px rgba(13,110,253,0.1);
    }
    .map-link{
      color:#0d6efd;
      font-weight:600;
      text-decoration:none;
      padding:8px 14px;
      border-radius:10px;
      background:linear-gradient(135deg,#0d6efd 0%,#0ea5e9 100%);
      color:#fff;
      border:none;
      transition:all .3s;
      box-shadow:0 2px 8px rgba(13,110,253,0.3);
    }
    .map-link:hover{
      background:linear-gradient(135deg,#0b5ed7 0%,#0284c7 100%);
      transform:translateY(-2px);
      box-shadow:0 4px 12px rgba(13,110,253,0.4);
    }
    .social-chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 16px;
      border-radius:12px;
      background:linear-gradient(135deg,#f8fafc 0%,#ffffff 100%);
      border:2px solid #e2e8f0;
      font-weight:600;
      color:#0f172a;
      text-decoration:none;
      margin:0 8px 8px 0;
      transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);
      position:relative;
      overflow:hidden;
    }
    .social-chip::after{
      content:'';
      position:absolute;
      top:50%;
      left:50%;
      width:0;
      height:0;
      border-radius:50%;
      background:rgba(13,110,253,0.1);
      transform:translate(-50%,-50%);
      transition:width .4s,height .4s;
    }
    .social-chip:hover::after{
      width:300px;
      height:300px;
    }
    .social-chip:hover{
      border-color:#0d6efd;
      color:#0d6efd;
      background:linear-gradient(135deg,#e8f2ff 0%,#f0f7ff 100%);
      transform:translateY(-3px);
      box-shadow:0 6px 16px rgba(13,110,253,0.2);
    }
    .social-chip i{
      position:relative;
      z-index:1;
      transition:transform .3s;
    }
    .social-chip:hover i{
      transform:scale(1.2) rotate(5deg);
    }
    .contact-info-item{
      background:linear-gradient(135deg,#f8fafc 0%,#ffffff 100%);
      border:2px solid #e5e7eb;
      border-radius:16px;
      text-decoration:none;
      color:inherit;
      transition:all .4s cubic-bezier(0.4, 0, 0.2, 1);
      position:relative;
      overflow:hidden;
    }
    .contact-info-item::before{
      content:'';
      position:absolute;
      top:0;
      left:-100%;
      width:100%;
      height:100%;
      background:linear-gradient(90deg,transparent,rgba(255,255,255,0.3),transparent);
      transition:left .5s;
    }
    .contact-info-item:hover::before{
      left:100%;
    }
    .contact-info-item:hover{
      background:linear-gradient(135deg,#e8f2ff 0%,#f0f7ff 100%);
      border-color:#0d6efd;
      transform:translateX(8px) translateY(-2px);
      box-shadow:0 8px 20px rgba(13,110,253,0.15);
    }
    .contact-icon-wrapper{
      width:56px;
      height:56px;
      border-radius:16px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-shrink:0;
      font-size:22px;
      transition:all .4s cubic-bezier(0.4, 0, 0.2, 1);
      position:relative;
      overflow:hidden;
    }
    .contact-info-item:hover .contact-icon-wrapper{
      transform:scale(1.1) rotate(5deg);
      box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }
    .contact-map-top .badge-soft{
      background:#e8f2ff;
      color:#0c66c4;
    }
    .contact-map-top .map-link{
      color:#0c66c4;
      background:transparent;
      border:none;
      padding:0;
      font-size:14px;
      font-weight:600;
    }
    .contact-map-top .map-link:hover{
      color:#fff;
      text-decoration:none;
    }
    .contact-iframe{
      transition:transform .4s;
    }
    .contact-card:hover .contact-iframe{
      transform:scale(1.02);
    }
    .contact-map-overlay{
      position:absolute;
      top:0;
      left:0;
      right:0;
      bottom:0;
      pointer-events:none;
      background:linear-gradient(180deg,transparent 0%,rgba(255,255,255,0.03) 100%);
      z-index:1;
    }

    /* Map Info Footer */
    .map-info-footer{
      background:linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      border-top:2px solid #e9ecef;
    }
    .map-info-icon{
      width:40px;
      height:40px;
      border-radius:10px;
      background:linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,110,253,0.05));
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:18px;
      flex-shrink:0;
      transition:all 0.3s ease;
    }
    .map-info-footer .col-md-6:hover .map-info-icon{
      transform:scale(1.1) rotate(5deg);
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    .map-info-footer h6{
      color:#1f2937;
      font-size:15px;
    }
    .map-info-footer .badge{
      font-size:11px;
      padding:6px 10px;
      font-weight:600;
      transition:all 0.3s ease;
    }
    .map-info-footer .badge:hover{
      background:#0d6efd !important;
      color:#fff !important;
      transform:translateY(-2px);
      box-shadow:0 4px 8px rgba(13,110,253,0.3);
    }
    .contact-note{
      background:linear-gradient(135deg,#fff7ed 0%,#fffbeb 100%);
      border:2px dashed #fbbf24;
      border-radius:12px;
      padding:12px 16px;
      color:#92400e;
      font-size:13px;
      font-weight:600;
      transition:all .3s;
    }
    .contact-note:hover{
      background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);
      border-color:#f59e0b;
      transform:scale(1.02);
    }
    .contact-note i{
      animation:pulse 2s infinite;
    }
    @keyframes pulse{
      0%,100%{opacity:1;}
      50%{opacity:0.6;}
    }
    .contact-btn-main{
      padding:14px 24px;
      font-size:16px;
      transition:all .4s cubic-bezier(0.4, 0, 0.2, 1);
      border:none;
      background:linear-gradient(135deg,#0d6efd 0%,#0ea5e9 100%);
    }
    .contact-btn-main:hover{
      transform:translateY(-3px);
      box-shadow:0 10px 25px rgba(13,110,253,0.4);
      background:linear-gradient(135deg,#0b5ed7 0%,#0284c7 100%);
    }
    .contact-btn-main:active{
      transform:translateY(-1px);
    }
    .contact-btn-ripple{
      position:absolute;
      top:50%;
      left:50%;
      width:0;
      height:0;
      border-radius:50%;
      background:rgba(255,255,255,0.3);
      transform:translate(-50%,-50%);
      transition:width .6s,height .6s;
    }
    .contact-btn-main:hover .contact-btn-ripple{
      width:300px;
      height:300px;
    }
    .contact-hours-card{
      background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);
      border:2px solid #bae6fd;
      transition:all .4s;
    }
    .contact-hours-card:hover{
      border-color:#0ea5e9;
      transform:translateY(-2px);
      box-shadow:0 8px 20px rgba(14,165,233,0.15);
    }
    .contact-hours-icon-wrapper{
      width:40px;
      height:40px;
      display:flex;
      align-items:center;
      justify-content:center;
      transition:transform .3s;
    }
    .contact-hours-card:hover .contact-hours-icon-wrapper{
      transform:rotate(15deg) scale(1.1);
    }
    .contact-hours-bg{
      position:absolute;
      top:-50%;
      right:-50%;
      width:200%;
      height:200%;
      background:radial-gradient(circle,rgba(14,165,233,0.05) 0%,transparent 70%);
      animation:float 6s ease-in-out infinite;
    }
    @keyframes float{
      0%,100%{transform:translate(0,0) rotate(0deg);}
      50%{transform:translate(-20px,-20px) rotate(180deg);}
    }
    @media (max-width:768px){
      .contact-icon-wrapper{
        width:48px;
        height:48px;
        font-size:18px;
      }
      .contact-info-item:hover{
        transform:translateX(4px);
      }
      .contact-card:hover{
        transform:none;
      }
    }
    
    /* ===================== MOBILE RESPONSIVE - ROOMS & FACILITIES ===================== */
    @media (max-width: 768px) {
      /* Rooms Section */
      .home-room-card {
        margin-bottom: 20px;
      }
      
      .home-room-thumb img {
        height: 250px !important;
      }
      
      .room-title {
        font-size: 18px !important;
      }
      
      .room-price {
        font-size: 20px !important;
      }
      
      .btn-view-all-rooms {
        padding: 12px 24px;
        font-size: 14px;
      }
      
      /* Facilities Section */
      .facility-card {
        padding: 20px 16px;
        min-height: 180px;
      }
      
      .facility-card img {
        max-width: 56px;
        max-height: 56px;
      }
      
      .facility-card .fw-semibold {
        font-size: 14px;
      }
      
      .facility-card .text-muted {
        font-size: 12px;
      }
      
      /* Sections spacing */
      .container {
        padding-left: 16px;
        padding-right: 16px;
      }
      
      /* Hero overlay */
      .hero-overlay {
        padding: 24px 32px;
        margin-bottom: 20px;
      }
      
      .hero-title {
        font-size: 28px;
      }
      
      .hero-sub {
        font-size: 15px;
      }
    }
    
    @media (max-width: 576px) {
      /* Rooms Section */
      .home-room-thumb img {
        height: 220px !important;
      }
      
      .room-title {
        font-size: 16px !important;
      }
      
      .room-price {
        font-size: 18px !important;
      }
      
      .room-badge-popular,
      .room-badge-discount {
        font-size: 10px;
        padding: 4px 10px;
        top: 8px;
        left: 8px;
      }
      
      .btn-view-all-rooms {
        padding: 10px 20px;
        font-size: 13px;
        width: 100%;
      }
      
      /* Facilities Section */
      .facility-card {
        padding: 16px 12px;
        min-height: 160px;
      }
      
      .facility-card img {
        max-width: 48px;
        max-height: 48px;
      }
      
      .facility-card .fw-semibold {
        font-size: 13px;
      }
      
      /* Hero overlay */
      .hero-overlay {
        padding: 20px 24px;
        margin-bottom: 16px;
        border-radius: 16px;
      }
      
      .hero-title {
        font-size: 24px;
        margin-bottom: 8px;
      }
      
      .hero-sub {
        font-size: 14px;
      }
      
      /* Sections spacing */
      .container {
        padding-left: 12px;
        padding-right: 12px;
      }
      
      /* Section titles */
      h2 {
        font-size: 24px !important;
      }
      
      h3 {
        font-size: 20px !important;
      }
      
      /* Testimonials/Reviews Section */
      .testimonial-card {
        padding: 20px;
        max-width: 100% !important;
      }
      
      .testimonial-card p {
        font-size: 13px;
      }
      
      .review-card {
        padding: 20px;
        min-height: auto;
      }
      
      .review-text {
        font-size: 13px;
      }
      
      .review-avatar {
        width: 40px;
        height: 40px;
      }
      
      .swiper-testimonials .swiper-slide {
        padding: 0 8px;
      }
      
      /* Swiper Navigation */
      .reviews-prev,
      .reviews-next {
        width: 36px;
        height: 36px;
        font-size: 14px;
      }
      
      /* Section Title */
      .home-section-title {
        font-size: 26px !important;
      }
    }
    
    @media (max-width: 576px) {
      /* Testimonials/Reviews Section */
      .testimonial-card {
        padding: 16px;
      }
      
      .testimonial-card p {
        font-size: 12px;
      }
      
      .review-card {
        padding: 16px;
      }
      
      .review-text {
        font-size: 12px;
      }
      
      .review-avatar {
        width: 36px;
        height: 36px;
      }
      
      .swiper-testimonials .swiper-slide {
        padding: 0 4px;
      }
      
      .reviews-prev,
      .reviews-next {
        width: 32px;
        height: 32px;
        font-size: 12px;
      }
      
      .home-section-title {
        font-size: 22px !important;
      }
      
      /* Contact Section */
      .contact-section {
        margin-top: 40px;
        margin-bottom: 40px;
      }
      
      .contact-card {
        border-radius: 16px;
      }
      
      .contact-btn-main {
        padding: 12px 20px;
        font-size: 14px;
        width: 100%;
      }
      
      /* Hero Carousel Mobile */
      .hero-carousel {
        min-height: 300px !important;
        border-radius: 12px;
      }
      
      .hero-swiper .swiper-slide {
        height: 300px !important;
      }
      
      .hero-carousel__content {
        left: 16px !important;
        right: 16px !important;
        bottom: 100px !important;
      }
      
      .hero-carousel__content h1 {
        font-size: 20px !important;
        margin-bottom: 8px;
      }
      
      .hero-carousel__content p {
        font-size: 12px !important;
        margin-bottom: 10px;
      }
      
      .hero-slide-overlay {
        left: 12px !important;
        right: 12px !important;
        bottom: 12px !important;
        padding: 10px 12px;
      }
      
      .hero-slide-badge {
        min-width: 36px;
        height: 36px;
        font-size: 12px;
      }
      
      .hero-slide-text h3 {
        font-size: 14px;
        margin-bottom: 4px;
      }
      
      .hero-slide-text p {
        font-size: 11px;
      }
      
      .hero-carousel__badge {
        top: 12px;
        left: 12px;
        padding: 6px 12px;
        font-size: 11px;
      }
      
      .hero-swiper-pagination {
        bottom: 16px !important;
      }
      
      .hero-swiper-pagination .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        margin: 0 4px !important;
      }
      
      .hero-swiper-pagination .swiper-pagination-bullet-active {
        width: 20px;
        height: 8px;
      }
      
      /* Back to top button */
      .back-to-top {
        width: 40px;
        height: 40px;
        right: 12px;
        bottom: 16px;
        font-size: 18px;
      }
      
      /* Prevent horizontal scroll */
      body {
        overflow-x: hidden;
      }
      
      /* Text overflow handling */
      .room-title,
      .home-room-card h5 {
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
      }
      
      /* Ensure touch targets are at least 44x44px */
      .btn,
      button,
      .counter-btn,
      .calendar-nav-btn {
        min-height: 44px;
        min-width: 44px;
      }
      
      /* Image optimization */
      img {
        max-width: 100%;
        height: auto;
      }
      
      /* Prevent text selection on buttons */
      .btn,
      button {
        -webkit-tap-highlight-color: transparent;
        user-select: none;
      }
      
      /* Smooth scrolling */
      html {
        scroll-behavior: smooth;
      }
      
      /* Better focus states for accessibility */
      .btn:focus,
      button:focus,
      input:focus,
      select:focus {
        outline: 2px solid #0ea5e9;
        outline-offset: 2px;
      }
    }
    
    /* Additional mobile optimizations for 768px */
    @media (max-width: 768px) {
      /* Prevent text overflow */
      .home-room-meta,
      .room-meta {
        font-size: 12px;
      }
      
      /* Better spacing for cards */
      .home-room-card,
      .facility-card,
      .review-card {
        margin-bottom: 16px;
      }
      
      /* Container padding */
      .container-fluid {
        padding-left: 12px;
        padding-right: 12px;
      }
      
      /* Section margins */
      section {
        margin-bottom: 40px;
      }
      
      /* Better button spacing */
      .btn-group {
        flex-direction: column;
        width: 100%;
      }
      
      .btn-group .btn {
        width: 100%;
        margin-bottom: 8px;
      }
    }

  </style>



  



</head>



<body class="bg-light">







  <?php require('inc/header.php'); ?>



  <?php ensureFavoritesTable(); ?>











<!-- =========== CAROUSEL ============== -->



<div class="container-fluid px-lg-4 mt-4">



    <div class="hero-carousel shadow-lg">



    <div class="hero-carousel__badge">Vĩnh Long Hotel</div>



    <div class="hero-carousel__content">



      <p class="text-uppercase small mb-2 text-white-50" data-i18n="hero.experience">Trải nghiệm sang trọng giữa lòng Vĩnh Long</p>

      <h1 class="fw-bold text-white mb-2" data-i18n="hero.title">Khách sạn nghỉ dưỡng sang trọng giá hấp dẫn</h1>

      <p class="text-white-50 mb-3" data-i18n="hero.description">Chọn phòng chuẩn 4-5★, ưu đãi QR, hỗ trợ 24/7.</p>

      <div class="d-flex gap-2 flex-wrap">



        <a href="rooms.php" class="btn btn-light fw-bold px-4" data-i18n="hero.exploreRooms">Khám phá phòng</a>



        <a href="#promo" class="btn btn-outline-light fw-bold px-4" data-i18n="hero.viewPromos">Xem ưu đãi</a>



      </div>



    </div>



    <div class="swiper hero-swiper">



      <div class="swiper-wrapper">



        <?php 



        $res = selectAll('carousel');



        $slide_idx = 0;



        while($row = mysqli_fetch_assoc($res))



        {



            $slide_idx++;



            $img = CAROUSEL_IMG_PATH.$row['image'];



            $title = htmlspecialchars($row['title'] ?? 'Vĩnh Long Hotel', ENT_QUOTES, 'UTF-8');

            // Lấy ngôn ngữ hiện tại
            $current_lang = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : 'vi';
            if($current_lang !== 'en' && $current_lang !== 'vi') {
              $current_lang = 'vi';
            }
            
            // Dịch subtitle
            $subtitle_default = $current_lang === 'en' 
              ? 'Experience 4–5★ accommodation in Vinh Long'
              : 'Trải nghiệm lưu trú 4–5★ tại Vĩnh Long';
            $subtitle = htmlspecialchars($row['subtitle'] ?? $subtitle_default, ENT_QUOTES, 'UTF-8');



            echo "



            <div class=\"swiper-slide carousel-slide\">



                <img src=\"{$img}\" class=\"carousel-img\" loading=\"lazy\" decoding=\"async\" alt=\"Slide {$slide_idx}\">



                <div class=\"hero-slide-overlay\">



                  <div class=\"hero-slide-badge\">{$slide_idx} /</div>



                  <div class=\"hero-slide-text\">



                    <h3 class=\"mb-1\">{$title}</h3>



                    <p class=\"mb-0\" data-i18n=\"hero.carouselSubtitle\">{$subtitle}</p>



                  </div>



                </div>



            </div>";



        }



        ?>



      </div>



      <div class="swiper-pagination hero-swiper-pagination"></div>



    </div>



  </div>



</div>







<style>



.hero-carousel{
  position:relative;
  border-radius:24px;
  overflow:hidden;
  min-height:520px;
  background:#1e293b;
  box-shadow:0 4px 24px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);
}



.hero-carousel::after{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(90deg,rgba(30,41,59,.85),rgba(30,41,59,.4),rgba(30,41,59,0));
  z-index:1;
  pointer-events:none;
}

.hero-carousel::before{
  content:'';
  position:absolute;
  inset:0;
  background:radial-gradient(circle at 30% 50%, rgba(14,165,233,0.08) 0%, transparent 50%);
  z-index:2;
  pointer-events:none;
}



.hero-carousel__badge{
  position:absolute;
  top:24px;
  left:24px;
  z-index:6;
  background:rgba(255,255,255,0.12);
  color:#fff;
  border:1px solid rgba(255,255,255,0.2);
  padding:8px 16px;
  border-radius:8px;
  font-weight:600;
  font-size:13px;
  backdrop-filter:blur(8px);
  box-shadow:0 2px 8px rgba(0,0,0,0.1);
  transition:all 0.2s ease;
  letter-spacing:0.3px;
}

.hero-carousel__badge:hover{
  background:rgba(255,255,255,0.18);
  border-color:rgba(255,255,255,0.3);
}



.hero-carousel__content{
  position:absolute;
  left:60px;
  top:50%;
  transform:translateY(-50%);
  z-index:5;
  max-width:580px;
  animation:fadeInUp 0.8s ease-out;
  pointer-events:none;
}

.hero-carousel__content *{
  pointer-events:auto;
}

@keyframes fadeInUp{
  from{
    opacity:0;
    transform:translateY(-50%) translateY(20px);
  }
  to{
    opacity:1;
    transform:translateY(-50%) translateY(0);
  }
}

.hero-carousel__content h1{
  font-size:40px;
  line-height:1.3;
  font-weight:700;
  text-shadow:0 2px 12px rgba(0,0,0,0.3);
  margin-bottom:16px;
  letter-spacing:-0.3px;
  word-wrap:break-word;
}

.hero-carousel__content p{
  text-shadow:0 2px 12px rgba(0,0,0,0.4);
  line-height:1.6;
}

.hero-carousel__content .btn{
  transition:all 0.2s ease;
  font-weight:600;
  padding:12px 24px;
  border-radius:8px;
  box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

.hero-carousel__content .btn:hover{
  transform:translateY(-2px);
  box-shadow:0 4px 12px rgba(0,0,0,0.15);
}



.hero-swiper{
  height:100%;
  position:relative;
}

.hero-swiper .swiper-slide{
  height:520px;
  position:relative;
  overflow:hidden;
}

.hero-swiper .carousel-img{
  width:100%;
  height:100%;
  object-fit:cover;
  filter:brightness(0.9) contrast(1.05);
  display:block;
  transition:transform 4s ease-out, filter 0.6s ease;
  transform:scale(1);
}

.hero-swiper .swiper-slide-active .carousel-img{
  transform:scale(1.08);
  filter:brightness(0.95) contrast(1.1);
}

.hero-swiper .swiper-slide::after{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 100%);
  z-index:1;
  pointer-events:none;
}



.hero-slide-overlay{
  position:absolute;
  left:40px;
  bottom:32px;
  z-index:3;
  display:flex;
  align-items:center;
  gap:16px;
  padding:16px 20px;
  border-radius:16px;
  background:linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.4));
  border:1px solid rgba(255,255,255,0.2);
  color:#fff;
  box-shadow:0 16px 40px rgba(0,0,0,0.4);
  backdrop-filter:blur(12px);
  transition:all 0.3s ease;
}

.hero-slide-overlay:hover{
  background:linear-gradient(135deg, rgba(0,0,0,0.8), rgba(0,0,0,0.5));
  transform:translateY(-4px);
  box-shadow:0 20px 50px rgba(0,0,0,0.5);
}



.hero-slide-badge{
  min-width:48px;
  height:48px;
  border-radius:12px;
  background:linear-gradient(135deg, rgba(14,165,233,0.3), rgba(56,189,248,0.2));
  border:1px solid rgba(255,255,255,0.2);
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:900;
  font-size:16px;
  letter-spacing:1px;
  box-shadow:0 4px 12px rgba(0,0,0,0.2);
}



.hero-slide-text h3{
  font-size:20px;
  font-weight:900;
  margin-bottom:6px;
  text-shadow:0 2px 8px rgba(0,0,0,0.3);
}

.hero-slide-text p{
  color:rgba(255,255,255,0.9);
  font-size:14px;
  line-height:1.5;
  text-shadow:0 1px 4px rgba(0,0,0,0.2);
}









.hero-swiper-pagination{
  bottom:24px !important;
  z-index:5;
}

.hero-swiper-pagination .swiper-pagination-bullet{
  background:#fff;
  opacity:0.5;
  width:10px;
  height:10px;
  transition:all 0.3s ease;
  margin:0 6px !important;
}

.hero-swiper-pagination .swiper-pagination-bullet:hover{
  opacity:0.8;
  transform:scale(1.2);
}

.hero-swiper-pagination .swiper-pagination-bullet-active{
  opacity:1;
  width:28px;
  height:10px;
  border-radius:999px;
  background:linear-gradient(90deg, #0ea5e9, #38bdf8);
  box-shadow:0 0 16px rgba(14,165,233,0.6);
  transform:scale(1.1);
}



@media (max-width: 992px){
  .hero-carousel{
    min-height:450px;
    border-radius:20px;
  }
  .hero-swiper .swiper-slide{height:450px;}
  .hero-carousel__content{
    left:32px;
    right:32px;
    max-width:100%;
  }
  .hero-carousel__content h1{font-size:32px;}
  .hero-slide-overlay{
    left:20px;
    right:20px;
    flex-wrap:wrap;
    bottom:20px;
  }
}

@media (max-width: 768px){
  .hero-carousel{
    min-height:400px;
    border-radius:16px;
  }
  .hero-swiper .swiper-slide{height:400px;}
  .hero-carousel__content{
    top:auto;
    bottom:140px;
    transform:none;
    max-width:100%;
    left:20px;
    right:20px;
    z-index:5;
  }
  .hero-carousel__content h1{
    font-size:24px;
    line-height:1.3;
    margin-bottom:12px;
  }
  .hero-carousel__content p{
    font-size:13px;
    margin-bottom:12px;
  }
  .hero-slide-overlay{
    bottom:16px;
    left:16px;
    right:16px;
    padding:12px 16px;
    z-index:3;
  }
  .hero-carousel__badge{
    top:16px;
    left:16px;
    padding:8px 14px;
    font-size:12px;
  }
}



</style>











<!-- ===================== HERO + SEARCH ===================== -->



<div class="hero-box">










  <form action="rooms.php" method="GET">



      <div class="traveloka-search-box">



          <div class="row" style="margin:0; display:flex; align-items:stretch; flex-wrap:nowrap;">



            <div class="col-lg-2" style="padding:20px 20px; border-right:1px solid #d1d5db; flex:1; min-width:150px;">



              <div class="search-label">
                <i class="bi bi-geo-alt-fill me-1"></i><span data-i18n="search.location">Địa điểm</span>
              </div>

              <input type="text" name="keyword" id="keyword_input" class="traveloka-input" placeholder="Thêm địa điểm" data-i18n-placeholder="search.selectLocation">



            </div>







            <div class="col-lg-3" style="padding:20px 24px; border-right:1px solid #d1d5db; position:relative; flex:1.4; min-width:180px;">
              <div class="search-label">
                <i class="bi bi-calendar-check me-1"></i><span data-i18n="search.checkIn">Ngày nhận phòng & Trả phòng</span>
              </div>
              <div class="date-range-wrapper" style="position:relative;">
                <input type="text" id="date-range-input" class="traveloka-input" placeholder="Chọn ngày đặt và trả phòng" readonly style="cursor:pointer;" data-i18n-placeholder="search.selectDatesHint">
                <input type="hidden" name="checkin" id="checkin_hidden">
                <input type="hidden" name="checkout" id="checkout_hidden">
                
                <!-- Calendar Popup -->
                <div id="date-range-calendar" class="date-range-calendar" style="display:none;">
                  <div class="calendar-container">
                    <div class="calendar-month">
                      <div class="calendar-header">
                        <button type="button" class="calendar-nav-btn" onclick="changeMonth(-1)">
                          <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="calendar-month-year" id="month1-label"></div>
                        <div style="width:40px;"></div>
                      </div>
                      <div class="calendar-grid" id="calendar1"></div>
                    </div>
                    <div class="calendar-month">
                      <div class="calendar-header">
                        <div style="width:40px;"></div>
                        <div class="calendar-month-year" id="month2-label"></div>
                        <button type="button" class="calendar-nav-btn" onclick="changeMonth(1)">
                          <i class="bi bi-chevron-right"></i>
                        </button>
                      </div>
                      <div class="calendar-grid" id="calendar2"></div>
                    </div>
                  </div>
                  <div class="calendar-footer">
                    <button type="button" class="btn-clear-dates" onclick="clearDates()" data-i18n="search.clear">Xóa</button>
                    <button type="button" class="btn-apply-dates" id="btn-apply-dates" onclick="applyDates()" disabled data-i18n="search.apply">Áp dụng</button>
                    <div class="date-picker-hint" id="date-picker-hint" style="display:none;">
                      <i class="bi bi-info-circle"></i>
                      <span data-i18n="search.selectDatesHint">Vui lòng chọn đủ ngày nhận phòng và ngày trả phòng</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>







            <div class="col-lg-2" style="padding:20px 20px; border-right:1px solid #d1d5db; position:relative; flex:1.2; min-width:180px;">
              <div class="search-label">
                <i class="bi bi-people-fill me-1"></i><span data-i18n="search.guests">Người lớn & Trẻ em</span>
              </div>
              <div class="guests-wrapper" style="position:relative;">
                <input type="text" id="guests-input" class="traveloka-input" placeholder="Chọn số khách" readonly style="cursor:pointer;" data-i18n-placeholder="search.selectGuests">
                <input type="hidden" name="adult" id="adult_hidden" value="">
                <input type="hidden" name="children" id="children_hidden" value="">
                
                <!-- Guests Popup -->
                <div id="guests-popup" class="guests-popup" style="display:none;">
                  <div class="guests-section">
                    <div class="guests-item">
                      <div class="guests-item-info">
                        <div class="guests-item-label" data-i18n="search.adults">Người lớn</div>
                        <div class="guests-item-desc" data-i18n="search.adultsDesc">Từ 13 tuổi trở lên</div>
                      </div>
                      <div class="guests-counter">
                        <button type="button" class="counter-btn" onclick="changeGuests('adult', -1)">
                          <i class="bi bi-dash"></i>
                        </button>
                        <span class="counter-value" id="adult-count">0</span>
                        <button type="button" class="counter-btn" onclick="changeGuests('adult', 1)">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>
                    </div>
                    
                    <div class="guests-item">
                      <div class="guests-item-info">
                        <div class="guests-item-label" data-i18n="search.children">Trẻ em</div>
                        <div class="guests-item-desc" data-i18n="search.childrenDesc">Độ tuổi dưới 12</div>
                      </div>
                      <div class="guests-counter">
                        <button type="button" class="counter-btn" onclick="changeGuests('children', -1)">
                          <i class="bi bi-dash"></i>
                        </button>
                        <span class="counter-value" id="children-count">0</span>
                        <button type="button" class="counter-btn" onclick="changeGuests('children', 1)">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="guests-footer">
                    <button type="button" class="btn-clear-guests" onclick="clearGuests()" data-i18n="search.clear">Xóa</button>
                    <button type="button" class="btn-apply-guests" id="btn-apply-guests" onclick="applyGuests()" disabled data-i18n="search.apply">Áp dụng</button>
                    <div class="guests-picker-hint" id="guests-picker-hint" style="display:none;">
                      <i class="bi bi-info-circle"></i>
                      <span data-i18n="search.selectGuestsHint">Vui lòng chọn ít nhất 1 người lớn</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>







            <div class="col-lg-2" style="padding:20px 16px; border-right:1px solid #d1d5db; flex:0 0 auto; min-width:140px; max-width:180px;">
              <div class="search-label">
                <i class="bi bi-star-fill me-1"></i><span data-i18n="search.stars">Số sao</span>
              </div>
              <select name="star" id="stars" class="traveloka-select">
                <option value="" data-i18n="search.selectStars">Chọn số sao</option>
                <option value="1">1 sao</option>
                <option value="2">2 sao</option>
                <option value="3">3 sao</option>
                <option value="4">4 sao</option>
                <option value="5">5 sao</option>
              </select>
            </div>

            <div class="col-lg-2" style="padding:20px 16px; border-right:1px solid #d1d5db; flex:0 0 auto; min-width:180px; max-width:220px;">
              <div class="search-label">
                <i class="bi bi-geo-alt me-1"></i><span data-i18n="search.area">Khu vực Vĩnh Long</span>
              </div>
              <select name="district" id="district_input" class="traveloka-select">
                <option value="" data-i18n="search.selectArea">Tất cả khu vực</option>
                <option value="TP. Vĩnh Long">TP. Vĩnh Long</option>
                <option value="Long Hồ">Long Hồ</option>
                <option value="Mang Thít">Mang Thít</option>
                <option value="Vũng Liêm">Vũng Liêm</option>
                <option value="Tam Bình">Tam Bình</option>
                <option value="Bình Tân">Bình Tân</option>
                <option value="Trà Ôn">Trà Ôn</option>
                <option value="Bình Minh">Thị xã Bình Minh</option>
              </select>
            </div>







            <div class="col-lg-2 d-flex align-items-center justify-content-center" style="padding:10px 6px; flex-shrink:0; min-width:auto; flex:0.4;">



              <button type="submit" class="traveloka-btn">
                <i class="bi bi-search"></i>
              </button>



            </div>



          </div>



      </div>



  </form>



</div>



<script>
// Date Range Picker
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedStartDate = null;
let selectedEndDate = null;

const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

document.addEventListener('DOMContentLoaded', function() {
  const dateInput = document.getElementById('date-range-input');
  const calendar = document.getElementById('date-range-calendar');
  
  if(dateInput && calendar) {
    // Toggle calendar
    dateInput.addEventListener('click', function(e) {
      e.stopPropagation();
      calendar.style.display = calendar.style.display === 'none' ? 'block' : 'none';
      if(calendar.style.display === 'block') {
        renderCalendars();
        updateApplyButton();
      }
    });
    
    // Close calendar when clicking outside - ONLY if both dates are selected
    document.addEventListener('click', function(e) {
      if(!dateInput.contains(e.target) && !calendar.contains(e.target)) {
        // Only close if both dates are selected
        if(selectedStartDate && selectedEndDate) {
          calendar.style.display = 'none';
        }
      }
    });
    
    // Initialize calendars
    renderCalendars();
    updateApplyButton();
  }
});

function renderCalendars() {
  renderCalendar(1, currentMonth, currentYear);
  renderCalendar(2, currentMonth + 1, currentYear);
  
  // Update month labels with icon
  document.getElementById('month1-label').innerHTML = '<i class="bi bi-calendar3 me-2"></i>' + monthNames[currentMonth] + ' ' + currentYear;
  let nextMonth = currentMonth + 1;
  let nextYear = currentYear;
  if(nextMonth > 11) {
    nextMonth = 0;
    nextYear++;
  }
  document.getElementById('month2-label').innerHTML = '<i class="bi bi-calendar3 me-2"></i>' + monthNames[nextMonth] + ' ' + nextYear;
}

function renderCalendar(calendarNum, month, year) {
  const calendar = document.getElementById('calendar' + calendarNum);
  if(!calendar) return;
  
  // Adjust year if month is out of range
  if(month < 0) {
    month = 11;
    year--;
  } else if(month > 11) {
    month = 0;
    year++;
  }
  
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const today = new Date();
  today.setHours(0,0,0,0);
  
  let html = '<div class="calendar-day-header">CN</div>';
  for(let i = 1; i < 7; i++) {
    html += '<div class="calendar-day-header">' + dayNames[i] + '</div>';
  }
  
  // Empty cells for days before month starts
  for(let i = 0; i < firstDay; i++) {
    html += '<div class="calendar-day disabled"></div>';
  }
  
  // Days of the month
  for(let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day);
    const dateStr = formatDate(date);
    let classes = 'calendar-day';
    
    // Check if disabled (past dates)
    if(date < today) {
      classes += ' disabled';
    }
    
    // Check if today
    if(dateStr === formatDate(today)) {
      classes += ' today';
    }
    
    // Check if selected
    if(selectedStartDate && dateStr === selectedStartDate) {
      classes += ' selected-start';
    } else if(selectedEndDate && dateStr === selectedEndDate) {
      classes += ' selected-end';
    } else if(isInRange(dateStr)) {
      classes += ' in-range';
    }
    
    // Add data attribute for hover effect when selecting end date
    const dataAttr = selectedStartDate && !selectedEndDate && date >= new Date(selectedStartDate) && date >= today 
      ? ' data-selectable-end="true"' 
      : '';
    
    html += '<div class="' + classes + '" onclick="selectDate(\'' + dateStr + '\')"' + dataAttr + '>' + day + '</div>';
  }
  
  calendar.innerHTML = html;
}

function selectDate(dateStr) {
  const date = new Date(dateStr);
  const today = new Date();
  today.setHours(0,0,0,0);
  
  if(date < today) return; // Can't select past dates
  
  if(!selectedStartDate || (selectedStartDate && selectedEndDate)) {
    // Start new selection
    selectedStartDate = dateStr;
    selectedEndDate = null;
  } else if(selectedStartDate && !selectedEndDate) {
    // Select end date
    if(new Date(dateStr) < new Date(selectedStartDate)) {
      // If end date is before start date, swap them
      selectedEndDate = selectedStartDate;
      selectedStartDate = dateStr;
    } else {
      selectedEndDate = dateStr;
    }
  }
  
  updateInput();
  renderCalendars();
  updateApplyButton();
}

function isInRange(dateStr) {
  if(!selectedStartDate || !selectedEndDate) return false;
  const date = new Date(dateStr);
  const start = new Date(selectedStartDate);
  const end = new Date(selectedEndDate);
  return date > start && date < end;
}

function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return year + '-' + month + '-' + day;
}

function formatDateDisplay(dateStr) {
  if(!dateStr) return '';
  const date = new Date(dateStr);
  const day = date.getDate();
  const month = date.getMonth() + 1;
  return day + ' tháng ' + month;
}

function changeMonth(direction) {
  currentMonth += direction;
  if(currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if(currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendars();
}

function clearDates() {
  selectedStartDate = null;
  selectedEndDate = null;
  updateInput();
  renderCalendars();
  updateApplyButton();
  document.getElementById('date-range-calendar').style.display = 'none';
}

function applyDates() {
  // Only allow apply if both dates are selected
  if(!selectedStartDate || !selectedEndDate) {
    showDateHint();
    return;
  }
  
  updateInput();
  document.getElementById('date-range-calendar').style.display = 'none';
  hideDateHint();
}

function updateApplyButton() {
  const applyBtn = document.getElementById('btn-apply-dates');
  const hint = document.getElementById('date-picker-hint');
  
  if(applyBtn) {
    if(selectedStartDate && selectedEndDate) {
      applyBtn.disabled = false;
      if(hint) hint.style.display = 'none';
    } else {
      applyBtn.disabled = true;
    }
  }
}

function showDateHint() {
  const hint = document.getElementById('date-picker-hint');
  if(hint) {
    hint.style.display = 'flex';
    setTimeout(function() {
      hint.style.display = 'none';
    }, 3000);
  }
}

function hideDateHint() {
  const hint = document.getElementById('date-picker-hint');
  if(hint) {
    hint.style.display = 'none';
  }
}

function updateInput() {
  const input = document.getElementById('date-range-input');
  const checkinInput = document.getElementById('checkin_hidden');
  const checkoutInput = document.getElementById('checkout_hidden');
  
  if(selectedStartDate && selectedEndDate) {
    input.value = formatDateDisplay(selectedStartDate) + ' - ' + formatDateDisplay(selectedEndDate);
    checkinInput.value = selectedStartDate;
    checkoutInput.value = selectedEndDate;
  } else if(selectedStartDate) {
    input.value = formatDateDisplay(selectedStartDate) + ' - Chọn ngày trả phòng';
    checkinInput.value = selectedStartDate;
    checkoutInput.value = '';
  } else {
    input.value = '';
    checkinInput.value = '';
    checkoutInput.value = '';
  }
}
</script>

<script>
// Guests Picker
let adultCount = 0;
let childrenCount = 0;

document.addEventListener('DOMContentLoaded', function() {
  const guestsInput = document.getElementById('guests-input');
  const guestsPopup = document.getElementById('guests-popup');
  
  if(guestsInput && guestsPopup) {
    // Toggle popup
    guestsInput.addEventListener('click', function(e) {
      e.stopPropagation();
      guestsPopup.style.display = guestsPopup.style.display === 'none' ? 'block' : 'none';
      if(guestsPopup.style.display === 'block') {
        updateGuestsButtons();
        updateApplyGuestsButton();
      }
    });
    
    // Close popup when clicking outside - ONLY if at least 1 adult is selected
    document.addEventListener('click', function(e) {
      if(!guestsInput.contains(e.target) && !guestsPopup.contains(e.target)) {
        // Only close if at least 1 adult is selected
        if(adultCount > 0) {
          guestsPopup.style.display = 'none';
        }
      }
    });
    
    // Initialize display
    updateGuestsInput();
    updateGuestsButtons();
    updateApplyGuestsButton();
  }
});

function changeGuests(type, delta) {
  if(type === 'adult') {
    adultCount = Math.max(0, Math.min(10, adultCount + delta));
    document.getElementById('adult-count').textContent = adultCount;
  } else if(type === 'children') {
    childrenCount = Math.max(0, Math.min(10, childrenCount + delta));
    document.getElementById('children-count').textContent = childrenCount;
  }
  
  updateGuestsButtons();
  updateGuestsInput();
  updateApplyGuestsButton();
}

function updateGuestsButtons() {
  const adultMinus = document.querySelector('.guests-item:first-child .counter-btn:first-child');
  const adultPlus = document.querySelector('.guests-item:first-child .counter-btn:last-child');
  const childrenMinus = document.querySelector('.guests-item:last-child .counter-btn:first-child');
  const childrenPlus = document.querySelector('.guests-item:last-child .counter-btn:last-child');
  
  if(adultMinus) adultMinus.disabled = adultCount === 0;
  if(adultPlus) adultPlus.disabled = adultCount >= 10;
  if(childrenMinus) childrenMinus.disabled = childrenCount === 0;
  if(childrenPlus) childrenPlus.disabled = childrenCount >= 10;
}

function updateApplyGuestsButton() {
  const applyBtn = document.getElementById('btn-apply-guests');
  const hint = document.getElementById('guests-picker-hint');
  
  if(applyBtn) {
    if(adultCount > 0) {
      applyBtn.disabled = false;
      if(hint) hint.style.display = 'none';
    } else {
      applyBtn.disabled = true;
    }
  }
}

function updateGuestsInput() {
  const input = document.getElementById('guests-input');
  const adultInput = document.getElementById('adult_hidden');
  const childrenInput = document.getElementById('children_hidden');
  
  const total = adultCount + childrenCount;
  
  if(total === 0) {
    input.value = '';
    adultInput.value = '';
    childrenInput.value = '';
  } else {
    // Sử dụng i18n để dịch
    let adultsText = 'người lớn';
    let childrenText = 'trẻ em';
    if(window.i18n && typeof window.i18n.translate === 'function') {
      adultsText = window.i18n.translate('search.adults');
      childrenText = window.i18n.translate('search.children');
    }
    let text = '';
    if(adultCount > 0) {
      text = adultCount + ' ' + adultsText;
    }
    if(childrenCount > 0) {
      if(text) text += ', ';
      text += childrenCount + ' ' + childrenText;
    }
    input.value = text;
    adultInput.value = adultCount;
    childrenInput.value = childrenCount;
  }
  
  updateGuestsButtons();
}

function clearGuests() {
  adultCount = 0;
  childrenCount = 0;
  document.getElementById('adult-count').textContent = '0';
  document.getElementById('children-count').textContent = '0';
  updateGuestsInput();
  updateApplyGuestsButton();
  document.getElementById('guests-popup').style.display = 'none';
}

function applyGuests() {
  // Only allow apply if at least 1 adult is selected
  if(adultCount === 0) {
    showGuestsHint();
    return;
  }
  
  updateGuestsInput();
  document.getElementById('guests-popup').style.display = 'none';
  hideGuestsHint();
}

function showGuestsHint() {
  const hint = document.getElementById('guests-picker-hint');
  if(hint) {
    hint.style.display = 'flex';
    setTimeout(function() {
      hint.style.display = 'none';
    }, 3000);
  }
}

function hideGuestsHint() {
  const hint = document.getElementById('guests-picker-hint');
  if(hint) {
    hint.style.display = 'none';
  }
}
</script>







<!-- ===================== MÃ GIẢM GIÁ ===================== -->

<!-- ===================== MÃ GIẢM GIÁ ===================== -->

<!-- ===================== MÃ GIẢM GIÁ ===================== -->

<!-- ===================== MÃ GIẢM GIÁ ===================== -->



<style>



  .promo-wrap{background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:18px 20px;box-shadow:0 12px 30px rgba(15,23,42,0.06);}

  .promo-tags .btn{border-radius:999px;font-weight:700;}

  .promo-tag-btn.active{background:#0d6efd;color:#fff;border-color:#0d6efd;}

  .promo-tag-btn:not(.active){background:#fff;color:#0f172a;border-color:#e5e7eb;}

  .promo-item{transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);}

  .promo-item:hover{transform:translateY(-8px);}

  /* ========== HORIZONTAL SCROLL PROMO ========== */
  .promo-scroll-wrapper{
    position: relative;
    padding: 0 50px;
  }

  .promo-scroll-container{
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
    padding: 10px 0;
    cursor: grab;
    user-select: none;
  }
  
  .promo-scroll-container:active{
    cursor: grabbing;
  }

  .promo-scroll-container::-webkit-scrollbar{
    display: none; /* Chrome/Safari */
  }

  .promo-scroll-content{
    display: flex;
    gap: 20px;
  }

  .promo-scroll-btn{
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #0d6efd;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
  }

  .promo-scroll-btn:hover{
    background: #0d6efd;
    color: #ffffff;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
  }

  .promo-scroll-btn:active{
    transform: translateY(-50%) scale(0.95);
  }

  .promo-scroll-prev{
    left: 0;
  }

  .promo-scroll-next{
    right: 0;
  }

  .promo-scroll-btn:disabled{
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
  }

  @media (max-width: 768px){
    .promo-scroll-wrapper{
      padding: 0 40px;
    }

    .promo-scroll-btn{
      width: 38px;
      height: 38px;
      font-size: 16px;
    }

    .promo-item{
      width: 320px !important;
      min-width: 320px !important;
    }
  }

  /* ========== COUPON/VOUCHER STYLE ========== */
  .promo-card{
    background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border:2px solid #e5e7eb;
    border-radius:20px;
    padding:0;
    box-shadow:0 8px 24px rgba(15,23,42,0.08);
    height:100%;
    display:flex;
    overflow:hidden;
    position:relative;
    transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .promo-card::before{
    content:'';
    position:absolute;
    left:65%;
    top:0;
    bottom:0;
    width:2px;
    background:repeating-linear-gradient(
      to bottom,
      transparent 0,
      transparent 8px,
      #e5e7eb 8px,
      #e5e7eb 16px
    );
    z-index:1;
  }

  .promo-card::after{
    content:'';
    position:absolute;
    left:calc(65% - 12px);
    top:50%;
    transform:translateY(-50%);
    width:24px;
    height:24px;
    background:#f8fafc;
    border-radius:50%;
    border:2px solid #e5e7eb;
    z-index:2;
  }

  .promo-card:hover{
    box-shadow:0 16px 40px rgba(15,23,42,0.15);
    border-color:#0d6efd;
    transform:scale(1.02);
  }

  .promo-card-left{
    flex:1;
    padding:20px;
    position:relative;
    background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  }

  .promo-card-right{
    width:35%;
    background:linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:20px 16px;
    position:relative;
    overflow:hidden;
  }

  .promo-card-right::before{
    content:'';
    position:absolute;
    top:-50%;
    right:-50%;
    width:200%;
    height:200%;
    background:radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation:shine 3s infinite;
  }

  @keyframes shine{
    0%, 100%{transform:translate(0, 0) rotate(0deg);}
    50%{transform:translate(20px, 20px) rotate(180deg);}
  }

  .promo-badge{
    display:inline-flex;
    align-items:center;
    background:linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius:20px;
    padding:6px 12px;
    font-size:11px;
    font-weight:800;
    color:#78350f;
    margin-bottom:12px;
    box-shadow:0 4px 12px rgba(251,191,36,0.3);
    text-transform:uppercase;
    letter-spacing:0.5px;
  }

  .promo-badge i{
    animation:pulse 2s infinite;
  }

  @keyframes pulse{
    0%, 100%{transform:scale(1);}
    50%{transform:scale(1.1);}
  }

  .promo-title{
    font-weight:900;
    font-size:18px;
    margin-bottom:8px;
    color:#0f172a;
    line-height:1.3;
    background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
  }

  .promo-desc{
    color:#64748b;
    font-size:13px;
    margin-bottom:12px;
    line-height:1.5;
  }

  .promo-exp{
    font-size:12px;
    color:#94a3b8;
    margin-bottom:16px;
    display:flex;
    align-items:center;
    gap:6px;
  }

  .promo-exp::before{
    content:'⏰';
    font-size:14px;
  }

  .promo-code-wrapper{
    text-align:center;
    color:#ffffff;
  }

  .promo-code-label{
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:1px;
    opacity:0.9;
    margin-bottom:8px;
    font-weight:600;
  }

  .promo-code{
    display:inline-block;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    border:2px dashed rgba(255,255,255,0.4);
    border-radius:12px;
    padding:12px 20px;
    font-weight:900;
    font-size:20px;
    letter-spacing:2px;
    color:#ffffff;
    text-shadow:0 2px 8px rgba(0,0,0,0.2);
    margin-bottom:12px;
    transition:all 0.3s ease;
  }

  .promo-code:hover{
    background:rgba(255,255,255,0.25);
    transform:scale(1.05);
  }

  .promo-actions{
    display:flex;
    flex-direction:column;
    gap:8px;
    width:100%;
    padding:0 8px;
  }

  .promo-copy{
    background:rgba(255,255,255,0.2);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.3);
    border-radius:10px;
    padding:10px 16px;
    font-weight:700;
    color:#ffffff;
    font-size:13px;
    transition:all 0.3s ease;
    cursor:pointer;
    width:100%;
  }

  .promo-copy:hover{
    background:rgba(255,255,255,0.3);
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
  }

  .promo-save{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.3);
    border-radius:10px;
    padding:10px 16px;
    font-weight:700;
    color:#ffffff;
    font-size:13px;
    transition:all 0.3s ease;
    cursor:pointer;
    width:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
  }

  .promo-save:hover{
    background:rgba(255,255,255,0.25);
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
  }

  .promo-save.saved{
    background:rgba(34,197,94,0.3);
    border-color:rgba(34,197,94,0.5);
  }

  /* Responsive */
  @media (max-width:768px){
    .promo-card{
      flex-direction:column;
    }
    .promo-card::before,
    .promo-card::after{
      display:none;
    }
    .promo-card-right{
      width:100%;
      padding:16px;
    }
    .promo-actions{
      flex-direction:row;
    }
  }



  /* WHY CHOOSE US SECTION */

  .why-choose-section{
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 60%, #f1f5f9 100%);
    padding: 40px 0;
    margin: 30px 0;
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
  }

  .why-choose-section::before{
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
      radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
      radial-gradient(circle at 80% 80%, rgba(148, 163, 184, 0.03) 0%, transparent 50%);
    pointer-events: none;
    z-index: 1;
  }

  /* Wave animation like water surface - subtle and gentle */
  .why-choose-section::after{
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200%;
    height: 80px;
    background: 
      repeating-linear-gradient(
        90deg,
        transparent,
        transparent 40px,
        rgba(59, 130, 246, 0.08) 40px,
        rgba(59, 130, 246, 0.08) 80px
      ),
      linear-gradient(180deg, rgba(59, 130, 246, 0.06) 0%, rgba(59, 130, 246, 0.03) 50%, transparent 100%);
    clip-path: polygon(
      0% 100%,
      0% 75%,
      10% 70%,
      20% 75%,
      30% 65%,
      40% 70%,
      50% 60%,
      60% 65%,
      70% 55%,
      80% 60%,
      90% 50%,
      100% 55%,
      100% 100%
    );
    animation: wave 15s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
  }

  @keyframes wave{
    0%, 100%{
      transform: translateX(0) translateY(0);
      opacity: 0.4;
    }
    25%{
      transform: translateX(-25%) translateY(-3px);
      opacity: 0.5;
    }
    50%{
      transform: translateX(-50%) translateY(0);
      opacity: 0.4;
    }
    75%{
      transform: translateX(-25%) translateY(3px);
      opacity: 0.5;
    }
  }

  /* Second wave layer - very subtle dashed pattern */
  .why-wave-layer{
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200%;
    height: 60px;
    background: 
      repeating-linear-gradient(
        90deg,
        transparent,
        transparent 60px,
        rgba(59, 130, 246, 0.06) 60px,
        rgba(59, 130, 246, 0.06) 120px
      ),
      linear-gradient(180deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.02) 50%, transparent 100%);
    clip-path: polygon(
      0% 100%,
      0% 80%,
      15% 75%,
      30% 80%,
      45% 70%,
      60% 75%,
      75% 65%,
      90% 70%,
      100% 60%,
      100% 100%
    );
    animation: wave2 18s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
  }

  @keyframes wave2{
    0%, 100%{
      transform: translateX(-10%) translateY(0);
      opacity: 0.3;
    }
    33%{
      transform: translateX(-35%) translateY(-2px);
      opacity: 0.4;
    }
    66%{
      transform: translateX(-60%) translateY(0);
      opacity: 0.3;
    }
  }

  .why-choose-section .container{
    position: relative;
    z-index: 2;
  }

  .why-main-title{
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.3;
    letter-spacing: -0.5px;
    position: relative;
    z-index: 2;
    margin-bottom: 16px !important;
  }

  .why-stats-text{
    font-size: 14px;
    font-weight: 500;
    color: #475569;
    line-height: 1.5;
    position: relative;
    z-index: 1;
    margin-bottom: 12px !important;
  }

  .why-ratings{
    display: flex;
    gap: 16px;
    position: relative;
    z-index: 1;
  }

  .why-rating-item{
    text-align: center;
    background: rgba(241, 245, 249, 0.8);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .why-rating-item:hover{
    transform: translateY(-2px);
    background: rgba(226, 232, 240, 0.9);
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .why-rating-icon{
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
    width: 32px !important;
    height: 32px !important;
    font-size: 14px !important;
  }

  .why-rating-item:hover .why-rating-icon{
    transform: scale(1.1);
  }

  .why-rating-value{
    font-size: 18px;
    color: #1e293b;
    margin-bottom: 2px;
    font-weight: 700;
  }

  .why-rating-label{
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .why-card-traveloka{
    background: #ffffff;
    border-radius: 12px;
    padding: 18px 16px;
    box-shadow: 
      0 4px 16px rgba(0, 0, 0, 0.1),
      0 1px 4px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
  }

  .why-card-traveloka::before{
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s ease;
  }

  .why-card-traveloka:hover::before{
    left: 100%;
  }

  .why-card-traveloka:hover{
    transform: translateY(-4px);
    box-shadow: 
      0 8px 24px rgba(0, 0, 0, 0.15),
      0 2px 8px rgba(0, 0, 0, 0.1);
    border-color: #0ea5e9;
  }

  .why-card-icon-wrapper{
    display: flex;
    align-items: center;
    margin-bottom: 12px !important;
  }

  .why-card-icon{
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s ease;
    position: relative;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .why-card-icon::after{
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 18px;
    padding: 2px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.5), rgba(118, 75, 162, 0.5));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
  }

  .why-card-traveloka:hover .why-card-icon{
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .why-card-title{
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
    line-height: 1.4;
    transition: color 0.3s ease;
  }

  .why-card-traveloka:hover .why-card-title{
    color: #0ea5e9;
  }

  .why-card-desc{
    font-size: 12px;
    color: #4b5563;
    line-height: 1.6;
    margin: 0;
  }

  .why-card-desc strong{
    color: #0ea5e9;
    font-weight: 600;
  }

  /* Animation on scroll - mặc định hiển thị, chỉ animate khi scroll vào */
  .why-choose-section .row{
    opacity: 1;
    transform: translateY(0);
  }

  .why-card-traveloka{
    opacity: 1;
    transform: translateY(0);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Optional: Animation khi scroll vào view (nếu muốn) */
  @supports (animation: fadeIn) {
    .why-choose-section.animate-on-scroll:not(.visible) .row{
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .why-choose-section.animate-on-scroll.visible .row{
      opacity: 1;
      transform: translateY(0);
    }

    .why-choose-section.animate-on-scroll:not(.visible) .why-card-traveloka{
      opacity: 0;
      transform: translateY(20px);
    }

    .why-choose-section.animate-on-scroll.visible .why-card-traveloka{
      opacity: 1;
      transform: translateY(0);
    }

    .why-choose-section.animate-on-scroll.visible .why-card-traveloka:nth-child(1){ transition-delay: 0.1s; }
    .why-choose-section.animate-on-scroll.visible .why-card-traveloka:nth-child(2){ transition-delay: 0.2s; }
    .why-choose-section.animate-on-scroll.visible .why-card-traveloka:nth-child(3){ transition-delay: 0.3s; }
    .why-choose-section.animate-on-scroll.visible .why-card-traveloka:nth-child(4){ transition-delay: 0.4s; }
  }

  @media (max-width: 768px){
    .why-choose-section{
      padding: 30px 0;
      margin: 20px 0;
    }

    .why-main-title{
      font-size: 20px;
    }

    .why-stats-text{
      font-size: 16px;
    }

    .why-card-traveloka{
      padding: 24px 20px;
    }
  }






</style>











<div class="container my-4" id="promo">



  <div class="promo-wrap">



    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">



      <div class="flex-grow-1">



        <div class="d-flex align-items-center gap-2 fw-bold"><span data-i18n="promo.offers">Ưu đãi</span><span data-i18n="promo.title">Mã giảm giá hấp dẫn</span></div>



        <div class="text-muted small" data-i18n="promo.onlineOnly">Chỉ áp dụng khi đặt phòng online</div>



      </div>
      
      <div>
        <button type="button" class="btn btn-outline-primary btn-sm" id="copy-all-promos-btn" title="Copy tất cả mã giảm giá" data-i18n-title="promo.copyAllTitle">
          <i class="bi bi-clipboard-check me-1"></i><span data-i18n="promo.copyAll">Copy tất cả</span>
        </button>
      </div>



      <!-- Chỉ hiển thị "Mã giảm giá hấp dẫn", các tab khác đã được xóa -->



    </div>







<!-- Horizontal scroll container với navigation arrows -->
<div class="promo-scroll-wrapper position-relative">
  <button class="promo-scroll-btn promo-scroll-prev" type="button" aria-label="Cuộn trái">
    <i class="bi bi-chevron-left"></i>
  </button>
  <div class="promo-scroll-container">
    <div class="promo-scroll-content d-flex gap-3">



      <?php



      // Lấy từ bảng promos (admin quản lý), fallback sang dữ liệu tĩnh nếu trống



        ensurePromosTable($con);

        // Lấy tất cả mã active từ database (giống như admin)
        $promo_rows = [];
        $res = mysqli_query($con, "
          SELECT * FROM `promos`
          WHERE `active`=1
          ORDER BY `priority` DESC, `id` DESC
        ");
        if($res){
          while($row = mysqli_fetch_assoc($res)){
            $promo_rows[] = $row;
          }
        }

        $promo_list = [];
        if(!empty($promo_rows)){
          foreach($promo_rows as $p){
            $promo_list[] = [
              "label" => $p['label'] ?? '',
              "title" => $p['title'] ?? '',
              "desc"  => $p['description'] ?? '',
              "code"  => $p['code'] ?? '',
              "category" => $p['category'] ?? 'hot',
              "expires_at" => $p['expires_at'] ?? null
            ];
          }
        }







        // Fallback data - luôn có sẵn để đảm bảo có đủ mã giảm giá
        $fallback_promos = [



            [



              "label"=>"Hết hạn sau 2 ngày",



              "title"=>"Giảm đến 500.000đ quốc tế",



              "desc"=>"Giảm 2% tối đa 500.000đ | Đặt tối thiểu 3.000.000đ",



              "code"=>"KS1212QT","category"=>"hot","expires_at"=>null



            ],



            [



              "label"=>"Sắp hết mã",



              "title"=>"Giảm 50% phòng nội địa",



              "desc"=>"Giảm 4% tối đa 300.000đ | Đặt tối thiểu 2.000.000đ",



              "code"=>"KS1212VN","category"=>"hot","expires_at"=>null



            ],



            [



              "label"=>"Có hạn",



              "title"=>"Giảm 100.000đ cuối tuần",



              "desc"=>"Giảm 3% tối đa 100.000đ | Không yêu cầu tối thiểu",



              "code"=>"WKND100","category"=>"hot","expires_at"=>null



            ],

            [

              "label"=>"Hot",

              "title"=>"Giảm 15% cho đơn từ 5 triệu",

              "desc"=>"Giảm 15% tối đa 800.000đ | Đặt tối thiểu 5.000.000đ",

              "code"=>"VINHLONG15","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Mới",

              "title"=>"Giảm 200.000đ cho khách mới",

              "desc"=>"Giảm 5% tối đa 200.000đ | Đặt tối thiểu 1.000.000đ",

              "code"=>"NEW200","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Ưu đãi",

              "title"=>"Giảm 10% mọi đơn hàng",

              "desc"=>"Giảm 10% tối đa 500.000đ | Không yêu cầu tối thiểu",

              "code"=>"SAVE10","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Đặc biệt",

              "title"=>"Giảm 25% cho đơn lớn",

              "desc"=>"Giảm 25% tối đa 1.000.000đ | Đặt tối thiểu 8.000.000đ",

              "code"=>"BIG25","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Khuyến mãi",

              "title"=>"Giảm 150.000đ cho mọi đơn",

              "desc"=>"Giảm 6% tối đa 150.000đ | Đặt tối thiểu 500.000đ",

              "code"=>"PROMO150","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Siêu hot",

              "title"=>"Giảm 20% cuối tuần",

              "desc"=>"Giảm 20% tối đa 600.000đ | Đặt tối thiểu 3.000.000đ",

              "code"=>"WEEKEND20","category"=>"hot","expires_at"=>null

            ],

            [

              "label"=>"Tặng ngay",

              "title"=>"Giảm 50.000đ cho đơn nhỏ",

              "desc"=>"Giảm 2% tối đa 50.000đ | Đặt tối thiểu 300.000đ",

              "code"=>"SMALL50","category"=>"hot","expires_at"=>null

            ]



          ];
        
        // Hiển thị TẤT CẢ mã active từ database (không lọc category)
        // Chỉ bổ sung từ fallback nếu database trống hoàn toàn
        if(empty($promo_list)){
          $promo_list = $fallback_promos;
        }











        // Lấy ngôn ngữ hiện tại - Mặc định là tiếng Việt
        // Chỉ chuyển sang tiếng Anh khi người dùng chủ động chọn
        $lang_cookie = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : '';
        $current_lang = ($lang_cookie === 'en') ? 'en' : 'vi';
        // Đảm bảo chỉ nhận 'vi' hoặc 'en'
        if ($current_lang !== 'en' && $current_lang !== 'vi') {
          $current_lang = 'vi';
        }
        
        // Hàm dịch tiêu đề và mô tả mã giảm giá
        function t_promo_text($text, $lang = 'vi') {
          if($lang === 'vi') {
            return $text; // Giữ nguyên tiếng Việt
          }
          
          // Mapping các tiêu đề và mô tả phổ biến
          $text_map = [
            // Titles
            'Giảm 5% qua ví/QR' => '5% off via wallet/QR',
            'Giảm 8% phòng Vĩnh Long' => '8% off Vinh Long rooms',
            'Giảm 10% qua ngân hàng' => '10% off via bank',
            'Giảm 3% cuối tuần' => '3% off weekend',
            'Giảm 4% mã sắp hết' => '4% off limited codes',
            'Giảm 2% mã hết hạn sớm' => '2% off expiring soon',
            'Giảm đến 500.000đ quốc tế' => 'Up to 500,000₫ off international',
            'Giảm 50% phòng nội địa' => '50% off domestic rooms',
            'Giảm 100.000đ cuối tuần' => '100,000₫ off weekend',
            
            // Descriptions
            'Giảm 5% tối đa 150.000đ · Cho mọi đơn' => '5% off, max 150,000 VND · For all orders',
            'Giảm 8% tối đa 250.000đ · Chỉ áp dụng Vĩnh Long' => '8% off, max 250,000 VND · Vinh Long only',
            'Giảm 10% tối đa 400.000đ · Đơn từ 500.000đ' => '10% off, max 400,000 VND · Orders from 500,000 VND',
            'Giảm 3% tối đa 100.000đ · Cho mọi đơn' => '3% off, max 100,000 VND · For all orders',
            'Giảm 4% tối đa 300.000đ · Đơn từ 2.000.000đ' => '4% off, max 300,000 VND · Orders from 2,000,000 VND',
            'Giảm 2% tối đa 500.000đ · Đơn từ 3.000.000đ' => '2% off, max 500,000 VND · Orders from 3,000,000 VND',
            'Giảm 2% tối đa 500.000đ | Đặt tối thiểu 3.000.000đ' => '2% off, max 500,000₫ | Minimum order 3,000,000₫',
            'Giảm 4% tối đa 300.000đ | Đặt tối thiểu 2.000.000đ' => '4% off, max 300,000₫ | Minimum order 2,000,000₫',
            'Giảm 3% tối đa 100.000đ | Không yêu cầu tối thiểu' => '3% off, max 100,000₫ | No minimum requirement',
          ];
          
          // Kiểm tra mapping chính xác
          if(isset($text_map[$text])) {
            return $text_map[$text];
          }
          
          // Nếu không tìm thấy, thử dịch các từ phổ biến
          $translated = $text;
          $common_words = [
            'Giảm' => 'Off',
            'tối đa' => 'max',
            'Cho mọi đơn' => 'For all orders',
            'Chỉ áp dụng' => 'Only applies to',
            'Đơn từ' => 'Orders from',
            'qua ví/QR' => 'via wallet/QR',
            'qua ngân hàng' => 'via bank',
            'phòng' => 'rooms',
            'cuối tuần' => 'weekend',
            'mã sắp hết' => 'limited codes',
            'mã hết hạn sớm' => 'expiring soon',
            'quốc tế' => 'international',
            'nội địa' => 'domestic',
            'Đặt tối thiểu' => 'Minimum order',
            'Không yêu cầu tối thiểu' => 'No minimum requirement',
            'đến' => 'up to',
          ];
          
          foreach($common_words as $vi => $en) {
            $translated = str_replace($vi, $en, $translated);
          }
          
          return $translated;
        }

        foreach($promo_list as $p){



          // Xử lý label với i18n
          $label_raw = trim($p['label'] ?? '');
          $label_map = [
            'Hết hạn sau 2 ngày' => 'promo.expiresIn2Days',
            'Sắp hết mã' => 'promo.runningOut',
            'Có hạn' => 'promo.limited',
            'Ưu đãi ngân hàng' => 'promo.bankOffer',
            'Thanh toán QR' => 'promo.qrPayment',
            'Điểm đến hot' => 'promo.hotDestination',
          ];
          $label_i18n_key = isset($label_map[$label_raw]) ? $label_map[$label_raw] : '';
          $label_escaped = htmlspecialchars($label_raw, ENT_QUOTES, 'UTF-8');
          if($label_i18n_key) {
            $label_html = '<span data-i18n="'.htmlspecialchars($label_i18n_key, ENT_QUOTES, 'UTF-8').'">'.$label_escaped.'</span>';
          } else {
            $label_html = $label_escaped;
          }



          $title = htmlspecialchars(t_promo_text($p['title'], $current_lang),ENT_QUOTES,'UTF-8');



          $desc = htmlspecialchars(t_promo_text($p['desc'], $current_lang),ENT_QUOTES,'UTF-8');



          $code = htmlspecialchars($p['code'],ENT_QUOTES,'UTF-8');



          $cat  = htmlspecialchars($p['category'] ?? 'hot',ENT_QUOTES,'UTF-8');



          $exp_raw = (!empty($p['expires_at']) && $p['expires_at']!='0000-00-00') ? $p['expires_at'] : '';



          // Xử lý expiration với i18n
          if($exp_raw) {
            $exp_text = date('d/m/Y', strtotime($exp_raw));
            $exp_text_escaped = htmlspecialchars($exp_text, ENT_QUOTES, 'UTF-8');
            $exp_raw_escaped = htmlspecialchars($exp_raw, ENT_QUOTES, 'UTF-8');
            $exp_html = "<div class='text-muted small mb-1 promo-exp' data-exp='{$exp_raw_escaped}' data-exp-text='{$exp_text_escaped}'><span data-i18n='promo.expires'>Hết hạn</span>: {$exp_text_escaped}</div>";
          } else {
            $exp_html = "<div class='text-muted small mb-1 promo-exp' data-exp='' data-exp-text=''><span data-i18n='promo.expires'>Hết hạn</span>: <span data-i18n='promo.unlimited'>Không giới hạn</span></div>";
          }



          echo <<<PROMO



          <div class="promo-item flex-shrink-0" data-category="$cat" style="width: 380px; min-width: 380px;">
            <div class="promo-card h-100">
              <div class="promo-card-left">
                <div class="promo-badge"><i class="bi bi-fire me-1"></i>$label_html</div>
                <div class="promo-title">$title</div>
                <div class="promo-desc">$desc</div>
                $exp_html
              </div>
              <div class="promo-card-right">
                <div class="promo-code-wrapper">
                  <div class="promo-code-label" data-i18n="promo.codeLabel">Mã giảm giá</div>
                  <div class="promo-code">$code</div>
                </div>
                <div class="promo-actions">
                  <button class="promo-copy" type="button" data-code="$code">
                    <i class="bi bi-clipboard me-1"></i><span data-i18n="promo.copy">Sao chép</span>
                  </button>
                  <button class="promo-save" type="button" data-code="$code" data-i18n-title="promo.saveTitle">
                    <i class="bi bi-bookmark-plus me-1"></i><span data-i18n="promo.save">Lưu</span>
                  </button>
                </div>
              </div>
            </div>
          </div>



PROMO;



        }



      ?>



    </div>
  </div>
  <button class="promo-scroll-btn promo-scroll-next" type="button" aria-label="Cuộn phải">
    <i class="bi bi-chevron-right"></i>
  </button>
</div>

  </div>



  </section>



</div>













  <!-- ===================== DANH SÁCH PHÒNG ===================== -->



  <section class="container mt-5 mb-4" id="rooms-section">



    <div class="text-center mb-4">



      <p class="text-uppercase text-muted small mb-1" data-i18n="common.explore">Khám phá</p>



      <h2 class="fw-bold mb-0 home-section-title" data-i18n="section.rooms">Danh sách phòng</h2>



      <p class="text-muted mt-2 mb-0" data-i18n="section.roomsDesc">Chọn phòng phù hợp với nhu cầu của bạn</p>



    </div>







  <div class="container">



    <div class="row g-4">







      <?php 



        $fav_ids = [];



        $is_logged = (isset($_SESSION['login']) && $_SESSION['login']==true);



        if($is_logged){



          $fav_q = select("SELECT room_id FROM favorites WHERE user_id=?", [$_SESSION['uId']], 'i');



          if($fav_q){



            while($fav_row = mysqli_fetch_assoc($fav_q)){



              $fav_ids[] = (int)$fav_row['room_id'];



            }



          }



        }



        // Kiểm tra xem cột approved có tồn tại không
        $has_approved = false;
        $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
        if($check_approved && mysqli_num_rows($check_approved) > 0){
          $has_approved = true;
        }
        
        // Chỉ hiển thị phòng đã duyệt (approved = 1)
        $approved_condition = $has_approved ? " AND r.approved = 1" : "";
        $params = [1, 0];
        $types = 'ii';

        $room_res = select("



          SELECT r.*, COALESCE(AVG(rr.rating),0) AS avg_rating, COUNT(rr.sr_no) AS review_count



          FROM `rooms` r



          LEFT JOIN rating_review rr ON r.id = rr.room_id
          LEFT JOIN hotel_owners ho ON r.owner_id = ho.id



          WHERE r.status=? AND r.removed=?{$approved_condition}
          AND (r.owner_id IS NULL OR ho.status = 1)



          GROUP BY r.id



          ORDER BY avg_rating DESC, review_count DESC, r.id DESC



          LIMIT 6



        ", $params, $types);







        while($room_data = mysqli_fetch_assoc($room_res))



        {



          /* Thumbnail */



          $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";



          $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");



          if(mysqli_num_rows($thumb_q)>0){



            $thumb_res = mysqli_fetch_assoc($thumb_q);



            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];



          }







          /* Features */



          $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f INNER JOIN `room_features` rfea ON f.id = rfea.features_id WHERE rfea.room_id = '$room_data[id]'");



          // Map các feature names phổ biến để dịch
          $feature_map = [
            'Phòng Ngủ' => 'features.bedroom',
            'Ban Công' => 'features.balcony',
            'Nhà Bếp' => 'features.kitchen',
            'Ghế Sofa' => 'features.sofa',
            'Phòng Tắm' => 'features.bathroom',
            'Phòng Khách' => 'features.livingRoom',
            'Tủ Lạnh' => 'features.refrigerator',
            'Máy Lạnh' => 'features.airConditioner',
            'TV' => 'features.tv',
            'WiFi' => 'features.wifi',
          ];
          
          $features_data = "";



          while($fea_row = mysqli_fetch_assoc($fea_q)){



            $fea_name_raw = $fea_row['name'] ?? '';
            $fea_name = htmlspecialchars($fea_name_raw, ENT_QUOTES, 'UTF-8');
            $fea_i18n_key = isset($feature_map[$fea_name_raw]) ? $feature_map[$fea_name_raw] : '';
            
            if($fea_i18n_key) {
              $features_data .="<span class='home-tag'><span data-i18n='{$fea_i18n_key}'>{$fea_name}</span></span>";
            } else {
              $features_data .="<span class='home-tag'>{$fea_name}</span>";
            }



          }



          if($features_data === ""){



            $features_data = "<span class='home-tag text-muted' data-i18n='room.updating'>Đang cập nhật</span>";



          }







          /* Facilities */



          $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id WHERE rfac.room_id = '$room_data[id]'");



          // Map các facility names phổ biến để dịch
          $facility_map = [
            'Máy Sưởi' => 'facilities.heater',
            'Dịch vụ 24/24 giờ' => 'facilities.service24',
            'Xe đưa đón sân bay' => 'facilities.airportShuttle',
            'Hồ bơi' => 'facilities.swimmingPool',
            'Sân golf' => 'facilities.golfCourse',
            'Quầy bar' => 'facilities.bar',
            'WiFi miễn phí' => 'facilities.freeWifi',
            'Bãi đỗ xe' => 'facilities.parking',
            'Nhà hàng' => 'facilities.restaurant',
            'Phòng gym' => 'facilities.gym',
            'Spa' => 'facilities.spa',
            'Dịch vụ giặt ủi' => 'facilities.laundry',
          ];
          
          $facilities_data = "";



          while($fac_row = mysqli_fetch_assoc($fac_q)){



            $fac_name_raw = $fac_row['name'] ?? '';
            $fac_name = htmlspecialchars($fac_name_raw, ENT_QUOTES, 'UTF-8');
            $fac_i18n_key = isset($facility_map[$fac_name_raw]) ? $facility_map[$fac_name_raw] : '';
            
            if($fac_i18n_key) {
              $facilities_data .="<span class='home-tag'><span data-i18n='{$fac_i18n_key}'>{$fac_name}</span></span>";
            } else {
              $facilities_data .="<span class='home-tag'>{$fac_name}</span>";
            }



          }



          if($facilities_data === ""){



            $facilities_data = "<span class='home-tag text-muted' data-i18n='room.updating'>Đang cập nhật</span>";



          }







          /* Giá */



          $price = isset($room_data['price']) ? (int)$room_data['price'] : 0;



          $discount = isset($room_data['discount']) ? (int)$room_data['discount'] : 0;



          $final_price = $discount>0 ? floor($price*(100-$discount)/100) : $price;



          $price_txt = number_format($final_price,0,'.','.');



          $old_price_txt = number_format($price,0,'.','.');



          $price_box = $discount>0 



            ? "<div class='d-flex align-items-center gap-2 flex-wrap'><span class='text-danger fw-bold fs-5'>{$price_txt} VND</span><span class='text-decoration-line-through text-muted small'>{$old_price_txt} VND</span><span class='badge bg-danger'>-{$discount}%</span></div>"



            : "<span class='fw-bold fs-5 text-primary'>{$price_txt} VND</span>";







          $location = !empty($room_data['location']) ? htmlspecialchars($room_data['location'], ENT_QUOTES, 'UTF-8') : 'Vĩnh Long';



          $area_text = !empty($room_data['area']) ? $room_data['area'].' m²' : '<span data-i18n="room.updating">Đang cập nhật</span>';







          $remaining = null;



          if(array_key_exists('remaining', $room_data)){



            $remaining = (int)$room_data['remaining'];



          } elseif(array_key_exists('quantity', $room_data)){



            $remaining = (int)$room_data['quantity'];



          }



          $availability_text = $remaining === null ? '' : ($remaining > 0 ? "<span data-i18n='room.remaining'>Còn</span> {$remaining} <span data-i18n='room.rooms'>phòng</span>" : "<span data-i18n='room.soldOut'>Hết phòng</span>");



          $availability_html = $availability_text ? "<span class='badge-soft ms-2 ".(($remaining !== null && $remaining <=0)?'bg-danger text-white':'')."'><i class='bi bi-door-open me-1'></i>{$availability_text}</span>" : '';



          $can_book = ($remaining === null) || ($remaining > 0);







          $loginFlag = (isset($_SESSION['login']) && $_SESSION['login']==true) ? 1 : 0;



          $avg = round($room_data['avg_rating'],1);



          $room_name = htmlspecialchars($room_data['name'], ENT_QUOTES, 'UTF-8');



          $capacity_text = "{$room_data['adult']} <span data-i18n='search.adults'>người lớn</span> • {$room_data['children']} <span data-i18n='search.children'>trẻ em</span>";



          $review_count = isset($room_data['review_count']) ? (int)$room_data['review_count'] : 0;



          // Badge phổ biến/nổi bật

          $popular_badge = '';

          if($review_count >= 5 && $avg >= 4.5){

            $popular_badge = '<span class="room-badge-popular"><i class="bi bi-fire me-1"></i><span data-i18n="room.popular">Phổ biến</span></span>';

          } else if($discount > 0){

            $popular_badge = '<span class="room-badge-discount"><i class="bi bi-tag me-1"></i><span data-i18n="room.discountPercent">Giảm</span> '.$discount.'%</span>';

          }



          $is_fav = in_array((int)$room_data['id'], $fav_ids);



          $fav_class = $is_fav ? 'active' : '';



          $fav_icon = $is_fav ? 'bi-heart-fill' : 'bi-heart';



          $book_btn = $can_book



            ? "<button onclick=\"checkLoginToBook({$loginFlag},{$room_data['id']})\" class=\"btn btn-primary\" data-i18n=\"room.bookNow\">Đặt ngay</button>"



            : "<button class=\"btn btn-secondary\" disabled data-i18n=\"room.soldOut\">Hết phòng</button>";



          // Xử lý review count display

          $review_count_html = $review_count > 0 ? "<span class='text-muted small'>($review_count)</span>" : "";







          echo <<<CARD



          <div class="col-lg-4 col-md-6">



            <div class="home-room-card shadow-sm border-0 h-100">



              <button class="home-fav-btn $fav_class" data-room="$room_data[id]" title="Yêu thích"><i class="bi $fav_icon"></i></button>



              $popular_badge



              <div class="home-room-thumb position-relative">



                <img src="$room_thumb" alt="$room_name" loading="lazy">



                <div class="room-thumb-overlay">



                  <a href="room_details.php?id=$room_data[id]" class="btn btn-sm btn-light rounded-pill shadow-none">Xem ảnh</a>



                </div>



              </div>



              <div class="p-3 d-flex flex-column h-100" style="flex:1 1 auto;">



                <div class="mb-2">



                  <div class="d-flex align-items-start justify-content-between gap-2 mb-1">



                    <h5 class="mb-0 flex-grow-1">$room_name</h5>



                    <div class="text-end">



                      <div class="home-rating d-flex align-items-center gap-1">



                        <i class="bi bi-star-fill text-warning"></i>



                        <span class="fw-bold">$avg</span>



                        $review_count_html



                      </div>



                    </div>



                  </div>



                  <div class="home-room-meta d-flex align-items-center gap-2 flex-wrap">



                    <span><i class="bi bi-geo-alt me-1"></i>$location</span>



                    <span class="text-muted">•</span>



                    <span><i class="bi bi-rulers me-1"></i>$area_text</span>



                  </div>



                </div>



                <div class="home-price-row mb-3">



                  $price_box



                </div>



                <div class="home-room-meta mb-3">



                  <div class="d-flex align-items-center gap-2 flex-wrap">



                    <span><i class="bi bi-people me-1"></i>$capacity_text</span>



                    $availability_html



                  </div>



                </div>



                <div class="flex-grow-1" style="margin-bottom: 0.125rem;">



                  <div class="fw-semibold small text-muted" style="margin-bottom: 0.125rem;" data-i18n="room.features">Đặc điểm nổi bật</div>



                  <div class="d-flex flex-wrap gap-1">$features_data</div>



                </div>



                <div class="home-room-actions mt-auto border-top">



                  <a href="room_details.php?id=$room_data[id]" class="btn btn-outline-dark flex-grow-1" data-i18n="room.details">Chi tiết</a>



                  $book_btn



                </div>



              </div>



            </div>



          </div>



CARD;



        }







      ?>







      <div class="col-lg-12 text-center mt-5 mb-4">

        <a href="rooms.php" class="btn-view-all-rooms">

          <i class="bi bi-grid-3x3-gap me-2"></i><span data-i18n="room.viewAll">Xem tất cả phòng</span>

        </a>

      </div>



    </div>



  </div>











  <!-- ===================== PHÒNG NGHỈ GẦN ĐÂY ===================== -->
  <section class="container mt-5 mb-4" id="recently-viewed-section" style="display: none;">
    <div class="text-center mb-4">
      <p class="text-uppercase text-muted small mb-1" data-i18n="common.recentlyViewed">Gần đây</p>
      <h2 class="fw-bold mb-0 home-section-title" data-i18n="section.recentlyViewedRooms">Phòng nghỉ gần đây</h2>
      <p class="text-muted mt-2 mb-0" data-i18n="section.recentlyViewedDesc">Các phòng bạn đã xem gần đây</p>
    </div>

    <div class="container">
      <div class="row g-4" id="recently-viewed-rooms-container">
        <!-- Phòng sẽ được load bằng JavaScript -->
      </div>
    </div>
  </section>

  <!-- Tiện ích nổi bật -->



  <section class="container mt-5" id="facilities-section">



    <div class="text-center mb-4">



      <p class="text-uppercase text-muted small mb-1" data-i18n="nav.facilities">Tiện ích</p>



      <h2 class="fw-bold mb-0 home-section-title" data-i18n="section.facilities">Tiện ích nổi bật</h2>



    </div>



    <div class="row g-4 justify-content-center">



      <?php



        $facilities_q = mysqli_query($con,"SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 6");



        if(!$facilities_q || mysqli_num_rows($facilities_q)==0){



          echo "<div class='col-12'><div class='alert alert-secondary text-center' data-i18n='room.updatingFacilities'>Đang cập nhật tiện ích</div></div>";



        } else {



          $fac_path = FACILITIES_IMG_PATH;



          while($facility = mysqli_fetch_assoc($facilities_q)){



            $name_raw = $facility['name'] ?? '';
            // Map các facility names phổ biến để dịch
            $facility_map = [
              'Máy Sưởi' => 'facilities.heater',
              'Dịch vụ 24/24 giờ' => 'facilities.service24',
              'Xe đưa đón sân bay' => 'facilities.airportShuttle',
              'Hồ bơi' => 'facilities.swimmingPool',
              'Sân golf' => 'facilities.golfCourse',
              'Quầy bar' => 'facilities.bar',
            ];
            $name_i18n_key = $facility_map[$name_raw] ?? '';
            $name = htmlspecialchars($name_raw, ENT_QUOTES, 'UTF-8');
            $name_html = $name_i18n_key ? '<span data-i18n="'.$name_i18n_key.'">'.$name.'</span>' : $name;

            // Chỉ hiển thị icon nếu có, không dùng user.png (sai folder)
            $icon = '';
            if($facility['icon'] && !empty($facility['icon'])) {
                $icon = $fac_path.$facility['icon'];
            }

            $desc_raw = $facility['description'] ?? '';
            // Map descriptions
            $desc_map = [
              'Hệ thống sưởi ấm giúp khách hàng tận hưởng không gian ấm áp trong mùa lạnh.' => 'facilities.heaterDesc',
              'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ mọi nhu cầu của khách hàng.' => 'facilities.service24Desc',
              'Dịch vụ đưa đón sân bay nhanh chóng, tiện lợi và an toàn.' => 'facilities.airportShuttleDesc',
              'Hồ bơi ngoài trời, nước trong xanh, thích hợp để thư giãn hoặc bơi lội.' => 'facilities.swimmingPoolDesc',
              'Sân golf gần khách sạn, khu vực giải trí cao cấp.' => 'facilities.golfCourseDesc',
              'Quầy bar sang trọng đa dạng đồ uống, phục vụ khách hàng thư giãn buổi tối.' => 'facilities.barDesc',
            ];
            $desc_i18n_key = $desc_map[$desc_raw] ?? '';
            $desc = !empty($desc_raw) ? htmlspecialchars($desc_raw, ENT_QUOTES, 'UTF-8') : '';
            $desc_content = $desc_i18n_key ? '<span data-i18n="'.$desc_i18n_key.'">'.$desc.'</span>' : $desc;



            $desc_html = $desc ? "<p class='text-muted small text-center mb-0'>$desc_content</p>" : '';



            // Chỉ hiển thị icon nếu có
            $icon_html = '';
            if(!empty($icon)) {
                $icon_html = "<img src=\"$icon\" alt=\"$name\" loading=\"lazy\" onerror=\"this.style.display='none';\">";
            } else {
                // Hiển thị icon mặc định hoặc ẩn
                $icon_html = "<div style=\"width: 64px; height: 64px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;\"><i class=\"bi bi-star\" style=\"font-size: 24px; color: #999;\"></i></div>";
            }
            
            echo <<<FACILITY



            <div class="col-sm-6 col-md-4 col-lg-4">



              <div class="facility-card">



                $icon_html



                <div class="fw-semibold">$name_html</div>



                $desc_html



              </div>



            </div>



FACILITY;



          }



        }



      ?>



      <div class="col-12 text-center mt-5 mb-4">

        <a href="facilities.php" class="btn-view-all-rooms">

          <i class="bi bi-grid-3x3-gap me-2"></i><span data-i18n="facilities.viewMore">Xem thêm tiện ích</span>

        </a>

      </div>



    </div>



  </section>







  <!-- Đánh giá khách hàng -->



  <section class="container mt-5" id="reviews-section">



    <div class="text-center mb-4">



      <p class="text-uppercase text-muted small mb-1" data-i18n="reviews.feelings">Cảm nhận</p>



      <h2 class="fw-bold mb-0 home-section-title" data-i18n="reviews.fromGuests">Đánh giá từ khách</h2>



    </div>



    <?php

      // Tính toán thống kê đánh giá

      $stats_q = "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating FROM `rating_review`";

      $stats_res = mysqli_query($con, $stats_q);

      $stats = mysqli_fetch_assoc($stats_res);

      $total_reviews = isset($stats['total']) ? (int)$stats['total'] : 0;

      $avg_rating = isset($stats['avg_rating']) ? round((float)$stats['avg_rating'], 1) : 0.0;

      $img_path = USERS_IMG_PATH;

    ?>



    <!-- Thống kê đánh giá -->

    <?php if($total_reviews > 0): ?>

    <div class="row g-4 mb-5">

      <div class="col-md-4">

        <div class="review-stat-card text-center p-3">

          <div class="review-stat-number"><?php echo number_format($total_reviews); ?></div>

          <div class="review-stat-label" data-i18n="reviews.total">Tổng đánh giá</div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="review-stat-card text-center p-3">

          <div class="review-stat-number text-warning"><?php echo number_format($avg_rating, 1); ?><i class="bi bi-star-fill ms-1"></i></div>

          <div class="review-stat-label" data-i18n="reviews.average">Điểm trung bình</div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="review-stat-card text-center p-3">

          <div class="review-stat-number text-success"><?php echo $total_reviews >= 10 ? '100%' : ($total_reviews * 10).'%'; ?></div>

          <div class="review-stat-label" data-i18n="reviews.satisfied">Khách hàng hài lòng</div>

        </div>

      </div>

    </div>

    <?php endif; ?>



    <div class="position-relative">



      <div class="swiper swiper-testimonials">



        <div class="swiper-wrapper">



          <?php



            // Kiểm tra bảng review_helpful có tồn tại không
            $check_helpful_table = @mysqli_query($con, "SHOW TABLES LIKE 'review_helpful'");
            $has_helpful_table = $check_helpful_table && mysqli_num_rows($check_helpful_table) > 0;
            
            // Kiểm tra cột helpful_count có tồn tại không
            $check_helpful_col = @mysqli_query($con, "SHOW COLUMNS FROM `rating_review` LIKE 'helpful_count'");
            $has_helpful_col = $check_helpful_col && mysqli_num_rows($check_helpful_col) > 0;
            
            // Xây dựng query với helpful_count
            if($has_helpful_table && $has_helpful_col){
              $helpful_count_select = "COALESCE((SELECT COUNT(*) FROM review_helpful WHERE review_id = rr.sr_no), rr.helpful_count, 0) AS helpful_count";
            } else if($has_helpful_table){
              $helpful_count_select = "COALESCE((SELECT COUNT(*) FROM review_helpful WHERE review_id = rr.sr_no), 0) AS helpful_count";
            } else if($has_helpful_col){
              $helpful_count_select = "COALESCE(rr.helpful_count, 0) AS helpful_count";
            } else {
              $helpful_count_select = "0 AS helpful_count";
            }
            
            $reviews_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname, r.id AS room_id, r.price AS room_price, COALESCE(r.discount, 0) AS room_discount, $helpful_count_select FROM `rating_review` rr\n            LEFT JOIN `user_cred` uc ON rr.user_id = uc.id\n            LEFT JOIN `rooms` r ON rr.room_id = r.id\n            WHERE r.removed = 0 OR r.removed IS NULL\n            ORDER BY rr.sr_no DESC LIMIT 8";



            $reviews_res = mysqli_query($con,$reviews_q);



            if(!$reviews_res || mysqli_num_rows($reviews_res)==0){



              echo '<div class="swiper-slide">\n                    <div class="testimonial-card text-center">\n                      <p class="text-muted mb-0" data-i18n="reviews.updating">Đang cập nhật đánh giá từ khách hàng.</p>\n                    </div>\n                  </div>';



            } else {



            while($review = mysqli_fetch_assoc($reviews_res)){
              // Khởi tạo tất cả biến trước khi sử dụng
              $uname = isset($review['uname']) && !empty($review['uname']) ? htmlspecialchars($review['uname'], ENT_QUOTES, 'UTF-8') : '<span data-i18n="reviews.guest">Khách hàng</span>';
              $room_name = isset($review['rname']) && !empty($review['rname']) ? htmlspecialchars($review['rname'], ENT_QUOTES, 'UTF-8') : 'Vĩnh Long Hotel';
              $room_id = isset($review['room_id']) ? (int)$review['room_id'] : 0;

              // Lấy thumbnail phòng
              $room_thumb = ROOMS_IMG_PATH.'thumbnail.jpg';
              if($room_id > 0){
                $thumb_q = mysqli_query($con, "SELECT image FROM room_images WHERE room_id='$room_id' AND thumb='1' LIMIT 1");
                if($thumb_q && mysqli_num_rows($thumb_q)){
                  $thumb_row = mysqli_fetch_assoc($thumb_q);
                  $room_thumb = ROOMS_IMG_PATH.$thumb_row['image'];
                }
              }

              // Tính giá phòng
              $room_price = isset($review['room_price']) ? (int)$review['room_price'] : 0;
              $room_discount = isset($review['room_discount']) ? (int)$review['room_discount'] : 0;
              $final_price = $room_discount > 0 ? floor($room_price * (100 - $room_discount) / 100) : $room_price;
              $price_display = $room_price > 0 ? number_format($final_price, 0, '.', '.').' VND' : '<span data-i18n="nav.contact">Liên hệ</span>';

              // Link đến phòng
              $room_link = $room_id > 0 ? 'room_details.php?id='.$room_id : 'rooms.php';

              // Xử lý review text an toàn
              $raw_review = isset($review['review']) ? (string)$review['review'] : '';
              if(empty($raw_review)){
                $review_text = '<span data-i18n="reviews.noReview">Chưa có đánh giá chi tiết.</span>';
              } else {
                $review_text = htmlspecialchars($raw_review, ENT_QUOTES, 'UTF-8');
                if(function_exists('mb_strlen')){
                  if(mb_strlen($review_text, 'UTF-8') > 180){
                    $review_text = mb_substr($review_text, 0, 180, 'UTF-8').'…';
                  }
                } else {
                  if(strlen($review_text) > 180){
                    $review_text = substr($review_text, 0, 180).'…';
                  }
                }
              }

              // Chỉ hiển thị ảnh nếu có profile, không dùng user.png
              $profile = '';
              $has_profile_img = false;
              if(isset($review['profile']) && !empty($review['profile']) && $review['profile'] != 'user.png') {
                  $profile = $img_path.$review['profile'];
                  $has_profile_img = true;
              }
              
              // Tạo avatar từ chữ cái đầu nếu không có ảnh
              $user_initial = 'U';
              if(isset($review['uname']) && !empty($review['uname'])) {
                  $user_initial = mb_strtoupper(mb_substr(trim($review['uname']), 0, 1, 'UTF-8'), 'UTF-8');
              }
              $avatar_html = '';
              if($has_profile_img) {
                  $avatar_html = '<img src="' . htmlspecialchars($profile, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($review['uname'] ?? 'User', ENT_QUOTES, 'UTF-8') . '" class="rounded-circle review-avatar" width="56" height="56" loading="lazy" onerror="this.onerror=null; this.style.display=\'none\'; const parent=this.parentElement; if(parent && !parent.querySelector(\'.review-avatar-initial\')) { const fallback=document.createElement(\'div\'); fallback.className=\'review-avatar-initial\'; fallback.style.cssText=\'width: 56px; height: 56px; border-radius: 50%; background-color: #4A90E2; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 20px; border: 3px solid #e5e7eb;\'; fallback.textContent=\'' . htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8') . '\'; parent.appendChild(fallback); }">';
              } else {
                  $avatar_html = '<div class="review-avatar-initial" style="width: 56px; height: 56px; border-radius: 50%; background-color: #4A90E2; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 20px; border: 3px solid #e5e7eb;">' . htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8') . '</div>';
              }

              $rating = isset($review['rating']) ? (int)$review['rating'] : 5;
              if($rating < 0) $rating = 0;
              if($rating > 5) $rating = 5;
              $rating_label = number_format($rating, 1);
              $stars = str_repeat("<i class='bi bi-star-fill text-warning'></i>", $rating);

              $date_txt = isset($review['datentime']) && !empty($review['datentime']) ? date('d/m/Y', strtotime($review['datentime'])) : '<span data-i18n="rooms.new">Mới</span>';

              // Tính thời gian đã qua

              $time_ago = '';

              if(isset($review['datentime']) && !empty($review['datentime'])){

                $review_date = new DateTime($review['datentime']);

                $now = new DateTime();

                $diff = $now->diff($review_date);

                if($diff->days == 0) {
                  $time_ago = '<span data-i18n="reviews.today">Hôm nay</span>';
                } else if($diff->days == 1) {
                  $time_ago = '<span data-i18n="reviews.yesterday">Hôm qua</span>';
                } else if($diff->days < 7) {
                  $days = $diff->days;
                  $time_ago = "{$days} <span data-i18n='reviews.daysAgo'>ngày trước</span>";
                } else if($diff->days < 30) {
                  $weeks = floor($diff->days/7);
                  $time_ago = "{$weeks} <span data-i18n='reviews.weeksAgo'>tuần trước</span>";
                } else if($diff->days < 365) {
                  $months = floor($diff->days/30);
                  $time_ago = "{$months} <span data-i18n='reviews.monthsAgo'>tháng trước</span>";
                } else {
                  $years = floor($diff->days/365);
                  $time_ago = "{$years} <span data-i18n='reviews.yearsAgo'>năm trước</span>";
                }

              }

              // Verified badge (giả sử khách đã đặt phòng thành công là verified)

              $verified_badge = '<span class="verified-badge" data-i18n-title="reviews.verified" title="Khách hàng đã xác thực"><i class="bi bi-patch-check-fill text-primary"></i></span>';

              // Xử lý time_ago HTML

              $time_ago_html = '';

              if(!empty($time_ago)){

                $time_ago_html = '<span class="text-muted">•</span><span>'.$time_ago.'</span>';

              }

              // Đảm bảo tất cả biến đã được khởi tạo

              if(!isset($review_text)) $review_text = 'Chưa có đánh giá chi tiết.';
              
              // Lấy lượt like (helpful_count)
              $helpful_count = isset($review['helpful_count']) ? (int)$review['helpful_count'] : 0;
              $like_display = $helpful_count > 0 ? number_format($helpful_count) : '0';
              $like_icon = $helpful_count > 0 ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';

              echo <<<REVIEW

              <div class="swiper-slide">
                <div class="testimonial-card review-card">
                  <!-- Header với avatar và thông tin khách -->
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                      <div class="position-relative">
                        $avatar_html
                        $verified_badge
                      </div>
                      <div>
                        <div class="d-flex align-items-center gap-2">
                          <div class="fw-semibold">$uname</div>
                        </div>
                        <div class="text-muted small d-flex align-items-center gap-1">
                          <i class="bi bi-calendar-event"></i>
                          <span>$date_txt</span>
                          $time_ago_html
                        </div>
                      </div>
                    </div>
                    <span class="review-badge"><i class="bi bi-star-fill text-warning me-1"></i>$rating_label</span>
                  </div>

                  <!-- Thumbnail và thông tin phòng -->
                  <a href="$room_link" class="review-room-link text-decoration-none mb-3">
                    <div class="review-room-info d-flex gap-3 p-2 rounded">
                      <img src="$room_thumb" alt="$room_name" class="review-room-thumb" width="80" height="60" loading="lazy">
                      <div class="flex-grow-1">
                        <div class="fw-semibold text-dark small mb-1">$room_name</div>
                        <div class="text-primary fw-bold small">$price_display</div>
                      </div>
                      <i class="bi bi-arrow-right text-muted align-self-center"></i>
                    </div>
                  </a>

                  <!-- Đánh giá và sao -->
                  <div class="mb-3">
                    <div class="text-warning mb-2">$stars</div>
                    <p class="flex-grow-1 review-text mb-0">"$review_text"</p>
                  </div>

                  <!-- Footer với lượt like và nút xem phòng -->
                  <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                    <div class="d-flex align-items-center gap-3">
                      <div class="text-muted small">
                        <i class="bi bi-house-door me-1"></i>
                        <span data-i18n="room.reviewFrom">Đánh giá từ phòng</span>
                      </div>
                      <div class="review-like-count d-flex align-items-center gap-1">
                        <i class="bi $like_icon text-primary"></i>
                        <span class="small fw-semibold">$like_display</span>
                      </div>
                    </div>
                    <a href="$room_link" class="btn btn-sm btn-primary rounded-pill shadow-none">
                      <i class="bi bi-eye me-1"></i><span data-i18n="room.viewRoom">Xem phòng</span>
                    </a>
                  </div>
                </div>
              </div>

REVIEW;

            }



          }



        ?>



      </div>



      <div class="swiper-pagination reviews-pagination"></div>



      <div class="swiper-button-prev reviews-prev"></div>

      <div class="swiper-button-next reviews-next"></div>



    </div>



    </div>



    <!-- Nút xem tất cả đánh giá -->

    <div class="text-center mt-4">

      <a href="rooms.php#reviews" class="btn-view-all-rooms">

        <i class="bi bi-chat-square-text me-2"></i><span data-i18n="reviews.viewAll">Xem tất cả đánh giá</span>

      </a>

    </div>



  </section>



    </div>



  </section>











  <!-- ===================== TẠI SAO CHỌN CHÚNG TÔI ===================== -->

  <section class="why-choose-section" id="why-choose-us">
    <div class="why-wave-layer"></div>
    <div class="container px-lg-4">
      <div class="row align-items-center">
        <!-- Left Section: Statistics -->
        <div class="col-lg-5 mb-4 mb-lg-0">
          <h2 class="why-main-title mb-2" data-i18n="section.whyChoose">Lý do nên đặt phòng với Vĩnh Long Hotel?</h2>
          <div class="why-stats mb-3">
            <p class="why-stats-text mb-2" data-i18n="section.stats">Hơn 10,000 lượt đặt phòng, hơn 5,000 đánh giá</p>
            <div class="why-ratings d-flex gap-3">
              <div class="why-rating-item">
                <div class="why-rating-icon bg-white text-primary rounded d-inline-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px; font-size: 14px;">
                  <i class="bi bi-star-fill"></i>
                </div>
                <div class="why-rating-value fw-bold">4.8 ★</div>
                <div class="why-rating-label small text-muted" data-i18n="section.rating">Đánh giá</div>
              </div>
              <div class="why-rating-item">
                <div class="why-rating-icon bg-white text-success rounded d-inline-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px; font-size: 14px;">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="why-rating-value fw-bold">98%</div>
                <div class="why-rating-label small text-muted" data-i18n="section.satisfied">Hài lòng</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Section: Feature Cards -->
        <div class="col-lg-7">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="why-card-traveloka">
                <div class="why-card-icon-wrapper mb-2">
                  <div class="why-card-icon bg-primary-subtle text-primary rounded p-2 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-shield-check"></i>
                  </div>
                </div>
                <h5 class="why-card-title mb-2" data-i18n="why.meetNeeds">Đáp ứng mọi nhu cầu của bạn</h5>
                <p class="why-card-desc mb-0" data-i18n="why.meetNeedsDesc">Từ phòng nghỉ, tiện ích, đến điểm tham quan, bạn có thể tin chọn dịch vụ hoàn chỉnh và hướng dẫn du lịch của chúng tôi.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="why-card-traveloka">
                <div class="why-card-icon-wrapper mb-2">
                  <div class="why-card-icon bg-warning-subtle text-warning rounded p-2 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar-check"></i>
                  </div>
                </div>
                <h5 class="why-card-title mb-2" data-i18n="why.flexibleBooking">Tùy chọn đặt phòng linh hoạt</h5>
                <p class="why-card-desc mb-0" data-i18n="why.flexibleBookingDesc" data-i18n-html="true">Kế hoạch thay đổi bất ngờ? Đừng lo! <strong>Đổi lịch</strong> hoặc <strong>Hoàn tiền</strong> dễ dàng.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="why-card-traveloka">
                <div class="why-card-icon-wrapper mb-2">
                  <div class="why-card-icon bg-success-subtle text-success rounded p-2 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-credit-card"></i>
                  </div>
                </div>
                <h5 class="why-card-title mb-2" data-i18n="why.safePayment">Thanh toán an toàn và thuận tiện</h5>
                <p class="why-card-desc mb-0" data-i18n="why.safePaymentDesc" data-i18n-html="true">Tận hưởng nhiều cách <strong>thanh toán an toàn</strong>, bằng loại tiền thuận tiện nhất cho bạn.</p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="why-card-traveloka">
                <div class="why-card-icon-wrapper mb-2">
                  <div class="why-card-icon bg-danger-subtle text-danger rounded p-2 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-award"></i>
                  </div>
                </div>
                <h5 class="why-card-title mb-2" data-i18n="why.quality">Chất lượng dịch vụ 5 sao</h5>
                <p class="why-card-desc mb-0" data-i18n="why.qualityDesc">Phòng nghỉ tiện nghi, dịch vụ chuyên nghiệp, đạt tiêu chuẩn quốc tế và được khách hàng đánh giá cao.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <!-- Liên hệ -->



  <section class="container mt-5 contact-section">



    <div class="text-center mb-4">



      <p class="text-uppercase text-muted small mb-1" data-i18n="contact.support247">Hỗ trợ 24/7</p>
      <h2 class="fw-bold mb-0 home-section-title" data-i18n="nav.contact">Liên hệ</h2>
      <p class="text-muted mt-2 mb-0" data-i18n="contact.readyToHelp">Chúng tôi luôn sẵn sàng hỗ trợ bạn mọi lúc</p>



    </div>



    <div class="row g-4">



      <!-- Cột trái: Bản đồ và thông tin liên hệ -->



      <div class="col-lg-7">



        <div class="contact-card h-100 overflow-hidden position-relative">



          <div class="contact-map-top d-flex justify-content-between align-items-center px-4 py-3">



            <span class="badge-soft d-flex align-items-center gap-1">
              <i class="bi bi-geo-alt-fill"></i> 
              <span data-i18n="contact.hotelLocation">Vị trí khách sạn</span>
            </span>
            <?php 
              $address = !empty($contact_r['address']) ? urlencode($contact_r['address']) : '';
              $maps_url = !empty($address) ? 'https://www.google.com/maps/search/?api=1&query=' . $address : ($contact_r['gmap'] ?? '#');
              if(!empty($address) || !empty($contact_r['gmap'])):
            ?>
              <a class="map-link d-flex align-items-center gap-2" href="<?php echo $maps_url; ?>" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i> 
                <span data-i18n="contact.viewLargeMap">Xem bản đồ lớn</span>
              </a>
            <?php endif; ?>



          </div>
          <div class="position-relative" style="overflow:hidden;">
            <iframe class="w-100 contact-iframe" style="min-height: 420px; border:0; display:block;" src="<?php echo $contact_r['iframe'] ?>" loading="lazy" allowfullscreen></iframe>
            <div class="contact-map-overlay"></div>
          </div>
          
          <!-- Thông tin chi tiết bên dưới bản đồ -->
          <div class="map-info-footer p-4 bg-light">
            <div class="row g-3">
              <!-- Địa chỉ chi tiết -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.address">Địa chỉ</h6>
                    <p class="text-muted mb-0 small"><?php echo htmlspecialchars($contact_r['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php 
                      $address = !empty($contact_r['address']) ? urlencode($contact_r['address']) : '';
                      $maps_url = !empty($address) ? 'https://www.google.com/maps/search/?api=1&query=' . $address : ($contact_r['gmap'] ?? '#');
                      if(!empty($address) || !empty($contact_r['gmap'])):
                    ?>
                      <a href="<?php echo $maps_url; ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="bi bi-arrow-right-circle me-1"></i><span data-i18n="contact.directions">Chỉ đường</span>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              
              <!-- Hướng dẫn đường đi -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-signpost-2-fill text-success"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.guide">Hướng dẫn</h6>
                    <ul class="text-muted small mb-0 ps-3">
                      <li data-i18n="contact.guide1">Dễ dàng tìm thấy từ trung tâm thành phố</li>
                      <li data-i18n="contact.guide2">Có bãi đỗ xe miễn phí</li>
                      <li data-i18n="contact.guide3">Gần các điểm du lịch nổi tiếng</li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <!-- Điểm đánh dấu gần đó -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-pin-map-fill text-warning"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.nearby">Điểm gần đây</h6>
                    <div class="d-flex flex-wrap gap-2">
                      <span class="badge bg-light text-dark border">Chợ đêm - 1.1 km</span>
                      <span class="badge bg-light text-dark border">Nhà thờ lớn - 1.6 km</span>
                      <span class="badge bg-light text-dark border">Ga trung tâm - 2.0 km</span>
                      <span class="badge bg-light text-dark border">Siêu thị - 400 m</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Giao thông -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-bus-front-fill text-info"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.transportation">Giao thông</h6>
                    <div class="d-flex flex-column gap-1">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-car-front-fill text-primary small"></i>
                        <span class="small text-muted" data-i18n="contact.transport1">Có bãi đỗ xe</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-taxi-front-fill text-warning small"></i>
                        <span class="small text-muted" data-i18n="contact.transport2">Dễ gọi taxi</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bicycle text-success small"></i>
                        <span class="small text-muted" data-i18n="contact.transport3">Có thể đi bộ từ trung tâm</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>



      </div>



      <!-- Cột phải: Thông tin liên hệ và form nhanh -->



      <div class="col-lg-5">



        <div class="contact-card h-100 p-4 d-flex flex-column">



          <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-headset text-primary"></i> <span data-i18n="contact.quickConnect">Kết nối nhanh</span>
          </h5>



          <!-- Thông tin liên hệ chính -->



          <div class="mb-4">
            <a href="tel:+<?php echo $contact_r['pn1'] ?>" class="contact-info-item d-flex align-items-center gap-3 p-3 mb-2">
              <div class="contact-icon-wrapper bg-primary">
                <i class="bi bi-telephone-fill text-white"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold" data-i18n="contact.hotline">Hotline</div>
                <div class="text-muted small">+<?php echo $contact_r['pn1'] ?></div>
              </div>
              <i class="bi bi-arrow-right text-muted"></i>
            </a>



            <?php if(!empty($contact_r['email'])): ?>
            <a href="mailto:<?php echo $contact_r['email']; ?>" class="contact-info-item d-flex align-items-center gap-3 p-3 mb-2">
              <div class="contact-icon-wrapper bg-danger">
                <i class="bi bi-envelope-fill text-white"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold" data-i18n="contact.email">Email</div>
                <div class="text-muted small"><?php echo $contact_r['email']; ?></div>
              </div>
              <i class="bi bi-arrow-right text-muted"></i>
            </a>
            <?php endif; ?>



            <?php if(!empty($contact_r['address'])): ?>
            <div class="contact-info-item d-flex align-items-start gap-3 p-3">
              <div class="contact-icon-wrapper bg-success">
                <i class="bi bi-geo-alt-fill text-white"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold mb-1" data-i18n="contact.address">Địa chỉ</div>
                <div class="text-muted small"><?php echo htmlspecialchars($contact_r['address'], ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
            </div>
            <?php endif; ?>
          </div>



          <!-- Giờ làm việc -->



          <div class="mb-4 p-4 contact-hours-card rounded-3 position-relative overflow-hidden">
            <div class="position-relative z-1">
              <div class="fw-bold mb-3 d-flex align-items-center gap-2">
                <div class="contact-hours-icon-wrapper bg-primary rounded-3 p-2">
                  <i class="bi bi-clock-fill text-white"></i>
                </div>
                <span data-i18n="contact.workingHours">Giờ làm việc</span>
              </div>
              <div class="small">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="bi bi-check-circle-fill text-success"></i>
                  <span class="fw-semibold" data-i18n="contact.hours247">Thứ 2 - Chủ nhật: 24/7</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                  <i class="bi bi-headset"></i>
                  <span data-i18n="contact.supportAnytime">Hỗ trợ khách hàng mọi lúc</span>
                </div>
              </div>
            </div>
            <div class="contact-hours-bg"></div>
          </div>



          <!-- Mạng xã hội -->



          <div class="mb-4">
            <div class="fw-semibold mb-2" data-i18n="contact.followUs">Theo dõi chúng tôi</div>
            <div class="d-flex flex-wrap gap-2">
              <?php if($contact_r['fb']){ echo "<a class='social-chip' target='_blank' rel='noopener' href='{$contact_r['fb']}'><i class='bi bi-facebook text-primary'></i>Facebook</a>"; } ?>
              <?php if($contact_r['insta']){ echo "<a class='social-chip' target='_blank' rel='noopener' href='{$contact_r['insta']}'><i class='bi bi-instagram text-danger'></i>Instagram</a>"; } ?>
              <?php if($contact_r['tw']){ echo "<a class='social-chip' target='_blank' rel='noopener' href='{$contact_r['tw']}'><i class='bi bi-twitter text-info'></i>Twitter</a>"; } ?>
            </div>
          </div>



          <!-- Nút liên hệ -->



          <div class="mt-auto">
            <a href="contact.php" class="btn btn-primary w-100 rounded-pill fw-semibold shadow-none contact-btn-main position-relative overflow-hidden">
              <span class="position-relative z-1">
                <i class="bi bi-chat-dots-fill me-2"></i><span data-i18n="contact.sendMessage">Gửi tin nhắn</span>
              </span>
              <span class="contact-btn-ripple"></span>
            </a>
            <div class="contact-note text-center mt-3">
              <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
              <span data-i18n="contact.responseTime">Phản hồi trong vòng 1 giờ</span>
            </div>
          </div>



        </div>



      </div>



    </div>



  </section>



<!-- Password reset modal and code -->







  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">



    <div class="modal-dialog">



      <div class="modal-content">



        <form id="recovery-form">



          <div class="modal-header">



            <h5 class="modal-title d-flex align-items-center">



              <i class="bi bi-shield-lock fs-3 me-2"></i> Tạo mật khẩu mới



            </h5>



          </div>



          <div class="modal-body">



            <div class="mb-4">



              <label class="form-label">Mật khẩu mới</label>



              <input type="password" name="pass" required class="form-control shadow-none">



              <input type="hidden" name="email">



              <input type="hidden" name="token">



            </div>



            <div class="mb-2 text-end">



              <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">Huỷ</button>



              <button type="submit" class="btn btn-dark shadow-none">Tiếp tục</button>



            </div>



          </div>



        </form>



      </div>



    </div>



  </div>







  <?php require('inc/modals.php'); ?>



  <?php require('inc/footer.php'); ?>







  <?php



  



    if(isset($_GET['account_recovery']))



    {



      $data = filteration($_GET);







      $t_date = date("Y-m-d");







      $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",



        [$data['email'],$data['token'],$t_date],'sss');







      if(mysqli_num_rows($query)==1)



      {



        echo<<<showModal



          <script>



            var myModal = document.getElementById('recoveryModal');







            myModal.querySelector("input[name='email']").value = '$data[email]';



            myModal.querySelector("input[name='token']").value = '$data[token]';







            var modal = bootstrap.Modal.getOrCreateInstance(myModal);



            modal.show();



          </script>



        showModal;



      }



      else{



        alert("error","Liên kết không còn khả dụng!");



      }







    }







  ?>



  



  <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>







  <script>



    const heroSwiper = new Swiper(".hero-swiper", {
      loop: true,
      loopAdditionalSlides: 2,
      spaceBetween: 0,
      effect: "fade",
      fadeEffect: {
        crossFade: true
      },
      speed: 1500,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
        pauseOnMouseEnter: false,
        stopOnLastSlide: false,
      },
      pagination: {
        el: ".hero-swiper-pagination",
        clickable: true,
        dynamicBullets: true,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      grabCursor: true,
      watchSlidesProgress: true,
    });














    // Pause on hover



    const heroEl = document.querySelector('.hero-swiper');



    if(heroEl){



      heroEl.addEventListener('mouseenter', ()=> heroSwiper.autoplay.stop());



      heroEl.addEventListener('mouseleave', ()=> heroSwiper.autoplay.start());



    }







  const testimonialSwiper = new Swiper(".swiper-testimonials", {



    effect: "coverflow",



    grabCursor: true,



    centeredSlides: true,



    slidesPerView: "auto",



    loop: true,



    autoplay: {

      delay: 4000,

      disableOnInteraction: false,

      pauseOnMouseEnter: true,

    },



    coverflowEffect: {



      rotate: 50,



      stretch: 0,



      depth: 100,



      modifier: 1,



      slideShadows: false,



    },



    pagination: {



      el: ".reviews-pagination",



      clickable: true,

      dynamicBullets: true,

    },



    navigation: {

      nextEl: ".reviews-next",

      prevEl: ".reviews-prev",

    },



    breakpoints: {



      320: {



        slidesPerView: 1,



      },



      640: {



        slidesPerView: 1,



      },



      768: {



        slidesPerView: 2,



      },



      1024: {



        slidesPerView: 3,



      },



    }



  });







  // promo filter tabs



  // Chỉ hiển thị "Mã giảm giá hấp dẫn", không còn filter tabs
  const promoItems = document.querySelectorAll('.promo-item');







  function filterPromos(cat = 'hot'){
    // Hiển thị tất cả các promo vì PHP đã filter chỉ lấy category='hot' rồi
    // Không cần filter nữa, hiển thị tất cả
    promoItems.forEach(card => {
      card.classList.remove('d-none');
    });
    return;
    
    // Code cũ (đã comment)
    // Chỉ hiển thị "Mã giảm giá hấp dẫn" (hot), không còn filter tabs







    let hasVisible = false;



    promoItems.forEach(card => {



      const match = card.dataset.category === cat;



      card.classList.toggle('d-none', !match);



      if(match) hasVisible = true;



    });







    // Nếu không có promo thuộc nhóm đang chọn, hiển thị tất cả để tránh trống



    if(!hasVisible){



      promoItems.forEach(card => card.classList.remove('d-none'));



    }



  }







  // Chỉ hiển thị "Mã giảm giá hấp dẫn" (hot), không còn filter tabs
  filterPromos('hot');

  // Horizontal scroll với navigation arrows
  const promoScrollContainer = document.querySelector('.promo-scroll-container');
  const promoScrollPrev = document.querySelector('.promo-scroll-prev');
  const promoScrollNext = document.querySelector('.promo-scroll-next');

  if(promoScrollContainer && promoScrollPrev && promoScrollNext){
    function updateScrollButtons(){
      const container = promoScrollContainer;
      const scrollLeft = container.scrollLeft;
      const scrollWidth = container.scrollWidth;
      const clientWidth = container.clientWidth;

      // Ẩn nút prev nếu đã scroll về đầu
      promoScrollPrev.style.display = scrollLeft <= 10 ? 'none' : 'flex';
      // Ẩn nút next nếu đã scroll đến cuối
      promoScrollNext.style.display = scrollLeft >= scrollWidth - clientWidth - 10 ? 'none' : 'flex';
    }

    // Scroll trái
    promoScrollPrev.addEventListener('click', () => {
      promoScrollContainer.scrollBy({
        left: -400,
        behavior: 'smooth'
      });
    });

    // Scroll phải
    promoScrollNext.addEventListener('click', () => {
      promoScrollContainer.scrollBy({
        left: 400,
        behavior: 'smooth'
      });
    });

    // Cập nhật trạng thái nút khi scroll
    promoScrollContainer.addEventListener('scroll', updateScrollButtons);
    
    // Cập nhật khi resize
    window.addEventListener('resize', updateScrollButtons);
    
    // Cập nhật lần đầu
    setTimeout(updateScrollButtons, 100);
  }

  // Copy tất cả mã giảm giá
  const copyAllPromosBtn = document.getElementById('copy-all-promos-btn');
  if(copyAllPromosBtn){
    copyAllPromosBtn.addEventListener('click', function(){
      const promoItems = document.querySelectorAll('.promo-item');
      const allCodes = [];
      
      promoItems.forEach(item => {
        const codeElement = item.querySelector('.promo-code');
        if(codeElement){
          const code = codeElement.textContent.trim();
          if(code && !allCodes.includes(code)){
            allCodes.push(code);
          }
        }
      });
      
      if(allCodes.length === 0){
        if(typeof showToast === 'function') {
          showToast('warning', 'Không có mã giảm giá nào để copy!', 2000);
        } else {
          alert('Không có mã giảm giá nào để copy!');
        }
        return;
      }
      
      // Tạo text với tất cả mã, mỗi mã một dòng
      const codesText = allCodes.join('\n');
      
      // Copy vào clipboard
      navigator.clipboard.writeText(codesText).then(() => {
        if(typeof showToast === 'function') {
          showToast('success', `Đã copy ${allCodes.length} mã giảm giá vào clipboard!`, 3000);
        } else {
          alert(`Đã copy ${allCodes.length} mã giảm giá:\n${codesText}`);
        }
        
        // Thay đổi icon tạm thời để feedback
        const icon = this.querySelector('i');
        if(icon){
          const originalClass = icon.className;
          icon.className = 'bi bi-check-circle-fill me-1';
          setTimeout(() => {
            icon.className = originalClass;
          }, 2000);
        }
      }).catch(err => {
        console.error('Copy failed:', err);
        // Fallback: dùng cách cũ
        const textarea = document.createElement('textarea');
        textarea.value = codesText;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
          document.execCommand('copy');
          if(typeof showToast === 'function') {
            showToast('success', `Đã copy ${allCodes.length} mã giảm giá vào clipboard!`, 3000);
          } else {
            alert(`Đã copy ${allCodes.length} mã giảm giá:\n${codesText}`);
          }
        } catch(e) {
          if(typeof showToast === 'function') {
            showToast('error', 'Không thể copy. Vui lòng thử lại!', 3000);
          } else {
            alert('Không thể copy. Vui lòng thử lại!');
          }
        }
        document.body.removeChild(textarea);
      });
    });
  }







  // countdown expiry



  function updateExpCountdown(){
    const nodes = document.querySelectorAll('.promo-exp');
    const now = new Date();
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    
    nodes.forEach(el => {
      const exp = el.dataset.exp;
      const baseText = el.dataset.expText || '';
      
      // Lấy text từ i18n
      const expiresText = window.i18n ? window.i18n.translate('promo.expires') : 'Hết hạn';
      const unlimitedText = window.i18n ? window.i18n.translate('promo.unlimited') : 'Không giới hạn';
      const expiredText = window.i18n ? window.i18n.translate('promo.expired') : 'Đã hết hạn';
      const expiresInText = window.i18n ? window.i18n.translate('promo.expiresIn') : 'Còn';
      
      if(!exp){
        // Không có ngày hết hạn - hiển thị "Unlimited"
        // Kiểm tra xem đã có i18n attributes chưa
        if(el.querySelector('[data-i18n="promo.expires"]') && el.querySelector('[data-i18n="promo.unlimited"]')) {
          // Đã có i18n attributes, chỉ cần trigger update
          if(window.i18n) window.i18n.updateTranslations();
          return;
        }
        // Nếu chưa có, tạo HTML với i18n
        el.innerHTML = `<span data-i18n="promo.expires">${expiresText}</span>: <span data-i18n="promo.unlimited">${unlimitedText}</span>`;
        // Trigger i18n update
        if(window.i18n) window.i18n.updateTranslations();
        return;
      }

      const end = new Date(exp + 'T23:59:59');
      const diff = end - now;

      if(diff <= 0){
        el.textContent = expiredText;
        el.classList.add('text-danger');
        return;
      }

      const days = Math.floor(diff / (1000*60*60*24));
      const hours = Math.floor((diff / (1000*60*60)) % 24);
      
      let label;
      if(currentLang === 'en') {
        label = days > 0 ? `${days} day${days > 1 ? 's' : ''} ${hours} hour${hours !== 1 ? 's' : ''}` : `${hours} hour${hours !== 1 ? 's' : ''}`;
        el.textContent = `${expiresText}: ${baseText} · ${expiresInText} ${label}`;
      } else {
        label = days > 0 ? `${days} ngày ${hours} giờ` : `${hours} giờ`;
        el.textContent = `${expiresText}: ${baseText} · ${expiresInText} ${label}`;
      }
    });
  }



  updateExpCountdown();

  setInterval(updateExpCountdown, 60*1000);
  
  // Cập nhật lại khi ngôn ngữ thay đổi
  document.addEventListener('languageChanged', function() {
    setTimeout(updateExpCountdown, 100);
  });







  // copy promo code feedback



  document.querySelectorAll('.promo-copy').forEach(btn => {
    btn.addEventListener('click', () => {
      const code = btn.getAttribute('data-code') || '';
      const originalHTML = btn.innerHTML;
      
      if(navigator.clipboard){
        navigator.clipboard.writeText(code).then(()=>{
          // Visual feedback
          btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã sao chép!';
          btn.style.background = 'rgba(34,197,94,0.3)';
          btn.style.borderColor = 'rgba(34,197,94,0.5)';
          
          setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.style.borderColor = '';
          }, 2000);
          
          if(typeof showToast === 'function'){
            showToast('success','Đã sao chép mã: ' + code, 3000);
          } else {
            alert('Đã sao chép mã: ' + code);
          }
        });
      } else {
        if(typeof showToast === 'function'){
          showToast('error', 'Trình duyệt không hỗ trợ sao chép tự động', 3000);
        } else {
          alert('Sao chép thủ công: ' + code);
        }
      }
    });
  });







  // save promo code



  document.querySelectorAll('.promo-save').forEach(btn => {
    btn.addEventListener('click', () => {
      const code = (btn.getAttribute('data-code') || '').trim();
      if(!code) return;
      
      // Disable button during request
      btn.disabled = true;
      const originalHTML = btn.innerHTML;
      
      fetch('ajax/promos_saved.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`save=1&code=${encodeURIComponent(code)}`
      }).then(r=>r.json()).then(resp=>{
        btn.disabled = false;
        
        if(resp.status === 'ok'){
          // Visual feedback - mark as saved
          btn.classList.add('saved');
          btn.innerHTML = '<i class="bi bi-bookmark-check-fill me-1"></i>Đã lưu';
          
          if(typeof showToast === 'function'){ 
            showToast('success','Đã lưu mã giảm giá: ' + code, 3000); 
          } else { 
            alert('Hệ thống đã lưu lại mã ' + code); 
          }



        } else if(resp.status === 'login_required'){
          btn.innerHTML = originalHTML;
          if(typeof showToast === 'function'){ 
            showToast('warning','Vui lòng đăng nhập để lưu mã giảm giá', 3000); 
          } else { 
            alert('Vui lòng đăng nhập để lưu mã giảm giá'); 
          }
        } else {
          btn.innerHTML = originalHTML;
          if(typeof showToast === 'function'){ 
            showToast('error', 'Không thể lưu mã', 3000); 
          } else { 
            alert('Không thể lưu mã'); 
          }
        }
      }).catch(()=>{
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        if(typeof showToast === 'function'){ 
          showToast('error', 'Không thể lưu mã', 3000); 
        } else { 
          alert('Không thể lưu mã'); 
        }
      });



    });



  });







  // Favorites toggle



  document.querySelectorAll('.home-fav-btn').forEach(btn=>{



    btn.addEventListener('click', ()=>{



      const roomId = btn.getAttribute('data-room');



      fetch('ajax/favorites.php', {



        method:'POST',



        headers:{'Content-Type':'application/x-www-form-urlencoded'},



        body: new URLSearchParams({toggle:1, room_id: roomId})



      }).then(res=>res.json()).then(res=>{



        if(res.status === 'login_required'){



          if(typeof showToast === 'function'){



            showToast('warning','Vui lòng đăng nhập để lưu yêu thích', 3000);



          } else {



            alert('Vui lòng đăng nhập để lưu yêu thích');



          }



          return;



        }



        if(res.status === 'added'){



          btn.classList.add('active');



          const icon = btn.querySelector('i');



          if(icon) icon.className='bi bi-heart-fill';



        } else if(res.status === 'removed'){



          btn.classList.remove('active');



          const icon = btn.querySelector('i');



          if(icon) icon.className='bi bi-heart';



        }



      });



    });



  });







// quick fill Vĩnh Long

  const quickVlBtn = document.getElementById('quick_vl');



  const keywordInput = document.getElementById('keyword_input');



  if(quickVlBtn && keywordInput){



    quickVlBtn.addEventListener('click', () => {



      keywordInput.value = 'Vĩnh Long';

      keywordInput.focus();



    });



  }







  // Khôi phục tài khoản

    



    let recovery_form = document.getElementById('recovery-form');







    recovery_form.addEventListener('submit', (e)=>{



      e.preventDefault();







      let data = new FormData();







      data.append('email',recovery_form.elements['email'].value);



      data.append('token',recovery_form.elements['token'].value);



      data.append('pass',recovery_form.elements['pass'].value);



      data.append('recover_user','');







      var myModal = document.getElementById('recoveryModal');



      var modal = bootstrap.Modal.getInstance(myModal);



      modal.hide();







      let xhr = new XMLHttpRequest();



      xhr.open("POST","ajax/login_register.php",true);







      xhr.onload = function(){



        if(this.responseText == 'failed'){

          showAlertBox('error',"Khôi phục tài khoản thất bại!");

        }

        else{

          showAlertBox('success', 'Khôi phục tài khoản thành công!');





          recovery_form.reset();



        }



      }







      xhr.send(data);



    });







  </script>



<script>



let login_form = document.getElementById('login-form');



let register_form = document.getElementById('register-form');







// Đăng nhập  

login_form.addEventListener('submit', (e)=>{  



  e.preventDefault();                    // Ngăn form tự reload trang khi submit



  let data = new FormData(login_form);   // Lấy toàn bộ dữ liệu người dùng nhập trong form

  data.append('login', '');              // Thêm biến login để PHP nhận biết đây là request đăng nhập



  fetch('ajax/login_register.php', {     // Gửi AJAX tới file xử lý PHP

    method: 'POST',                      // Gửi bằng phương thức POST

    body: data                           // Gửi dữ liệu form

  })

  .then(res => res.text())               // Nhận response dạng text từ PHP

  .then(res => {                         // Bắt đầu xử lý kết quả trả về



    if(res === 'login_success'){         // Nếu PHP trả về "login_success" tức đăng nhập thành công

        location.reload();               // Reload lại trang để cập nhật giao diện (hiện nút user, avatar,...)

    }

    else if(res === 'invalid_email_mob'){ // Sai email hoặc số điện thoại

        showToast('danger', 'Email hoặc số điện thoại không tồn tại!', 3000);

    }

    else if(res === 'invalid_password'){  // Sai mật khẩu

        showToast('error','Sai mật khẩu', 3000);

    }

    else if(res === 'not_verified'){      // Email chưa được xác thực
        showToast('error','Email chưa được xác thực! Vui lòng xác thực email trước khi đăng nhập.', 3000);
    }



    else if(res === 'inactive'){          // Tài khoản đã bị khóa



        showToast('danger', 'Tài khoản đã bị khóa!', 3000);



    }



    else{                                  // Trường hợp khác – lỗi không xác định



        showToast('danger', 'Lỗi không xác định: ' + res, 3000);



    }







  });



});











// Đăng ký

register_form.addEventListener('submit', (e)=>{



  e.preventDefault();



  let data = new FormData(register_form);



  data.append('register', '');







  fetch('ajax/login_register.php', { method: 'POST', body: data })



  .then(res => res.text())



  .then(res => {







    res = res.trim(); // tránh lỗi do khoảng trắng / xuống dòng







    if(res === 'registration_success'){



      showToast('success','Đăng ký thành công! Vui lòng đăng nhập.', 3000);







      // Đóng modal đăng ký



      const regModalEl = document.getElementById('registerModal');



      if (regModalEl) {



        const regModal = bootstrap.Modal.getInstance(regModalEl);



        if (regModal) {



          regModal.hide();



        }



      }







      // Mở modal đăng nhập



      const loginModalEl = document.getElementById('loginModal');



      if (loginModalEl) {



        setTimeout(() => {



          const loginModal = new bootstrap.Modal(loginModalEl);



          loginModal.show();



        }, 500);



      }







      register_form.reset();







  });



});











</script>






<script>
// ===================== PHÒNG NGHỈ GẦN ĐÂY =====================
function loadRecentlyViewedRooms() {
  try {
    // Lấy danh sách phòng đã xem từ localStorage
    const viewedRooms = JSON.parse(localStorage.getItem('recently_viewed_rooms') || '[]');
    
    if(viewedRooms.length === 0) {
      // Ẩn section nếu không có phòng nào
      const section = document.getElementById('recently-viewed-section');
      if(section) section.style.display = 'none';
      return;
    }
    
    // Hiển thị section
    const section = document.getElementById('recently-viewed-section');
    if(section) section.style.display = 'block';
    
    // Gọi AJAX để lấy thông tin phòng
    const roomIds = JSON.stringify(viewedRooms);
    fetch(`ajax/rooms.php?recently_viewed=1&room_ids=${encodeURIComponent(roomIds)}`)
      .then(response => response.json())
      .then(data => {
        if(data.status === 'ok' && data.rooms && data.rooms.length > 0) {
          renderRecentlyViewedRooms(data.rooms);
        } else {
          // Ẩn section nếu không có phòng
          if(section) section.style.display = 'none';
        }
      })
      .catch(error => {
        console.error('Error loading recently viewed rooms:', error);
        if(section) section.style.display = 'none';
      });
  } catch(e) {
    console.error('Error parsing recently viewed rooms:', e);
    const section = document.getElementById('recently-viewed-section');
    if(section) section.style.display = 'none';
  }
}

function renderRecentlyViewedRooms(rooms) {
  const container = document.getElementById('recently-viewed-rooms-container');
  if(!container) return;
  
  // Lấy ngôn ngữ hiện tại
  const currentLang = document.documentElement.lang || 'vi';
  const isEn = currentLang === 'en';
  
  // Lấy danh sách favorites nếu user đã đăng nhập
  let favIds = [];
  fetch('ajax/favorites.php?list=1')
    .then(res => res.json())
    .then(data => {
      if(data.status === 'ok' && data.rooms) {
        favIds = data.rooms;
      }
      renderRooms(rooms, favIds);
    })
    .catch(() => {
      renderRooms(rooms, []);
    });
  
  function renderRooms(rooms, favIds) {
    let html = '';
    
    rooms.forEach(room => {
      const isFav = favIds.includes(room.id);
      const favClass = isFav ? 'active' : '';
      const favIcon = isFav ? 'bi-heart-fill' : 'bi-heart';
      
      // Tính giá sau giảm
      const discount = room.discount || 0;
      const finalPrice = discount > 0 ? Math.round(room.price * (1 - discount / 100)) : room.price;
      const priceFmt = finalPrice.toLocaleString('vi-VN');
      const oldPriceFmt = discount > 0 ? room.price.toLocaleString('vi-VN') : '';
      
      // Stars
      const avgRating = room.avg_rating || 0;
      let starsHtml = '';
      const fullStars = Math.floor(avgRating);
      const hasHalf = (avgRating - fullStars) >= 0.5;
      for(let i = 0; i < fullStars; i++) {
        starsHtml += '<i class="bi bi-star-fill text-warning"></i>';
      }
      if(hasHalf) {
        starsHtml += '<i class="bi bi-star-half text-warning"></i>';
      }
      for(let i = fullStars + (hasHalf ? 1 : 0); i < 5; i++) {
        starsHtml += '<i class="bi bi-star text-warning"></i>';
      }
      
      // Owner display
      let ownerDisplay = '';
      if(room.hotel_name || room.owner_name) {
        const ownerLabel = room.hotel_name || room.owner_name;
        ownerDisplay = `<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
          <i class='bi bi-building me-1'></i>${ownerLabel}
        </small>`;
      } else {
        ownerDisplay = `<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
          <i class='bi bi-shield-check me-1'></i>Admin
        </small>`;
      }
      
      const discountBadge = discount > 0 ? `<span class='badge bg-danger text-white ms-1'>${discount}%</span>` : '';
      const guestText = `${room.adult} ${isEn ? 'adults' : 'người lớn'} · ${room.children} ${isEn ? 'children' : 'trẻ em'}`;
      const viewDetailsText = isEn ? 'View details' : 'Xem chi tiết';
      const bookText = isEn ? 'Book now' : 'Đặt ngay';
      
      html += `
        <div class="col-lg-4 col-md-6">
          <div class="list-card">
            <div>
              <div class="list-card__img-wrap">
                <img src="${room.thumb}" class="list-card__img" alt="${room.name}" loading="lazy">
                <button class="home-fav-btn ${favClass}" data-room="${room.id}" title="Yêu thích">
                  <i class="bi ${favIcon}"></i>
                </button>
              </div>
            </div>
            <div class="list-card__info">
              <h5 class="mb-0">${room.name}</h5>
              ${ownerDisplay}
              <div class="list-card__meta mb-2">
                <i class="bi bi-geo-alt me-1"></i> ${room.location}
                <span class="ms-2"><i class="bi bi-people me-1"></i> ${guestText}</span>
              </div>
              <div class="mb-2 d-flex align-items-center gap-2">
                ${starsHtml}
                <span class="badge bg-light text-dark">${avgRating > 0 ? avgRating + '/5' : (isEn ? 'New' : 'Mới')}</span>
              </div>
            </div>
            <div class="list-card__price d-flex flex-column justify-content-between">
              <div class="text-end">
                ${oldPriceFmt ? `<div class="list-card__price-old">${oldPriceFmt} đ</div>` : ''}
                <div class="list-card__price-new">${priceFmt} đ ${discountBadge}</div>
              </div>
              <div class="list-card__cta">
                <a href="room_details.php?id=${room.id}" class="btn btn-primary btn-sm w-100 mb-2">${bookText}</a>
                <a href="room_details.php?id=${room.id}" class="btn btn-outline-dark btn-sm w-100">${viewDetailsText}</a>
              </div>
            </div>
          </div>
        </div>
      `;
    });
    
    container.innerHTML = html;
    
    // Bind favorite buttons
    if(typeof bindFavButtons === 'function') {
      bindFavButtons();
    }
  }
}

// Load khi trang load xong
if(document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadRecentlyViewedRooms);
} else {
  loadRecentlyViewedRooms();
}
</script>

</body>



</html>


