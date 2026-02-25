<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" data-biolink-block-type="<?= $data->link->type ?>" class="col-12 my-<?= $data->biolink->settings->block_spacing ?? '2' ?>">
    <video class="w-100 link-round" title="<?= $data->link->settings->name ?>" <?= $data->link->settings->poster_url ? 'poster="' . $data->link->settings->poster_url . '"' : null ?> <?= $data->link->settings->video_controls ? 'controls' : null ?> <?= $data->link->settings->video_autoplay ? 'autoplay' : null ?> <?= $data->link->settings->video_loop ? 'loop' : null ?> <?= $data->link->settings->video_muted ? 'muted' : null ?> playsinline>
        <source src="<?= \Altum\Uploads::get_full_url('files') . $data->link->settings->file ?>">
    </video>
</div>
