{{-- SEO: editable meta title and description. Leave them empty to use the automatic text. --}}
<div class="form-group">
  <label for="meta_title" class="col-form-label">Meta Title (SEO)</label>
  <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="60"
    value="{{ old('meta_title', $item->meta_title ?? '') }}" placeholder="Leave empty to use the automatic title">
  <small class="text-muted">Up to 60 characters. Example: Product Name | Category | Aashi Retail</small>
</div>

<div class="form-group">
  <label for="meta_description" class="col-form-label">Meta Description (SEO)</label>
  <textarea class="form-control" rows="3" id="meta_description" name="meta_description" maxlength="160"
    placeholder="Leave empty to use the automatic description">{{ old('meta_description', $item->meta_description ?? '') }}</textarea>
  <small class="text-muted">150 to 155 characters is best.</small>
</div>
