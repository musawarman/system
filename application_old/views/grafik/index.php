<div id="headerbar">

    <h1 class="headerbar-title">Data Grafik</h1>

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
        <div class="table-responsive" style="width:80%;margin:0 auto;">
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
            <div id="data"></div>
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
    $('#data').load('<?=site_url()?>/grafik/data/'+bulan+'/'+tahun);
}

</script>