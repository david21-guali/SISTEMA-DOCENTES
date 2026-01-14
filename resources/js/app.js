import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Validación global de tamaño de archivos
document.addEventListener('change', function (e) {
    if (e.target.type === 'file' && e.target.files.length > 0) {
        const file = e.target.files[0];
        // Límite de 10MB por defecto (puedes ajustarlo si lo deseas)
        // Pero el servidor (PHP) suele tener límites como 2M, 8M o 100M
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (file.size > maxSize) {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Archivo pesado',
                    text: `El archivo detectado mide ${sizeInMB} MB. El sistema recomienda archivos menores a 10 MB para evitar errores.`,
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert(`El archivo (${sizeInMB} MB) es muy grande. Se recomienda menos de 10 MB.`);
            }

            // Opcional: Impedir la subida si es exageradamente grande (ej: > 2GB)
            const absoluteLimit = 1900 * 1024 * 1024;
            if (file.size > absoluteLimit) {
                Swal.fire({
                    icon: 'error',
                    title: 'Límite crítico excedido',
                    text: 'El archivo es demasiado grande para ser procesado por el servidor.',
                    confirmButtonColor: '#d33'
                });
                e.target.value = '';
            }
        }
    }
});

Alpine.start();
