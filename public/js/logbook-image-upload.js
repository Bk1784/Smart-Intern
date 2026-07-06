
document.addEventListener('DOMContentLoaded', function () {
    const imagesInput = document.getElementById('images-input');
    const dropzone = document.getElementById('dropzone');
    const fileCountLabel = document.getElementById('file-count-label');
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewGrid = document.getElementById('preview-grid');

    if (!imagesInput || !dropzone || !previewGrid) {
        return;
    }

    let fileStore = new DataTransfer();

    function renderPreview() {
        previewGrid.innerHTML = '';

        if (fileStore.files.length === 0) {
            previewWrapper.classList.add('hidden');
            fileCountLabel.textContent = 'Belum ada foto dipilih';
            return;
        }

        previewWrapper.classList.remove('hidden');
        fileCountLabel.textContent = fileStore.files.length + ' foto dipilih';

        Array.from(fileStore.files).forEach(function (file, index) {
            const url = URL.createObjectURL(file);

            const item = document.createElement('div');
            item.className = 'group relative aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-100 dark:bg-neutral-800';
            item.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover">
                <button type="button" data-index="${index}"
                    class="remove-preview-btn absolute top-1 right-1 size-6 inline-flex items-center justify-center rounded-full bg-white/90 text-red-600 hover:bg-white shadow-sm dark:bg-neutral-900/90"
                    title="Batalkan foto ini">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        <line x1="10" x2="10" y1="11" y2="17" />
                        <line x1="14" x2="14" y1="11" y2="17" />
                    </svg>
                </button>
            `;
            previewGrid.appendChild(item);
        });

        // Sinkronkan ke <input type="file"> supaya ikut ter-submit
        imagesInput.files = fileStore.files;
    }

    function addFiles(newFiles) {
        Array.from(newFiles).forEach(function (file) {
            if (file.type.startsWith('image/')) {
                fileStore.items.add(file);
            }
        });
        renderPreview();
    }

    // Klik / pilih file biasa
    imagesInput.addEventListener('change', function () {
        addFiles(this.files);
    });

    // Hapus 1 foto dari daftar preview
    previewGrid.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-preview-btn');
        if (!btn) return;

        const indexToRemove = parseInt(btn.dataset.index, 10);
        const newStore = new DataTransfer();

        Array.from(fileStore.files).forEach(function (file, i) {
            if (i !== indexToRemove) newStore.items.add(file);
        });

        fileStore = newStore;
        renderPreview();
    });

    // Drag & drop
    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-blue-400', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
    });

    dropzone.addEventListener('drop', function (e) {
        const dropped = e.dataTransfer.files;
        if (dropped.length > 0) addFiles(dropped);
    });
});