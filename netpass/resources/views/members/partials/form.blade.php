<form class="form-card" action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-card-header">
        <span class="form-section-icon"><x-icon name="id-card" /></span>
        <div>
            <h2>{{ $formTitle }}</h2>
            <p>{{ $formDescription }}</p>
        </div>
        <span class="required-note"><span class="required-mark">*</span> Required fields</span>
    </div>

    @if ($errors->any())
        <div class="validation-summary" role="alert">
            <strong>Please review the highlighted fields.</strong>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="form-section">
        <h3>Personal details</h3>
        <div class="form-grid">
            <x-form.field name="full_name" label="Full name" :required="true">
                <input class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" type="text" value="{{ old('full_name', $member?->full_name) }}" placeholder="e.g. Alex Morgan" autocomplete="name" required @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
            </x-form.field>
            <x-form.field name="email" label="Email address" :required="true">
                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $member?->email) }}" placeholder="alex@example.com" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            </x-form.field>
            <x-form.field name="contact_number" label="Phone number" :required="true">
                <input class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" type="tel" value="{{ old('contact_number', $member?->contact_number) }}" placeholder="+63 912 345 6789" autocomplete="tel" required @error('contact_number') aria-invalid="true" aria-describedby="contact_number-error" @enderror>
            </x-form.field>
            <x-form.field name="address" label="Address" :required="true" hint="Used for the member’s profile record.">
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Street, city, and province" autocomplete="street-address" required @error('address') aria-invalid="true" aria-describedby="address-error" @enderror>{{ old('address', $member?->address) }}</textarea>
            </x-form.field>
        </div>
    </div>

    <div class="form-section form-section-last">
        <h3>Membership details</h3>
        <div class="form-grid">
            <x-form.field name="membership_category_id" label="Membership category" :required="true">
                <select class="form-control @error('membership_category_id') is-invalid @enderror" id="membership_category_id" name="membership_category_id" required @error('membership_category_id') aria-invalid="true" aria-describedby="membership_category_id-error" @enderror>
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('membership_category_id', $member?->membership_category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </x-form.field>
            <x-form.field name="membership_level_id" label="Membership level" :required="true">
                <select class="form-control @error('membership_level_id') is-invalid @enderror" id="membership_level_id" name="membership_level_id" required @error('membership_level_id') aria-invalid="true" aria-describedby="membership_level_id-error" @enderror>
                    <option value="">Choose a level</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected((string) old('membership_level_id', $member?->membership_level_id) === (string) $level->id)>{{ $level->name }}</option>
                    @endforeach
                </select>
            </x-form.field>
            <x-form.field name="status" label="Member status" :required="true">
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
                    <option value="Active" @selected(old('status', $member?->status ?? 'Active') === 'Active')>Active</option>
                    <option value="Inactive" @selected(old('status', $member?->status ?? 'Active') === 'Inactive')>Inactive</option>
                </select>
            </x-form.field>
            @if ($member)
                <div class="registration-note">
                    <span class="registration-note-icon"><x-icon name="calendar" /></span>
                    <span><small>Registration date</small><strong>{{ $member->registration_date->format('F d, Y') }}</strong></span>
                </div>
            @else
                <div class="registration-note">
                    <span class="registration-note-icon"><x-icon name="calendar" /></span>
                    <span><small>Registration date</small><strong>Today, {{ now()->format('F d, Y') }}</strong></span>
                </div>
            @endif
        </div>
    </div>

    <div class="form-footer">
        <a class="button button-secondary" href="{{ $member ? route('members.show', $member) : route('members.index') }}">Cancel</a>
        <button class="button button-primary" type="submit"><x-icon name="check" /> {{ $submitLabel }}</button>
    </div>
</form>
