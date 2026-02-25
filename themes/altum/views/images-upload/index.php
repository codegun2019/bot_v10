<?php defined('ALTUMCODE') || die(); ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0 text-truncate">
            <h1 class="h4 m-0 text-truncate"><i class="fas fa-fw fa-xs fa-file-image mr-1"></i> <?= l('images_upload.title') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('images_upload.description') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex flex-wrap gap-3 d-print-none">
            <div>
                <a href="<?= url('images-upload?' . $data->filters->get_get() . '&view=grid') ?>" class="btn btn-light dropdown-toggle-simple <?= $data->view_mode === 'grid' ? 'active' : null ?>" data-toggle="tooltip" title="<?= l('images_upload.view_grid') ?>">
                    <i class="fas fa-fw fa-sm fa-th"></i>
                </a>
            </div>

            <div>
                <a href="<?= url('images-upload?' . $data->filters->get_get() . '&view=list') ?>" class="btn btn-light dropdown-toggle-simple <?= $data->view_mode === 'list' ? 'active' : null ?>" data-toggle="tooltip" title="<?= l('images_upload.view_list') ?>">
                    <i class="fas fa-fw fa-sm fa-list"></i>
                </a>
            </div>

            <?php if($data->view_mode === 'list'): ?>
            <div>
                <button id="bulk_enable" type="button" class="btn btn-light" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>">
                    <i class="fas fa-fw fa-sm fa-list"></i>
                </button>

                <div id="bulk_group" class="btn-group d-none" role="group">
                    <div class="btn-group dropdown" role="group">
                        <button id="bulk_actions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                            <?= l('global.bulk_actions') ?> <span id="bulk_counter" class="d-none"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="bulk_actions">
                            <button type="button" class="dropdown-item" data-bulk-action="copy_links"><i class="fas fa-fw fa-sm fa-copy mr-2"></i> <?= l('images_upload.bulk_copy_links') ?></button>
                            <button type="button" class="dropdown-item" data-bulk-action="delete"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></button>
                        </div>
                    </div>

                    <button id="bulk_disable" type="button" class="btn btn-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fas fa-fw fa-times"></i></button>
                </div>
            </div>
            <?php endif ?>

            <div>
                <div class="dropdown">
                    <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-dark' : 'btn-light' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if($data->filters->has_applied_filters): ?>
                                <a href="<?= url('images-upload?view=' . $data->view_mode) ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <input type="hidden" name="view" value="<?= $data->view_mode ?>" />

                            <div class="form-group px-4">
                                <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <input type="hidden" name="search_by" value="title" />

                            <div class="form-group px-4">
                                <label for="filters_mime_type" class="small"><?= l('images_upload.mime_type') ?></label>
                                <select name="mime_type" id="filters_mime_type" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <option value="image/png" <?= isset($data->filters->filters['mime_type']) && $data->filters->filters['mime_type'] == 'image/png' ? 'selected="selected"' : null ?>>image/png</option>
                                    <option value="image/jpeg" <?= isset($data->filters->filters['mime_type']) && $data->filters->filters['mime_type'] == 'image/jpeg' ? 'selected="selected"' : null ?>>image/jpeg</option>
                                    <option value="image/gif" <?= isset($data->filters->filters['mime_type']) && $data->filters->filters['mime_type'] == 'image/gif' ? 'selected="selected"' : null ?>>image/gif</option>
                                    <option value="image/webp" <?= isset($data->filters->filters['mime_type']) && $data->filters->filters['mime_type'] == 'image/webp' ? 'selected="selected"' : null ?>>image/webp</option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_extension" class="small"><?= l('images_upload.extension') ?></label>
                                <input type="text" name="extension" id="filters_extension" class="form-control form-control-sm" value="<?= $data->filters->filters['extension'] ?? '' ?>" placeholder="png" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_date_start" class="small"><?= l('images_upload.date_start') ?></label>
                                <input type="date" name="date_start" id="filters_date_start" class="form-control form-control-sm" value="<?= $data->filters->get['date_start'] ?? '' ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_date_end" class="small"><?= l('images_upload.date_end') ?></label>
                                <input type="date" name="date_end" id="filters_date_end" class="form-control form-control-sm" value="<?= $data->filters->get['date_end'] ?? '' ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="imgbb_upload_id" <?= $data->filters->order_by == 'imgbb_upload_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="size_bytes" <?= $data->filters->order_by == 'size_bytes' ? 'selected="selected"' : null ?>><?= l('images_upload.size_bytes') ?></option>
                                    <option value="width" <?= $data->filters->order_by == 'width' ? 'selected="selected"' : null ?>><?= l('images_upload.width') ?></option>
                                    <option value="height" <?= $data->filters->order_by == 'height' ? 'selected="selected"' : null ?>><?= l('images_upload.height') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                                <select name="order_type" id="filters_order_type" class="custom-select custom-select-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                                <select name="results_per_page" id="filters_results_per_page" class="custom-select custom-select-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-light" onclick="location.reload();" data-toggle="tooltip" title="<?= l('images_upload.refresh') ?>">
                    <i class="fas fa-fw fa-sm fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .upload-zone {
            width: 100%;
            min-height: 250px;
            border: 2px dashed #cbd5e0;
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: #f8fafc;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            padding: 2rem;
        }

        .upload-zone:hover, .upload-zone.drag-over {
            border-color: var(--primary);
            background: #ebf8ff;
        }

        .upload-zone-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .upload-zone-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .upload-zone-subtitle {
            color: #718096;
            font-size: 1rem;
        }

        #image_preview_container {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: white;
            z-index: 10;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        #image_preview_img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .preview-remove-btn {
            margin-top: 1rem;
        }
    </style>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form id="upload_form" action="<?= url('images-upload/upload') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="global_token" value="<?= \Altum\Csrf::get('global_token') ?>" />

                <div id="drop_zone" class="upload-zone">
                    <input id="image" type="file" name="image" class="d-none" accept="image/*" multiple />

                    <div id="upload_ui_default" class="text-center">
                        <div class="upload-zone-icon">
                            <i class="fas fa-file-image"></i>
                        </div>
                        <div class="upload-zone-title"><?= l('images_upload.drop_title') ?></div>
                        <div class="upload-zone-subtitle"><?= l('images_upload.drop_subtitle') ?></div>
                        <small class="text-muted mt-2 d-block"><?= l('images_upload.drop_info') ?></small>
                    </div>

                    <div id="image_preview_container">
                        <img id="image_preview_img" src="#" alt="Preview" />
                        <button type="button" id="remove_image_btn" class="btn btn-sm btn-outline-danger preview-remove-btn">
                            <i class="fas fa-times mr-1"></i> <?= l('global.cancel') ?>
                        </button>
                    </div>
                </div>

                <div id="upload_queue_container" class="d-none mt-3">
                    <div class="small text-muted mb-2" id="upload_queue_summary"></div>
                    <div id="upload_queue_list"></div>
                </div>

                <button type="submit" name="submit" id="submit_btn" class="btn btn-primary btn-block mt-3 d-none"><?= l('global.submit') ?></button>
            </form>
        </div>
    </div>

    <div id="upload_result_container" class="d-none mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div id="recent_uploads_strip" class="d-none mb-3">
                    <div class="small text-muted mb-2"><?= l('images_upload.recent_uploads') ?></div>
                    <div id="recent_uploads_thumbs" class="d-flex flex-row flex-nowrap overflow-auto"></div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                        <div class="d-flex flex-column align-items-center bg-gray-50 p-3 rounded">
                            <img id="upload_preview" src="" class="img-fluid rounded shadow-sm mb-3" style="max-height: 400px; object-fit: contain;" alt="Preview" />
                            <a id="btn_url_viewer" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt mr-1"></i> <?= l('images_upload.open_viewer') ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small font-weight-bold"><?= l('images_upload.embed_codes') ?></span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyAllEmbeds(this)">
                                <i class="fas fa-copy mr-1"></i> <?= l('images_upload.copy_all_embeds') ?>
                            </button>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small mb-1"><?= l('images_upload.embed_preset') ?></label>
                            <select id="embed_preset" class="custom-select custom-select-sm" onchange="applyEmbedPreset()">
                                <option value="original"><?= l('images_upload.embed_preset_original') ?></option>
                                <option value="thumb"><?= l('images_upload.embed_preset_thumb') ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold"><?= l('images_upload.direct_link') ?></label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="embed_direct" class="form-control" readonly />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('embed_direct', this)"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold"><?= l('images_upload.html_image') ?></label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="embed_html" class="form-control" readonly />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('embed_html', this)"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold"><?= l('images_upload.markdown') ?></label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="embed_markdown" class="form-control" readonly />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('embed_markdown', this)"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold"><?= l('images_upload.bbcode') ?></label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="embed_bbcode" class="form-control" readonly />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('embed_bbcode', this)"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($data->view_mode === 'grid'): ?>
        <div class="row">
            <?php if(empty($data->uploads)): ?>
                <div class="col-12">
                    <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                        'filters_get' => $data->filters->get ?? [],
                        'name' => 'images_upload',
                        'has_secondary_text' => true,
                        'has_wrapper' => false,
                    ]); ?>
                </div>
            <?php else: ?>
                <?php foreach($data->uploads as $row): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <a href="<?= $row->url ?>" target="_blank" class="d-block">
                                <img src="<?= $row->thumb_url ?: $row->url ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= $row->title ?>" loading="lazy" />
                            </a>

                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="text-truncate pr-2" style="max-width: 75%;">
                                        <div class="small text-muted">#<?= (int) $row->imgbb_upload_id ?></div>
                                        <div class="font-weight-bold text-truncate"><?= $row->title ?: l('images_upload.view_image') ?></div>
                                    </div>

                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" data-boundary="viewport">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <button type="button" class="dropdown-item btn-open-panel" data-row='<?= json_encode($row) ?>'>
                                                <i class="fas fa-eye fa-sm mr-2 text-primary"></i> <?= l('images_upload.show_embeds') ?>
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item btn-copy-link" data-url="<?= $row->url ?>">
                                                <i class="fas fa-link fa-sm mr-2"></i> <?= l('images_upload.copy_link') ?>
                                            </button>
                                            <?php if($row->url_viewer): ?>
                                                <a class="dropdown-item" href="<?= $row->url_viewer ?>" target="_blank" rel="noreferrer">
                                                    <i class="fas fa-external-link-alt fa-sm mr-2"></i> <?= l('images_upload.open_viewer') ?>
                                                </a>
                                            <?php endif ?>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger" onclick="deleteImage(<?= $row->imgbb_upload_id ?>, this)">
                                                <i class="fas fa-trash-alt fa-sm mr-2"></i> <?= l('global.delete') ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <span class="badge badge-success"><?= \Altum\Date::get($row->datetime, 1) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>

        <div class="mt-3">
            <?= $data->pagination ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <form id="bulk_form" action="<?= url('images-upload/bulk') ?>" method="post" role="form">
            <input type="hidden" name="global_token" value="<?= \Altum\Csrf::get('global_token') ?>" />
            <input type="hidden" name="type" value="" data-bulk-type />

            <div class="table-responsive table-custom-container">
                <table class="table table-custom mb-0" id="uploads_table">
                <thead>
                    <tr>
                        <th data-bulk-table class="d-none">
                            <div class="custom-control custom-checkbox">
                                <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                                <label class="custom-control-label" for="bulk_select_all"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th><?= l('images_upload.view_image') ?></th>
                        <th><?= l('global.datetime') ?></th>
                        <th><?= l('images_upload.actions') ?></th>
                        <?php if((int) $this->user->type === 1): ?>
                            <th><?= l('global.delete') ?></th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data->uploads)): ?>
                        <tr>
                            <td colspan="<?= (int) $this->user->type === 1 ? 6 : 5 ?>">
                                <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                                    'filters_get' => $data->filters->get ?? [],
                                    'name' => 'images_upload',
                                    'has_secondary_text' => true,
                                    'has_wrapper' => false,
                                ]); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data->uploads as $row): ?>
                            <tr data-row-id="<?= $row->imgbb_upload_id ?>">
                                <td data-bulk-table class="d-none">
                                    <div class="custom-control custom-checkbox">
                                        <input id="selected_imgbb_upload_id_<?= $row->imgbb_upload_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->imgbb_upload_id ?>" />
                                        <label class="custom-control-label" for="selected_imgbb_upload_id_<?= $row->imgbb_upload_id ?>"></label>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light"><?= (int) $row->imgbb_upload_id ?></span>
                                </td>
                                <td>
                                    <a href="<?= $row->url ?>" target="_blank" title="<?= l('images_upload.view_image') ?>">
                                        <img src="<?= $row->thumb_url ?: $row->url ?>" class="rounded shadow-sm" style="width: 64px; height: 64px; object-fit: cover;" alt="<?= $row->title ?>" loading="lazy" />
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-success"><?= \Altum\Date::get($row->datetime, 1) ?></span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" data-boundary="viewport">
                                            <?= l('images_upload.actions') ?>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button type="button" class="dropdown-item btn-open-panel" data-row='<?= json_encode($row) ?>'>
                                                <i class="fas fa-eye fa-sm mr-2 text-primary"></i> <?= l('images_upload.show_embeds') ?>
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item btn-copy-link" data-url="<?= $row->url ?>">
                                                <i class="fas fa-link fa-sm mr-2"></i> <?= l('images_upload.copy_link') ?>
                                            </button>
                                            <?php if($row->url_viewer): ?>
                                                <a class="dropdown-item" href="<?= $row->url_viewer ?>" target="_blank" rel="noreferrer">
                                                    <i class="fas fa-external-link-alt fa-sm mr-2"></i> <?= l('images_upload.open_viewer') ?>
                                                </a>
                                            <?php endif ?>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger" onclick="deleteImage(<?= $row->imgbb_upload_id ?>, this)">
                                                <i class="fas fa-trash-alt fa-sm mr-2"></i> <?= l('global.delete') ?>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <?php if((int) $this->user->type === 1): ?>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($row->delete_url)): ?>
                                                <a href="<?= $row->delete_url ?>" target="_blank" rel="noreferrer" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" title="Delete from ImgBB Account (External Link)">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif ?>

                                            <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="deleteImage(<?= $row->imgbb_upload_id ?>, this)" data-toggle="tooltip" title="<?= l('global.delete') ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3">
            <?= $data->pagination ?>
        </div>
    </div>
    <?php endif ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    'use strict';

    const MAX_FILE_BYTES = 3 * 1000 * 1000;

    const dropZone = document.querySelector('#drop_zone');
    const fileInput = document.querySelector('#image');
    const previewContainer = document.querySelector('#image_preview_container');
    const previewImg = document.querySelector('#image_preview_img');
    const removeBtn = document.querySelector('#remove_image_btn');
    const submitBtn = document.querySelector('#submit_btn');
    const defaultUI = document.querySelector('#upload_ui_default');

    const queueContainer = document.querySelector('#upload_queue_container');
    const queueSummary = document.querySelector('#upload_queue_summary');
    const queueList = document.querySelector('#upload_queue_list');

    let uploadQueue = [];
    let isUploading = false;
    let stopRequested = false;

    let recentUploads = [];

    dropZone.addEventListener('click', () => fileInput.click());

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        resetUploadUI();
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = Array.from(dt.files || []);
        if (files.length) {
            enqueueFiles(files);
        }
    });

    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files || []);
        if (files.length) {
            enqueueFiles(files);
        }
    });

    function enqueueFiles(files) {
        if(isUploading) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Upload is in progress. Please wait until it finishes.'
            });
            return;
        }

        const accepted = [];
        const rejected = [];

        for (const file of files) {
            const isImage = file && file.type && file.type.startsWith('image/');
            const sizeOk = file && typeof file.size === 'number' && file.size > 0 && file.size <= MAX_FILE_BYTES;

            if (!isImage) {
                rejected.push({ file, reason: 'invalid_type' });
                continue;
            }

            if (!sizeOk) {
                rejected.push({ file, reason: 'too_large' });
                continue;
            }

            accepted.push(file);
        }

        if (rejected.length) {
            const first = rejected[0];
            let message = 'Invalid file.';
            if (first.reason === 'invalid_type') message = 'Please select image files only.';
            if (first.reason === 'too_large') message = 'File size must be 3MB or less.';

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        if (!accepted.length) {
            return;
        }

        for (const file of accepted) {
            uploadQueue.push({
                id: 'q_' + Date.now().toString(36) + '_' + Math.random().toString(16).slice(2),
                file,
                status: 'queued',
                progress: 0,
                error: null,
                result: null
            });
        }

        renderQueue();

        previewFirst(accepted[0]);

        submitBtn.classList.remove('d-none');

        fileInput.value = '';
    }

    function previewFirst(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'flex';
            defaultUI.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function resetUploadUI() {
        fileInput.value = '';
        previewContainer.style.display = 'none';
        defaultUI.style.display = 'block';
        submitBtn.classList.add('d-none');

        uploadQueue = [];
        isUploading = false;
        stopRequested = false;

        renderQueue();
    }

    function renderQueue() {
        if (!uploadQueue.length) {
            queueContainer.classList.add('d-none');
            queueSummary.textContent = '';
            queueList.innerHTML = '';
            return;
        }

        queueContainer.classList.remove('d-none');

        const total = uploadQueue.length;
        const done = uploadQueue.filter(i => i.status === 'done').length;
        const failed = uploadQueue.filter(i => i.status === 'error').length;
        const uploading = uploadQueue.filter(i => i.status === 'uploading').length;

        queueSummary.textContent = `Files: ${total} | Uploading: ${uploading} | Done: ${done} | Errors: ${failed}`;

        queueList.innerHTML = '';

        for (const item of uploadQueue) {
            const row = document.createElement('div');
            row.className = 'mb-3';

            const safeName = item.file && item.file.name ? item.file.name : 'file';

            let badgeClass = 'badge-light';
            if (item.status === 'queued') badgeClass = 'badge-secondary';
            if (item.status === 'uploading') badgeClass = 'badge-primary';
            if (item.status === 'done') badgeClass = 'badge-success';
            if (item.status === 'error') badgeClass = 'badge-danger';

            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-truncate mr-2" style="max-width: 70%;">
                        <strong>${escapeHtml(safeName)}</strong>
                        <span class="badge ${badgeClass} ml-2">${item.status}</span>
                    </div>
                    <div class="text-muted small">${Math.round(item.progress)}%</div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar ${item.status === 'error' ? 'bg-danger' : item.status === 'done' ? 'bg-success' : 'bg-primary'}" role="progressbar" style="width: ${Math.max(0, Math.min(100, item.progress))}%"></div>
                </div>
                ${item.error ? `<div class="small text-danger mt-1">${escapeHtml(item.error)}</div>` : ''}
            `;

            queueList.appendChild(row);
        }
    }

    function escapeHtml(text) {
        return String(text)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    window.addEventListener('load', () => {
        let urlParams = new URLSearchParams(window.location.search);
        let lastId = urlParams.get('last_id');

        if (lastId) {
            let row = document.querySelector(`tr[data-row-id="${lastId}"]`);
            if (row) {
                let openBtn = row.querySelector('.btn-open-panel');
                if (openBtn) openBtn.click();
            }
        }
    });

    document.querySelector('#upload_form').addEventListener('submit', event => {
        event.preventDefault();

        if(!uploadQueue.length) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Please select at least one image.'
            });
            return;
        }

        if(isUploading) {
            return;
        }

        stopRequested = false;
        isUploading = true;

        submitBtn.disabled = true;

        startNextUpload();
    });

    function startNextUpload() {
        if(stopRequested) {
            isUploading = false;
            submitBtn.disabled = false;
            return;
        }

        const next = uploadQueue.find(i => i.status === 'queued');
        if(!next) {
            isUploading = false;
            submitBtn.disabled = false;

            const hasError = uploadQueue.some(i => i.status === 'error');
            if(!hasError) {
                resetUploadUI();
            } else {
                renderQueue();
            }

            return;
        }

        next.status = 'uploading';
        next.progress = 0;
        next.error = null;
        renderQueue();

        const form = document.querySelector('#upload_form');
        const formData = new FormData();
        formData.append('global_token', form.querySelector('input[name="global_token"]').value);
        formData.append('image', next.file, next.file.name);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', e => {
            if (e.lengthComputable) {
                next.progress = Math.round((e.loaded / e.total) * 100);
                renderQueue();
            }
        });

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== 4) return;

            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                next.status = 'error';
                next.error = 'Invalid server response.';
                next.progress = 0;
                renderQueue();

                isUploading = false;
                submitBtn.disabled = false;

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: next.error
                });
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300 && response && response.success) {
                next.status = 'done';
                next.progress = 100;
                next.result = response.data || null;
                renderQueue();

                if (response.data) {
                    recentUploads.unshift(response.data);
                    recentUploads = recentUploads.slice(0, 10);
                    renderRecentUploadsStrip();
                    selectRecentUpload(response.data.imgbb_upload_id);

                    let urlParams = new URLSearchParams(window.location.search);
                    let currentPage = parseInt(urlParams.get('page')) || 1;

                    const uploadsTableBody = document.querySelector('#uploads_table tbody');
                    if(uploadsTableBody && currentPage === 1) {
                        prependToTable(response.data);
                    }

                    const uploadsGrid = document.querySelector('#uploads_grid');
                    if(uploadsGrid && currentPage === 1) {
                        prependToGrid(response.data);
                    }
                }

                startNextUpload();
                return;
            }

            next.status = 'error';
            next.progress = 0;
            next.error = (response && response.error && response.error.message) ? response.error.message : (response && response.message ? response.message : 'Upload failed.');
            renderQueue();

            stopRequested = true;
            isUploading = false;
            submitBtn.disabled = false;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: next.error
            });
        };

        xhr.send(formData);
    }

    function renderRecentUploadsStrip() {
        const strip = document.querySelector('#recent_uploads_strip');
        const thumbs = document.querySelector('#recent_uploads_thumbs');

        if(!strip || !thumbs) return;

        if(!recentUploads.length) {
            strip.classList.add('d-none');
            thumbs.innerHTML = '';
            return;
        }

        strip.classList.remove('d-none');
        thumbs.innerHTML = '';

        recentUploads.slice(0, 10).forEach(item => {
            const id = item.imgbb_upload_id || 'new';
            const thumbUrl = item.thumb && item.thumb.url ? item.thumb.url : item.url;

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-light p-1 mr-2 recent-upload-thumb';
            button.setAttribute('data-id', String(id));
            button.style.borderRadius = '0.75rem';

            button.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <img src="${thumbUrl}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:0.5rem;" loading="lazy" />
                    <small class="mt-1 text-muted">#${id}</small>
                </div>
            `;

            button.addEventListener('click', () => selectRecentUpload(id));

            thumbs.appendChild(button);
        });

        /* Highlight currently selected */
        if(strip.getAttribute('data-selected-id')) {
            setSelectedRecentThumb(strip.getAttribute('data-selected-id'));
        }
    }

    function setSelectedRecentThumb(selectedId) {
        document.querySelectorAll('.recent-upload-thumb').forEach(btn => {
            const isActive = btn.getAttribute('data-id') === String(selectedId);
            btn.classList.toggle('border', isActive);
            btn.classList.toggle('border-primary', isActive);
        });
    }

    function selectRecentUpload(imgbb_upload_id) {
        const found = recentUploads.find(i => String(i.imgbb_upload_id) === String(imgbb_upload_id));
        if(!found) return;

        const strip = document.querySelector('#recent_uploads_strip');
        if(strip) strip.setAttribute('data-selected-id', String(imgbb_upload_id));
        setSelectedRecentThumb(String(imgbb_upload_id));

        openInPanel(found);
    }

    function applyEmbedPreset() {
        const resultContainer = document.querySelector('#upload_result_container');
        if(!resultContainer) return;

        const presetSelect = document.querySelector('#embed_preset');
        const preset = presetSelect ? presetSelect.value : 'original';

        const originalUrl = resultContainer.dataset.originalUrl;
        const thumbUrl = resultContainer.dataset.thumbUrl || originalUrl;

        const url = preset === 'thumb' ? thumbUrl : originalUrl;
        if(!url) return;

        const embedDirect = document.querySelector('#embed_direct');
        const embedHtml = document.querySelector('#embed_html');
        const embedMarkdown = document.querySelector('#embed_markdown');
        const embedBbcode = document.querySelector('#embed_bbcode');

        if(embedDirect) embedDirect.value = url;
        if(embedHtml) embedHtml.value = `<img src="${url}" alt="image" />`;
        if(embedMarkdown) embedMarkdown.value = `![image](${url})`;
        if(embedBbcode) embedBbcode.value = `[img]${url}[/img]`;
    }

    function copyAllEmbeds(btn) {
        const parts = [];

        const direct = document.querySelector('#embed_direct')?.value || '';
        const html = document.querySelector('#embed_html')?.value || '';
        const markdown = document.querySelector('#embed_markdown')?.value || '';
        const bbcode = document.querySelector('#embed_bbcode')?.value || '';

        if(direct) parts.push('Direct: ' + direct);
        if(html) parts.push('HTML: ' + html);
        if(markdown) parts.push('Markdown: ' + markdown);
        if(bbcode) parts.push('BBCode: ' + bbcode);

        if(!parts.length) return;

        navigator.clipboard.writeText(parts.join('\n'));

        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i>' + <?= json_encode(l('images_upload.copied')) ?>;

        Swal.fire({
            icon: 'success',
            title: <?= json_encode(l('images_upload.copied')) ?>,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1200
        });

        setTimeout(() => {
            btn.innerHTML = original;
        }, 1500);
    }

    function openInPanel(data) {
        let resultContainer = document.querySelector('#upload_result_container');
        let preview = document.querySelector('#upload_preview');
        let btnViewer = document.querySelector('#btn_url_viewer');

        const originalUrl = data.url;
        const thumbUrl = data.thumb && data.thumb.url ? data.thumb.url : data.url;

        resultContainer.dataset.originalUrl = originalUrl;
        resultContainer.dataset.thumbUrl = thumbUrl;

        preview.src = originalUrl;
        if (data.url_viewer) {
            btnViewer.href = data.url_viewer;
            btnViewer.classList.remove('d-none');
        } else {
            btnViewer.classList.add('d-none');
        }

        if(typeof applyEmbedPreset === 'function') {
            applyEmbedPreset();
        }

        resultContainer.classList.remove('d-none');
        resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function prependToTable(data) {
        let table = document.querySelector('#uploads_table tbody');
        if(!table) return;

        let noDataRow = document.querySelector('#no_data_row');
        if (noDataRow) noDataRow.remove();

        let isAdmin = <?= (int) $this->user->type === 1 ? 'true' : 'false' ?>;
        let rowId = data.imgbb_upload_id || 'new';
        let now = new Date().toLocaleString();

        let newRow = document.createElement('tr');
        newRow.setAttribute('data-row-id', rowId);
        newRow.innerHTML = `
            <td>
                <span class="badge badge-light">${rowId}</span>
            </td>
            <td>
                <a href="${data.url}" target="_blank" data-toggle="tooltip" title="<?= l('images_upload.view_image') ?>">
                    <img src="${data.thumb ? data.thumb.url : data.url}" class="rounded shadow-sm" style="width: 64px; height: 64px; object-fit: cover;" alt="" loading="lazy" />
                </a>
            </td>
            <td>
                <span class="badge badge-success">${now}</span>
            </td>
            <td>
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" data-boundary="viewport"><?= l('images_upload.actions') ?></button>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item btn-open-panel">
                            <i class="fas fa-eye fa-sm mr-2"></i> <?= l('images_upload.show_embeds') ?>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item btn-copy-link">
                            <i class="fas fa-copy fa-sm mr-2"></i> <?= l('images_upload.copy_link') ?>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item text-danger btn-delete-image">
                            <i class="fas fa-trash-alt fa-sm mr-2"></i> <?= l('global.delete') ?>
                        </button>
                    </div>
                </div>
            </td>
            ${isAdmin ? `<td>-</td>` : ''}
        `;

        table.insertBefore(newRow, table.firstChild);

        if($.fn.tooltip) {
            $(newRow).find('[data-toggle="tooltip"]').tooltip();
        }

        newRow.querySelector('.btn-open-panel').addEventListener('click', () => openInPanel(data));
        newRow.querySelector('.btn-copy-link').addEventListener('click', () => {
            navigator.clipboard.writeText(data.url);
            Swal.fire({
                icon: 'success',
                title: <?= json_encode(l('images_upload.copied')) ?>,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        });
        newRow.querySelector('.btn-delete-image').addEventListener('click', (e) => {
            deleteImage(rowId, e.target);
        });
    }

    function prependToGrid(data) {
        const grid = document.querySelector('#uploads_grid');
        if(!grid) return;

        const noData = document.querySelector('#uploads_grid_no_data');
        if(noData) noData.remove();

        const rowId = data.imgbb_upload_id || 'new';
        const title = data.title || <?= json_encode(l('images_upload.view_image')) ?>;
        const thumbUrl = (data.thumb && data.thumb.url) ? data.thumb.url : data.url;

        const col = document.createElement('div');
        col.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-4';
        col.innerHTML = `
            <div class="card border-0 shadow-sm h-100">
                <a href="${data.url}" target="_blank" class="d-block">
                    <img src="${thumbUrl}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="" loading="lazy" />
                </a>

                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-truncate pr-2" style="max-width: 75%;">
                            <div class="small text-muted">#${rowId}</div>
                            <div class="font-weight-bold text-truncate">${escapeHtml(title)}</div>
                        </div>

                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" data-boundary="viewport">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button type="button" class="dropdown-item btn-open-panel" data-row='${escapeHtml(JSON.stringify(data))}'>
                                    <i class="fas fa-eye fa-sm mr-2 text-primary"></i> <?= l('images_upload.show_embeds') ?>
                                </button>
                                <div class="dropdown-divider"></div>
                                <button type="button" class="dropdown-item btn-copy-link" data-url="${data.url}">
                                    <i class="fas fa-link fa-sm mr-2"></i> <?= l('images_upload.copy_link') ?>
                                </button>
                                ${(data.url_viewer ? `<a class="dropdown-item" href="${data.url_viewer}" target="_blank" rel="noreferrer"><i class="fas fa-external-link-alt fa-sm mr-2"></i> <?= l('images_upload.open_viewer') ?></a>` : '')}
                                <div class="dropdown-divider"></div>
                                <button type="button" class="dropdown-item text-danger" onclick="deleteImage(${rowId}, this)">
                                    <i class="fas fa-trash-alt fa-sm mr-2"></i> <?= l('global.delete') ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <span class="badge badge-success">${new Date().toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;

        grid.insertBefore(col, grid.firstChild);

        const openBtn = col.querySelector('.btn-open-panel');
        if(openBtn) {
            openBtn.addEventListener('click', (event) => {
                let row = event.currentTarget.getAttribute('data-row');
                try {
                    openInPanel(JSON.parse(row));
                } catch (e) {}
            });
        }

        const copyBtn = col.querySelector('.btn-copy-link');
        if(copyBtn) {
            copyBtn.addEventListener('click', event => {
                let url = event.currentTarget.getAttribute('data-url');
                navigator.clipboard.writeText(url);
                Swal.fire({
                    icon: 'success',
                    title: <?= json_encode(l('images_upload.copied')) ?>,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    }

    document.querySelectorAll('.btn-open-panel').forEach(button => {
        button.addEventListener('click', event => {
            let data = JSON.parse(event.currentTarget.getAttribute('data-row'));
            openInPanel(data);
        });
    });

    document.querySelectorAll('.btn-copy-link').forEach(button => {
        button.addEventListener('click', event => {
            let url = event.currentTarget.getAttribute('data-url');
            navigator.clipboard.writeText(url);
            Swal.fire({
                icon: 'success',
                title: <?= json_encode(l('images_upload.copied')) ?>,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });

    function deleteImage(imgbb_upload_id, element) {
        Swal.fire({
            title: <?= json_encode(l('images_upload.delete_title')) ?>,
            text: <?= json_encode(l('images_upload.delete_confirm')) ?>,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: <?= json_encode(l('global.delete')) ?>,
            cancelButtonText: <?= json_encode(l('global.cancel')) ?>
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('imgbb_upload_id', imgbb_upload_id);
                formData.append('global_token', document.querySelector('input[name="global_token"]').value);

                fetch('<?= url('images-upload/delete') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    let data = null;

                    try {
                        data = await response.json();
                    } catch (e) {
                        const text = await response.text();
                        throw new Error(`Invalid server response (${response.status}). ${text ? text.substring(0, 200) : ''}`);
                    }

                    if(!response.ok) {
                        throw new Error(data && data.message ? data.message : `Server error (${response.status})`);
                    }

                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            position: 'top-end'
                        });

                        const row = element.closest('tr');
                        if(row) {
                            row.style.transition = 'all 0.5s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                row.remove();

                                const tableBody = document.querySelector('#uploads_table tbody');
                                if(tableBody && document.querySelectorAll('#uploads_table tbody tr').length === 0) {
                                    let isAdmin = <?= (int) $this->user->type === 1 ? 'true' : 'false' ?>;
                                    let noDataRow = document.createElement('tr');
                                    noDataRow.id = 'no_data_row';
                                    noDataRow.innerHTML = `<td colspan="${isAdmin ? 5 : 4}" class="text-muted"><?= l('global.no_data') ?></td>`;
                                    tableBody.appendChild(noDataRow);
                                }
                            }, 500);
                        } else {
                            const card = element.closest('.col-12');
                            if(card) {
                                card.style.transition = 'all 0.5s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'translateY(10px)';
                                setTimeout(() => {
                                    card.remove();
                                }, 500);
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Unknown error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Communication error with server.'
                    });
                });
            }
        });
    }

    function copyToClipboard(id, btn) {
        let input = document.getElementById(id);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);

        let originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.replace('btn-outline-secondary', 'btn-success');

        Swal.fire({
            icon: 'success',
            title: <?= json_encode(l('images_upload.copied')) ?>,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1000
        });

        setTimeout(() => {
            btn.innerHTML = originalIcon;
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    }

    /* Bulk actions (list view only) */
    const bulkEnableBtn = document.querySelector('#bulk_enable');
    const bulkDisableBtn = document.querySelector('#bulk_disable');
    const bulkGroup = document.querySelector('#bulk_group');
    const bulkSelectAll = document.querySelector('#bulk_select_all');
    const bulkForm = document.querySelector('#bulk_form');
    const bulkCounter = document.querySelector('#bulk_counter');

    function getSelectedIds() {
        if(!bulkForm) return [];
        return Array.from(bulkForm.querySelectorAll('input[name="selected[]"]:checked')).map(el => parseInt(el.value)).filter(Boolean);
    }

    function updateBulkCounter() {
        if(!bulkCounter) return;
        const count = getSelectedIds().length;
        if(count) {
            bulkCounter.classList.remove('d-none');
            bulkCounter.textContent = `(${count})`;
        } else {
            bulkCounter.classList.add('d-none');
            bulkCounter.textContent = '';
        }
    }

    function setBulkMode(isEnabled) {
        const bulkCells = document.querySelectorAll('[data-bulk-table]');
        bulkCells.forEach(el => {
            el.classList.toggle('d-none', !isEnabled);
        });

        if(bulkGroup && bulkEnableBtn) {
            bulkGroup.classList.toggle('d-none', !isEnabled);
            bulkEnableBtn.classList.toggle('d-none', isEnabled);
        }

        if(!isEnabled && bulkForm) {
            bulkForm.querySelectorAll('input[name="selected[]"]').forEach(el => el.checked = false);
            if(bulkSelectAll) bulkSelectAll.checked = false;
        }

        updateBulkCounter();
    }

    if(bulkEnableBtn) {
        bulkEnableBtn.addEventListener('click', () => setBulkMode(true));
    }

    if(bulkDisableBtn) {
        bulkDisableBtn.addEventListener('click', () => setBulkMode(false));
    }

    if(bulkSelectAll && bulkForm) {
        bulkSelectAll.addEventListener('change', event => {
            const checked = event.currentTarget.checked;
            bulkForm.querySelectorAll('input[name="selected[]"]').forEach(el => el.checked = checked);
            updateBulkCounter();
        });

        bulkForm.querySelectorAll('input[name="selected[]"]').forEach(el => {
            el.addEventListener('change', updateBulkCounter);
        });
    }

    document.querySelectorAll('[data-bulk-action]').forEach(button => {
        button.addEventListener('click', async (event) => {
            const action = event.currentTarget.getAttribute('data-bulk-action');
            const ids = getSelectedIds();

            if(!ids.length) {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: <?= json_encode(l('global.error_message.empty_fields')) ?>
                });
                return;
            }

            if(action === 'copy_links') {
                const formData = new FormData();
                formData.append('global_token', document.querySelector('input[name="global_token"]').value);
                formData.append('type', 'copy_links');
                ids.forEach(id => formData.append('selected[]', id));

                try {
                    const response = await fetch('<?= url('images-upload/bulk') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if(!response.ok || !data.success) {
                        throw new Error(data.message || `Server error (${response.status})`);
                    }

                    const text = (data.urls || []).join('\n');
                    await navigator.clipboard.writeText(text);

                    Swal.fire({
                        icon: 'success',
                        title: <?= json_encode(l('images_upload.copied')) ?>,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch(e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.message || 'Error'
                    });
                }

                return;
            }

            if(action === 'delete') {
                const confirm = await Swal.fire({
                    title: <?= json_encode(l('images_upload.delete_title')) ?>,
                    text: <?= json_encode(l('images_upload.bulk_delete_confirm')) ?>,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: <?= json_encode(l('global.delete')) ?>,
                    cancelButtonText: <?= json_encode(l('global.cancel')) ?>
                });

                if(!confirm.isConfirmed) return;

                const formData = new FormData();
                formData.append('global_token', document.querySelector('input[name="global_token"]').value);
                formData.append('type', 'delete');
                ids.forEach(id => formData.append('selected[]', id));

                try {
                    const response = await fetch('<?= url('images-upload/bulk') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if(!response.ok || !data.success) {
                        throw new Error(data.message || `Server error (${response.status})`);
                    }

                    ids.forEach(id => {
                        const row = document.querySelector(`tr[data-row-id="${id}"]`);
                        if(row) row.remove();
                    });

                    updateBulkCounter();

                    Swal.fire({
                        icon: 'success',
                        title: data.message || <?= json_encode(l('images_upload.success_delete')) ?>,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch(e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.message || 'Error'
                    });
                }

                return;
            }
        });
    });
</script>
