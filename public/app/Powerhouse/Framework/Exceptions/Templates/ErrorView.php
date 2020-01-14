<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Whoa!</title>

        <link href="<?php echo path('/resources/lib/prism/prism-dark.css'); ?>" rel="stylesheet" />
		<style>
			*{
				box-sizing: border-box;
			}
			body{
				font: 12px "Helvetica Neue", helvetica, arial, sans-serif;
				background-color: #484848;
			}
			.skeleton{
				padding: 15px;
			}
			.header{
				text-align: center;
				padding: 15px;
			}
			.header img{
				vertical-align: middle;	
			}
			.file-location{
				color: white;
				background-color: #2d2d2d;
				padding: 10px;
			}
			.error-message{
				color: white;
				background-color: #2d2d2d;
				padding: 10px;
				margin-top: 20px;
			}
			.details{
				color: white;
				background-color: #2d2d2d;
				padding: 10px;
			}

			.row::after {
				content: "";
				clear: both;
				display: table;
			}

			[class*="col-"] {
				float: left;
				padding: 15px;
			}

			.col-1 {width: 8.33%;}
			.col-2 {width: 16.66%;}
			.col-3 {width: 25%;}
			.col-4 {width: 33.33%;}
			.col-5 {width: 41.66%;}
			.col-6 {width: 50%;}
			.col-7 {width: 58.33%;}
			.col-8 {width: 66.66%;}
			.col-9 {width: 75%;}
			.col-10 {width: 83.33%;}
			.col-11 {width: 91.66%;}
			.col-12 {width: 100%;}
						
			table {
				border-collapse: collapse;
				width: 100%;
			}

			th, td {
				padding: 8px;
				text-align: left;
				border-bottom: 1px solid #ddd;
			}
		</style>
    </head>
    <body>
        
        <!-- Header -->
        <div class="skeleton">
            <div class="header">
                <img src="<?php echo path('/resources/static/framework/images/favicon.png'); ?>" style="margin-right: 10px;">
                <img src="<?php echo path('/resources/static/framework/images/logo-text.png'); ?>">
            </div>
            <div class="file-location">
                <?php echo $code->file; ?> - on Line <?php echo $code->line; ?>
            </div>
        </div>

		<div class="container">
			<div class="row">
				<div class="col-12">
					<pre class="line-numbers" style="white-space: pre-wrap; margin-top:0; border-radius: 5px;" data-start="<?php echo $code->start; ?>" data-line="<?php echo $code->line; ?>"><code class="language-php language-html"><?php echo $code->content; ?></code></pre>
					<div class="error-message">
						<?php echo $message; ?>
					</div>
				</div>

			</div>
		</div>

        <!-- Javascript -->
        <script src="<?php echo path('/resources/lib/prism/prism-dark.js'); ?>"></script>
    </body>
</html>