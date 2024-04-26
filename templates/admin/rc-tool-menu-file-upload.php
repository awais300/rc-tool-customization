<?php
namespace EWA\RCTool\Admin;
?>

<div class="wrap">

    <h1><?php echo __('Upload/Import Pricing Sheet', 'rct-customization'); ?></h1>
<?php if($file_obj && !$file_obj->is_upload_successful($upload_details)):  ?>
     <div class="notice notice-error is-dismissable">
        <p><?php echo $file_obj->handle_upload_error($upload_details); ?></p>
    </div>
<?php endif; ?>

<?php if($file_obj && $file_obj->is_upload_successful($upload_details)): ?>
     <div class="notice notice-success is-dismissable">
        <p><?php _e( 'File imported.', 'rct-customization' ); ?></p>
    </div>
<?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="pricing_file" id="pricing_file" accept=".csv">
        <br />
        <br />
        <br />
         <?php wp_nonce_field( 'import_nonce', 'import_nonce_field' ); ?>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Import', 'rct-customization'); ?>">
    </form>
</div>