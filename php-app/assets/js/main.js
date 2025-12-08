function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('regionSelect');
    const comunaSelect = document.getElementById('comunaSelect');
    
    if (regionSelect && comunaSelect) {
        regionSelect.addEventListener('change', function() {
            const regionId = this.value;
            
            if (!regionId) {
                comunaSelect.innerHTML = '<option value="">Seleccionar comuna</option>';
                return;
            }
            
            fetch('/api/comunas.php?region_id=' + regionId)
                .then(response => response.json())
                .then(comunas => {
                    let options = '<option value="">Seleccionar comuna</option>';
                    comunas.forEach(comuna => {
                        options += `<option value="${comuna.id}">${comuna.name}</option>`;
                    });
                    comunaSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error('Error loading comunas:', error);
                });
        });
    }
});
