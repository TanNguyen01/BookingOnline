
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Booking Confirmation</title>
    @vite('resources/css/app.css')
    <style>
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #497ffc;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white p-10 w-11/12 max-w-lg rounded-lg shadow-lg text-center">
        <h3 class="title">Kính gửi quý khách thông tin lịch Booking</h3>
        <div class="booking-info text-left mb-8">
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold w-2/5 py-2">Tên khách hàng :</td>
                    <td class="py-2">{{ $output['customer_name'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Ngày Hẹn :</td>
                    <td class="py-2">{{ $output['date_order'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Giờ hẹn:</td>
                    <td class="py-2">{{ $output['time_order'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Tên cửa hàng:</td>
                    <td class="py-2">{{ $output['store_name'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Địa chỉ cửa hàng:</td>
                    <td class="py-2">{{ $output['store_address'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Nhân Viên:</td>
                    <td class="py-2">{{ $output['staff_name'] }}.</td>
                </tr>
                <tr>
                    <td class="font-bold w-2/5 py-2">Ghi Chú:</td>
                    <td class="py-2">{{ $output['customer_note'] }}</td>
                </tr>
            </table>
        </div>
        <div class="services text-left mb-8">
            <h4 class="font-bold mb-2">Dịch vụ đã chọn:</h4>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 border-b border-gray-300 text-center" style="width: 10%">STT</th>
                        <th class="py-2 border-b border-gray-300 text-center" style="width: 40%">Dịch vụ</th>
                        <th class="py-2 border-b border-gray-300 text-center">Giá tiền</th>
                        <th class="py-2 border-b border-gray-300 text-center">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($output['services'] as $index => $service)
                    <tr>
                        <td class="py-2 border-b border-gray-300 text-center" style="width: 10%">{{ $index + 1 }}</td>
                        <td class="py-2 border-b border-gray-300 text-center" style="width: 40%">{{ $service['name']}}</td>
                        <td class="py-2 border-b border-gray-300 text-center">{{ $service['price']}}VND</td>
                        <td class="py-2 border-b border-gray-300 text-center">{{ $service['time']}}p</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            <table class="w-full border border-none">
                <tbody>
                    <tr>
                        <td class="py-2 text-center" style="width: 10%"></td>
                        <td class="py-2 text-center font-bold" style="width: 40%">Tổng:</td>
                        <td class="py-2 text-center">{{ $output['total_price']}}VND</td>
                        <td class="py-2 text-center">{{$output['total_time']}}p</td>
                    </tr>
                </tbody>

            </table>

        </div>
        <hr class="border-t border-gray-300 my-4" />
        <div class="closing-note text-gray-700 text-lg font-medium text-center">
            Xin chân thành cảm ơn quý khách đã sử dụng dịch vụ của IMTATECH!
        </div>
        <div class="contact-info text-center mt-8">
            <div class="text-sm font-bold mb-2">Mọi thắc mắc xin liên hệ:</div>
            <div class="text-sm mb-1">
                Phone: <a href="tel:0926755061" class="text-blue-500 hover:underline">0926755061</a>
            </div>
            <div class="text-sm">
                Email: <a href="mailto:manhpkph30134@gmail.com" class="text-blue-500 hover:underline">manhpkph30134@gmail.com</a>
            </div>
        </div>
    </div>

</body>

</html>
