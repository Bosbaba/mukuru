<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Purchase confirmation</h2>

		<div>
			<table cellspacing="0" cellpadding="2px" style="width:600px;">
				<tr>
					<td><strong>South African Rand</strong></td>
					<td>R {{ $local_amount }}[ZAR] {{ (int)$cur->discount ? sprintf("(inc %s%% discount)", floatval($cur->discount)) : ''; }}</td>
				</tr>
				<tr>
					<td><strong>{{ $cur->display_name }}</strong></td>
					<td>{{ $foreign_amount }}[{{ $cur->currency }}]</td>
				</tr>
				<tr>
					<td><strong>Rate</strong></td>
					<td>{{ $cur->rate }}</td>
				</tr>
				<tr>
					<td><strong>Surcharge</strong></td>
					<td>{{ floatval($cur->surcharge) }}%</td>
				</tr>
			</table>
		</div>
	</body>
</html>
