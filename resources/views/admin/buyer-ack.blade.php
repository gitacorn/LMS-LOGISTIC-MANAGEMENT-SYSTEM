<table style="min-width:300px;" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td  style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:13px;color:#202020;line-height:1.5;padding:4px 0">
            Dear Team,<br><br>
            <?php echo (isset($supplierName) && !empty($supplierName) ? $supplierName : '')?> Purchase Order No. <?php echo (isset($poSalesInvoiceNo) && !empty($poSalesInvoiceNo) ? $poSalesInvoiceNo : '')?> consisting of <?php echo (isset($palletsBoxesType) && !empty($palletsBoxesType) ? $palletsBoxesType : '')?> <?php echo (isset($noOfPalletBox) && !empty($noOfPalletBox) ? $noOfPalletBox : '') ?> was delivered at <?php echo (isset($warehouseName) && !empty($warehouseName) ? $warehouseName : '')?>.
            <?php if (isset($withAttachment) && $withAttachment != false) {?>
            <br><br>Please check attached Goods In Document.
            <?php } ?>
            <br><br>
            <?php if (isset($warehouseComment) && !empty($warehouseComment)) {?>
            <b>Note : <?php echo $warehouseComment ?></b>
            <br><br>
            <?php }?>
            Regards,<br>
            LMS Team
            </td>
        </tr>
    </tbody>
</table>