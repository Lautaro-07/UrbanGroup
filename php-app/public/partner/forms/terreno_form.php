<?php
$existingPhotos = $isEdit ? $photoModel->getByPropertyId($editProperty['id']) : [];
$t = $terrenoDetails ?? [];
?>

<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2">Informacion General</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proyecto *</label>
                <input type="text" name="terreno[nombre_proyecto]"
                       value="<?= htmlspecialchars($t['nombre_proyecto'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titulo para Listado *</label>
                <input type="text" name="title" required
                       value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicacion / Direccion</label>
            <input type="text" name="terreno[ubicacion]"
                   value="<?= htmlspecialchars($t['ubicacion'] ?? $editProperty['address'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Direccion completa del terreno">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                <select name="region_id" id="region_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar Region...</option>
                    <?php foreach ($regions as $region): ?>
                    <option value="<?= $region['id'] ?>" <?= ($editProperty['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($region['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comuna</label>
                <select name="comuna_id" id="comuna_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Primero seleccione Region...</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Roles (separados por coma)</label>
            <input type="text" name="terreno[roles]"
                   value="<?= htmlspecialchars($t['roles'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Ej: 1234-5, 1234-6">
        </div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-semibold text-green-800 mb-2">Parametros Normativos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zona PRC Edificacion</label>
                <input type="text" name="terreno[zona_prc_edificacion]"
                       value="<?= htmlspecialchars($t['zona_prc_edificacion'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usos de Suelo Permitidos</label>
                <input type="text" name="terreno[usos_suelo_permitidos]"
                       value="<?= htmlspecialchars($t['usos_suelo_permitidos'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usos de Suelo</label>
                <input type="text" name="terreno[usos_suelo]"
                       value="<?= htmlspecialchars($t['usos_suelo'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sistema de Agrupamiento</label>
                <input type="text" name="terreno[sistema_agrupamiento]"
                       value="<?= htmlspecialchars($t['sistema_agrupamiento'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Altura Maxima</label>
                <input type="text" name="terreno[altura_maxima]"
                       value="<?= htmlspecialchars($t['altura_maxima'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rasante</label>
                <input type="text" name="terreno[rasante]"
                       value="<?= htmlspecialchars($t['rasante'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coef. Constructibilidad Max</label>
                <input type="text" name="terreno[coef_constructibilidad_max]"
                       value="<?= htmlspecialchars($t['coef_constructibilidad_max'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coef. Ocupacion Suelo Max</label>
                <input type="text" name="terreno[coef_ocupacion_suelo_max]"
                       value="<?= htmlspecialchars($t['coef_ocupacion_suelo_max'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coef. Area Libre Min</label>
                <input type="text" name="terreno[coef_area_libre_min]"
                       value="<?= htmlspecialchars($t['coef_area_libre_min'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Antejardin Minimo</label>
                <input type="text" name="terreno[antejardin_min]"
                       value="<?= htmlspecialchars($t['antejardin_min'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Distanciamientos</label>
                <input type="text" name="terreno[distanciamientos]"
                       value="<?= htmlspecialchars($t['distanciamientos'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Articulos Normativos</label>
            <textarea name="terreno[articulos_normativos]" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($t['articulos_normativos'] ?? '') ?></textarea>
        </div>
    </div>
    
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <h3 class="font-semibold text-amber-800 mb-2">Datos Dimensionales</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Frente (m)</label>
                <input type="number" name="terreno[frente]" step="0.01"
                       value="<?= $t['frente'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fondo (m)</label>
                <input type="number" name="terreno[fondo]" step="0.01"
                       value="<?= $t['fondo'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Total (m²)</label>
                <input type="number" name="terreno[superficie_total_terreno]" step="0.01"
                       value="<?= $t['superficie_total_terreno'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Util (m²)</label>
                <input type="number" name="terreno[superficie_util]" step="0.01"
                       value="<?= $t['superficie_util'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Bruta (m²)</label>
                <input type="number" name="terreno[superficie_bruta]" step="0.01"
                       value="<?= $t['superficie_bruta'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expropiacion (m²)</label>
                <input type="number" name="terreno[expropiacion]" step="0.01"
                       value="<?= $t['expropiacion'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
    
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-purple-800">Datos Anteproyecto</h3>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="terreno[has_anteproyecto]" value="1" 
                       <?= !empty($t['has_anteproyecto']) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm text-gray-700">Tiene Anteproyecto</span>
            </label>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">N° Viviendas</label>
                <input type="number" name="terreno[num_viviendas]"
                       value="<?= $t['num_viviendas'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Edificada (m²)</label>
                <input type="number" name="terreno[superficie_edificada]" step="0.01"
                       value="<?= $t['superficie_edificada'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sup. Util Anteproyecto (m²)</label>
                <input type="number" name="terreno[superficie_util_anteproyecto]" step="0.01"
                       value="<?= $t['superficie_util_anteproyecto'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Neta</label>
                <input type="text" name="terreno[densidad_neta]"
                       value="<?= htmlspecialchars($t['densidad_neta'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Maxima</label>
                <input type="text" name="terreno[densidad_maxima]"
                       value="<?= htmlspecialchars($t['densidad_maxima'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">N° Estacionamientos</label>
                <input type="number" name="terreno[num_estacionamientos]"
                       value="<?= $t['num_estacionamientos'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Est. Visitas</label>
                <input type="number" name="terreno[num_est_visitas]"
                       value="<?= $t['num_est_visitas'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Est. Bicicletas</label>
                <input type="number" name="terreno[num_est_bicicletas]"
                       value="<?= $t['num_est_bicicletas'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">N° Locales Comerciales</label>
                <input type="number" name="terreno[num_locales_comerciales]"
                       value="<?= $t['num_locales_comerciales'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">N° Bodegas</label>
                <input type="number" name="terreno[num_bodegas]"
                       value="<?= $t['num_bodegas'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Superficies Aprobadas</label>
            <textarea name="terreno[superficies_aprobadas]" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($t['superficies_aprobadas'] ?? '') ?></textarea>
        </div>
    </div>
    
    <div class="bg-rose-50 border border-rose-200 rounded-lg p-4">
        <h3 class="font-semibold text-rose-800 mb-2">Comercializacion</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio (UF) *</label>
                <input type="number" name="price" step="0.01" required
                       value="<?= $editProperty['price'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <input type="hidden" name="currency" value="UF">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Alternativo</label>
                <input type="text" name="terreno[precio]"
                       value="<?= htmlspecialchars($t['precio'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: 150.000 UF">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comision (%)</label>
                <input type="text" name="terreno[comision]"
                       value="<?= htmlspecialchars($t['comision'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: 2%">
            </div>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Observaciones</h3>
        <textarea name="terreno[observaciones]" rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Observaciones adicionales..."><?= htmlspecialchars($t['observaciones'] ?? '') ?></textarea>
        <textarea name="description" rows="3" class="hidden"><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fotos (max. 12)</h3>
        
        <?php if (!empty($existingPhotos)): ?>
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Fotos actuales (<?= count($existingPhotos) ?>):</p>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                <?php foreach ($existingPhotos as $idx => $photo): ?>
                <div class="relative group">
                    <img src="<?= htmlspecialchars($photo['photo_url']) ?>" alt="Foto <?= $idx + 1 ?>" 
                         class="w-full h-20 object-cover rounded border">
                    <label class="absolute bottom-0 right-0 bg-red-600 text-white text-xs px-1 cursor-pointer">
                        <input type="checkbox" name="delete_photos[]" value="<?= $photo['id'] ?>" class="hidden">
                        X
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <input type="file" name="property_photos[]" multiple accept="image/jpeg,image/png"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Documento PDF</h3>
        <?php if (!empty($t['pdf_documento'])): ?>
        <div class="mb-4 p-3 bg-gray-100 rounded-lg flex items-center justify-between">
            <span class="text-sm">PDF actual: <?= basename($t['pdf_documento']) ?></span>
            <a href="<?= htmlspecialchars($t['pdf_documento']) ?>" target="_blank" class="text-blue-600 text-sm hover:underline">Ver PDF</a>
        </div>
        <?php endif; ?>
        <input type="file" name="pdf_documento" accept="application/pdf"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Sube un PDF con la ficha tecnica del terreno.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const comunaSelect = document.getElementById('comuna_id');
    const currentComunaId = '<?= $editProperty['comuna_id'] ?? '' ?>';
    
    function loadComunas(regionId) {
        if (!regionId) {
            comunaSelect.innerHTML = '<option value="">Primero seleccione Region...</option>';
            return;
        }
        
        fetch('/api/comunas.php?region_id=' + regionId)
            .then(response => response.json())
            .then(data => {
                comunaSelect.innerHTML = '<option value="">Seleccionar Comuna...</option>';
                data.forEach(comuna => {
                    const option = document.createElement('option');
                    option.value = comuna.id;
                    option.textContent = comuna.name;
                    if (comuna.id == currentComunaId) option.selected = true;
                    comunaSelect.appendChild(option);
                });
            })
            .catch(() => {
                comunaSelect.innerHTML = '<option value="">Error al cargar comunas</option>';
            });
    }
    
    regionSelect.addEventListener('change', function() {
        loadComunas(this.value);
    });
    
    if (regionSelect.value) {
        loadComunas(regionSelect.value);
    }
});
</script>
