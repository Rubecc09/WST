<form class="form-card catalog-form" action="{{ $category ? route('categories.update', $category) : route('categories.store') }}" method="POST">
    @csrf
    @if ($category)
        @method('PUT')
    @endif
    <div class="form-card-header">
        <span class="form-section-icon"><x-icon name="users" /></span>
        <div><h2>Category information</h2><p>Names must be unique, regardless of letter case.</p></div>
        <span class="required-note"><span class="required-mark">*</span> Required fields</span>
    </div>
    @if ($errors->any())
        <div class="validation-summary" role="alert">Please review the highlighted fields.</div>
    @endif
    <div class="form-section form-section-last">
        <div class="row g-4">
            <div class="col-12">
                <x-form.field name="name" label="Category name" :required="true">
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category?->name) }}" maxlength="255" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                </x-form.field>
            </div>
            <div class="col-12">
                <x-form.field name="description" label="Description">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="5000" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $category?->description) }}</textarea>
                </x-form.field>
            </div>
        </div>
    </div>
    <div class="form-footer">
        <a class="button button-secondary" href="{{ route('categories.index') }}">Cancel</a>
        <button class="button button-primary" type="submit"><x-icon name="check" /> Save category</button>
    </div>
</form>
