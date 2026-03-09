<!DOCTYPE html>
<html>
<body style="font-family:Arial">

<h2>Confirm Your Booking</h2>

<p>Please click the button below to verify your appointment:</p>

<p>
    <a href="{{ $verifyUrl }}"
       style="padding:12px 20px;background:#4f46e5;color:#fff;
              text-decoration:none;border-radius:6px">
        Confirm Booking
    </a>
</p>

<p style="font-size:12px;color:#666">
    Or copy this link:<br>
    {{ $verifyUrl }}
</p>

</body>
</html>
