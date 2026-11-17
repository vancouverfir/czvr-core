<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Vancouver FIR</title>
<style>
	body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
	table { border-collapse: collapse !important; }
	img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
	body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #ffffff; }

	.email-body p { margin: 0 0 16px 0; }
	.email-body h1 { font-size: 24px; line-height: 1.25; font-weight: 600; color: #1d1d1f; margin: 0 0 20px 0; letter-spacing: -0.01em; }
	.email-body h2 { font-size: 18px; line-height: 1.3; font-weight: 600; color: #1d1d1f; margin: 24px 0 12px 0; }
	.email-body a { color: #2f7a1f; }
	.email-body ul, .email-body ol { margin: 0 0 16px 0; padding-left: 22px; }

	@media screen and (max-width: 600px) {
		.email-wrap { padding: 28px 20px !important; }
		.email-body h1 { font-size: 21px !important; }
	}
</style>
</head>

<body style="margin:0; padding:0; background-color:#ffffff;">
	<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff;">
		<tr>
			<td align="center" class="email-wrap" style="padding:40px 24px;">
				<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px;">

					<!-- Logo -->
					<tr>
						<td style="padding:0 0 28px 0;">
							<a href="{{ config('app.url') }}" target="_blank" rel="noopener" style="text-decoration:none;">
								<img src="https://czvr.ca/storage/files/branding/czvr-long-wordmark.png" width="320" alt="Vancouver FIR" style="display:block; width:320px; max-width:100%;">
							</a>
						</td>
					</tr>

					<!-- Message -->
					<tr>
						<td class="email-body" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size:15px; line-height:1.6; color:#1d1d1f;">
							@hasSection('headline')
								<h1 style="font-size:24px; line-height:1.25; font-weight:600; color:#1d1d1f; margin:0 0 24px 0;">@yield('headline')</h1>
							@endif
							@hasSection('to-line')
								<p style="margin:0 0 16px 0;">@yield('to-line')</p>
							@endif
							<div>@yield('message-content')</div>
							@hasSection('from-line')
								<p style="margin:24px 0 0 0;">@yield('from-line')</p>
							@endif
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td style="padding:40px 0 0 0;">
							<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
								<tr>
									<td style=" padding:20px 0 0 0; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size:12px; line-height:1.6; color:#6e6e73;">
										<p style="margin:0 0 10px 0;">
											This email was sent to
											@hasSection('footer-to-line')
												<a href="mailto:@yield('footer-to-line')" style="color:#6e6e73; text-decoration:none;">@yield('footer-to-line')</a>
											@else
												you
											@endif
											because @yield('footer-reason-line')
											This mailbox isn't monitored; for help, <a href="{{ config('app.url') }}/staff" target="_blank" rel="noopener" style="color:#6e6e73; text-decoration:underline;">contact our staff</a>.
										</p>
										<p style="margin:0;">
											<a href="{{ route('dashboard.index') }}#preferences" target="_blank" rel="noopener" style="color:#6e6e73; text-decoration:underline;">Manage your preferences</a>
											@isset($unsubscribeUrl)
												&nbsp;&middot;&nbsp;
												<a href="{{ $unsubscribeUrl }}" target="_blank" rel="noopener" style="color:#6e6e73; text-decoration:underline;">Unsubscribe</a>
											@endisset
											&nbsp;&middot;&nbsp;
											<a href="{{ config('app.url') }}/privacy" target="_blank" rel="noopener" style="color:#6e6e73; text-decoration:underline;">Privacy Policy</a>
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>
			</td>
		</tr>
	</table>
</body>
</html>
