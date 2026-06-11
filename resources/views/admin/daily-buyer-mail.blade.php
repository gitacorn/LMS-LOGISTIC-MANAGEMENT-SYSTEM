<table style="min-width:300px;" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td  style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:13px;color:#202020;line-height:1.5;padding:4px 0">
            Dear Buyer & Warehouse Team,<br><br>
			This is to bring to your attention that the <?php echo (isset($palletsBoxesType) && !empty($palletsBoxesType) ? $palletsBoxesType : '')?> <?php echo (isset($noOfPalletBox) && !empty($noOfPalletBox) ? $noOfPalletBox : '') ?> for PO No. <?php echo (isset($poSalesInvoiceNo) && !empty($poSalesInvoiceNo) ? $poSalesInvoiceNo : '')?> from <?php echo (isset($supplierName) && !empty($supplierName) ? $supplierName : '')?> was scheduled for delivery at <?php echo (isset($warehouseName) && !empty($warehouseName) ? $warehouseName : '')?> on <?php echo (isset($buyerDeliveryDate) && !empty($buyerDeliveryDate) ? date('d/m/Y',strtotime($buyerDeliveryDate)) : '')?>.
			<br><br>
			We need confirmation on the following:
			<br><br>
			Buyer Team: Please verify with the supplier regarding the delivery status and update the new delivery date in the LMS if it has not yet been delivered.
			<br>
			Warehouse Team: If the delivery has been made, kindly clear the entry in the system.
			<br><br>
			Thank you for your prompt attention to this matter.
            <br><br>
            Regards,<br>
            LMS Team
            </td>
        </tr>
    </tbody>
</table>