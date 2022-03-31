
<script type="text/javascript">
    $(function () {
        $("#supplier_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<form method="post" action="<?=site_url('supplier/proses/'.$id)?>">

    
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('supplier_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden">
        

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        <?php _trans('personal_information'); ?>

                        <div class="pull-right">
                            <label for="supplier_active" class="control-label">
                                <?php _trans('active_client'); ?>
                                <?php
                                if($id==-1)
                                    echo '<input id="supplier_active" name="supplier_active" checked type="checkbox" value="1">';
                                else
                                {
                                    if($det[0]->supplier_active==1)
                                        echo '<input id="supplier_active" name="supplier_active" checked type="checkbox" value="1">';
                                    else
                                        echo '<input id="supplier_active" name="supplier_active" type="checkbox" value="1">';
                                }
                                ?>
                            </label>
                        </div>
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="supplier_name">
                                Supplier Name
                            </label>
                            <input id="supplier_name" name="supplier_name" type="text" class="form-control" required
                                   autofocus
                                   value="<?php
                                    if($id!=-1)
                                    {
                                        echo $det[0]->supplier_name;
                                    }
                                   ?>">
                        </div>

                        <div class="form-group">
                            <label for="supplier_surname">
                                <?php _trans('supplier_surname_optional'); ?>
                            </label>
                            <input id="supplier_surname" name="supplier_surname" type="text" class="form-control"
                                   value="<?php
                                    if($id!=-1)
                                    {
                                        echo $det[0]->supplier_surname;
                                    }
                                   ?>">
                        </div>

                        <div class="form-group no-margin">
                            <label for="supplier_language">
                                <?php _trans('language'); ?>
                            </label>
                            <select name="supplier_language" id="supplier_language" class="form-control simple-select">
                                <option value="system">
                                    <?php _trans('use_system_language') ?>
                                </option>
                                <?php foreach ($languages as $language) {
                                    if($id!=-1)
                                    {
                                    ?>
                                        <option value="<?php echo $language; ?>" <?=($language==$det[0]->supplier_language ? 'selected="selected"' : '')?>>
                                            <?php echo ucfirst($language); ?>
                                        </option>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <option value="<?php echo $language; ?>">
                                            <?php echo ucfirst($language); ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
             <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('personal_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_web">Contact Person Name</label>

                            <div class="controls">
                                <input type="text" name="supplier_contact_person_name" id="supplier_contact_person_name" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_contact_person_name;
                                            }
                                        ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="supplier_gender"><?php _trans('gender'); ?></label>

                            <div class="controls">
                                <select name="supplier_gender" id="supplier_gender" class="form-control simple-select">
                                    <?php
                                    $genders = array(
                                        trans('gender_male'),
                                        trans('gender_female'),
                                        trans('gender_other'),
                                    );
                                    foreach ($genders as $key => $val) { 
                                        if($id!=-1)
                                        {
                                    ?>
                                            <option value=" <?php echo $key; ?>" <?=($key==$det[0]->supplier_gender ? 'selected="selected"' : '')?>>
                                                <?php echo $val; ?>
                                            </option>
                                    <?php
                                        }   
                                        else
                                        {
                                    ?>
                                            <option value=" <?php echo $key; ?>">
                                                <?php echo $val; ?>
                                            </option>
                                    <?php
                                        } 
                                    ?>
                                        
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="supplier_birthdate"><?php _trans('birthdate'); ?></label>
                            <?php
                            // $bdate = $this->mdl_clients->form_value('supplier_birthdate');
                            // } else {
                                // }
                                if($id!=-1)
                                {
                                    $bdate=$det[0]->supplier_birthdate;
                                    if ($bdate && $bdate != "0000-00-00")
                                        $bdate = date_from_mysql($bdate);
                                    else
                                        $bdate='';
                                }
                                else
                                    $bdate = '';
                            ?>
                            <div class="input-group">
                                <input type="text" name="supplier_birthdate" id="supplier_birthdate"
                                       class="form-control datepicker"
                                       value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                       

                       
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('address'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_address_1"><?php _trans('street_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_address_1" id="supplier_address_1" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_address_1;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_address_2"><?php _trans('street_address_2'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_address_2" id="supplier_address_2" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_address_2;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_city"><?php _trans('city'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_city" id="supplier_city" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_city;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_state"><?php _trans('state'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_state" id="supplier_state" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_state;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_zip"><?php _trans('zip_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_zip" id="supplier_zip" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_zip;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_country"><?php _trans('country'); ?></label>

                            <div class="controls">
                                <select name="supplier_country" id="supplier_country" class="form-control">
                                    <option value=""><?php _trans('none'); ?></option>
                                    <?php foreach ($countries as $cldr => $country) { ?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>
                                        ><?php echo $country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                       
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('contact_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_phone"><?php _trans('phone_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_phone" id="supplier_phone" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_phone;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_fax"><?php _trans('fax_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_fax" id="supplier_fax" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_fax;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_mobile"><?php _trans('mobile_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_mobile" id="supplier_mobile" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_mobile;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_email"><?php _trans('email_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_email" id="supplier_email" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_email;
                                            }
                                        ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_web"><?php _trans('web_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_web" id="supplier_web" class="form-control"
                                       value="<?php
                                            if($id!=-1)
                                            {
                                                echo $det[0]->supplier_web;
                                            }
                                        ?>">
                            </div>
                        </div>

                        
                    </div>

                </div>

            </div>
        </div>

    </div>
</form>
