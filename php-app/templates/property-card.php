<article class="property-card">
    <a href="/propiedad.php?id=<?= $property['id'] ?>">
        <div class="property-card-image">
            <img src="<?= getFirstImage($property['images']) ?>" alt="<?= htmlspecialchars($property['title']) ?>">
            <span class="property-badge <?= getOperationBadgeColor($property['operation_type']) ?>">
                <?= $property['operation_type'] ?>
            </span>
            <?php if ($property['is_featured']): ?>
                <span class="property-featured-badge">Destacada</span>
            <?php endif; ?>
        </div>
        
        <div class="property-card-content">
            <span class="property-type-badge <?= getPropertyTypeBadgeColor($property['property_type']) ?>">
                <?= $property['property_type'] ?>
            </span>
            
            <h3 class="property-card-title"><?= htmlspecialchars($property['title']) ?></h3>
            
            <div class="property-card-location">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <?= htmlspecialchars($property['comuna_name'] ?? '') ?><?= !empty($property['region_name']) ? ', ' . htmlspecialchars($property['region_name']) : '' ?>
            </div>
            
            <div class="property-card-features">
                <?php if (!empty($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                    <div class="property-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <?= $property['bedrooms'] ?> Dorm.
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                    <div class="property-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                        </svg>
                        <?= $property['bathrooms'] ?> Baños
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($property['built_area']) && $property['built_area'] > 0): ?>
                    <div class="property-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        <?= formatArea($property['built_area']) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="property-card-footer">
                <div class="property-price">
                    <?= formatPrice($property['price'], $property['currency']) ?>
                    <?php if ($property['operation_type'] === 'Arriendo'): ?>
                        <span class="property-price-period">/mes</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </a>
</article>
