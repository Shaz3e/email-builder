<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body style="margin:0;padding:0;background-color:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">

                <!-- CONTAINER -->
                <table width="800" cellpadding="0" cellspacing="0" role="presentation"
                    style="width:800px;max-width:100%;">

                    <!-- HEADER IMAGE -->
                    @if ($header_image)
                        <tr>
                            <td align="center" style="padding:0;">
                                <img src="{{ $header_image }}" alt=""
                                    style="
                        display:block;
                        max-width:100%;
                        height:auto;
                        border:0;
                        outline:none;
                        text-decoration:none;
                    ">
                            </td>
                        </tr>
                    @endif

                    <!-- HEADER TEXT -->
                    @if ($header_text)
                        <tr>
                            <td
                                style="
                    padding:16px;
                    color:{{ $header_text_color }};
                    background-color:{{ $header_background_color }};
                    font-family:Arial, Helvetica, sans-serif;
                    font-size:14px;
                    line-height:1.5;
                ">
                                {!! $header_text !!}
                            </td>
                        </tr>
                    @endif

                    <!-- BODY -->
                    <tr>
                        <td
                            style="
                    padding:16px;
                    font-family:Arial, Helvetica, sans-serif;
                    font-size:14px;
                    line-height:1.6;
                    color:#000000;
                ">
                            {!! $body !!}
                        </td>
                    </tr>

                    <!-- FOOTER IMAGE -->
                    @if ($footer_image)
                        <tr>
                            <td align="center">
                                <img src="{{ $footer_image }}" alt=""
                                    style="
                        display:block;
                        max-width:100%;
                        height:auto;
                        border:0;
                    ">
                            </td>
                        </tr>
                    @endif

                    <!-- FOOTER TEXT -->
                    @if ($footer_text)
                        <tr>
                            <td
                                style="
                    padding:16px;
                    color:{{ $footer_text_color }};
                    background-color:{{ $footer_background_color }};
                    font-family:Arial, Helvetica, sans-serif;
                    font-size:12px;
                    line-height:1.5;
                ">
                                {!! $footer_text !!}
                            </td>
                        </tr>
                    @endif

                    <!-- FOOTER BOTTOM IMAGE -->
                    @if ($footer_bottom_image)
                        <tr>
                            <td align="center">
                                <img src="{{ $footer_bottom_image }}" alt=""
                                    style="
                        display:block;
                        max-width:100%;
                        height:auto;
                        border:0;
                    ">
                            </td>
                        </tr>
                    @endif

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
