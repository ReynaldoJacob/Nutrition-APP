<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida</title>
</head>
<body style="margin:0;padding:0;background:#f4f8f6;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#006d4e;color:#e5fff0;padding:24px 28px;">
                            <h1 style="margin:0;font-size:24px;line-height:1.3;">Bienvenida a Clinical Sanctuary</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;">
                                Hola {{ $user->full_name }},
                            </p>
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;">
                                Tu cuenta fue creada exitosamente en la plataforma. Ya puedes iniciar sesión y comenzar a gestionar tu práctica nutricional.
                            </p>
                            <p style="margin:0 0 22px 0;font-size:15px;line-height:1.7;">
                                Correo de acceso: <strong>{{ $user->email }}</strong>
                            </p>
                            <a href="{{ url('/login') }}" style="display:inline-block;background:#006d4e;color:#e5fff0;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:700;font-size:14px;">
                                Iniciar sesión
                            </a>
                            <p style="margin:22px 0 0 0;font-size:13px;line-height:1.6;color:#6b7280;">
                                Si no solicitaste esta cuenta, por favor ignora este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
