<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4">
    <tr>
        <td align="center">

            <!-- MAIN CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:100%; background-color:#ffffff;">

                {{-- ================= HEADER IMAGE ================= --}}
                @if ($header_image)
                <tr>
                    <td align="center" style="padding:0; margin:0;">
                        <img
                            src="{{ asset($header_image) }}"
                            width="600"
                            style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none;"
                            alt="Header Image"
                        >
                    </td>
                </tr>
                @endif

                {{-- ================= HEADER TEXT ================= --}}
                @if ($header_text)
                <tr>
                    <td
                        align="left"
                        bgcolor="{{ $header_background_color }}"
                        style="
                            padding:20px;
                            color:{{ $header_text_color }};
                            background-color:{{ $header_background_color }};
                            font-family:Arial, Helvetica, sans-serif;
                            font-size:16px;
                            line-height:1.5;
                        "
                    >
                        {!! $header_text !!}
                    </td>
                </tr>
                @endif

                {{-- ================= BODY ================= --}}
                <tr>
                    <td
                        align="left"
                        style="
                            padding:20px;
                            color:#333333;
                            font-family:Arial, Helvetica, sans-serif;
                            font-size:15px;
                            line-height:1.6;
                        "
                    >
                        {!! $body !!}
                    </td>
                </tr>

                {{-- ================= FOOTER IMAGE ================= --}}
                @if ($footer_image)
                <tr>
                    <td align="center" style="padding:0; margin:0;">
                        <img
                            src="{{ asset($footer_image) }}"
                            width="600"
                            style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none;"
                            alt="Footer Image"
                        >
                    </td>
                </tr>
                @endif

                {{-- ================= FOOTER TEXT ================= --}}
                @if ($footer_text)
                <tr>
                    <td
                        align="center"
                        bgcolor="{{ $footer_background_color }}"
                        style="
                            padding:20px;
                            color:{{ $footer_text_color }};
                            background-color:{{ $footer_background_color }};
                            font-family:Arial, Helvetica, sans-serif;
                            font-size:13px;
                            line-height:1.5;
                        "
                    >
                        {!! $footer_text !!}
                    </td>
                </tr>
                @endif

                {{-- ================= FOOTER BOTTOM IMAGE ================= --}}
                @if ($footer_bottom_image)
                <tr>
                    <td align="center" style="padding:0; margin:0;">
                        <img
                            src="{{ asset($footer_bottom_image) }}"
                            width="600"
                            style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none;"
                            alt="Footer Bottom Image"
                        >
                    </td>
                </tr>
                @endif

            </table>
            <!-- END MAIN CONTAINER -->

        </td>
    </tr>
</table>
</body>
</html>
