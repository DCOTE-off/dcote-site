<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>503 | Сайт на обслуживании | DCOTE</title>
    <style>
        :root {
            --gap10: clamp(1px, 0.65vw, 10px);
            --gap20: clamp(1px, 1.3vw, 20px);
        }

        * {
            box-sizing: border-box;
        }

        body {
            align-items: center;
            background-color: rgb(14, 10, 21);
            color: #fff;
            display: flex;
            font-family: 'Vag Rounded Next', sans-serif;
            margin: 0;
            min-height: 100vh;
            padding: clamp(10px, 1.95vw, 30px);
        }

        .wrapper {
            align-items: center;
            display: flex;
            flex-direction: column;
            margin: auto;
        }

        .text-404 {
            font-size: clamp(5px, 26.065vw, 401px);
            line-height: 0.75;
            margin: 0;
            z-index: 0;
        }

        .ptichka {
            display: block;
            height: clamp(1px, 30.615vw, 417px);
            margin-top: calc(-0.5 * clamp(5px, 26.065vw, 401px));
            width: auto;
            z-index: 1;
        }

        .desc-404 {
            align-items: center;
            display: flex;
            flex-direction: column;
            gap: var(--gap20);
            margin-top: calc(-0.2 * clamp(1px, 30.615vw, 417px));
            text-align: center;
            z-index: 2;
        }

        .desc-404 h2 {
            font-size: clamp(1px, 2.86vw, 44px);
            margin: 0;
        }

        .return {
            align-items: center;
            border: 2px solid rgba(98, 59, 146, 1);
            border-radius: 100px;
            color: #fff;
            display: inline-flex;
            font-weight: 700;
            justify-content: center;
            padding: clamp(1px, 0.78vw, 12px) clamp(1px, 1.17vw, 18px);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .text-404 {
                font-size: clamp(1px, 45vw, 1000px);
            }

            .ptichka {
                height: clamp(1px, 60vw, 1000px);
                margin-top: calc(-0.5 * clamp(1px, 45vw, 1000px));
            }

            .desc-404 {
                gap: var(--gap10);
                margin-top: calc(-0.2 * clamp(1px, 60vw, 1000px));
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1 class="text-404">503</h1>
        <img class="ptichka" src="/images/errors/ptichka-500.webp" alt="Иллюстрация ошибки">
        <div class="desc-404">
            <h2>ПРОБЛЕМАТИЧНО, САЙТ СЕЙЧАС ОБСЛУЖИВАЕТСЯ</h2>
            <a class="return" href="{{ url('/') }}">НА ГЛАВНУЮ</a>
        </div>
    </div>
</body>
</html>
