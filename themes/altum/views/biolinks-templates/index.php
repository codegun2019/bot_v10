<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0 text-truncate">
            <h1 class="h4 m-0 text-truncate"><?= l('biolinks_templates.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('biolinks_templates.subheader') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex flex-wrap gap-3 d-print-none">
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= url('biolinks-templates?order_by=total_usage&order_type=DESC') ?>" class="btn btn-sm <?= ($data->filters->order_by == 'total_usage' && $data->filters->order_type == 'DESC') ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="fas fa-fw fa-fire mr-1"></i> ยอดนิยม
                </a>
                <a href="<?= url('biolinks-templates?order_by=datetime&order_type=DESC') ?>" class="btn btn-sm <?= ($data->filters->order_by == 'datetime' && $data->filters->order_type == 'DESC') ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="fas fa-fw fa-clock mr-1"></i> ใหม่ล่าสุด
                </a>
                <a href="<?= url('biolinks-templates?order_by=total_usage&order_type=DESC') ?>" class="btn btn-sm <?= ($data->filters->order_by == 'total_usage' && $data->filters->order_type == 'DESC') ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="fas fa-fw fa-users mr-1"></i> ใช้มากที่สุด
                </a>
                <a href="<?= url('biolinks-templates/favorites') ?>" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-fw fa-heart mr-1"></i> My Favorites
                </a>
                <button id="compare-btn" class="btn btn-sm btn-primary compare-btn" onclick="openCompareModal()">
                    <i class="fas fa-balance-scale mr-1"></i>
                    เปรียบเทียบ
                    <span class="badge badge-light" id="compare-count">0</span>
                </button>
            </div>
            <div>
                <div class="dropdown">
                    <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-dark' : 'btn-light' ?> filters-button dropdown-toggle-simple <?= count($data->biolinks_templates) || $data->filters->has_applied_filters ? null : 'disabled' ?>" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if($data->filters->has_applied_filters): ?>
                                <a href="<?= url(\Altum\Router::$original_request) ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="filters_search_by" class="custom-select custom-select-sm">
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="biolink_template_id" <?= $data->filters->order_by == 'biolink_template_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="order" <?= $data->filters->order_by == 'order' ? 'selected="selected"' : null ?>><?= l('global.order') ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                    <option value="total_usage" <?= $data->filters->order_by == 'total_usage' ? 'selected="selected"' : null ?>>ใช้มากที่สุด</option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>>ใหม่ล่าสุด</option>
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
        </div>
    </div>

    <?php if (!empty($data->biolinks_templates)): ?>
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="all-templates-tab" data-toggle="tab" href="#all-templates" role="tab">
                    <i class="fas fa-fw fa-th-large mr-1"></i> เทมเพลตทั้งหมด
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="recently-viewed-tab" data-toggle="tab" href="#recently-viewed" role="tab">
                    <i class="fas fa-fw fa-clock mr-1"></i> เทมเพลตที่คุณเพิ่งดู
                    <span id="recently-viewed-count" class="badge badge-primary ml-1" style="display: none;">0</span>
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- All Templates Tab -->
            <div class="tab-pane fade show active" id="all-templates" role="tabpanel">
        <style>
            .template-card-wrapper {
                position: relative;
            }
            
            .template-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                z-index: 10;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(10px);
            }
            
            .template-badge.popular {
                background: linear-gradient(135deg, #FF6B6B, #FF8E53);
                color: white;
            }
            
            .template-badge.new {
                background: linear-gradient(135deg, #4ECDC4, #44A08D);
                color: white;
            }
            
            .template-favorite-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 10;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }
            
            .template-favorite-btn:hover {
                background: rgba(255, 255, 255, 1);
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }
            
            .template-favorite-btn.active {
                background: linear-gradient(135deg, #FF6B6B, #FF8E53);
                color: white;
            }
            
            .template-favorite-btn i {
                font-size: 1rem;
            }
            
            .template-refresh-btn {
                position: absolute;
                bottom: 10px;
                right: 10px;
                z-index: 10;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                opacity: 0;
            }
            
            .template-image-wrapper:hover .template-refresh-btn {
                opacity: 1;
            }
            
            .template-refresh-btn:hover {
                background: rgba(255, 255, 255, 1);
                transform: scale(1.1) rotate(90deg);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }
            
            .template-refresh-btn.refreshing {
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }
            
            .template-refresh-btn i {
                font-size: 0.9rem;
                color: var(--color-primary);
            }
            
            /* Comparison Checkbox */
            .template-compare-checkbox-wrapper {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem 0.75rem;
                border: 2px solid var(--palette-neutral-gray-300);
                border-radius: 0.5rem;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.875rem;
                background: white;
                color: var(--palette-neutral-gray-700);
            }
            
            .template-compare-checkbox-wrapper:hover {
                background: var(--palette-neutral-gray-50);
                border-color: var(--color-primary);
                color: var(--color-primary);
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            
            .template-compare-checkbox-wrapper input[type="checkbox"] {
                margin-right: 0.5rem;
                cursor: pointer;
                width: 18px;
                height: 18px;
                accent-color: var(--color-primary);
            }
            
            .template-compare-checkbox-wrapper input[type="checkbox"]:checked + span {
                color: var(--color-primary);
                font-weight: 600;
            }
            
            .template-compare-checkbox-wrapper:has(input[type="checkbox"]:checked) {
                background: linear-gradient(135deg, rgba(255, 128, 0, 0.1), rgba(255, 178, 102, 0.1));
                border-color: var(--color-primary);
                color: var(--color-primary);
            }
            
            /* Compare Button */
            .compare-btn {
                display: none;
            }
            
            .compare-btn.show {
                display: inline-flex;
                align-items: center;
            }
            
            .compare-btn .badge {
                margin-left: 8px;
            }
            
            /* Recently Viewed Templates */
            #recently-viewed-section {
                padding: 1rem 0;
            }
            
            
            .template-usage-count {
                display: flex;
                align-items: center;
                gap: 4px;
                color: var(--palette-neutral-gray-600);
                font-size: 0.85rem;
                margin-top: 8px;
            }
            
            .template-usage-count i {
                color: var(--color-primary);
            }
            
            .template-image-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 8px;
                background: var(--palette-neutral-gray-100);
                min-height: 24rem;
            }
            
            .template-image-skeleton {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s ease-in-out infinite;
            }
            
            @keyframes skeleton-loading {
                0% {
                    background-position: 200% 0;
                }
                100% {
                    background-position: -200% 0;
                }
            }
            
            .template-image-wrapper img {
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .template-image-wrapper img.loaded {
                opacity: 1;
            }
        </style>
        <div class="row">
            <?php 
            foreach($data->biolinks_templates as $biolink_template): 
                $is_popular = ($biolink_template->total_usage ?? 0) > 100;
                $is_new = strtotime($biolink_template->datetime) > (time() - 30 * 24 * 60 * 60); // 30 days
            ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="custom-row mb-4 d-flex flex-column justify-content-between template-card-wrapper">
                        <div class="mb-3 template-image-wrapper">
                            <div class="template-image-skeleton"></div>
                            <img 
                                id="template-image-<?= $biolink_template->biolink_template_id ?>"
                                src="https://s0.wp.com/mshots/v1/<?= urlencode($biolink_template->url) ?>?w=350&h=395&t=<?= time() ?>" 
                                style="width: 100%; height: 24rem; border: 0; position: relative; z-index: 1;" 
                                class="rounded container-disabled-simple" 
                                loading="lazy"
                                data-template-url="<?= htmlspecialchars($biolink_template->url, ENT_QUOTES, 'UTF-8') ?>"
                                onload="this.classList.add('loaded'); this.parentElement.querySelector('.template-image-skeleton').style.display='none';"
                                onerror="this.classList.add('error'); this.parentElement.querySelector('.template-image-skeleton').style.display='none';"
                            >
                            
                            <?php if($is_popular): ?>
                                <span class="template-badge popular">
                                    <i class="fas fa-fw fa-fire mr-1"></i> ยอดนิยม
                                </span>
                            <?php elseif($is_new): ?>
                                <span class="template-badge new">
                                    <i class="fas fa-fw fa-sparkles mr-1"></i> ใหม่
                                </span>
                            <?php endif; ?>
                            
                            <button type="button" class="template-favorite-btn" 
                                    onclick="toggleFavorite(<?= $biolink_template->biolink_template_id ?>, this)"
                                    data-template-id="<?= $biolink_template->biolink_template_id ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                            
                            <button type="button" class="template-refresh-btn" 
                                    onclick="refreshTemplateImage(<?= $biolink_template->biolink_template_id ?>, '<?= $biolink_template->url ?>', this)"
                                    data-template-id="<?= $biolink_template->biolink_template_id ?>"
                                    title="รีเฟรชภาพตัวอย่าง">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>

                        <div class="mb-2 text-center">
                            <h2 class="h6"><?= $biolink_template->name ?></h2>
                            <div class="template-usage-count justify-content-center">
                                <i class="fas fa-users"></i>
                                <span><?= number_format($biolink_template->total_usage ?? 0) ?> ผู้ใช้</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="template-compare-checkbox-wrapper">
                                <input type="checkbox" class="template-compare-checkbox" 
                                       data-template-id="<?= $biolink_template->biolink_template_id ?>"
                                       data-template-name="<?= htmlspecialchars($biolink_template->name, ENT_QUOTES, 'UTF-8') ?>"
                                       data-template-url="<?= htmlspecialchars($biolink_template->url, ENT_QUOTES, 'UTF-8') ?>"
                                       data-template-usage="<?= $biolink_template->total_usage ?? 0 ?>"
                                       onchange="updateCompareButton()">
                                <span><i class="fas fa-balance-scale mr-1"></i> เปรียบเทียบ</span>
                            </label>
                        </div>

                        <a href="<?= $biolink_template->url ?>" target="_blank" class="btn btn-block btn-sm btn-light mb-2" onclick="addToRecentlyViewed(<?= $biolink_template->biolink_template_id ?>, '<?= htmlspecialchars($biolink_template->name, ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($biolink_template->url, ENT_QUOTES, 'UTF-8') ?>', <?= $biolink_template->total_usage ?? 0 ?>, '<?= $biolink_template->datetime ?>')"><i class="fas fa-fw fa-sm fa-external-link-alt mr-1"></i> <?= l('biolinks_templates.preview') ?></a>

                        <button type="button" class="btn btn-block btn-sm btn-outline-primary" onclick="openTemplatePreview('<?= $biolink_template->url ?>', false); addToRecentlyViewed(<?= $biolink_template->biolink_template_id ?>, '<?= htmlspecialchars($biolink_template->name, ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($biolink_template->url, ENT_QUOTES, 'UTF-8') ?>', <?= $biolink_template->total_usage ?? 0 ?>, '<?= $biolink_template->datetime ?>')">
                            <i class="fas fa-fw fa-sm fa-mobile-alt mr-1"></i> ดูตัวอย่างแบบ Popup
                        </button>
                        
                        <button type="button" class="btn btn-block btn-sm btn-outline-secondary mt-2" onclick="openTemplatePreview('<?= $biolink_template->url ?>', true); addToRecentlyViewed(<?= $biolink_template->biolink_template_id ?>, '<?= htmlspecialchars($biolink_template->name, ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($biolink_template->url, ENT_QUOTES, 'UTF-8') ?>', <?= $biolink_template->total_usage ?? 0 ?>, '<?= $biolink_template->datetime ?>')">
                            <i class="fas fa-fw fa-sm fa-mobile-alt mr-1"></i> ดูตัวอย่างแบบ Popup (มีกรอบ)
                        </button>

                        <div class="mt-2" <?= in_array($biolink_template->biolink_template_id, $this->user->plan_settings->biolinks_templates ?? []) ? null : get_plan_feature_disabled_info() ?>>
                            <button type="button" class="btn btn-block btn-sm btn-primary <?= in_array($biolink_template->biolink_template_id, $this->user->plan_settings->biolinks_templates ?? []) ? null : 'container-disabled' ?>" data-toggle="modal" data-target="#create_biolink" onclick="document.querySelector(`input[name='biolink_template_id']`).value = <?= $biolink_template->biolink_template_id ?>;">
                                <i class="fas fa-fw fa-sm fa-plus-circle mr-1"></i> <?= l('biolinks_templates.choose') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <!-- Infinite Scroll Loader -->
        <div id="infinite-scroll-loader" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">กำลังโหลด...</span>
            </div>
            <p class="text-muted mt-2">กำลังโหลดเทมเพลตเพิ่มเติม...</p>
        </div>
        
        <div class="mt-3" id="pagination-container"><?= $data->pagination ?></div>
            </div>

            <!-- Recently Viewed Templates Tab -->
            <div class="tab-pane fade" id="recently-viewed" role="tabpanel">
                <div id="recently-viewed-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="m-0">
                            <i class="fas fa-fw fa-clock text-primary mr-2"></i>
                            เทมเพลตที่คุณเพิ่งดู
                        </h5>
                        <button type="button" class="btn btn-sm btn-link text-muted" onclick="clearRecentlyViewed()">
                            <i class="fas fa-times"></i> ล้างประวัติ
                        </button>
                    </div>
                    <div id="recently-viewed-templates" class="row"></div>
                    <div id="recently-viewed-empty" class="text-center py-5" style="display: none;">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <p class="text-muted">คุณยังไม่เคยดูเทมเพลตใดๆ</p>
                        <p class="text-muted small">เริ่มดูเทมเพลตเพื่อให้แสดงที่นี่</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>

        <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
            'filters_get' => $data->filters->get ?? [],
            'name' => 'global',
            'has_secondary_text' => false,
        ]); ?>

    <?php endif ?>
</div>

<!-- Comparison Modal -->
<div class="modal fade" id="compareModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-balance-scale mr-2"></i>
                    เปรียบเทียบเทมเพลต
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="compare-modal-body">
                <p class="text-muted text-center">กรุณาเลือกเทมเพลตที่ต้องการเปรียบเทียบ (2-3 อัน)</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="clearCompare()">
                    <i class="fas fa-times mr-1"></i> ล้างการเลือก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    'use strict';
    
    // Check if localStorage is available
    function isLocalStorageAvailable() {
        try {
            const test = '__localStorage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            console.error('localStorage is not available:', e);
            return false;
        }
    }
    
    // Get favorites from localStorage
    function getFavorites() {
        if (!isLocalStorageAvailable()) {
            console.warn('localStorage is not available');
            return [];
        }
        
        try {
            const favoritesStr = localStorage.getItem('biolink_template_favorites');
            if (!favoritesStr) {
                console.log('No favorites found in localStorage');
                return [];
            }
            const favorites = JSON.parse(favoritesStr);
            console.log('Loaded favorites from localStorage:', favorites);
            // Ensure all IDs are integers
            return Array.isArray(favorites) ? favorites.map(id => parseInt(id)) : [];
        } catch (e) {
            console.error('Error reading favorites from localStorage:', e);
            return [];
        }
    }
    
    // Save favorites to localStorage
    function saveFavorites(favorites) {
        if (!isLocalStorageAvailable()) {
            console.error('Cannot save: localStorage is not available');
            alert('ไม่สามารถบันทึก favorites ได้ localStorage ไม่พร้อมใช้งาน');
            return false;
        }
        
        try {
            // Ensure all IDs are integers
            const cleanFavorites = Array.isArray(favorites) ? favorites.map(id => parseInt(id)).filter(id => !isNaN(id)) : [];
            const favoritesStr = JSON.stringify(cleanFavorites);
            localStorage.setItem('biolink_template_favorites', favoritesStr);
            console.log('✅ Favorites saved to localStorage:', cleanFavorites);
            console.log('✅ localStorage value:', localStorage.getItem('biolink_template_favorites'));
            return true;
        } catch (e) {
            console.error('❌ Error saving favorites to localStorage:', e);
            alert('เกิดข้อผิดพลาดในการบันทึก favorites: ' + e.message);
            return false;
        }
    }
    
    // Initialize favorites from localStorage
    function initializeFavorites() {
        console.log('🔄 Initializing favorites...');
        const favorites = getFavorites();
        console.log('📋 Found favorites:', favorites);
        
        favorites.forEach(templateId => {
            const templateIdStr = String(templateId);
            const button = document.querySelector(`.template-favorite-btn[data-template-id="${templateIdStr}"]`);
            if (button) {
                button.classList.add('active');
                console.log('✅ Marked template', templateId, 'as favorite');
            } else {
                console.warn('⚠️ Button not found for template ID:', templateId);
            }
        });
        
        console.log('✅ Favorites initialization complete');
    }
    
    // Toggle favorite function (using localStorage)
    function toggleFavorite(templateId, button) {
        console.log('🔄 Toggle favorite called for template ID:', templateId);
        
        if (!button) {
            console.error('❌ Button element not provided');
            return;
        }
        
        const favorites = getFavorites();
        const templateIdInt = parseInt(templateId);
        
        if (isNaN(templateIdInt)) {
            console.error('❌ Invalid template ID:', templateId);
            return;
        }
        
        const isFavorite = favorites.includes(templateIdInt);
        console.log('📊 Current favorites:', favorites);
        console.log('📊 Template ID:', templateIdInt, 'Is favorite:', isFavorite);
        
        // Optimistic UI update
        button.classList.toggle('active');
        const icon = button.querySelector('i');
        if (icon) {
            icon.style.transform = 'scale(1.3)';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 200);
        }
        
        // Update favorites array
        let newFavorites = [...favorites]; // Create a copy
        
        if (isFavorite) {
            // Remove from favorites
            const index = newFavorites.indexOf(templateIdInt);
            if (index > -1) {
                newFavorites.splice(index, 1);
                console.log('➖ Removed template', templateIdInt, 'from favorites');
            }
        } else {
            // Add to favorites
            if (!newFavorites.includes(templateIdInt)) {
                newFavorites.push(templateIdInt);
                console.log('➕ Added template', templateIdInt, 'to favorites');
            }
        }
        
        console.log('💾 New favorites array:', newFavorites);
        
        // Save to localStorage
        const saved = saveFavorites(newFavorites);
        
        if (!saved) {
            // Revert on error
            console.error('❌ Failed to save favorites, reverting UI');
            button.classList.toggle('active');
            alert('ไม่สามารถบันทึก favorites ได้ กรุณาลองอีกครั้ง');
        } else {
            console.log('✅ Successfully saved favorites');
            
            // Verify it was saved
            const verifyFavorites = getFavorites();
            console.log('✅ Verified favorites after save:', verifyFavorites);
        }
    }
    
    // Refresh template image function - Clear cache and capture new
    function refreshTemplateImage(templateId, templateUrl, button) {
        const img = document.getElementById(`template-image-${templateId}`);
        if (!img) return;
        
        // Show loading state
        button.classList.add('refreshing');
        img.classList.remove('loaded', 'error');
        img.style.opacity = '0';
        
        // Show skeleton if exists
        const skeleton = img.parentElement.querySelector('.template-image-skeleton');
        if (skeleton) {
            skeleton.style.display = 'block';
        }
        
        // Generate unique timestamp and random number to bypass cache
        const timestamp = new Date().getTime();
        const random = Math.floor(Math.random() * 1000000);
        
        // Force mshots service to capture new screenshot by adding cache-busting parameters
        // URL format: https://s0.wp.com/mshots/v1/{encoded-url}?w=350&h=395
        const cleanUrl = encodeURIComponent(templateUrl.replace(/^https?:\/\//, '').replace(/\/$/, ''));
        const newSrc = `https://s0.wp.com/mshots/v1/${cleanUrl}?w=350&h=395&t=${timestamp}&nocache=${random}&refresh=1`;
        
        // Remove old image from cache by setting src to empty first
        img.src = '';
        img.removeAttribute('src');
        
        // Force browser to clear cache for this image
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                }
            }).catch(function() {
                // Ignore cache errors
            });
        }
        
        // Add cache-control headers via fetch to bypass cache
        fetch(newSrc, {
            cache: 'no-store',
            method: 'GET'
        }).catch(function() {
            // Ignore fetch errors, we'll use Image() instead
        });
        
        // Create new image element to preload with cache bypass
        const newImg = new Image();
        newImg.crossOrigin = 'anonymous';
        
        newImg.onload = function() {
            // Update the actual image
            img.src = newSrc;
            img.classList.add('loaded');
            img.style.opacity = '1';
            if (skeleton) {
                skeleton.style.display = 'none';
            }
            button.classList.remove('refreshing');
        };
        
        newImg.onerror = function() {
            // Try alternative format if first fails
            const alternativeSrc = `https://s0.wp.com/mshots/v1/${cleanUrl}?w=350&h=395&t=${timestamp}&nocache=${random}&refresh=2&force=1`;
            const altImg = new Image();
            altImg.crossOrigin = 'anonymous';
            
            altImg.onload = function() {
                img.src = alternativeSrc;
                img.classList.add('loaded');
                img.style.opacity = '1';
                if (skeleton) {
                    skeleton.style.display = 'none';
                }
                button.classList.remove('refreshing');
            };
            
            altImg.onerror = function() {
                // Last resort: try direct URL with cache busting
                const lastResort = `${templateUrl}?t=${timestamp}&nocache=${random}`;
                img.src = lastResort;
                img.classList.add('error');
                img.style.opacity = '0.5';
                if (skeleton) {
                    skeleton.style.display = 'none';
                }
                button.classList.remove('refreshing');
            };
            
            altImg.src = alternativeSrc;
        };
        
        // Start loading new image
        newImg.src = newSrc;
    }
    
    // Load favorites page with localStorage data
    function loadFavoritesPage(event) {
        event.preventDefault();
        const favorites = getFavorites();
        if (favorites.length === 0) {
            alert('คุณยังไม่มีเทมเพลตที่ชอบ');
            return;
        }
        const favoritesParam = btoa(JSON.stringify(favorites));
        window.location.href = '<?= url("biolinks-templates/favorites") ?>?favorites=' + encodeURIComponent(favoritesParam);
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Page loaded, initializing favorites...');
        console.log('🔍 localStorage available:', typeof(Storage) !== 'undefined' && typeof(localStorage) !== 'undefined');
        initializeFavorites();
        const currentFavorites = getFavorites();
        console.log('✅ Favorites initialization complete. Current favorites:', currentFavorites);
        console.log('✅ Total favorites count:', currentFavorites.length);
        
        // Test localStorage write/read
        try {
            localStorage.setItem('_test_favorites_', 'test');
            localStorage.removeItem('_test_favorites_');
            console.log('✅ localStorage is working');
        } catch(e) {
            console.error('❌ localStorage test failed:', e);
        }
    });
    
    function openTemplatePreview(url, showFrame = false) {
        // ขนาด iPhone 17 Pro Max (CSS pixels)
        const iphone17ProMaxWidth = 380;
        const iphone17ProMaxHeight = 800;
        // คำนวณขนาด window โดยเพิ่ม toolbar และ borders
        const windowWidth = iphone17ProMaxWidth + 20; // เพิ่ม margin สำหรับ scrollbar และ borders
        const windowHeight = iphone17ProMaxHeight + 0; // เพิ่ม margin สำหรับ address bar และ toolbar
        
        // คำนวณตำแหน่งกลางหน้าจอ
        const left = (window.screen.width - windowWidth) / 2;
        const top = (window.screen.height - windowHeight) / 2;
        
        // ตั้งค่า features สำหรับ popup window
        const features = [
            `width=${windowWidth}`,
            `height=${windowHeight}`,
            `left=${left}`,
            `top=${top}`,
            'resizable=yes',
            'scrollbars=yes',
            'toolbar=no',
            'menubar=no',
            'location=no',
            'status=no'
        ].join(',');
        
        // เปิด popup window
        const popup = window.open(url, 'templatePreview', features);
        
        // เพิ่ม CSS เพื่อจำลอง iPhone 17 Pro Max viewport
        if (popup) {
            popup.addEventListener('load', function() {
                try {
                    const style = popup.document.createElement('style');
                    
                    // สร้าง CSS โดยตรวจสอบว่าแสดงกรอบหรือไม่
                    let frameCSS = '';
                    if (showFrame) {
                        frameCSS = `
                        /* Container สำหรับจำลอง iPhone frame */
                        body::before {
                            content: '';
                            position: fixed;
                            top: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: ${iphone17ProMaxWidth}px;
                            height: ${iphone17ProMaxHeight}px;
                            border: 8px solid #1a1a1a;
                            border-radius: 40px;
                            box-shadow: 
                                0 0 0 2px #333,
                                inset 0 0 0 2px #333,
                                0 20px 60px rgba(0, 0, 0, 0.8);
                            pointer-events: none;
                            z-index: 9999;
                            box-sizing: border-box;
                        }
                        
                        /* Dynamic Island simulation */
                        body::after {
                            content: '';
                            position: fixed;
                            top: 12px;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 126px;
                            height: 37px;
                            background: #000;
                            border-radius: 19px;
                            z-index: 10000;
                            pointer-events: none;
                        }
                        `;
                    }
                    
                    style.textContent = `
                        /* iPhone 17 Pro Max Viewport Simulation */
                        body {
                            margin: 0;
                            padding: 0;
                            width: ${iphone17ProMaxWidth}px !important;
                            min-width: ${iphone17ProMaxWidth}px !important;
                            max-width: ${iphone17ProMaxWidth}px !important;
                            min-height: ${iphone17ProMaxHeight}px;
                            background: ${showFrame ? '#000' : 'transparent'};
                            display: flex;
                            justify-content: center;
                            align-items: flex-start;
                        }
                        
                        ${frameCSS}
                        
                        /* Ensure content fits within iPhone frame */
                        html {
                            width: ${iphone17ProMaxWidth}px !important;
                            min-width: ${iphone17ProMaxWidth}px !important;
                            max-width: ${iphone17ProMaxWidth}px !important;
                            margin: 0 auto;
                            background: ${showFrame ? '#000' : 'transparent'};
                        }
                        
                        /* Center content */
                        body > *:not(script):not(style) {
                            margin: 0 auto;
                            max-width: ${iphone17ProMaxWidth}px;
                        }
                    `;
                    popup.document.head.appendChild(style);
                } catch (e) {
                    // ถ้าไม่สามารถ inject CSS ได้ (cross-origin) ก็ไม่ต้องทำอะไร
                    console.log('Cannot inject CSS (cross-origin):', e);
                }
            });
        }
    }
    
    // ==================== RECENTLY VIEWED TEMPLATES ====================
    
    // Get recently viewed templates from localStorage
    function getRecentlyViewed() {
        try {
            const viewed = localStorage.getItem('biolink_template_recently_viewed');
            return viewed ? JSON.parse(viewed) : [];
        } catch (e) {
            return [];
        }
    }
    
    // Save recently viewed templates to localStorage
    function saveRecentlyViewed(viewed) {
        try {
            // Keep only last 10 items
            const limited = viewed.slice(-10);
            localStorage.setItem('biolink_template_recently_viewed', JSON.stringify(limited));
            return true;
        } catch (e) {
            console.error('Error saving recently viewed:', e);
            return false;
        }
    }
    
    // Add template to recently viewed
    function addToRecentlyViewed(templateId, name, url, usage, datetime) {
        const viewed = getRecentlyViewed();
        
        // Remove if already exists
        const index = viewed.findIndex(item => item.id === templateId);
        if (index > -1) {
            viewed.splice(index, 1);
        }
        
        // Add to end
        viewed.push({
            id: templateId,
            name: name,
            url: url,
            usage: usage,
            datetime: datetime,
            viewedAt: new Date().toISOString()
        });
        
        saveRecentlyViewed(viewed);
        loadRecentlyViewed();
    }
    
    // Clear recently viewed
    function clearRecentlyViewed() {
        if (confirm('ต้องการล้างประวัติการดูล่าสุดหรือไม่?')) {
            localStorage.removeItem('biolink_template_recently_viewed');
            loadRecentlyViewed();
        }
    }
    
    // Load and display recently viewed templates
    function loadRecentlyViewed() {
        const viewed = getRecentlyViewed();
        const container = document.getElementById('recently-viewed-templates');
        const emptyDiv = document.getElementById('recently-viewed-empty');
        const countBadge = document.getElementById('recently-viewed-count');
        
        if (!viewed || viewed.length === 0) {
            if (container) container.innerHTML = '';
            if (emptyDiv) emptyDiv.style.display = 'block';
            if (countBadge) {
                countBadge.style.display = 'none';
            }
            return;
        }
        
        if (emptyDiv) emptyDiv.style.display = 'none';
        if (countBadge) {
            countBadge.textContent = viewed.length;
            countBadge.style.display = 'inline-block';
        }
        
        // Reverse to show most recent first
        const reversed = [...viewed].reverse();
        container.innerHTML = reversed.map((template, index) => {
            const imgId = `recent-img-${template.id}-${index}`;
            const cleanUrl = encodeURIComponent(template.url.replace(/^https?:\/\//, '').replace(/\/$/, ''));
            const imgSrc = `https://s0.wp.com/mshots/v1/${cleanUrl}?w=350&h=395&t=${Date.now()}`;
            const safeName = template.name.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            const safeUrl = template.url.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const templateId = parseInt(template.id);
            const usage = parseInt(template.usage || 0);
            const datetime = template.datetime || '';
            
            // Check if popular or new
            const templateDate = new Date(datetime);
            const daysSinceCreated = (Date.now() - templateDate.getTime()) / (1000 * 60 * 60 * 24);
            const isPopular = usage > 100;
            const isNew = daysSinceCreated <= 30;
            
            // Check if favorite
            const favorites = getFavorites();
            const isFavorite = favorites.includes(templateId);
            
            return `
            <div class="col-lg-6 col-xl-4">
                <div class="custom-row mb-4 d-flex flex-column justify-content-between template-card-wrapper">
                    <div class="mb-3 template-image-wrapper">
                        <div class="template-image-skeleton"></div>
                        <img 
                            id="${imgId}"
                            src="${imgSrc}" 
                            style="width: 100%; height: 24rem; border: 0; position: relative; z-index: 1;" 
                            class="rounded container-disabled-simple" 
                            loading="lazy"
                            data-template-url="${safeUrl}"
                            onload="const img = document.getElementById('${imgId}'); if(img) { img.classList.add('loaded'); const skeleton = img.parentElement.querySelector('.template-image-skeleton'); if(skeleton) skeleton.style.display='none'; }"
                            onerror="const img = document.getElementById('${imgId}'); if(img) { img.classList.add('error'); const skeleton = img.parentElement.querySelector('.template-image-skeleton'); if(skeleton) skeleton.style.display='none'; }"
                            alt="${safeName}">
                        
                        ${isPopular ? `
                        <span class="template-badge popular">
                            <i class="fas fa-fw fa-fire mr-1"></i> ยอดนิยม
                        </span>
                        ` : ''}
                        ${!isPopular && isNew ? `
                        <span class="template-badge new">
                            <i class="fas fa-fw fa-sparkles mr-1"></i> ใหม่
                        </span>
                        ` : ''}
                        
                        <button type="button" class="template-favorite-btn ${isFavorite ? 'active' : ''}" 
                                onclick="toggleFavorite(${templateId}, this)"
                                data-template-id="${templateId}">
                            <i class="fas fa-heart"></i>
                        </button>
                        
                        <button type="button" class="template-refresh-btn" 
                                onclick="refreshTemplateImage(${templateId}, '${safeUrl}', this)"
                                data-template-id="${templateId}"
                                title="รีเฟรชภาพตัวอย่าง">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                    <div class="mb-2 text-center">
                        <h2 class="h6">${safeName}</h2>
                        <div class="template-usage-count justify-content-center">
                            <i class="fas fa-users"></i>
                            <span>${usage.toLocaleString()} ผู้ใช้</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="template-compare-checkbox-wrapper">
                            <input type="checkbox" class="template-compare-checkbox" 
                                   data-template-id="${templateId}"
                                   data-template-name="${safeName}"
                                   data-template-url="${safeUrl}"
                                   data-template-usage="${usage}"
                                   onchange="updateCompareButton()">
                            <span><i class="fas fa-balance-scale mr-1"></i> เปรียบเทียบ</span>
                        </label>
                    </div>

                    <a href="${template.url}" target="_blank" class="btn btn-block btn-sm btn-light mb-2" onclick="addToRecentlyViewed(${templateId}, '${safeName}', '${safeUrl}', ${usage}, '${datetime}')">
                        <i class="fas fa-fw fa-sm fa-external-link-alt mr-1"></i> ดูตัวอย่าง
                    </a>

                    <button type="button" class="btn btn-block btn-sm btn-outline-primary" onclick="openTemplatePreview('${safeUrl}', false); addToRecentlyViewed(${templateId}, '${safeName}', '${safeUrl}', ${usage}, '${datetime}')">
                        <i class="fas fa-fw fa-sm fa-mobile-alt mr-1"></i> ดูตัวอย่างแบบ Popup
                    </button>
                    
                    <button type="button" class="btn btn-block btn-sm btn-outline-secondary mt-2" onclick="openTemplatePreview('${safeUrl}', true); addToRecentlyViewed(${templateId}, '${safeName}', '${safeUrl}', ${usage}, '${datetime}')">
                        <i class="fas fa-fw fa-sm fa-mobile-alt mr-1"></i> ดูตัวอย่างแบบ Popup (มีกรอบ)
                    </button>
                </div>
            </div>
            `;
        }).join('');
    }
    
    // ==================== TEMPLATE COMPARISON ====================
    
    let compareTemplates = [];
    
    // Update compare button
    function updateCompareButton() {
        const checkboxes = document.querySelectorAll('.template-compare-checkbox:checked');
        compareTemplates = Array.from(checkboxes).map(cb => ({
            id: parseInt(cb.dataset.templateId),
            name: cb.dataset.templateName,
            url: cb.dataset.templateUrl,
            usage: parseInt(cb.dataset.templateUsage)
        }));
        
        const compareBtn = document.getElementById('compare-btn');
        const compareCount = document.getElementById('compare-count');
        
        if (compareTemplates.length > 0) {
            compareBtn.classList.add('show');
            compareCount.textContent = compareTemplates.length;
        } else {
            compareBtn.classList.remove('show');
        }
        
        // Limit to 3 templates
        if (compareTemplates.length > 3) {
            alert('สามารถเปรียบเทียบได้สูงสุด 3 เทมเพลต');
            const last = Array.from(document.querySelectorAll('.template-compare-checkbox:checked')).pop();
            if (last) last.checked = false;
            updateCompareButton();
        }
    }
    
    // Open compare modal
    function openCompareModal() {
        if (compareTemplates.length < 2) {
            alert('กรุณาเลือกอย่างน้อย 2 เทมเพลตเพื่อเปรียบเทียบ');
            return;
        }
        
        const modalBody = document.getElementById('compare-modal-body');
        
        // Calculate column width based on number of templates (2 or 3 max)
        const colWidth = compareTemplates.length === 2 ? 6 : 4;
        
        // Escape HTML to prevent XSS
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
        modalBody.innerHTML = `
            <div class="row">
                ${compareTemplates.map(template => {
                    // Escape all user input
                    const safeName = escapeHtml(template.name);
                    const safeUrl = template.url.replace(/['"]/g, '');
                    const safeId = parseInt(template.id);
                    const colSize = compareTemplates.length === 2 ? 'col-md-6' : 'col-md-4';
                    
                    return `
                    <div class="${colSize} mb-3">
                        <div class="card h-100">
                            <div class="card-img-top" style="height: 300px; overflow: hidden; background: #f0f0f0;">
                                <img src="https://s0.wp.com/mshots/v1/${encodeURIComponent(safeUrl.replace(/^https?:\/\//, ''))}?w=400&h=300" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27%3E%3Crect fill=%27%23f0f0f0%27 width=%27400%27 height=%27300%27/%3E%3C/svg%3E'"
                                     alt="${safeName}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${safeName}</h5>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-users"></i> ${parseInt(template.usage || 0).toLocaleString()} ผู้ใช้
                                    </small>
                                </div>
                                <a href="${safeUrl}" target="_blank" class="btn btn-sm btn-primary btn-block">
                                    <i class="fas fa-external-link-alt"></i> ดูตัวอย่าง
                                </a>
                                <button class="btn btn-sm btn-outline-danger btn-block mt-2" onclick="removeFromCompare(${safeId})">
                                    <i class="fas fa-times"></i> ลบออก
                                </button>
                            </div>
                        </div>
                    </div>
                    `;
                }).join('')}
            </div>
        `;
        
        $('#compareModal').modal('show');
    }
    
    // Remove template from compare
    function removeFromCompare(templateId) {
        const checkbox = document.querySelector(`.template-compare-checkbox[data-template-id="${templateId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            updateCompareButton();
        }
        
        if (compareTemplates.length < 2) {
            $('#compareModal').modal('hide');
        } else {
            openCompareModal();
        }
    }
    
    // Clear all comparisons
    function clearCompare() {
        document.querySelectorAll('.template-compare-checkbox:checked').forEach(cb => {
            cb.checked = false;
        });
        updateCompareButton();
        $('#compareModal').modal('hide');
    }
    
    // ==================== INFINITE SCROLL ====================
    
    let isLoading = false;
    let currentPage = <?= isset($data->paginator) && method_exists($data->paginator, 'getCurrentPage') ? (int)$data->paginator->getCurrentPage() : 1 ?>;
    let hasMorePages = <?php 
        if (isset($data->paginator) && method_exists($data->paginator, 'getNumPages') && method_exists($data->paginator, 'getCurrentPage')) {
            echo ($data->paginator->getNumPages() > $data->paginator->getCurrentPage()) ? 'true' : 'false';
        } else {
            echo 'false';
        }
    ?>;
    
    function loadMoreTemplates() {
        if (isLoading || !hasMorePages) return;
        
        isLoading = true;
        const loader = document.getElementById('infinite-scroll-loader');
        loader.style.display = 'block';
        
        const nextPage = currentPage + 1;
        const url = new URL(window.location.href);
        url.searchParams.set('page', nextPage);
        
        fetch(url.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTemplates = doc.querySelectorAll('.row .col-lg-6, .row .col-xl-4');
                const container = document.querySelector('.row');
                
                if (newTemplates.length > 0) {
                    newTemplates.forEach(template => {
                        container.appendChild(template);
                    });
                    currentPage = nextPage;
                    
                    // Check if there are more pages
                    const pagination = doc.querySelector('#pagination-container');
                    hasMorePages = pagination && pagination.textContent.includes('ต่อไป');
                } else {
                    hasMorePages = false;
                }
                
                loader.style.display = 'none';
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading more templates:', error);
                loader.style.display = 'none';
                isLoading = false;
            });
    }
    
    // Infinite scroll detection
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            // Load more when 200px from bottom
            if (scrollTop + windowHeight >= documentHeight - 200) {
                loadMoreTemplates();
            }
        }, 100);
    });
    
    // ==================== KEYBOARD SHORTCUTS ====================
    
    document.addEventListener('keydown', function(e) {
        // Don't trigger shortcuts when typing in input fields
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
            return;
        }
        
        // F key - Toggle favorite for first visible template
        if (e.key === 'f' || e.key === 'F') {
            const firstFavoriteBtn = document.querySelector('.template-favorite-btn:not(.active)');
            if (firstFavoriteBtn) {
                const templateId = firstFavoriteBtn.dataset.templateId;
                toggleFavorite(templateId, firstFavoriteBtn);
                e.preventDefault();
            }
        }
        
        // Arrow keys - Navigate templates
        if (e.key === 'ArrowRight') {
            const current = document.querySelector('.template-card-wrapper:focus-within, .template-card-wrapper:hover');
            if (current) {
                const next = current.closest('.col-lg-6, .col-xl-4')?.nextElementSibling;
                if (next) {
                    next.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    e.preventDefault();
                }
            }
        }
        
        if (e.key === 'ArrowLeft') {
            const current = document.querySelector('.template-card-wrapper:focus-within, .template-card-wrapper:hover');
            if (current) {
                const prev = current.closest('.col-lg-6, .col-xl-4')?.previousElementSibling;
                if (prev) {
                    prev.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    e.preventDefault();
                }
            }
        }
        
        // / key - Focus search
        if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
            const searchInput = document.querySelector('#filters_search, input[type="search"]');
            if (searchInput) {
                searchInput.focus();
                e.preventDefault();
            }
        }
        
        // C key - Open compare modal (if templates selected)
        if (e.key === 'c' || e.key === 'C') {
            if (compareTemplates.length >= 2) {
                openCompareModal();
                e.preventDefault();
            }
        }
        
        // Escape - Close modals
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRecentlyViewed();
        updateCompareButton();
        console.log('✅ Features initialized: Recently Viewed, Comparison, Infinite Scroll, Keyboard Shortcuts');
    });
</script>
