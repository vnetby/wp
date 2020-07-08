<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="content-box content-wpmf-ftp-import">
    <div class="ju-settings-option wpmf_width_100 p-d-20 btnoption">
        <div class="wpmf_row_full">
            <div id="wpmf_foldertree" class="wpmf-no-padding"></div>
            <div class="process_import_ftp_full" style="">
                <div class="process_import_ftp" data-w="0"></div>
            </div>
            <button type="button" id="import_button"
                    name="import_folder"
                    class="ju-button no-background orange-button waves-effect waves-light"><?php esc_html_e('Import Folder', 'wpmf'); ?></button>
            <span class="spinner" style="float: left;margin: 15px 10px 15px 6px;"></span>
            <span class="info_import"><?php esc_html_e('Imported !', 'wpmf'); ?></span>
        </div>
        <p class="description">
            <?php esc_html_e('Import folder structure and media from your
         server in the standard WordPress media manager', 'wpmf'); ?>
            <br><span class="text-orange">7z,bz2,gz,rar,tgz,zip,csv,doc,docx,ods,odt,pdf,
            pps,ppt,pptx,rtf,txt,xls,xlsx,bmp,psd,tif,tiff,mid,
            mp3,mp4,ogg,wma,3gp,avi,flv,m4v,mkv,mov,mpeg,mpg,swf,vob,wmv</span>
        </p>
    </div>
</div>