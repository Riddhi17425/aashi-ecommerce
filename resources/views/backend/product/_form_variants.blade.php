@php
    $sizeData   = $product ? (json_decode($product->size, true) ?: []) : [];
    $sizes      = $sizeData['size'] ?? [];
    $prices     = $sizeData['price'] ?? [];
    $colors     = $product ? $product->color()->with('images')->get() : collect();
    $variantMap = $product
        ? $product->variants->mapWithKeys(fn ($v) => [$v->size . '|' . ($v->color_id ?? 'none') => $v->quantity])->all()
        : [];
@endphp

@if($errors->has('size.*') || $errors->has('size') || $errors->has('color_name.*'))
    <div class="alert alert-danger">Please check the sizes and colours. Every size needs a price, and every colour needs a name.</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Sizes & Prices</h6>
        <div id="size-rows">
            @foreach($sizes as $i => $size)
                <div class="row mb-2 size-row">
                    <div class="col-md-5">
                        <select name="size[]" class="form-control">
                            <option value="">--Select Size--</option>
                            @foreach($postSize as $val)
                                <option value="{{ $val->title }}" {{ $val->title == $size ? 'selected' : '' }}>{{ $val->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" name="price[]" min="0" class="form-control" placeholder="Price" value="{{ $prices[$i] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-size">-</button>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-size-row" class="btn btn-primary btn-sm mt-2">+ Add Size</button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Colours & Images</h6>
        <div id="color-rows">
            @foreach($colors as $color)
                <div class="row mb-3 color-group">
                    <input type="hidden" name="color_id[]" value="{{ $color->id }}">
                    <div class="col-md-4">
                        <input type="text" name="color_name[]" class="form-control" placeholder="Colour name" value="{{ $color->color_name }}">
                    </div>
                    <div class="col-md-2">
                        <input type="color" name="color_code[]" class="form-control" value="{{ $color->color_code }}">
                    </div>
                    <div class="col-md-4">
                        <input type="file" name="color_images[{{ $loop->index }}][]" class="form-control" multiple>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-color">-</button>
                    </div>
                    @if($color->images->count())
                        <div class="col-md-12 mt-2">
                            @foreach($color->images as $image)
                                <img src="{{ asset('public/storage/products/'.$image->image) }}" width="80" class="me-2">
                                <label class="me-3"><input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Delete</label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <input type="hidden" name="deleted_colors" id="deleted-colors" value="">
        <button type="button" id="add-color-row" class="btn btn-primary btn-sm mt-2">+ Add Colour</button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-1">Quantity per Size & Colour</h6>
        <p class="text-muted small mb-3">Enter the stock for each size and colour combination.</p>
        <div id="variant-grid"></div>
    </div>
</div>

<template id="size-row-template">
    <div class="row mb-2 size-row">
        <div class="col-md-5">
            <select name="size[]" class="form-control">
                <option value="">--Select Size--</option>
                @foreach($postSize as $val)
                    <option value="{{ $val->title }}">{{ $val->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <input type="number" name="price[]" min="0" class="form-control" placeholder="Price">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-size">-</button>
        </div>
    </div>
</template>

<template id="color-row-template">
    <div class="row mb-3 color-group">
        <input type="hidden" name="color_id[]" value="">
        <div class="col-md-4">
            <input type="text" name="color_name[]" class="form-control" placeholder="Colour name">
        </div>
        <div class="col-md-2">
            <input type="color" name="color_code[]" class="form-control" value="#000000">
        </div>
        <div class="col-md-4">
            <input type="file" name="color_images[__IDX__][]" class="form-control" multiple>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-color">-</button>
        </div>
    </div>
</template>

<script type="application/json" id="variant-prefill">@json($variantMap)</script>

@push('scripts')
<script>
$(function () {
    var uid = 0;
    var prefill = JSON.parse(document.getElementById('variant-prefill').textContent || '{}');

    function esc(s) {
        return String(s === undefined || s === null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function parseRow(html) {
        return $($.parseHTML(html.trim()));
    }

    function colorKey($row) {
        var id = $row.find('input[name="color_id[]"]').val();
        return id ? String(id) : 'new-' + $row.attr('data-uid');
    }

    function currentValues() {
        var vals = {};
        $('#variant-grid input[data-key]').each(function () {
            vals[$(this).attr('data-key')] = $(this).val();
        });
        return vals;
    }

    function rebuildGrid() {
        var old = $.extend({}, prefill, currentValues());

        var sizes = [];
        $('#size-rows .size-row').each(function (i) {
            sizes.push({ idx: i, name: $(this).find('select[name="size[]"]').val() });
        });

        var colors = [];
        $('#color-rows .color-group').each(function (i) {
            colors.push({
                idx: i,
                key: colorKey($(this)),
                name: $(this).find('input[name="color_name[]"]').val() || ('Colour ' + (i + 1))
            });
        });
        if (!colors.length) {
            colors = [{ idx: 0, key: 'none', name: 'Quantity' }];
        }

        var html = '';
        if (!sizes.some(function (s) { return s.name; })) {
            html = '<p class="text-muted mb-0">Select at least one size to enter quantities.</p>';
        } else {
            html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Size</th>';
            colors.forEach(function (c) { html += '<th>' + esc(c.name) + '</th>'; });
            html += '</tr></thead><tbody>';

            sizes.forEach(function (s) {
                if (!s.name) return;
                html += '<tr><th>' + esc(s.name) + '</th>';
                colors.forEach(function (c) {
                    var key = s.name + '|' + c.key;
                    var val = old[key] !== undefined ? old[key] : 0;
                    html += '<td><input type="number" min="0" class="form-control" data-key="' + esc(key) +
                            '" name="variant_qty[' + s.idx + '][' + c.idx + ']" value="' + esc(val) + '"></td>';
                });
                html += '</tr>';
            });
            html += '</tbody></table></div>';
        }

        $('#variant-grid').html(html);
    }

    $(document).on('click', '#add-size-row', function () {
        $('#size-rows').append(parseRow(document.getElementById('size-row-template').innerHTML));
        rebuildGrid();
    });

    $(document).on('click', '.remove-size', function () {
        $(this).closest('.size-row').remove();
        rebuildGrid();
    });

    $(document).on('click', '#add-color-row', function () {
        uid++;
        var html = document.getElementById('color-row-template').innerHTML
            .replace(/__IDX__/g, $('#color-rows .color-group').length);
        var $row = parseRow(html);
        $row.attr('data-uid', uid);
        $('#color-rows').append($row);
        rebuildGrid();
    });

    $(document).on('click', '.remove-color', function () {
        var $row = $(this).closest('.color-group');
        var id = $row.find('input[name="color_id[]"]').val();
        if (id) {
            var deleted = $('#deleted-colors').val();
            $('#deleted-colors').val(deleted ? deleted + ',' + id : id);
        }
        $row.remove();
        rebuildGrid();
    });

    $(document).on('change input', '#size-rows select, #size-rows input, #color-rows input[name="color_name[]"], #color-rows input[name="color_code[]"]', rebuildGrid);

    // Number colour image fields by row order, and refresh grid names, just before submit
    $('form').has('#variant-grid').on('submit', function () {
        $('#color-rows .color-group').each(function (i) {
            $(this).find('input[type="file"]').attr('name', 'color_images[' + i + '][]');
        });
        rebuildGrid();
    });

    // Start with one size row on the create page
    if (!$('#size-rows .size-row').length) {
        $('#size-rows').append(parseRow(document.getElementById('size-row-template').innerHTML));
    }

    rebuildGrid();
});
</script>
@endpush