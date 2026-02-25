<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0 text-truncate">
            <h1 class="h4 m-0 text-truncate">
                <i class="fas fa-fw fa-heart text-warning mr-2"></i> My Favorites
            </h1>
        </div>

        <div class="col-12 col-lg-auto d-flex flex-wrap gap-3 d-print-none">
            <a href="<?= url('biolinks-templates') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-fw fa-arrow-left mr-1"></i> กลับหน้าเทมเพลต
            </a>
        </div>
    </div>

    <?php 
    // Get favorites from localStorage (will be handled by JavaScript)
    // For server-side, we'll filter in JavaScript or load all and filter client-side
    ?>
    <div id="favorites-loading" style="display: none;" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 text-muted">กำลังโหลดเทมเพลตที่คุณชอบ...</p>
    </div>
    
    <div id="favorites-container">
    <?php if (!empty($data->biolinks_templates)): ?>
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
        </style>
        <div class="row">
            <?php 
            $favorites = $data->favorites ?? [];
            foreach($data->biolinks_templates as $biolink_template): 
                $is_favorite = in_array($biolink_template->biolink_template_id, $favorites);
                $is_popular = ($biolink_template->total_usage ?? 0) > 100;
                $is_new = strtotime($biolink_template->datetime) > (time() - 30 * 24 * 60 * 60); // 30 days
            ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="custom-row mb-4 d-flex flex-column justify-content-between template-card-wrapper">
                        <div class="mb-3 template-image-wrapper">
                            <div class="template-image-skeleton"></div>
                            <img 
                                id="template-image-<?= $biolink_template->biolink_template_id ?>"
                                src="https://s0.wp.com/mshots/v1/<?= $biolink_template->url . '?preview_template' ?>?version=v4?w=350&h=395&t=<?= time() ?>" 
                                style="width: 100%; height: 24rem; border: 0; position: relative; z-index: 1;" 
                                class="rounded container-disabled-simple" 
                                loading="lazy"
                                data-template-url="<?= $biolink_template->url ?>"
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
                            
                            <button type="button" class="template-favorite-btn active" 
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

                        <a href="<?= $biolink_template->url ?>" target="_blank" class="btn btn-block btn-sm btn-light mb-2"><i class="fas fa-fw fa-sm fa-external-link-alt mr-1"></i> <?= l('biolinks_templates.preview') ?></a>

                        <button type="button" class="btn btn-block btn-sm btn-outline-primary" onclick="openTemplatePreview('<?= $biolink_template->url ?>', false)">
                            <i class="fas fa-fw fa-sm fa-mobile-alt mr-1"></i> ดูตัวอย่างแบบ Popup
                        </button>
                        
                        <button type="button" class="btn btn-block btn-sm btn-outline-secondary mt-2" onclick="openTemplatePreview('<?= $biolink_template->url ?>', true)">
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
    <?php else: ?>
        <div id="favorites-empty" class="text-center py-5">
            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
            <p class="text-muted">คุณยังไม่มีเทมเพลตที่ชอบ</p>
            <a href="<?= url('biolinks-templates') ?>" class="btn btn-primary">
                <i class="fas fa-fw fa-arrow-left mr-1"></i> ไปเลือกเทมเพลต
            </a>
        </div>
    <?php endif ?>
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
            
            // Reload page if removed from favorites page
            if(isFavorite && window.location.pathname.includes('favorites')) {
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            }
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
    
    /**
     * เปิด Popup Window แสดงตัวอย่าง Template ขนาดเท่ากับ iPhone 17 Pro Max
     */
    function openTemplatePreview(url, showFrame = false) {
        const iphone17ProMaxWidth = 380;
        const iphone17ProMaxHeight = 800;
        const windowWidth = iphone17ProMaxWidth + 20;
        const windowHeight = iphone17ProMaxHeight + 0;
        const left = (window.screen.width - windowWidth) / 2;
        const top = (window.screen.height - windowHeight) / 2;
        
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
        
        const popup = window.open(url, 'templatePreview', features);
        
        if (popup) {
            popup.addEventListener('load', function() {
                try {
                    const style = popup.document.createElement('style');
                    let frameCSS = '';
                    if (showFrame) {
                        frameCSS = `
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
                        html {
                            width: ${iphone17ProMaxWidth}px !important;
                            min-width: ${iphone17ProMaxWidth}px !important;
                            max-width: ${iphone17ProMaxWidth}px !important;
                            margin: 0 auto;
                            background: ${showFrame ? '#000' : 'transparent'};
                        }
                        body > *:not(script):not(style) {
                            margin: 0 auto;
                            max-width: ${iphone17ProMaxWidth}px;
                        }
                    `;
                    popup.document.head.appendChild(style);
                } catch (e) {
                    console.log('Cannot inject CSS (cross-origin):', e);
                }
            });
        }
    }
    
    // Load favorites from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Loading favorites page...');
        
        const favorites = getFavorites();
        console.log('📋 Favorites from localStorage:', favorites);
        
        // If no favorites in localStorage, show empty state
        if (favorites.length === 0) {
            console.log('⚠️ No favorites found');
            const emptyDiv = document.getElementById('favorites-empty');
            if (emptyDiv) emptyDiv.style.display = 'block';
            return;
        }
        
        // If we already have templates from server (via URL parameter), we're done
        <?php if (!empty($data->biolinks_templates)): ?>
            console.log('✅ Templates already loaded from server');
            return;
        <?php endif ?>
        
        // Otherwise, load templates by redirecting to URL with favorites parameter
        console.log('📡 Redirecting to load templates via server...');
        const loadingDiv = document.getElementById('favorites-loading');
        const containerDiv = document.getElementById('favorites-container');
        
        if (loadingDiv) loadingDiv.style.display = 'block';
        if (containerDiv) containerDiv.style.display = 'none';
        
        // Encode favorites for URL
        const favoritesParam = btoa(JSON.stringify(favorites));
        const favoritesUrl = '<?= url("biolinks-templates/favorites") ?>?favorites=' + encodeURIComponent(favoritesParam);
        
        // Redirect to URL with favorites parameter to load via server
        console.log('🔄 Redirecting to:', favoritesUrl);
        window.location.href = favoritesUrl;
    });
</script>
