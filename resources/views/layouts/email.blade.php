<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title></title>

<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap" rel="stylesheet">

<style>
	body, table, td, a {
	-webkit-text-size-adjust: 100%; 
	-ms-text-size-adjust: 100%;
	}
	table { border-collapse: collapse !important; width: 100%; }
	img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; max-width: 100%; }
	body { margin: 0; padding: 0; font-family: 'Source Sans Pro', Arial, sans-serif; color: #333333; }
	a { color: #0068A5; text-decoration: underline; }
	p { margin: 0 0 15px 0; line-height: 1.5; }

	/* Container */
	.container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 15px; }

	/* Header */
	.header { text-align: left; padding-bottom: 15px; }
	.header img { width: 300px; }

	/* Footer */
	.footer { font-size: 12px; color: #888888; line-height: 1.4; padding-top: 25px; border-top: 1px solid #dddddd; }
	.footer a { color: #0068A5; text-decoration: underline; }

	/* Responsive */
	@media screen and (max-width: 520px) {
	.container { padding: 15px; }
	.header img { width: 150px; }
	}
</style>

</head>

<body>
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td>
        <table class="container" cellpadding="0" cellspacing="0" role="presentation">

		<!-- Header / Logo -->
		<tr>
			<td class="header">
				<img src="https://czvr.ca/storage/files/branding/czvr-long-wordmark.png">
			</td>
		</tr>

		<!-- Message Content -->
		<tr>
			<td>
				<p>@yield('to-line')</p>
				<p>@yield('message-content')</p>
				<p>@yield('from-line')</p>
			</td>
		</tr>

		<tr>
			<td class="footer" style="font-size:14px; color:#555555; padding: 15px 10px;">
				<p style="margin:5px 0;">
				This email was sent from Vancouver FIR to you @yield('footer-to-line') because @yield('footer-reason-line')
				</p>
				<p style="margin:5px 0;">
				Please do not reply to this email! <a href="{{config('app.url')}}" target="_blank" rel="noopener">Manage your membership or subscriptions</a>
				</p>
				<p style="margin:5px 0;">
				<a href="{{config('app.url')}}" target="_blank">Visit</a> | 
				<a href="{{config('app.url')}}/staff" target="_blank">Contact Us</a> | 
				<a href="{{config('app.url')}}/privacy" target="_blank">Privacy Policy</a>
				</p>
			</td>
		</tr>


        </table>
      </td>
    </tr>
  </table>
</body>

</html>
