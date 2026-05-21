@props([
    'dropdownId' => 'pdfOptions',
    'paperSizeId' => 'paperSize',
    'orientationId' => 'orientation',
    'align' => 'right',
    'landscapeDefault' => false,
    'exportOnclick' => 'exportPdf()',
])

<div {{ $attributes->merge(['class' => 'pdf-export-dropdown pdf-export-dropdown--' . $align]) }}>
    <button type="button" class="btn-pdf" data-pdf-toggle="{{ $dropdownId }}" aria-expanded="false" aria-haspopup="true">
        <i class="fas fa-file-pdf" aria-hidden="true"></i>
        <span>Export PDF</span>
    </button>
    <div id="{{ $dropdownId }}" class="pdf-options" role="menu" aria-label="PDF export options">
        <div class="pdf-options-field">
            <label class="pdf-options-label" for="{{ $paperSizeId }}">Paper Size</label>
            <select id="{{ $paperSizeId }}" class="pdf-options-select">
                <option value="A4">A4</option>
                <option value="Letter">Letter</option>
                <option value="Legal">Legal</option>
            </select>
        </div>
        <div class="pdf-options-field">
            <label class="pdf-options-label" for="{{ $orientationId }}">Orientation</label>
            <select id="{{ $orientationId }}" class="pdf-options-select">
                <option value="portrait" @selected(!$landscapeDefault)>Portrait</option>
                <option value="landscape" @selected($landscapeDefault)>Landscape</option>
            </select>
        </div>
        <button type="button" class="btn-pdf btn-pdf--block" onclick="{{ $exportOnclick }}">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>
            <span>Export PDF</span>
        </button>
    </div>
</div>
