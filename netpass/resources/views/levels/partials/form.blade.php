<form class="form-card catalog-form" action="{{ $level ? route('levels.update', $level) : route('levels.store') }}" method="POST">
    @csrf
    @if ($level)
        @method('PUT')
    @endif
    <div class="form-card-header">
        <span class="form-section-icon"><x-icon name="sparkles" /></span>
        <div><h2>Level information</h2><p>Enter your café’s actual membership terms.</p></div>
        <span class="required-note"><span class="required-mark">*</span> Required fields</span>
    </div>
    @if ($errors->any())
        <div class="validation-summary" role="alert">Please review the highlighted fields.</div>
    @endif
    @if ($level && ! $level->isConfigured())
        <div class="configuration-notice">Needs configuration: enter a fee, validity period, and benefits to complete this level.</div>
    @endif
    <div class="form-section form-section-last">
        <div class="row g-4">
            <div class="col-12">
                <x-form.field name="name" label="Level name" :required="true">
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $level?->name) }}" maxlength="255" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                </x-form.field>
            </div>
            <div class="col-12">
                <x-form.field name="description" label="Description">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="5000" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $level?->description) }}</textarea>
                </x-form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-form.field name="fee" label="Membership fee (₱)" :required="true">
                    <input class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" type="number" min="0" max="99999999.99" step="0.01" value="{{ old('fee', $level?->fee) }}" required @error('fee') aria-invalid="true" aria-describedby="fee-error" @enderror>
                </x-form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-form.field name="validity_days" label="Validity period (days)" :required="true">
                    <input class="form-control @error('validity_days') is-invalid @enderror" id="validity_days" name="validity_days" type="number" min="1" max="4294967295" step="1" value="{{ old('validity_days', $level?->validity_days) }}" required @error('validity_days') aria-invalid="true" aria-describedby="validity_days-error" @enderror>
                </x-form.field>
            </div>
            <div class="col-12">
                <x-form.field name="benefits" label="Membership benefits" :required="true" hint="Describe the privileges included in this level.">
                    <textarea class="form-control @error('benefits') is-invalid @enderror" id="benefits" name="benefits" rows="4" maxlength="10000" required @error('benefits') aria-invalid="true" aria-describedby="benefits-error" @enderror>{{ old('benefits', $level?->benefits) }}</textarea>
                </x-form.field>
            </div>
        </div>
    </div>
    <div class="form-footer">
        <a class="button button-secondary" href="{{ route('levels.index') }}">Cancel</a>
        <button class="button button-primary" type="submit"><x-icon name="check" /> Save level</button>
    </div>
</form>
