<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body>
    @if ($header_image)
        <img src="{{ asset($header_image) }}" />
    @endif

    @if ($header_text)
        <div style="color: {{ $header_text_color }}; background-color:{{ $header_background_color }}">
            {!! $header_text !!}
        </div>
    @endif

    {!! $body !!}

    @if ($footer_image)
        <img src="{{ asset($footer_image) }}" />
    @endif

    @if ($footer_text)
        <div style="color: {{ $footer_text_color }};background-color: {{ $footer_background_color }}">
            {!! $footer_text !!}
        </div>
    @endif

    @if ($footer_bottom_image)
        <img src="{{ asset($footer_bottom_image) }}" />
    @endif
</body>

</html>
