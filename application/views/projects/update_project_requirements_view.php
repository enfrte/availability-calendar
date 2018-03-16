<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-6 col-lg-offset-3">
    <h1><?php echo $project_name ?> requirements</h1>
    <?php echo $showMessages; ?>
    <?php echo form_open('',array('role'=>'form'));?>

    <?php if(!empty($requirements)) : ?>

        <div class="form-group">
          <fieldset>
            <legend>Assign project requirements (Optional)</legend>
            <?php foreach($requirements as $visibile) : ?>
              <div>
                <input 
                  type="checkbox" 
                  id="visability_<?php echo $visibile->id ?>" 
                  name="requirements[]" 
                  value="<?php echo $visibile->id ?>" 
                  <?php echo (!is_null($visibile->checked) ? 'checked' : '') ?>>
                <label for="visability_<?php echo $visibile->id ?>"><?php echo $visibile->title ?></label>
              </div>
            <?php endforeach; ?>
          </fieldset>
        </div>

      <?php else: ?>
        
        <div class="panel panel-warning">
          <div class="panel-heading">Attention</div>
          <div class="panel-body">No project requirements have been created yet. To create a new requirements, go to <span class="label label-default">Menu > Project requirements > Create new requirements</span> or click <a href="<?php echo site_url('admin/requirements/create');?>">here.</a></div>
        </div>
      
      <?php endif; ?>

      <?php if(!empty($requirements)) : ?>
        <?php echo form_submit('submit', 'Update project requirements', 'class="btn btn-success btn-lg btn-block"');?>
      <?php endif; ?>

  </div>
</div>
