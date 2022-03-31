<?php $this->load->helper('country'); ?>

<span class="vendor-address-street-line">
    <?php echo($vendor->vendor_address_1 ? htmlsc($vendor->vendor_address_1) . '<br>' : ''); ?>
</span>
<span class="vendor-address-street-line">
    <?php echo($vendor->vendor_address_2 ? htmlsc($vendor->vendor_address_2) . '<br>' : ''); ?>
</span>
<span class="vendor-adress-town-line">
    <?php echo($vendor->vendor_city ? htmlsc($vendor->vendor_city) . ' ' : ''); ?>
    <?php echo($vendor->vendor_state ? htmlsc($vendor->vendor_state) . ' ' : ''); ?>
    <?php echo($vendor->vendor_zip ? htmlsc($vendor->vendor_zip) : ''); ?>
</span>
<span class="vendor-adress-country-line">
    <?php echo($vendor->vendor_country ? '<br>' . get_country_name(trans('cldr'), $vendor->vendor_country) : ''); ?>
</span>
