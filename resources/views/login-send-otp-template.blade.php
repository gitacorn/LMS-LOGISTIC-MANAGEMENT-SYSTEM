


										<table style="min-width:300px;" border="0" cellspacing="0" cellpadding="0">
  												<tbody>
														<tr>
                                                            <td  style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:13px;color:#202020;line-height:1.5;padding:4px 0">
                                                                Dear {{ isset($userName) ? $userName : 'User' }}, Your Login OTP is {{ ( isset($otpInfo) ? $otpInfo : '' )}} to access {{ config('constants.SITE_TITLE') }}.
                                                            </td>
                                                        </tr>
                     							</tbody>
                   						  </table>                                   