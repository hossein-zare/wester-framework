<!doctype html>
<html lang="en">
    <head>
        <title>{{ $code }} - {{ $message }}</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">

        <!-- Styles -->
        <style>
			html, body{
				margin: 0;
				height: 100%;
				font-family: 'Quicksand', sans-serif;
			}
			.container{
				display: flex;
				justify-content: center;
				align-content: center;
			}
			.align-self-center{
				align-self: center;
			}
			.full-height{
				height: 100%;
			}
			.text-center{
				text-align: center;
			}
			.text-important{
				font-weight: 400;
				font-size: 38px;
			}
			.font-size-big{
				font-size: 28px;
			}
			.border-bottom{
				border-bottom: 3px solid #e4e4e4;
				padding-bottom: 5px;
			}
        </style>
    </head>
    <body>
        <div class="container full-height">
            <div class="font-size-big text-center align-self-center">
				<div class="border-bottom">{{ $code }}</div>
				<div class="text-important">{{ $message }}</div>
            </div>
        </div>
    </body>
</html>