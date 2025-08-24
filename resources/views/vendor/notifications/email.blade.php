<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <!-- Header with Logo -->
            <div class="header">
                <a href="https://computerscienceresources.com">
                    <img src="https://computerscienceresources.com/images/LogoTitle.svg" alt="">
                </a>
            </div>

            <!-- Email Body -->
            <div class="body">
                <div class="inner-body">
                    {{-- Greeting --}}
                    @if (! empty($greeting))
                    <h1>{{ $greeting }}</h1>
                    @else
                    @if ($level === 'error')
                    <h1>@lang('Whoops!')</h1>
                    @else
                    <h1>@lang('Greetings, Developer!')</h1>
                    @endif
                    @endif

                    {{-- Intro Lines --}}
                    @foreach ($introLines as $line)
                    <p>{{ $line }}</p>
                    @endforeach

                    {{-- Action Button --}}
                    @isset($actionText)
                    <?php
                        $color = match ($level) {
                            'success', 'error' => $level,
                            default => 'primary',
                        };
                    ?>
                    <div style="text-align: center; margin: 32px 0;">
                        <a href="{{ $actionUrl }}" class="button button-{{ $color }}">
                            {{ $actionText }}
                        </a>
                    </div>
                    @endisset

                    {{-- Outro Lines --}}
                    @foreach ($outroLines as $line)
                    <p>{{ $line }}</p>
                    @endforeach

                    {{-- Salutation --}}
                    <p>
                        @if (! empty($salutation))
                        {{ $salutation }}
                        @else
                        @lang('Regards,')<br>
                        {{ config('app.name') }}
                        @endif
                    </p>

                    {{-- Subcopy --}}
                    @isset($actionText)
                    <div class="subcopy">
                        <p>
                            @lang(
                                "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                                'into your web browser:',
                                [
                                    'actionText' => $actionText,
                                ]
                            )
                        </p>
                        <p class="break-all">{{ $actionUrl }}</p>
                    </div>
                    @endisset
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
