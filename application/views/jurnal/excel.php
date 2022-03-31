<?php
    header("Content-Disposition: attachment; filename=Jurnal_".$bulan."_".$tahun.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<table class="table table-striped table-bordered" border="1" cellpadding="2" cellspacing="2" width="100%">
    <thead>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Account & Description</th>
        <th>#</th>
        <th>D</th>
        <th>C</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $no=1;
    foreach($payment as $item)
    {
        foreach($item as $v)
        {
            if(isset($invoice[$v->invoice_id]))
            {
                $inv=$invoice[$v->invoice_id];
                $st_inv=$inv->status_invoices;
                
                if($st_inv=='out')
                {
                    $ket='KAS<br>';
                    $ket.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penerimaan Invoice '.$inv->invoice_number;

                }
                else
                {
                    $ket='Pembayaran Invoice '.$inv->invoice_number.'<br>';
                    $ket.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KAS';
                    
                }
                $debit=($v->payment_amount).'<br>';
                $kredit='<br>'.($v->payment_amount);
        ?>
            <tr>
                <td  style="vertical-align:top !important;" class="text-center"><?=$no?></td>
                <td  style="vertical-align:top !important;" class="text-center"><?=date('d/m/Y',strtotime($v->payment_date))?></td>
                <td style="vertical-align:top !important;" ><?=$ket?></td>
                <td style="vertical-align:top !important;" ></td>
                <td  style="vertical-align:top !important;" class="text-right"><?=$debit?></td>
                <td  style="vertical-align:top !important;" class="text-right"><?=$kredit?></td>
            </tr>
        <?php
            $no++;
            }
        }
    }
    ?>
    </tbody>
</table>