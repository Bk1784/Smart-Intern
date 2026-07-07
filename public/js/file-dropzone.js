(() => {
    function initDropzone(wrapper) {
        const area = wrapper.querySelector('.dropzone-area');
        const input = wrapper.querySelector('.dropzone-file-input');
        const countLabel = wrapper.querySelector('.dropzone-count-label');
        const previewGrid = wrapper.querySelector('.dropzone-preview-grid');
        const maxFiles = parseInt(input.dataset.maxFiles || '10', 10);

        let selectedFiles = [];

        function renderPreviews() {
            previewGrid.innerHTML = '';

            if (selectedFiles.length === 0) {
                previewGrid.classList.add('hidden');
                countLabel.textContent = 'Belum ada foto dipilih';
                return;
            }

            previewGrid.classList.remove('hidden');
            countLabel.textContent = selectedFiles.length + ' foto dipilih';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const item = document.createElement('div');
                    item.className = 'relative group';
                    item.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg border border-gray-200 dark:border-neutral-700">
                        <button type="button" data-index="${index}"
                            class="dropzone-remove absolute -top-2 -right-2 size-5 inline-flex items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>`;
                    previewGrid.appendChild(item);

                    item.querySelector('.dropzone-remove').addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                        renderPreviews();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function syncInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file) => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }

        function addFiles(fileList) {
            const newFiles = Array.from(fileList).filter((file) => file.type.startsWith('image/'));
            selectedFiles = [...selectedFiles, ...newFiles].slice(0, maxFiles);
            syncInputFiles();
            renderPreviews();
        }

        input.addEventListener('change', (e) => addFiles(e.target.files));

        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('border-blue-400', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
        area.addEventListener('dragleave', (e) => {
            e.preventDefault();
            area.classList.remove('border-blue-400', 'bg-blue-50/40', 'dark:bg-blue-900/10');
        });
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('border-blue-400', 'bg-blue-50/40', 'dark:bg-blue-900/10');
            addFiles(e.dataTransfer.files);
        });
    }

    function initAll() {
        document.querySelectorAll('[data-dropzone-wrapper]:not([data-dz-init])').forEach((wrapper) => {
            wrapper.setAttribute('data-dz-init', '1');
            initDropzone(wrapper);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Kalau navigate-form / router SPA kamu memancarkan event setelah konten baru dipasang,
    // ganti nama event di bawah ini sesuai event yang tersedia, supaya dropzone di-init ulang.
    document.addEventListener('navigate:loaded', initAll);
})();