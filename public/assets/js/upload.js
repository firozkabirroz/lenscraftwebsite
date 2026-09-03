(function () {
    'use strict';

    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('videoFile');
    var list = document.getElementById('uploadList');
    var form = document.getElementById('videoForm');

    if (!dropzone || !fileInput || !form) {
        return;
    }

    var chunkSize = parseInt(dropzone.dataset.chunkSize, 10) || 4 * 1024 * 1024;
    var maxSize = parseInt(dropzone.dataset.maxSize, 10) || 0;
    var endpoint = dropzone.dataset.endpoint;
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var sourceInput = document.getElementById('sourceInput');
    var filePathInput = document.getElementById('filePathInput');
    var sizeInput = document.getElementById('sizeInput');
    var durationInput = document.getElementById('durationInput');
    var titleInput = document.getElementById('titleInput');

    // ------------------------------------------------------------ tab switch
    document.querySelectorAll('#sourceTabs .tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var source = tab.dataset.source;
            document.querySelectorAll('#sourceTabs .tab').forEach(function (t) {
                t.classList.toggle('is-active', t === tab);
            });
            document.querySelectorAll('[data-pane]').forEach(function (pane) {
                pane.hidden = pane.dataset.pane !== source;
            });
            sourceInput.value = source;
        });
    });

    // -------------------------------------------------------------- dropzone
    ['dragenter', 'dragover'].forEach(function (type) {
        dropzone.addEventListener(type, function (event) {
            event.preventDefault();
            dropzone.classList.add('is-over');
        });
    });

    ['dragleave', 'drop'].forEach(function (type) {
        dropzone.addEventListener(type, function (event) {
            event.preventDefault();
            dropzone.classList.remove('is-over');
        });
    });

    dropzone.addEventListener('drop', function (event) {
        if (event.dataTransfer.files.length) {
            handleFile(event.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) {
            handleFile(fileInput.files[0]);
        }
    });

    function humanSize(bytes) {
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = 0;
        while (bytes >= 1024 && i < units.length - 1) {
            bytes /= 1024;
            i++;
        }
        return (i > 0 ? bytes.toFixed(1) : bytes) + ' ' + units[i];
    }

    function uuid() {
        return 'u' + Date.now().toString(16) + Math.random().toString(16).slice(2, 8);
    }

    function readDuration(file) {
        var video = document.createElement('video');
        video.preload = 'metadata';
        video.onloadedmetadata = function () {
            durationInput.value = Math.round(video.duration || 0);
            URL.revokeObjectURL(video.src);
        };
        video.src = URL.createObjectURL(file);
    }

    function handleFile(file) {
        if (!file.type.startsWith('video/')) {
            window.alert('That file is not a video.');
            return;
        }
        if (maxSize && file.size > maxSize) {
            window.alert('This file is larger than the ' + humanSize(maxSize) + ' limit.');
            return;
        }

        if (!titleInput.value) {
            titleInput.value = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ');
        }

        readDuration(file);
        uploadInChunks(file, renderItem(file));
    }

    function renderItem(file) {
        var item = document.createElement('div');
        item.className = 'upload-item';
        item.innerHTML =
            '<div class="upload-item__head"><strong></strong><span>0% · ' + humanSize(file.size) + '</span></div>' +
            '<div class="upload-item__bar"><b></b></div>';
        item.querySelector('strong').textContent = file.name;
        list.appendChild(item);

        return {
            root: item,
            bar: item.querySelector('b'),
            label: item.querySelector('span')
        };
    }

    function uploadInChunks(file, ui) {
        var uploadId = uuid();
        var total = Math.max(1, Math.ceil(file.size / chunkSize));
        var index = 0;

        function sendNext() {
            if (index >= total) {
                return;
            }

            var start = index * chunkSize;
            var blob = file.slice(start, Math.min(start + chunkSize, file.size));
            var data = new FormData();
            data.append('_csrf', csrf);
            data.append('upload_id', uploadId);
            data.append('index', String(index));
            data.append('total', String(total));
            data.append('name', file.name);
            data.append('chunk', blob);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', endpoint, true);

            xhr.onload = function () {
                var response;
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (error) {
                    response = { ok: false, error: 'Unexpected server response.' };
                }

                if (!response.ok) {
                    ui.root.classList.add('is-failed');
                    ui.label.textContent = response.error || 'Upload failed';
                    return;
                }

                index++;
                var percent = Math.round(index / total * 100);
                ui.bar.style.width = percent + '%';
                ui.label.textContent = percent + '% · ' + humanSize(file.size);

                if (response.done) {
                    ui.root.classList.add('is-done');
                    ui.label.textContent = 'Uploaded · ' + humanSize(file.size);
                    filePathInput.value = response.path;
                    sizeInput.value = String(file.size);
                    sourceInput.value = 'local';
                    return;
                }

                sendNext();
            };

            xhr.onerror = function () {
                ui.root.classList.add('is-failed');
                ui.label.textContent = 'Network error — check XAMPP and try again.';
            };

            xhr.send(data);
        }

        sendNext();
    }

    form.addEventListener('submit', function (event) {
        if (sourceInput.value === 'local' && !filePathInput.value) {
            event.preventDefault();
            window.alert('Wait for the upload to finish, or switch to an embed link.');
        }
    });
})();
