<!DOCTYPE html>
<html lang="en">
<?php include '../src/includes/header.php'; ?>
<body>
    <div class="container mt-5">
        <div class="mb-4">
            <a href="chat.php?cid=<?=$_GET['cid'];?>" class="btn btn-secondary">Back to chat</a>
            <h2 class="mt-3">Group Files</h2>
        </div>

        <div id="filesContainer" class="row g-3">
            <div class="col-12">
                <p class="text-muted">Loading files...</p>
            </div>
        </div>

        <div id="emptyState" class="d-none">
            <div class="text-center mt-5">
                <p class="text-muted">No files uploaded yet</p>
            </div>
        </div>

        <div id="errorState" class="d-none">
            <div class="alert alert-danger" role="alert">
                <p id="errorMessage" class="mb-0"></p>
            </div>
        </div>
    </div>

    <script>
        const chatId = new URLSearchParams(window.location.search).get('cid');

        function getFileIcon(ext) {
            const iconMap = {
                'pdf': 'file-pdf',
                'doc': 'file-word',
                'docx': 'file-word',
                'xls': 'file-excel',
                'xlsx': 'file-excel',
                'ppt': 'file-powerpoint',
                'pptx': 'file-powerpoint',
                'txt': 'file-text',
                'jpg': 'file-image',
                'jpeg': 'file-image',
                'png': 'file-image',
                'gif': 'file-image',
                'zip': 'file-zip',
                'rar': 'file-zip',
                '7z': 'file-zip',
                'mp3': 'file-music',
                'wav': 'file-music',
                'mp4': 'file-video',
                'avi': 'file-video',
                'mov': 'file-video'
            };
            return iconMap[ext.toLowerCase()] || 'file';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

        function formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetch(`../src/api/getGroupFiles.php?cid=${chatId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('filesContainer');
                    const emptyState = document.getElementById('emptyState');
                    const errorState = document.getElementById('errorState');

                    if (data.status === 'error') {
                        container.classList.add('d-none');
                        emptyState.classList.add('d-none');
                        errorState.classList.remove('d-none');
                        document.getElementById('errorMessage').textContent = data.message;
                        return;
                    }

                    if (data.files.length === 0) {
                        container.classList.add('d-none');
                        emptyState.classList.remove('d-none');
                        errorState.classList.add('d-none');
                        return;
                    }

                    container.innerHTML = '';
                    data.files.forEach(file => {
                        const icon = getFileIcon(file.ext);
                        const card = document.createElement('div');
                        card.className = 'col-md-6 col-lg-4';
                        card.innerHTML = `
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="bi bi-${icon}" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title text-break">${escapeHtml(file.name)}</h6>
                                    <p class="card-text small text-muted mb-2">
                                        ${formatFileSize(file.size)}
                                    </p>
                                    <p class="card-text small text-muted mb-3">
                                        ${formatDate(file.modified)}
                                    </p>
                                    <a href="${file.url}" class="btn btn-sm btn-primary" download>
                                        Download
                                    </a>
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                })
                .catch(error => {
                    const container = document.getElementById('filesContainer');
                    const emptyState = document.getElementById('emptyState');
                    const errorState = document.getElementById('errorState');
                    container.classList.add('d-none');
                    emptyState.classList.add('d-none');
                    errorState.classList.remove('d-none');
                    document.getElementById('errorMessage').textContent = 'Failed to load files';
                });
        });

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
