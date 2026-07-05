@extends('layouts.admin')

@section('content')
    <style>
        .page-wrapper {

            position: relative;

            margin-bottom: 30px;

        }

        #signature {

            cursor: move;

            z-index: 999;

            border: 1px dashed #2563eb;

            background: rgba(255, 255, 255, 0.65);

        }
    </style>

    <div class="p-6">

        <h1 class="text-2xl font-bold mb-6">
            Atur Posisi Tanda Tangan
        </h1>

        <div class="bg-white rounded-lg shadow p-5">

            <div id="loading" class="text-center py-10 text-gray-500">

                <svg class="animate-spin h-8 w-8 mx-auto mb-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">

                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                    </path>

                </svg>

                <p>Memuat preview PDF...</p>

            </div>

            <div id="pdf-container"></div>

        </div>

        <div class="mt-5 flex gap-3">

            <button id="savePosition" class="bg-blue-600 text-white px-4 py-2 rounded">

                Simpan Posisi

            </button>
            

            <form id="generateFinalForm" action="{{ route('admin.agreement.generate-final', ['agreement' => $agreement->id]) }}" method="POST">

    @csrf
    <input type="hidden" name="page" id="final_page">
    <input type="hidden" name="x" id="final_x">
    <input type="hidden" name="y" id="final_y">
    <input type="hidden" name="width" id="final_width">
    <input type="hidden" name="height" id="final_height">

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
        Generate PDF Final
    </button>

</form>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const url =
            "{{ asset('storage/' . $agreement->file) }}";

        const pdfScale = 1.5;

        let loadedPages = 0;
        let signatureElement = null;
        const pageSizes = {};

        let signatureData = {

            page: @json(is_null($agreement->signature_x) || is_null($agreement->signature_y) ? null : ($agreement->signature_page ?? 1)),

            x: {{ $agreement->signature_x ?? 0 }},

            y: {{ $agreement->signature_y ?? 0 }},

            width: {{ $agreement->signature_width ?? 250 }},

            height: {{ $agreement->signature_height ?? 90 }}

        };

        pdfjsLib.getDocument(url).promise.then(function(pdf) {

            if (!signatureData.page || signatureData.page > pdf.numPages) {
                signatureData.page = pdf.numPages;
            }

            for (let page = 1; page <= pdf.numPages; page++) {

                pdf.getPage(page).then(function(p) {

                    let viewport = p.getViewport({
                        scale: pdfScale
                    });

                    let wrapper = document.createElement("div");

                    wrapper.className = "page-wrapper";

                    wrapper.dataset.page = page;

                    wrapper.style.position = "relative";

                    wrapper.style.marginBottom = "30px";

                    pageSizes[page] = {
                        previewWidth: viewport.width,
                        previewHeight: viewport.height,
                        pdfWidth: viewport.width / pdfScale,
                        pdfHeight: viewport.height / pdfScale,
                    };

                    let canvas = document.createElement("canvas");

                    canvas.width = viewport.width;

                    canvas.height = viewport.height;

                    wrapper.appendChild(canvas);

                    document
                        .getElementById("pdf-container")
                        .appendChild(wrapper);

                    let context = canvas.getContext("2d");

                    p.render({

                        canvasContext: context,

                        viewport: viewport

                    }).promise.then(function() {

                        loadedPages++;

                        // sembunyikan loading jika semua halaman selesai
                        if (loadedPages === pdf.numPages) {

                            document.getElementById("loading").style.display = "none";

                        }

                    });

                    if (page === signatureData.page) {

                        placeSignature(wrapper, page);

                    }

                });

            }

        });

        function toPreview(value, page, axis = "x") {
            const size = pageSizes[page];

            if (!size) {
                return value;
            }

            return value * (
                axis === "x"
                    ? size.previewWidth / size.pdfWidth
                    : size.previewHeight / size.pdfHeight
            );
        }

        function fromPreview(value, page, axis = "x") {
            const size = pageSizes[page];

            if (!size) {
                return value;
            }

            return value * (
                axis === "x"
                    ? size.pdfWidth / size.previewWidth
                    : size.pdfHeight / size.previewHeight
            );
        }

        function placeSignature(wrapper, page) {
            if (!signatureElement) {
                signatureElement = document.createElement("img");
                signatureElement.src = "{{ asset('storage/' . $agreement->signature_file) }}";
                signatureElement.id = "signature";
                signatureElement.style.position = "absolute";
                signatureElement.style.cursor = "move";
                signatureElement.style.zIndex = "999";
                enableDrag(signatureElement);
            }

            signatureData.page = page;

            signatureElement.style.left = toPreview(signatureData.x, page, "x") + "px";
            signatureElement.style.top = toPreview(signatureData.y, page, "y") + "px";
            signatureElement.style.width = toPreview(signatureData.width, page, "x") + "px";
            signatureElement.style.height = toPreview(signatureData.height, page, "y") + "px";

            wrapper.appendChild(signatureElement);
        }

        function updateSignatureDataFromElement(target) {
            const page = Number(target.parentElement.dataset.page);
            const left = parseFloat(target.style.left || 0);
            const top = parseFloat(target.style.top || 0);

            signatureData.page = page;
            signatureData.x = fromPreview(left, page, "x");
            signatureData.y = fromPreview(top, page, "y");
            signatureData.width = fromPreview(target.offsetWidth, page, "x");
            signatureData.height = fromPreview(target.offsetHeight, page, "y");
        }

        function getSignaturePayload() {
            if (signatureElement) {
                updateSignatureDataFromElement(signatureElement);
            }

            return {
                page: signatureData.page,
                x: signatureData.x,
                y: signatureData.y,
                width: signatureData.width,
                height: signatureData.height
            };
        }

        function fillGenerateForm() {
            const payload = getSignaturePayload();

            document.getElementById("final_page").value = payload.page;
            document.getElementById("final_x").value = payload.x;
            document.getElementById("final_y").value = payload.y;
            document.getElementById("final_width").value = payload.width;
            document.getElementById("final_height").value = payload.height;
        }



        function enableDrag(img) {

            interact(img)

                .draggable({

    listeners: {

        start(event) {

            event.target.dataset.startX =
                parseFloat(event.target.style.left || 0);

            event.target.dataset.startY =
                parseFloat(event.target.style.top || 0);
        },

        move(event) {

            let left =
                parseFloat(event.target.style.left || 0);

            let top =
                parseFloat(event.target.style.top || 0);

            left += event.dx;
            top += event.dy;

            // jangan keluar halaman
            left = Math.max(
                0,
                Math.min(
                    left,
                    event.target.parentElement.clientWidth -
                    event.target.offsetWidth
                )
            );

            top = Math.max(
                0,
                Math.min(
                    top,
                    event.target.parentElement.clientHeight -
                    event.target.offsetHeight
                )
            );

            event.target.style.left = left + "px";
            event.target.style.top = top + "px";

            updateSignatureDataFromElement(event.target);
        }

    }

})

                .resizable({

                    edges: {
                        left: true,
                        right: true,
                        top: true,
                        bottom: true
                    },

                    listeners: {

                        move(event) {

                            let target = event.target;

                            let left = parseFloat(target.style.left) || 0;
let top = parseFloat(target.style.top) || 0;

left += event.deltaRect.left;
top += event.deltaRect.top;

left = Math.max(
    0,
    Math.min(
        left,
        target.parentElement.clientWidth - event.rect.width
    )
);

top = Math.max(
    0,
    Math.min(
        top,
        target.parentElement.clientHeight - event.rect.height
    )
);

target.style.width = event.rect.width + "px";
target.style.height = event.rect.height + "px";
target.style.left = left + "px";
target.style.top = top + "px";

updateSignatureDataFromElement(target);

                        }

                    }

                });

        }

        document.getElementById("savePosition").onclick = function () {

    const payload = getSignaturePayload();

    if (payload.width <= 0) {

        alert("Ukuran tanda tangan tidak valid.");
        return;

    }
    

    fetch(
        "{{ route('admin.agreement.save-signature', ['agreement' => $agreement->id]) }}",
        {

            method: "POST",

            headers: {

                "Content-Type": "application/json",

                "X-CSRF-TOKEN": "{{ csrf_token() }}"

            },

            body: JSON.stringify(payload)

        }

    )

    .then(response => {

        if (!response.ok) {

            throw new Error("Gagal menyimpan posisi tanda tangan.");

        }

        return response.json();

    })

    .then(data => {

    if (data.success) {

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Posisi tanda tangan berhasil disimpan.',
            timer: 1500,
            showConfirmButton: false
        });

    } else {

        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Posisi tanda tangan gagal disimpan.'
        });

    }

})
.catch(error => {

    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: error.message
    });

});
        };

document.getElementById("generateFinalForm").addEventListener("submit", function () {
    fillGenerateForm();
});
    </script>
@endsection
