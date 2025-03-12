<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8ebef">
    @php
    $logo = get_setting('header_logo');
    @endphp
    <tr>
        <td align="center" valign="top" style="padding:50px 10px;">
            <!-- Container -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">
                        <table width="650" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#ffffff" style="width:650px; min-width:650px; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                    <!-- Header -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="background-color: #f8fafa" >
                                        <tr>
                                            <td style="padding: 40px 30px 40px 30px;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <th style="line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td style="line-height:0pt; text-align:left;"><img src="{{ uploaded_asset($logo) }}" width="" height="26" border="0" alt="" /></td>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                        <th width="170" style="line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td style="color:#000000; font-family:'Public Sans', sans-serif; font-size:14px; line-height:16px; text-align:right;">
                                                                        <a href="{{ env('APP_URL') }}" target="_blank" style="color:#000001; text-decoration:none; font-weight: 500">
                                                                            <span  style="color:#000001; text-decoration:none;">{{ env('APP_NAME') }}</span>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Header -->

                                    <!-- Content -->
                                    <div style="padding: 10px 30px 70px 30px;">
                                        <p>{!! $content !!}</p>
                                    </div>
                                    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- END Container -->
        </td>
    </tr>
</table>