<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ env('APP_NAME') }} - Profile Update Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #1B5E20; color: #FFFFFF; font-size: 16px; line-height: 1.5;">
    <!-- Main container table -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 700px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);">
        <!-- Header Section -->
        <tr>
            <td style="background: linear-gradient(135deg, #2E7D32, #4CAF50); padding: 5% 5% 4%; text-align: center;">
                <img src="{{ uploaded_asset(get_setting('header_logo')) }}" alt="{{ env('APP_NAME') }} Logo" width="160" style="max-width: 20%; height: auto; display: block; margin: 0 auto 5%;">
                <h1 style="color: #FFFFFF; font-size: 2.5em; margin: 0; font-weight: bold; letter-spacing: 1px;">{{ translate('Welcome to') }} {{ env('APP_NAME') }}!</h1>
            </td>
        </tr>
        <!-- Content Section -->
        <tr>
            <td style="padding: 5%;">
                <p style="color: #388E3C; font-size: 1em; line-height: 1.8; margin: 0 0 5%;">
                    {{ translate('Hi! Your profile has been successfully updated on') }} <strong>{{ env('APP_NAME') }}</strong>.
                    {{ translate('Below are your updated profile details:') }}
                </p>
                <!-- Details Container Table -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background: #F1F8E9; border-radius: 10px; border: 1px solid #C8E6C9; padding: 5%; margin-bottom: 5%;">
                    <!-- Name Row -->
                    <tr>
                        <td style="padding-bottom: 4%;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Name') }}</td>
                                    <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($name ?? 'N/A')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Email Row -->
                    <tr>
                        <td style="padding-bottom: 4%;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Email') }}</td>
                                    <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($email ?? 'N/A')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Phone Row -->
                    <tr>
                        <td style="padding-bottom: 4%;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Phone') }}</td>
                                    <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($phone ?? 'N/A')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Address Row -->
                    <tr>
                        <td style="padding-bottom: 4%;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Address') }}</td>
                                    <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($address ?? 'N/A')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- User Type Row -->
                    @if($is_wholeseller === 'Yes')
                        <tr>
                            <td style="padding-bottom: 4%;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('User Type') }}</td>
                                        <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ translate('Wholeseller') }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="padding-bottom: 4%;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('User Type') }}</td>
                                        <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($user_type ?? 'N/A')) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif
                    <!-- GST Number Row -->
                    @if($gst_no !== 'N/A')
                        <tr>
                            <td style="padding-bottom: 4%;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('GST Number') }}</td>
                                        <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($gst_no ?? 'N/A')) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif
                    <!-- Drug License Number Row -->
                    @if($drug_license_no !== 'N/A')
                        <tr>
                            <td style="padding-bottom: 4%;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Drug License Number') }}</td>
                                        <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($drug_license_no ?? 'N/A')) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif
                    <!-- Pan Card Number Row -->
                    @if($pan_card !== 'N/A')
                        <tr>
                            <td style="padding-bottom: 4%;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Pan Card Number') }}</td>
                                        <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($pan_card ?? 'N/A')) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif
                    <!-- Updated At Row -->
                    <tr>
                        <td style="padding-bottom: 4%;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 40%; font-weight: bold; color: #2E7D32; font-size: 1em; vertical-align: top;">{{ translate('Updated At') }}</td>
                                    <td style="width: 58%; color: #388E3C; font-size: 1em; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; white-space: pre-wrap; line-height: 1.6; vertical-align: top;">{{ nl2br(htmlspecialchars($updated_at ?? 'N/A')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="color: #388E3C; font-size: 1em; line-height: 1.8; margin: 0 0 5%;">
                    {{ translate('Explore your account and start enjoying our services!') }}
                </p>
                <!-- CTA Section -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center; margin: 5% 0;">
                    <tr>
                        <td>
                            <a href="{{ env('APP_URL') }}" style="display: inline-block; padding: 3% 6%; font-size: 1em; color: #FFFFFF; text-decoration: none; font-weight: bold; border-radius: 6px; background-color: #4CAF50;">{{ translate('Go to the Website') }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Footer Section -->
        <tr>
            <td style="background-color: #E8F5E9; padding: 4%; text-align: center; font-size: 0.8em; color: #4CAF50; border-top: 1px solid #C8E6C9;">
                <p>{{ translate('If you did not update your profile, please contact our support team immediately.') }}</p>
                <p>© {{ date('Y') }} {{ env('APP_NAME') }}. {{ translate('All rights reserved.') }}</p>
            </td>
        </tr>
    </table>
</body>
</html>