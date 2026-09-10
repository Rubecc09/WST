<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero">
    <p class="eyebrow">Simple. Organized. Ready.</p>
    <h1>Run your records with confidence.</h1>
    <p class="lead">Welcome to KONE POS, a clean and dependable workspace for managing customer information and your store team in one place.</p>
    <div class="actions">
        <a class="button" href="<?= site_url('customers') ?>">Explore customers <span aria-hidden="true">&rarr;</span></a>
        <a class="button secondary" href="<?= site_url('users') ?>">Meet the team</a>
    </div>
</section>

<section aria-labelledby="overview-title">
    <div class="section-heading">
        <p class="eyebrow">At a glance</p>
        <h2 id="overview-title">POS workspace</h2>
        <p>A quick overview of the information available in this first release.</p>
    </div>
    <div class="stats">
        <article class="stat"><strong>5</strong><span>Customer accounts</span></article>
        <article class="stat"><strong>5</strong><span>Staff accounts</span></article>
        <article class="stat"><strong>4</strong><span>Connected pages</span></article>
    </div>
</section>

<section aria-labelledby="features-title">
    <div class="section-heading">
        <p class="eyebrow">Core features</p>
        <h2 id="features-title">Everything you need to get started</h2>
        <p>Designed for clarity, speed, and easy navigation on desktop or mobile.</p>
    </div>
    <div class="feature-grid">
        <article class="feature">
            <span class="feature-icon" aria-hidden="true">C</span>
            <h3>Customer directory</h3>
            <p>Find names, email addresses, and phone numbers in a clear account table.</p>
        </article>
        <article class="feature">
            <span class="feature-icon" aria-hidden="true">U</span>
            <h3>User management</h3>
            <p>Review usernames, staff details, contact information, and assigned roles.</p>
        </article>
        <article class="feature">
            <span class="feature-icon" aria-hidden="true">N</span>
            <h3>Easy navigation</h3>
            <p>Move confidently between pages with a responsive, accessible active-page indicator.</p>
        </article>
    </div>
</section>
<?= $this->endSection() ?>