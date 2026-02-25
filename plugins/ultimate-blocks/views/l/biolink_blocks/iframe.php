<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" data-biolink-block-type="<?= $data->link->type ?>" class="col-12 my-<?= $data->biolink->settings->block_spacing ?? '2' ?>">
    <iframe src="<?= $data->link->location_url ?>" style="width: 100%; height: 500px; object-fit: contain; border:0;" class="w-100 rounded" loading="lazy"></iframe>
</div>
