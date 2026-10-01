<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<meta name="color-scheme" content="light"> 
<title>Verify Email - Orbii</title> 
</head> 

<body style="margin:0;padding:0;background-color:#eef2f8;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-text-size-adjust:100%;"> 

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef2f8;"> 
<tr>
<td align="center" style="padding:40px 16px;"> 

<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border-radius:16px;border:1px solid #e3e9f2;overflow:hidden;"> 

<tr>
<td height="6" style="height:6px;line-height:6px;font-size:0;background-color:#4c9aff;background-image:linear-gradient(90deg,#1976ff,#6ba7ff);">
&nbsp;
</td>
</tr> 
 
<!-- Header -->
<tr>
<td align="center" bgcolor="#1769d1" style="background-color:#1769d1;background-image:linear-gradient(135deg,#1769d1,#3f8cff);padding:36px 30px;"> 

<table role="presentation" cellpadding="0" cellspacing="0" border="0">
<tr> 

<!-- Logo Orbii -->
<td valign="middle" style="padding-right:12px;"> 
  <img src="{{ asset('images/Orbii.svg') }}" width="32" height="32" alt="Orbii Logo" style="display:block;border:0;"> 
</td> 

<td valign="middle" style="font-size:28px;font-weight:700;letter-spacing:1.5px;color:#ffffff;">
Orbii
</td> 

</tr>
</table> 

</td>
</tr> 
 
<tr>
<td align="center" style="padding:44px 40px 12px;"> 

<!-- Logo Orbii menggantikan ikon pesan -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0">
<tr> 

<td align="center" width="80" height="80" style="width:80px;height:80px;background-color:#eaf2ff;border:1px solid #d3e3ff;border-radius:24px;box-shadow:0 8px 16px rgba(0,0,0,0.05);"> 

  <img src="{{ asset('images/Orbii.svg') }}" width="42" height="42" alt="Orbii Logo" style="display:block;border:0;margin:0 auto;"> 

</td>

</tr>
</table> 
 
<h2 style="margin:26px 0 0;font-size:24px;line-height:1.35;font-weight:700;color:#0b1f4b;">
Welcome aboard, <span style="color:#0052cc;">{{ $user->name ?? 'Explorer' }}</span>!
</h2> 

<p style="margin:16px 0 0;font-size:15px;line-height:1.7;color:#5b6b82;">
We are thrilled to have you join our community. Your journey into a world of seamless connection is almost ready to begin. Please verify your email address to unlock your dashboard.
</p> 

</td>
</tr> 
 
<tr>
<td align="center" style="padding:28px 40px 12px;"> 

<table role="presentation" cellpadding="0" cellspacing="0" border="0">
<tr> 

<td align="center" bgcolor="#0052cc" style="background-color:#0052cc;border-radius:30px;"> 

<a href="{{ $verificationUrl }}" target="_blank" style="display:inline-block;padding:15px 40px;font-size:15px;font-weight:600;letter-spacing:0.3px;color:#ffffff;text-decoration:none;border-radius:30px;">
Verify Email Address
</a> 

</td>
</tr>
</table> 

</td>
</tr> 
 
<tr>
<td style="padding:24px 40px 0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td height="1" style="height:1px;line-height:1px;font-size:0;background-color:#e8edf5;">
&nbsp;
</td>
</tr>
</table>

</td>
</tr> 

<tr>
<td align="center" style="padding:22px 40px 40px;"> 

<p style="margin:0;font-size:13px;line-height:1.6;color:#8898aa;">
If you did not create an account with Orbii, feel free to safely ignore this email.
</p> 

</td>
</tr> 

<tr>
<td align="center" bgcolor="#f7f9fc" style="background-color:#f7f9fc;border-top:1px solid #e8edf5;padding:22px 20px;"> 

<p style="margin:0;font-size:12px;line-height:1.6;color:#8898aa;">
&copy; {{ date('Y') }} Orbii. All rights reserved.
</p> 

</td>
</tr> 

</table> 
</td>
</tr> 
</table> 

</body> 
</html>