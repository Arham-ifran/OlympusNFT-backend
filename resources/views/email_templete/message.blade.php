<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Check Email</title>

</head>

<body style="padding:0; margin:0px; background:#eee;">

    <div style="max-width:600px; margin:auto;  background:#fff; color:#222; font-size:17px; position: relative;">
        <div style="width:100%; text-align:center; margin-bottom: 15px;background: transparent linear-gradient(143deg, #1D114F 0%, #3A24A6 100%) 0% 0% no-repeat padding-box;padding: 30px 0;display: flex;justify-content: center;align-items: center;">
            <img src="{{ _asset('backend/images/logo_email.svg') }}" style=" width:45px;max-width: 100%;" />
            <h3 style="font-size: 25px;text-transform: capitalize;font-family: Segoe, 'Segoe UI', 'sans-serif';color: #fff;margin: 0;margin-left: 5px;">NFT </h3>
        </div>

        <div style=" padding:10px 30px 20px;  font-family: Segoe, 'Segoe UI', 'sans-serif';">
            @if(isset($data) ) {!! $data !!} @endif
        </div>
        <!--footer area-->
        <div style=" background: #2C1276; padding:10px 20px 10px; font-size: 12px;text-align: center; font-family: Segoe, 'Segoe UI', 'sans-serif';  color: #fff;">
            <!-- copyright area-->
            <p style="margin-bottom: 0; margin-top: 0">Copyright © {{ date('Y') }} {{settingValue('site_name') }} All rights reserved</p>
        </div>

    </div>
</body>

</html>