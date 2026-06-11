<html>

<head>
</head>

<body>

    <div>
        <table width="100%" style="padding: 20px 20px 0 20px;font-family: 'Poppins', sans-serif;">
            <thead style="padding-bottom: 10px;">
                <tr>
                    <th style="width:20%; border: 0;">

                    </th>
                    <th style="width:60%; border: 0;">
                        <img src="<?php echo asset('images/logo.png') ?>" style="width:25%; padding-bottom:10px;">
                        <br />
                        <p style="font-size:12px;">
                        </p>
                    </th>
                    <th style="width:20%; text-align:right; border: 0;">
                    </th>
                </tr>
            </thead>
        </table>

        <div style="font-family: 'Poppins', sans-serif;">
            <h2 style="text-align:center;">SHIPMENT COLLECTION REQUEST FORM</h2>
        </div>
        <table border="1" cellpadding="5" style="width:100%;margin:10px 20px 20px 20px;text-align: left;border: 1px solid;font-family: 'Poppins', sans-serif;border-collapse:collapse;" cellspacing="2">
            <tbody>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;"><?php echo trans('messages.purchase-order-number') ?>:</th>
                    <td style="vertical-align:middle"><strong>41865, 41860, 42028</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px"><?php echo trans('messages.pick-up-address-of-suppliers-w-h') ?></th>
                    <td style="vertical-align:top;line-height:24px"><strong>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, ratione <br> Solec 81B/73A <br> 00-382 Warsazva <br> Polska</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px"><?php echo trans('messages.invoice-address-if-different-from-pick-up-address') ?></th>
                    <td style="vertical-align:top;line-height:24px"><strong>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, ratione <br> Solec 81B/73A <br> 00-382 Warsazva <br> Polska</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px"><?php echo trans('messages.collection-details') ?></th>
                    <td style="vertical-align:top;line-height:24px">No. Of Pallets: <strong>3</strong><br> Dimensions: <strong>120*80*150 120*80*150 120*80*150</strong><br>Total Weight With Pallets: <strong>1076.8</strong><br> Stackable? <strong>Please Select</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px"><?php echo trans('messages.goods-type') ?></th>
                    <td style="vertical-align:top;line-height:24px">Hazardous: <strong><img src="<?php echo asset('images/check.png') ?>" alt="" style="width:12px;"></strong><br> Non-Hazardous: <strong></strong><br>If Hazardous ( Hazment/ Non-Hazment ) Please Attach DG Note</td>
                </tr>
                <tr>
                    <th style="text-align:left;line-height:22px;vertical-align:middle;height:100px;font-size:20px" colspan="2"><?php echo trans('messages.customs-import-export') ?></th>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;"><?php echo trans('messages.if-customs-import') ?></th>
                    <td style="vertical-align:top;line-height:24px">Customs Imported By: <strong>Our Logistics</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;"><?php echo trans('messages.if-customs-export') ?></th>
                    <td style="vertical-align:top;line-height:24px">Customs Exported By: <strong>Our Logistics</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;"><?php echo trans('messages.pick-up-timings-warehouse-open-close-time') ?></th>
                    <td style="vertical-align:top;line-height:24px"><strong>9 to 4</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:100px"><?php echo trans('messages.required-pick-up-reference-for-transport-company-driver') ?></th>
                    <td style="vertical-align:middle;line-height:24px"><strong></strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;"><?php echo trans('messages.contact-info-warehouse-logistics') ?></th>
                    <td style="vertical-align:top;line-height:24px"><strong></strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:170px"><?php echo trans('messages.delivery-address') ?>:</th>
                    <td style="vertical-align:top;line-height:27px"><strong>Virtual Works 360 Ltd <br> Unit 28 Brick Knoll Park, Ashley Road <br> AL15UG st Albans <br> United KingdomUnit 2B Brick Knoll Park <br> Ashley Road, St Albans, ALL 5UG <br> UK</strong></td>
                </tr>
            </tbody>

        </table>
    </div>



</body>

</html>