<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style> img { width: 100% } </style>
</head>

<body style="background-color: #f3f3f3; margin: 0; padding: 0; font-family: Arial, sans-serif;">

<br><br><br>

<table style="background-color: #fff; width: 500px; margin: 0 auto; padding: 0; border: 1px solid #ddd; border-top: 5px solid #83b716; border-bottom: none;">
    <tr style="padding: 0; margin: 0;">
        <td style="padding: 20px 40px; text-align: justify; width:500px">
            @yield('body')
        </td>
    </tr>
</table>

<table style="background-color: #333; width: 500px; margin: 0px auto 40px auto; padding: 20px 40px; border: 1px solid #ddd; border-top: none;">
    <tr style="padding: 0; margin: 0;">
        <td style="color: #fff; margin: 0; padding: 0;" width="50%">
            <strong>
                <span style="color: #83b716;">S.A. Proto</span>
            </strong>
            <br>
            Zilverling A230<br>
            Drienerlolaan 5<br>
            7522NB Enschede<br>
        </td>
        <td style="color: #fff; padding: 0;">
            <br>
            Mon&Fri, 08:30-16:00<br>
            Tue-Thu, 08:30-17:30<br>
            <a style="color: #fff; text-decoration: none;" href="tel:+31534894423">+31 (0)53 489 4423</a><br>
            <a style="color: #fff; text-decoration: none;" href="mailto:board@proto.utwente.nl">
                board@proto.utwente.nl
            </a>
        </td>
    </tr>
    <tr style="padding: 0; margin: 0;">
        <td style="color: #fff; margin: 0; padding: 0;" colspan="2">
            <br>
            <sub>
                If you feel that you should not have received this e-mail, please contact
                <a href="mailto:abuse@proto.utwente.nl" style="color: #00aac0; text-decoration: none;">abuse@proto.utwente.nl</a>.
            </sub>
        </td>
    </tr>
</table>

<center>
    <a href="{{route('homepage')}}">
    <img src="{{ asset('images/logo/regular.png') }}" style="width: 30%; max-width: 200px;"/>
    </a>
</center>

<br><br><br>

<hr style="border: none;">

</body>

</html>
