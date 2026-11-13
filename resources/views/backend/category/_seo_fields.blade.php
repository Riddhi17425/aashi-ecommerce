{{-- SEO fields for category / sub-category: intro text and FAQ (used by create and edit forms) --}}
@php
    $savedFaqs = isset($category) ? (json_decode($category->faq, true) ?: []) : [];
    // Rows to show: what was submitted (after a validation error), otherwise what is saved, and at least one empty row
    $faqQuestions = old('faq_question', array_column($savedFaqs, 'question'));
    $faqAnswers = old('faq_answer', array_column($savedFaqs, 'answer'));
    if (count($faqQuestions) === 0) {
        $faqQuestions = [''];
        $faqAnswers = [''];
    }
@endphp
<div class="form-group">
  <label for="intro_content" class="col-form-label">Intro Content (shown above the products on the website)</label>
  <textarea class="form-control" id="intro_content" name="intro_content">{{ old('intro_content', $category->intro_content ?? '') }}</textarea>
</div>

<div class="form-group">
  <label class="col-form-label">FAQ (shown below the products, leave empty to hide)</label>
  <div id="faq-rows">
    @foreach ($faqQuestions as $index => $question)
      <div class="faq-row border rounded p-2 mb-2">
        <input type="text" class="form-control mb-2" name="faq_question[]" placeholder="Question" value="{{ $question }}">
        <textarea class="form-control mb-2" rows="2" name="faq_answer[]" placeholder="Answer">{{ $faqAnswers[$index] ?? '' }}</textarea>
        <button type="button" class="btn btn-sm btn-outline-danger faq-remove">Remove</button>
      </div>
    @endforeach
  </div>
  <button type="button" id="faq-add" class="btn btn-sm btn-outline-primary">+ Add more FAQ</button>

  {{-- Empty row used by the "Add more FAQ" button --}}
  <template id="faq-template">
    <div class="faq-row border rounded p-2 mb-2">
      <input type="text" class="form-control mb-2" name="faq_question[]" placeholder="Question">
      <textarea class="form-control mb-2" rows="2" name="faq_answer[]" placeholder="Answer"></textarea>
      <button type="button" class="btn btn-sm btn-outline-danger faq-remove">Remove</button>
    </div>
  </template>
</div>

<script>
  (function () {
    var rows = document.getElementById('faq-rows');

    // Add an empty FAQ row
    document.getElementById('faq-add').addEventListener('click', function () {
      var template = document.getElementById('faq-template');
      rows.appendChild(template.content.cloneNode(true));
    });

    // Remove a FAQ row (the last remaining row is only cleared, so there is always one to type in)
    rows.addEventListener('click', function (event) {
      if (!event.target.classList.contains('faq-remove')) {
        return;
      }
      var row = event.target.closest('.faq-row');
      if (rows.querySelectorAll('.faq-row').length > 1) {
        row.remove();
      } else {
        row.querySelector('input').value = '';
        row.querySelector('textarea').value = '';
      }
    });
  })();
</script>

@include('backend.partials._meta_fields', ['item' => $category ?? null])
