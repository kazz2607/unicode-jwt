Front-End : React

Back-End : Laravel

API -> Authentication -> Token (JWT)

Request get Data : Gửi token thông qua Header (Authorization : Bearer <token>)

Server -> Kiểm tra token có hợp lệ hay không ? -> Decode Payload -> Truy vấn Database trả về dữ liệu

## Bảo mật Token

AccessToken : Nếu bị đánh cắp => Hacker khai thác dựa vào token
-> Giải pháp : Hạ thấp thời gian sống của AccessToken -> Gây phiền phức cho người dùng
-> Cần bổ sung : RefreshToken -> Thời gian sống lâu hơn -> Dùng cấp lại AccessToken mới sau khi AccessToken cũ hết hạn
-> Khi logout -> Thêm token và Backlist -> Khi Authorization -> Cần kiểm tra token có trong backlist không
    + Tính hợp lệ
    + Thời gian sống
    + Có trong blacklist hay không

