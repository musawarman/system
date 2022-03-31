<table class="table table-striped table-bordered">
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
                $debit=number_format($v->payment_amount,0,',','.').'<br>';
                $kredit='<br>'.number_format($v->payment_amount,0,',','.');
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