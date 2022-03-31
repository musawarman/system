<div id="headerbar">

    <h1 class="headerbar-title">PERHITUNGAN UNTUNG-RUGI PROJECT</h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php _trans('submenu'); ?>
        </button>
        
    </div>

    <div class="headerbar-item pull-right visible-lg">
        
    </div>

    <div class="headerbar-item pull-right visible-lg">
       
    </div>

</div>


<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <div class="" style="width:80%;margin:0 auto;">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        &nbsp;
                    </div>
                    <div class="col-md-6">&nbsp;</div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Bulan</label>
                            <select class="form-control" id="bulan" name="bulan" onchange="loaddata()">
                                <?php
                                for($x=1;$x<=12;$x++)
                                {
                                    if($x==date('n'))
                                        echo '<option value="'.$x.'" selected="selected">'.date('F',strtotime(date('Y-'.$x))).'</option>';
                                    else
                                        echo '<option value="'.$x.'">'.date('F',strtotime(date('Y-'.$x))).'</option>';
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tahun</label>
                            <select class="form-control" id="tahun" name="tahun" onchange="loaddata()">
                                <?php
                                for($x=(date('Y')-1);$x<=date('Y');$x++)
                                {
                                    if($x==date('Y'))
                                        echo '<option value="'.$x.'" selected="selected">'.$x.'</option>';
                                    else
                                        echo '<option value="'.$x.'">'.$x.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive" style="width:100%;margin:0 auto;">
            <div id="data" style="width:95%;margin:0 auto;"></div>
        </div>

    </div>

</div>
<script>
$(document).ready(function(){
    loaddata();
});

function loaddata()
{
    var bulan=$('#bulan').val();
    var tahun=$('#tahun').val();
    $('#data').load('<?=site_url()?>/procal/data/'+bulan+'/'+tahun);
}
function cogs(po_id,quote_id)
{
    
    $('#data-cogs').load('<?=site_url()?>/procal/datacogs/'+po_id+'/'+quote_id);
    $('#cogs').modal('show');
}
function pajak(po_id,quote_id)
{
    $('#data-pajak').load('<?=site_url()?>/procal/datapajak/'+po_id+'/'+quote_id);
    $('#pajak').modal('show');
}
function biayalain(po_id,quote_id)
{
    $('#data-lain').load('<?=site_url()?>/procal/datalain/'+po_id+'/'+quote_id);
    $('#biayalain').modal('show');
}
function excelpo(po_id,quote_id)
{
    window.open('<?=site_url()?>/procal/excel/'+po_id+'/'+quote_id,'_blank');
}
</script>

<div id="cogs" class="modal" style="width:40% !important" role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content" action="<?=site_url()?>/procal/simpancogs" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title">BIAYA PELAKSANAAN PROJECT (COGS)</h4>
        </div>
        <div class="modal-body">
            <div id="data-cogs" ></div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="" type="submit">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
<div id="pajak" class="modal" style="width:40% !important" role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content" action="<?=site_url()?>/procal/simpanpajak" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title">BIAYA PAJAK</h4>
        </div>
        <div class="modal-body" style="float:left;width:100%">
            <div id="data-pajak" ></div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="" type="submit">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
<div id="biayalain" class="modal" style="width:40% !important" role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content" action="<?=site_url()?>/procal/simpanlain" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title">BIAYA LAIN-LAIN</h4>
        </div>
        <div class="modal-body" style="float:left;width:100%">
            <div id="data-lain" ></div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="" type="submit">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>