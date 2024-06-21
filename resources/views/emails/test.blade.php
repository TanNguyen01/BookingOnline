<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Document</title>
  <style>
    body {
      margin: auto;
      width: 100vw;
      height: 100vh;
      padding: 10px;
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      color: #333;
      position: relative;
      background-color: rgb(174, 174, 174);

    }

    .main-content {
      background: white;
      padding: 30px;
      width: 80% !important;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      /* margin: 0 28px; */
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .thank-you {
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      word-wrap: break-word;
      color: rgb(103, 196, 94);
    }

    .separator {
      width: 80%;
      margin: 20px auto;
    }

    h3 {
      text-align: center;
      padding-top: 10px;
      color: rgb(73, 136, 254);
    }

    .booking-info {
      margin: auto;
      width: fit-content;
      padding: 10px;
    }

    .booking-info table {
      width: 100%;
      border-collapse: collapse;
    }

    .booking-info td {
      padding: 10px;
      vertical-align: top;
      word-wrap: break-word;
    }

    .booking-info ul {
      padding-left: 20px;
      margin: 0;
    }

    .contact-info {
      text-align: start;
      padding-top: 10px;
    }

    .closing-note {
      text-align: center;
      padding-top: 10px;
      margin-top: 28px;
    }

    td {
      font-size: 14px;
    }

    tr>td:nth-child(1) {
      font-weight: bold;
    }

    @media (max-width: 768px) {
      body {
        width: 90%;
      }

      .header,
      .main-content {
        width: 100%;
        padding: 15px;
      }
    }
  </style>
</head>

<body>

  <div class="main-content">
    <h3>Kính gửi quý khách thông tin lịch Booking</h3>
    <div class="booking-info">
      <table>
        <tr>
          <td>Tên khách hàng :</td>
          <td>{{ $output['customer_name'] }}.</td>
        </tr>
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
          <td>Địa chỉ cửa hàng:</td>
          <td>{{ $output['store_address'] }}.</td>
        </tr>
        <tr>
          <td>Nhân Viên:</td>
          <td>{{ $output['staff_name'] }}.</td>
        </tr>
        <tr>
          <td>Dịch vụ đã chọn:</td>
          <td>
            <ul>
              @foreach ($output['service_name'] as $service)
              <li>{{ $service }}</li>
              @endforeach
            </ul>
          </td>
        </tr>
        <tr>
          <td>Ghi Chú:</td>
          <td>{{ $output['customer_note'] }}</td>
        </tr>
      </table>
    </div>
    <hr class="separator" />

    <div class="closing-note">Xin chân thành cảm ơn quý khách đã sử dụng dịch vụ của IMTATECH!</div>
    <div style="margin-top: 28px; margin-left: 28px" class="contact-info">
      <span style="font-size: 14px; font-weight: bold">Mọi thắc mắc xin liên hệ:</span>
      <div style="margin-top: 8px; margin-left: 4px">
        <div style="font-size: 14px; margin: 4px 0">Phone: <a>0926755061</a></div>
        <div style="font-size: 14px; margin: 4px 0">Email: <a>manhpkph30134@gmail.com</a></div>
      </div>
    </div>
  </div>
</body>

</html>
