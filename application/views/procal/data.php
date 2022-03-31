<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th class="text-center">No</th>
        <th class="text-center">PO Number <br> Date</th>
        <th class="text-center">Nilai Project</th>
        <th class="text-center">COGS</th>
        <th class="text-center">Biaya Pajak</th>
        <th class="text-center">Biaya Lain</th>
        <th class="text-center">#</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $no=1;
    foreach($data_po as $item)
    {
    ?>
        <tr>
            <td style="vertical-align:top !important;" class="text-center"><?=$no?></td>
            <td style="vertical-align:top !important;" class="text-left">
                PO Number : <b><?=$item->po_number?></b><br><br>
                PO Date : <b><?=date('d/m/Y',strtotime($item->po_date))?></b><br><br>
                PO Status : <b><?=($item->status_po)?></b>
            </td>
            <td style="vertical-align:top !important;">
            <?php
            if(isset($items[$item->quotes_id]))
            {
                foreach($items[$item->quotes_id] as $itm)
                {
            ?>
                <div class="row" style="width:270px;font-size:10px;">
                    <div class="col-md-5"><?=$itm->item_name?></div>
                    <div class="col-md-2"><?=strtok($itm->item_quantity,'.')?> <?=$itm->item_product_unit?></div>
                    <div class="col-md-4 text-right"><b> <?=number_format($itm->item_quantity * $itm->item_price,0,',','.')?></b></div>
                </div>
            <?php
                }
            }
            ?>
                <div class="row" style="width:270px;font-size:10px;">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4" >PPN</div>
                    <?php
                    if(isset($tax[$item->quotes_id]))
                    {
                    ?>
                        <div class="col-md-4 text-right" ><b> <?=number_format($tax[$item->quotes_id]->quote_tax_rate_amount,0,',','.')?></b></div>
                    <?php
                    }
                    else
                    {
                    ?>
                        <div class="col-md-4 text-right" ><b> 0</b></div>
                    <?php 
                    }
                    ?>
                    
                </div>
                <div class="row" style="width:270px;font-size:10px;">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4" style="border-top:1px solid #ccc;">TOTAL</div>
                    <?php
                    if(isset($amount[$item->quotes_id]))
                    {
                    ?>
                    <div class="col-md-4 text-right" style="border-top:1px solid #ccc;"><b> <?=number_format($amount[$item->quotes_id]->quote_total,0,',','.')?></b></div>
                    <?php
                    }
                    else
                    {
                    ?>
                    <div class="col-md-4 text-right" style="border-top:1px solid #ccc;"><b> 0</b></div>
                    <?php
                    }
                    ?>
                </div>
            </td>
            <td style="vertical-align:top !important;">
                <?php
                $jlh_cogs=$pajak=0;
                if(isset($items[$item->quotes_id]))
                {
                    foreach($items[$item->quotes_id] as $itm)
                    {
                        if(isset($cogs[$item->po_id][createSlug($itm->item_name)]))
                        {
                            $nilai=$cogs[$item->po_id][createSlug($itm->item_name)]->item_nominal;
                            $pajak=$cogs[$item->po_id][createSlug($itm->item_name)]->pajak;
                        }
                        else
                            $nilai=$pajak=0;

                        $jlh_cogs+=$nilai;
                ?>
                    <div class="row" style="width:270px;font-size:10px;">
                        <div class="col-md-5"><?=$itm->item_name?></div>
                        <div class="col-md-2"><?=strtok($itm->item_quantity,'.')?> <?=$itm->item_product_unit?></div>
                        <div class="col-md-4 text-right"><b> <?=number_format($nilai,0,',','.')?></b></div>
                    </div>
                <?php
                    }
                    
                }
                $ppn=(($pajak * ($jlh_cogs==0 ? 1 : $jlh_cogs)/100));
            ?>
                <div class="row" style="width:270px;font-size:10px;">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4" >PPN</div>
                    <div class="col-md-4 text-right" ><b> <?=number_format($ppn,0,',','.')?></b></div>
                </div>
                <div class="row" style="width:270px;font-size:10px;">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4" style="border-top:1px solid #ccc;">TOTAL</div>
                    <div class="col-md-4 text-right" style="border-top:1px solid #ccc;"><b> <?=number_format(($jlh_cogs + $ppn),0,',','.')?></b></div>
                </div>
                <div class="row text-center" style="margin-top:20px;">
                    <a href="javascript:cogs(<?=$item->po_id?>,<?=$item->quotes_id?>)" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Add COGS</a>
                </div>
            </td>
            <td style="vertical-align:top !important;">
               <div class="row text-center" style="margin-left:0px;;margin-right:0px;">
                <?php
                    $npajak=$persen=0;
                    $jumlah_pajak=0;
                    if(isset($biaya_pajak[$item->po_id]))
                    {
                        foreach($biaya_pajak[$item->po_id] as $itm)
                        {
                            $persen=$itm->pajak_persen;
                            $npajak=$itm->pajak_nominal;
                            $jumlah_pajak+=$npajak;
                    ?>
                        <div class="row" style="width:270px;font-size:10px;">
                            <div class="col-md-5 text-left"><?=$itm->pajak_name?></div>
                            <div class="col-md-2 text-right"><?=$persen?>%</div>
                            <div class="col-md-4 text-right"><b> <?=number_format($npajak,0,',','.')?></b></div>
                        </div>
                    <?php
                    }
                }
                ?>
                    <div class="row" style="width:270px;font-size:10px;">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-4" style="border-top:1px solid #ccc;">TOTAL</div>
                        <div class="col-md-4 text-right" style="border-top:1px solid #ccc;"><b> <?=number_format($jumlah_pajak,0,',','.')?></b></div>
                    </div>
                    <a style="margin-top:20px;" href="javascript:pajak(<?=$item->po_id?>,<?=$item->quotes_id?>)"class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Add Pajak</a>
                </div> 
            </td>
            <td style="vertical-align:top !important;">
            <div class="row" style="width:200px;font-size:11px;"></div>
               <div class="row text-center" style="margin-left:0px;margin-right:0px;">
                    <div class="row text-center" style="margin-left:0px;">
                <?php
                    $n_lain=$persen=0;
                    $jumlah_lain=0;
                    if(isset($biaya_lain[$item->po_id]))
                    {
                        foreach($biaya_lain[$item->po_id] as $itm)
                        {
                            $persen=$itm->biaya_persen;
                            $n_lain=$itm->biaya_nominal;
                            $jumlah_lain+=$n_lain;
                ?>
                    <div class="row" style="width:270px;font-size:10px;">
                        <div class="col-md-5 text-left"><?=$itm->biaya_name?></div>
                        <div class="col-md-2 text-right"><?=$persen?>%</div>
                        <div class="col-md-4 text-right"><b> <?=number_format($n_lain,0,',','.')?></b></div>
                    </div>
                <?php
                        }
                    }
                ?>
                    <div class="row" style="width:270px;font-size:10px;">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-4" style="border-top:1px solid #ccc;">TOTAL</div>
                        <div class="col-md-4 text-right" style="border-top:1px solid #ccc;"><b> <?=number_format($jumlah_lain,0,',','.')?></b></div>
                    </div>

                    <a style="margin-top:20px;" href="javascript:biayalain(<?=$item->po_id?>,<?=$item->quotes_id?>)" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Add Biaya Lain</a>
                </div> 
            </td>
            <td style="vertical-align:top !important;;">
                    <a href="javascript:excelpo(<?=$item->po_id?>,<?=$item->quotes_id?>)" class="btn btn-xs btn-primary"><i class="fa fa-file-excel-o"></i></a>
            </td>
        </tr>
    <?php
    $no++;
    }
    ?>
    </tbody>
</table>