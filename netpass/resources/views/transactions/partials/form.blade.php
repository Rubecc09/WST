<form class="form-card" action="{{ $type === \App\Models\MembershipTransaction::APPLICATION ? route('transactions.applications.store') : route('transactions.renewals.store') }}" method="POST">
    @csrf
    <div class="form-card-header"><span class="form-section-icon"><x-icon name="id-card" /></span><div><h2>{{ $type }} request</h2><p>A Pending request does not activate or extend a membership.</p></div></div>
    @if($errors->any())<div class="validation-summary" role="alert">{{ $errors->first() }}</div>@endif
    @if($type === \App\Models\MembershipTransaction::RENEWAL)<div class="configuration-notice">Renewals require a prior Approved transaction. Existing member status alone does not establish eligibility. Payment and approval are required before validity is extended.</div>@endif
    @if($members->isEmpty())<div class="configuration-notice">No eligible members. Unresolved requests must be completed or cancelled; renewals require previously approved membership. <a href="{{ route('members.create') }}">Add a member</a></div>@endif
    <div class="form-section form-section-last">
        <div class="row g-4">
            <div class="col-12">
                <x-form.field name="member_id" label="Member" :required="true">
                    <select class="form-control @error('member_id') is-invalid @enderror" name="member_id" id="member_id" required data-transaction-member @error('member_id') aria-invalid="true" aria-describedby="member_id-error" @enderror>
                        <option value="">Choose a member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" @selected((string) old('member_id') === (string) $member->id) data-level="{{ $member->approvedMembership?->membership_level_id ?? $member->membership_level_id }}" data-info="Account status: {{ $member->status }} | Paid membership: {{ $member->paidMembershipStatus() }} | Assigned/requested level: {{ $member->membershipLevel->name }} | Approved level: {{ $member->approvedMembership?->level_name ?? 'None' }} | Membership ends: {{ $member->approvedMembership?->membership_end_date?->format('M d, Y') ?? 'Not assigned' }}">{{ $member->full_name }} | {{ $member->membership_number }}</option>
                        @endforeach
                    </select>
                </x-form.field>
                <p class="field-hint" data-member-information aria-live="polite">Select a member to view current membership information.</p>
            </div>
            <div class="col-12 col-md-6">
                <x-form.field name="membership_level_id" label="Requested level" :required="true">
                    <select class="form-control @error('membership_level_id') is-invalid @enderror" id="membership_level_id" name="membership_level_id" required data-transaction-level @error('membership_level_id') aria-invalid="true" aria-describedby="membership_level_id-error" @enderror>
                        <option value="">Choose a configured level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" data-fee="{{ $level->isConfigured() ? $level->fee : '' }}" data-days="{{ $level->validity_days }}" @disabled(! $level->isConfigured()) @selected((string) old('membership_level_id') === (string) $level->id)>{{ $level->name }} — {{ $level->isConfigured() ? '₱'.number_format((float) $level->fee, 2).' / '.$level->validity_days.' days' : 'Needs configuration' }}</option>
                        @endforeach
                    </select>
                </x-form.field>
            </div>
            <div class="col-12 col-md-6"><div class="registration-note"><span><small>Membership fee</small><strong data-transaction-fee aria-live="polite">Select a level</strong></span></div><p class="field-hint">The current database fee is saved when you submit.</p></div>
            <div class="col-12"><x-form.field name="remarks" label="Remarks"><textarea class="form-control @error('remarks') is-invalid @enderror" name="remarks" id="remarks" rows="3" maxlength="5000">{{ old('remarks') }}</textarea></x-form.field></div>
        </div>
    </div>
    <div class="form-footer"><a class="button button-secondary" href="{{ route('transactions.index') }}">Cancel</a><button class="button button-primary" type="submit">Create {{ strtolower($type) }}</button></div>
</form>
