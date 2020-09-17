<form id="submitConf" name="submitConf" action="#" method="post">
  <div class="table-responsive clearfix">
    <div class="form-group">
      <label for="form-field-1" class="col-sm-4 control-label">
        {l s='Pixel ID' mod='psfacebook'}
      </label>
      <div class="col-sm-6">
        <input type="text" minlength="15" maxlength="16" class="form-control" id="PS_PIXEL_ID" name="PS_PIXEL_ID" placeholder="{l s='Your facebook pixel ID' mod='psfacebook'}" value="{if isset($id_pixel) && !empty($id_pixel)}{$id_pixel|escape:'htmlall':'UTF-8'}{/if}" maxlength="16"/>
      </div>
    </div>
    <div class="form-group clear">
      <label for="form-field-1" class="col-sm-4 control-label">
        {l s='FBE Access Token' mod='psfacebook'}
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="PS_FBE_ACCESS_TOKEN" name="PS_FBE_ACCESS_TOKEN" placeholder="{l s='Your PS_FBE_ACCESS_TOKEN' mod='psfacebook'}" value="{if isset($access_token) && !empty($access_token)}{$access_token|escape:'htmlall':'UTF-8'}{/if}"/>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>

	<div class="panel-footer">
		<div class="btn-group pull-right">
			<button name="submitPixel" type="submit" class="btn btn-default" value="1">
        <i class="process-icon-save"></i> {l s='Save'  mod='psfacebook'}
      </button>
		</div>
	</div>
</form>
