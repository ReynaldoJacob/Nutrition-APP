<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta - Metabolé</title>
</head>
<body style="margin:0;padding:0;background-color:#f7f9fb;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f7f9fb;margin:0;padding:0;">
    <tr>
        <td align="center" style="padding:36px 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="620" style="width:620px;max-width:620px;">
                <tr>
                    <td align="center" style="padding:4px 0 24px 0;font-family:Arial,sans-serif;font-size:26px;font-weight:800;color:#00694b;letter-spacing:-0.01em;">
                        Metabolé
                    </td>
                </tr>

                <tr>
                    <td style="background:#ffffff;border-radius:16px;padding:34px 28px;font-family:Arial,sans-serif;color:#2c3437;">
                        <h1 style="margin:0 0 14px 0;text-align:center;font-size:34px;line-height:1.2;font-weight:800;color:#2c3437;">Verifica tu cuenta</h1>

                        <p style="margin:0 0 24px 0;text-align:center;font-size:15px;line-height:1.75;color:#596064;">
                            Hola {{ $user->first_name }}, bienvenido a la red de profesionales de <strong style="color:#2c3437;">Metabolé</strong>.<br>
                            Para completar tu registro, utiliza el siguiente codigo de verificacion:
                        </p>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 8px auto;">
                            <tr>
                                <td style="background:#7af9c7;border-radius:14px;padding:18px 24px;">
                                    <span style="display:block;font-family:Arial,sans-serif;font-size:42px;font-weight:800;letter-spacing:0.22em;color:#005e43;">{{ substr($otp, 0, 3) }} {{ substr($otp, 3) }}</span>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:14px 0 0 0;text-align:center;font-size:11px;font-weight:700;letter-spacing:0.14em;color:#596064;text-transform:uppercase;">
                            Este codigo expira en 10 minutos.
                        </p>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:28px;border-top:1px dashed #dce4e8;">
                            <tr>
                                <td style="padding-top:20px;text-align:center;font-size:12px;color:#596064;">
                                    Si no solicitaste este registro, puedes ignorar este correo de forma segura.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:22px 0 0 0;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td style="height:4px;width:96px;background:#7af9c7;border-radius:999px;">&nbsp;</td>
                                <td></td>
                                <td style="height:4px;width:32px;background:#006d4e;border-radius:999px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:28px 0 8px 0;font-family:Arial,sans-serif;">
                        <p style="margin:0 0 10px 0;font-size:10px;color:#98a2a9;letter-spacing:0.12em;text-transform:uppercase;">Soporte · Privacidad · Terminos</p>
                        <p style="margin:0;font-size:10px;color:#98a2a9;">© 2026 Metabolé. Todos los derechos reservados.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
