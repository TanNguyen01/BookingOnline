<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Booking Confirmation</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .main-content {
            background: #fff;
            padding: 40px;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h3 {
            color: #497ffc;
            margin-bottom: 20px;
        }

        .booking-info {
            text-align: left;
            margin-bottom: 30px;
        }

        .booking-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .booking-info td {
            padding: 10px;
            vertical-align: top;
        }

        .booking-info td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .booking-info ul {
            padding-left: 20px;
            margin: 0;
        }

        .separator {
            width: 80%;
            margin: 20px auto;
            border: none;
            border-top: 1px solid #ddd;
        }

        .closing-note {
            font-size: 16px;
            color: #666;
            margin-top: 20px;
        }

        .contact-info {
            text-align: left;
            margin-top: 30px;
        }

        .contact-info div {
            font-size: 14px;
            margin: 4px 0;
        }

        .contact-info a {
            color: #497ffc;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
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
                <td>aaaaaaaaaaaaaa.</td>
            </tr>
            <tr>
                <td>Ngày Hẹn :</td>
                <td>2024-01-02.</td>
            </tr>
            <tr>
                <td>Giờ hẹn:</td>
                <td>18:00:00.</td>
            </tr>
            <tr>
                <td>Tên cửa hàng:</td>
                <td>aaaaaaaaaaaa.</td>
            </tr>
            <tr>
                <td>Địa chỉ cửa hàng:</td>
                <td>Thanh ha hai duong.</td>
            </tr>
            <tr>
                <td>Nhân Viên:</td>
                <td>Pham Manh.</td>
            </tr>
            <tr>
                <td>Dịch vụ đã chọn:</td>
                <td>
                    <ul>

                            <li> Danh giay </li>
                            <li> Danh giay </li>
                            <li> Danh giay </li>

                    </ul>
                </td>
            </tr>
            <tr>
                <td>Ghi Chú:</td>
                <td>AAAAAAAAAAAAAAAAA</td>
            </tr>
        </table>
    </div>
    <hr class="separator" />

    <div class="closing-note">Xin chân thành cảm ơn quý khách đã sử dụng dịch vụ của IMTATECH!</div>
    <div class="contact-info">
        <div style="font-size: 14px; font-weight: bold">Mọi thắc mắc xin liên hệ:</div>
        <div>Phone: <a href="tel:0926755061">0926755061</a></div>
        <div>Email: <a href="mailto:manhpkph30134@gmail.com">manhpkph30134@gmail.com</a></div>
    </div>
</div>

</body>

</html>
