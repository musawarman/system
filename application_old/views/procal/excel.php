<?php
    header("Content-Disposition: attachment; filename=Procal_PO.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<table class="table table-striped table-bordered" border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
        <th colspan="8"><h2>PERHITUNGAN UNTUNG-RUGI PROJECT<br>
        PT JEDANKA GLOBAL SINERGI 										
        </h2></th>
    </tr>
    <tr>
        <th style="width:150px;text-align:right">Nama Project</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"><?=$namapr?></th>
        <th style="text-align:left;width:250px;" colspan="2">&nbsp;</th>
        <th style="width:150px;text-align:right">Tanggal</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"><?=date('d/m/Y')?></th>
    </tr>
    <tr>
        <th style="width:150px;text-align:right">Nama Customer</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"><?=$vnd?></th>
        <th style="text-align:left;width:250px;" colspan="2">&nbsp;</th>
        <th style="width:150px;text-align:right">Nama Sales</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"></th>
    </tr>
    <tr>
        <th style="width:150px;text-align:right">Nama Distributor</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"></th>
        <th style="text-align:left;width:250px;" colspan="2">&nbsp;</th>
        <th style="width:150px;text-align:right">No. PO</th>
        <th>:</th>
        <th style="text-align:left;width:250px;"><?=$data_po->po_number?></th>
    </tr>
    
</table>
<table class="table table-striped table-bordered" border="1" cellpadding="2" cellspacing="2" width="100%">
    <thead>
    <tr>
        <th style="text-align:center" colspan="4">Penjelasan Item Pekerjaan</th>
        <th style="text-align:center">QTY</th>
        <th style="text-align:center">Harga Item</th>
        <th style="text-align:center">Catatan</th>
        <th style="text-align:center">Total</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>A</td>
            <td colspan="3">NILAI JUAL PROJECT<br>
            
            <?php
                $qty=$total=$hargaitem='';
                $tot=0;
                $total_project=0;
                foreach($items[$data_po->quotes_id] as $itm)
                {
                    $qty.=strtok($itm->item_quantity,'.').' '.$itm->item_product_unit.'<br>';
                    $total.=($itm->item_quantity * $itm->item_price).'<br>';
                    $hargaitem.=($itm->item_price).'<br>';
                    echo '- '.$itm->item_name.'<br>';

                    $tot+=$itm->item_quantity * $itm->item_price;
                }
                $total_project=$tot;
                $persen= $tax[$data_po->quotes_id]->quote_tax_rate_amount / $tot * 100;
            ?>
            <br>
                Nilai Pajak Penjualan (PPN)
                    <br>
                    <b>TOTAL NILAI JUAL PROJECT</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br><?=$qty?></td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br><?=$hargaitem?></td>
            <td style="vertical-align:top;text-align:center">&nbsp;<br>&nbsp;<br><br><b>TOTAL</b><br><?=number_format($persen,2)?> %
            <br>
            <b>GRAND TOTAL</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br>
            <?=$total?><br>
                <b><?=($tot)?></b><br>
                <?=($tax[$data_po->quotes_id]->quote_tax_rate_amount)?>
            <br>
            <b><?=($amount[$data_po->quotes_id]->quote_total)?></b>
            </td>
        </tr>
        <tr>
            <td>B</td>
            <td colspan="3">BIAYA PELAKSANAAN PROJECT (COGS)<br>
            
            <?php
                $qty=$total=$hargaitem='';
                $tot=$grandtotal=0;
                $jlh_cogs=$pajak=0;

                $total_biaya=0;
                foreach($items[$data_po->quotes_id] as $itm)
                {
                    if(isset($cogs[$data_po->po_id][createSlug($itm->item_name)]))
                    {
                        $nilai=$cogs[$data_po->po_id][createSlug($itm->item_name)]->item_nominal;
                        $pajak=$cogs[$data_po->po_id][createSlug($itm->item_name)]->pajak;
                    }
                    else
                        $nilai=$pajak=0;

                    $jlh_cogs+=$nilai;

                    $qty.=strtok($itm->item_quantity,'.').' '.$itm->item_product_unit.'<br>';
                    $total.=($itm->item_quantity * $itm->item_price).'<br>';
                    $hargaitem.=($nilai).'<br>';
                    echo '- '.$itm->item_name.'<br>';

                    $tot+=$nilai;
                    
                }
                $total_biaya+=$tot;
                $grandtotal=$tot;
                $persen= $pajak * $tot /100;
            ?>
            <br>
                Nilai Pajak (PPN)
                    <br>
                    <b>SUB TOTAL BIAYA PELAKSANAAN PROJECT</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br><?=$qty?></td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br><?=$hargaitem?></td>
            <td style="vertical-align:top;text-align:center">&nbsp;<br>&nbsp;<br><br><b>TOTAL</b><br><?=$pajak?> %
            <br>
            <b>GRAND TOTAL</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br><?=$hargaitem?>
            <br>
            <b><?=($tot)?></b>
            <br><?=($persen)?>
            <br>
            <b><?=(($tot+$persen))?></b>
            </td>
        </tr>
        <tr>
            <td>C</td>
            <td colspan="3">BIAYA PAJAK - PAJAK<br>
            
            <?php
                $npajak=$persen=0;
                $jumlah_pajak=0;
                $nom_pajak=$n_persen='';
                if(isset($biaya_pajak[$data_po->po_id]))
                {
                    foreach($biaya_pajak[$data_po->po_id] as $itm)
                    {
                        $persen=$itm->pajak_persen;
                        $npajak=$itm->pajak_nominal;
                        $nom_pajak.=($npajak).'<br>';
                        $n_persen.=$persen.'%<br>';
                        $jumlah_pajak+=$npajak;
                ?>
                    <?=$itm->pajak_name?><br>
                <?php
                    }
                }
                $total_biaya+=$jumlah_pajak;
            ?>
                    <br>
                    <b>SUB TOTAL BIAYA PAJAK</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:center">&nbsp;<br>&nbsp;<?=$n_persen?>
            <br>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br>
            <b><?=$nom_pajak?></b>
            <br>
            <b><?=(($jumlah_pajak))?></b>
            </td>
        </tr>
        <tr>
            <td>D</td>
            <td colspan="3">BIAYA LAIN-LAIN<br>
            
            <?php
                    $n_lain=$persen=0;
                    $jumlah_lain=0;
                    $nom_pajak=$n_persen='';
                    if(isset($biaya_lain[$data_po->po_id]))
                    {
                        foreach($biaya_lain[$data_po->po_id] as $itm)
                        {
                            $persen=$itm->biaya_persen;
                            $n_lain=$itm->biaya_nominal;
                            $jumlah_lain+=$n_lain;
                            $nom_pajak.=($n_lain).'<br>';
                            $n_persen.=$persen.'%<br>';
                ?>
                    <?=$itm->biaya_name?><br>
                <?php
                        }
                    }
                    $total_biaya+=$jumlah_lain;
            ?>
                    <br>
                    <b>SUB TOTAL BIAYA LAIN-LAIN</b>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:center">&nbsp;<br>&nbsp;<?=$n_persen?>
            <br>
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br>
            <b><?=$nom_pajak?></b>
            <br>
            <b><?=(($jumlah_lain))?></b>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">TOTAL BIAYA PROJECT<br>
           
            </td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:center"></td>
            <td style="vertical-align:top;text-align:right"><?=(($total_biaya))?>
            </td>
        </tr>
        <tr>
            <?php
                $prof=$total_project-$total_biaya;
                $prsn=$prof / $total_project *100;
            ?>
            <td>E</td>
            <td colspan="3">PROYEKSI NILAI KEUNTUNGAN / MARGIN</td>
            <td style="vertical-align:top;text-align:right">&nbsp;<br></td>
            <td style="vertical-align:top;text-align:center"><?=(number_format($prsn,2))?> %</td>
            <td style="vertical-align:top;text-align:right"><?=(($prof))?></td>
            <td style="vertical-align:top;text-align:right"></td>
        </tr>
    </tbody>
</table>