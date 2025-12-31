function get_bookings(search = '') {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/refund_bookings.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('table-data').innerHTML = this.responseText;
  }

  xhr.send('get_bookings=1&search=' + encodeURIComponent(search));
}

/* ----------------------------
   💰 Refund booking
----------------------------- */
function refund_booking(id) {
  if (confirm("Hoàn tiền cho đặt chỗ này?")) {
    let data = new FormData();
    data.append('refund_booking', 1);
    data.append('booking_id', id);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/refund_bookings.php", true);

    xhr.onload = function () {
      let res = this.responseText.trim();
      console.log("Refund response:", res);

      if (res === "1") {
        showAlertBox('success', 'Tiền đã được hoàn lại!');
        get_bookings();
      } 
      else if (res === "0") {
        showAlertBox('error', 'Không có thay đổi (có thể đã hoàn trước đó).');
      } 
      else if (res === "invalid_id") {
        showAlertBox('error', 'Mã đơn đặt không hợp lệ!');
      } 
      else {
        showAlertBox('error', 'Server Down!');
      }
    }

    xhr.send(data);
  }
}

window.onload = function () {
  get_bookings();
}
