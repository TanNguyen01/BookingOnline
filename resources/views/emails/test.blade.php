<!-- <style>
 .booking-bill {
    width: 500px;
    margin: 0 auto;
    padding: 15px;
    text-align: center;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

 .booking-bill h1 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
  }

 .booking-bill p {
    font-size: 16px;
    margin-bottom: 20px;
  }

 .booking-bill ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

 .booking-bill li {
    font-size: 16px;
    margin-bottom: 10px;
  }

 .booking-bill li:before {
    content: "\2022";
    font-size: 18px;
    color: #666;
    margin-right: 10px;
  }

 .booking-bill ul ul {
    padding-left: 20px;
  }

 .booking-bill ul ul li {
    font-size: 14px;
    margin-bottom: 5px;
  }

 .booking-bill > * {
    margin-bottom: 20px;
  }

 .booking-bill p:last-child {
    font-size: 18px;
    font-weight: bold;
    color: #666;
  }
</style>

<div class="booking-bill">
  <h1>Thông tin đặt chỗ</h1>
  <p>Xin chào {{ $output['customer_name'] }},</p>
  <p>Bạn đã đặt chỗ thành công với các thông tin sau:</p>
  <ul>
    <li>Ngày đặt: {{ $output['date_order'] }}</li>
    <li>Giờ đặt: {{ $output['time_order'] }}</li>
    <li>Tên cửa hàng: {{ $output['store_name'] }}</li>
    <li>Địa chỉ cửa hàng: {{ $output['store_address'] }}</li>
    <li>Nhân viên phục vụ: {{ $output['staff_name'] }}</li>
    <li>Điện thoại nhân viên: {{ $output['staff_phone'] }}</li>
    <li>Email nhân viên: {{ $output['staff_email'] }}</li>
    <li>Dịch vụ đã chọn:
      <ul>
        @foreach ($output['service_name'] as $service)
          <li>{{ $service }}</li>
        @endforeach
      </ul>
    </li>
    <li>Ghi chú: {{ $output['customer_note'] }}</li>
  </ul>
  <p>Cảm ơn bạn đã đặt chỗ!</p>
</div> -->



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body style="margin: auto;  width: 80%;  padding: 10px;">
    <div style=" background-color:rgb(174, 174, 174)">
        <div style="padding-top: 20px; opacity: 0.8; text-align: center">
            <span style="font-size: 40px">
            </span>
            <span style="font-size: 40px">
            </span>
        </div>

    </div>
    <div style="margin: auto;  background-color: aliceblue; padding: 30px ">
        <br>
        <div style="text-align :center">Cám ơn quý khách {{ $output['customer_name'] }} đã sử dụng dịch vụ của IMATEACH</div>
        <hr style="width: 80%">
        <h3 style="text-align: center ; padding-top: 10px">Thông tin lịch Booking</h3>
        <div style="margin: auto;  width: max-content;  padding: 10px;">
            <table>
                <tr>
                    <td>Ngày Hẹn :</td>
                    <td>{{ $output['date_order'] }}.</td>
                </tr>
                <tr>
                    <td>Giờ hẹn:</td>
                    <td>{{ $output['time_order'] }}.</td>
                </tr>
                <tr>
                    <td>Tên cửa hàng:</td>
                    <td>{{ $output['store_name'] }}.</td>
                </tr>
                <tr>
                    <td>Địa chi cửa hàng:</td>
                    <td>{{ $output['store_address'] }}.</td>
                </tr>
                <tr>
                    <td>Nhân Viên:</td>
                    <td>{{ $output['staff_name'] }}.</td>
                </tr>
                <tr>
                    <td>Dịch vụ đã chọn:</td>
                    <td>
                        @foreach ($output['service_name'] as $service)
                        <li>{{ $service }}</li>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td>Ghi Chú:</td>
                    <td>{{ $output['customer_note'] }}</td>
                </tr>
            </table>
        </div>
        <hr style="width: 80%">
        <div style="text-align: start ; padding-top: 10px">Mọi thắc mắc xin liên hệ: 0926755061</div>
        <div style="text-align: center ; padding-top: 10px">XIN CHÂN THÀNH CẢM ƠN QUÝ KHÁCH ĐÃ SỬ DỤNG DỊCH VỤ!</div>

    </div>
</body>

</html>
