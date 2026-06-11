<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Booking Lashedia</title>
</head>
<body style="margin:0; padding:0; background:#fff7fb; font-family:Arial, sans-serif; color:#1f1f1f;">

    <div style="max-width:680px; margin:30px auto; background:white; border-radius:18px; overflow:hidden; border:1px solid #f7c8d8;">

        <div style="background:#f7c8d8; padding:28px; text-align:center;">
            <h1 style="margin:0; color:#1f1f1f;">Lashedia</h1>
            <p style="margin:8px 0 0;">Invoice Booking</p>
        </div>

        <div style="padding:30px;">

            <h2 style="margin-top:0;">Halo, {{ $booking->name }}</h2>

            <p>
                Pesanan Anda telah disetujui oleh admin Lashedia.
                Berikut invoice otomatis untuk booking Anda.
            </p>

            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; margin-top:20px;">
                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>No. Invoice</strong></td>
                    <td style="border:1px solid #f3dce6;">{{ $booking->invoice_number }}</td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Tanggal Invoice</strong></td>
                    <td style="border:1px solid #f3dce6;">
                        {{ optional($booking->invoice_date)->format('d M Y H:i') }}
                    </td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Layanan</strong></td>
                    <td style="border:1px solid #f3dce6;">{{ $booking->service }}</td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Stylist</strong></td>
                    <td style="border:1px solid #f3dce6;">{{ $booking->stylist }}</td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Tanggal Booking</strong></td>
                    <td style="border:1px solid #f3dce6;">
                        {{ $booking->date }} - {{ $booking->time }}
                    </td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Alamat</strong></td>
                    <td style="border:1px solid #f3dce6;">{{ $booking->full_address ?? '-' }}</td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;"><strong>Jarak Transport</strong></td>
                    <td style="border:1px solid #f3dce6;">
                        {{ $booking->distance_km ?? 0 }} km
                    </td>
                </tr>
            </table>

            <h3 style="margin-top:28px;">Rincian Biaya</h3>

            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                    <td style="border:1px solid #f3dce6;">Biaya Layanan</td>
                    <td style="border:1px solid #f3dce6; text-align:right;">
                        Rp{{ number_format($booking->invoice_subtotal ?? 0, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6;">Biaya Transport</td>
                    <td style="border:1px solid #f3dce6; text-align:right;">
                        Rp{{ number_format($booking->invoice_transport ?? 0, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td style="border:1px solid #f3dce6; background:#fff7fb;">
                        <strong>Total</strong>
                    </td>
                    <td style="border:1px solid #f3dce6; background:#fff7fb; text-align:right;">
                        <strong>Rp{{ number_format($booking->invoice_total ?? 0, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>

            <p style="margin-top:28px;">
                Terima kasih sudah melakukan booking di Lashedia.
            </p>

        </div>

    </div>

</body>
</html>
