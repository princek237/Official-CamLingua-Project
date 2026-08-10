<?php
$pageTitle = 'CamLingua – Support / Help Center';
$extraCss = array (
  0 => 'support.css',
);
$activePage = 'support';
include 'includes/header.php';
?>
<main class="page-bg">
    <div class="container">

      <!-- ------------------------------------------------
           HERO SECTION
      ------------------------------------------------ -->
      <section class="hero" aria-labelledby="hero-heading">
        <!-- Left: text + search -->
        <div class="hero__text">
          <h1 id="hero-heading" class="hero__heading">How can we help you?</h1>
          <p class="hero__subtext">Find answers to common questions or get help using CamLingua.</p>

          <!-- Search bar -->
          <div class="search-bar" role="search">
            <!-- Magnifying glass (decorative, inside input) -->
            <span class="search-bar__icon" aria-hidden="true">
              <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="9" cy="9" r="5.5" stroke="#9CA3AF" stroke-width="1.6"/>
                <path d="M13.5 13.5L17 17" stroke="#9CA3AF" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
            </span>
            <input
              type="search"
              class="search-bar__input"
              placeholder="Search for help topics..."
              aria-label="Search for help topics"
            />
            <!-- Green submit button -->
            <button class="search-bar__btn" aria-label="Submit search">
              <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="9" cy="9" r="5.5" stroke="white" stroke-width="1.8"/>
                <path d="M13.5 13.5L17 17" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Right: support illustration -->
        <div class="hero__illustration" aria-hidden="true">
          <!-- Blob background -->
          <div class="blob"></div>
          <!-- Dark green circle with headset icon -->
          <div class="hero__icon-circle">
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="hero__headset-svg">
              <!-- Headset arc -->
              <path d="M14 32C14 21.5 22.5 13 33 13C43.5 13 52 21.5 52 32" stroke="white" stroke-width="3.5" stroke-linecap="round"/>
              <!-- Left ear cup -->
              <rect x="10" y="30" width="8" height="12" rx="4" fill="white"/>
              <!-- Right ear cup -->
              <rect x="46" y="30" width="8" height="12" rx="4" fill="white"/>
              <!-- Mic boom -->
              <path d="M54 38 Q58 50 48 54" stroke="white" stroke-width="3" stroke-linecap="round" fill="none"/>
              <!-- Mic tip -->
              <circle cx="46" cy="55" r="2.5" fill="white"/>
            </svg>
          </div>
          <!-- Question mark bubble -->
          <div class="hero__question-bubble">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="40" height="40" rx="12" fill="white"/>
              <text x="50%" y="57%" dominant-baseline="middle" text-anchor="middle" font-size="22" font-weight="800" fill="#15803d">?</text>
              <!-- Bubble tail -->
              <polygon points="6,36 2,46 16,38" fill="white"/>
            </svg>
          </div>
        </div>
      </section>

      <!-- ------------------------------------------------
           POPULAR TOPICS GRID
      ------------------------------------------------ -->
      <section class="topics" aria-labelledby="topics-heading">
        <h2 id="topics-heading" class="topics__heading">Popular Topics</h2>

        <div class="topics__grid" role="list">

          <!-- Card 1: Getting Started -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--blue" aria-hidden="true">
              <!-- Rocket -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C12 2 17 5 17 12L15.5 13.5C14.5 11 13 9 12 8C11 9 9.5 11 8.5 13.5L7 12C7 5 12 2 12 2Z" fill="#3B82F6"/>
                <path d="M8.5 13.5C8.5 13.5 7 15 7 18H17C17 15 15.5 13.5 15.5 13.5H8.5Z" fill="#3B82F6"/>
                <path d="M10 18L9 22H15L14 18" stroke="#3B82F6" stroke-width="1.2" fill="none"/>
                <circle cx="12" cy="12" r="1.5" fill="white"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Getting Started</h3>
              <p class="topic-card__text">Learn how to use CamLingua and translate seamlessly.</p>
            </div>
          </article>

          <!-- Card 2: Translation Issues -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--orange" aria-hidden="true">
              <!-- Warning triangle -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L22 21H2L12 3Z" fill="#F97316"/>
                <path d="M12 10V14" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <circle cx="12" cy="17" r="1" fill="white"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Translation Issues</h3>
              <p class="topic-card__text">Fix problems with translations or language errors.</p>
            </div>
          </article>

          <!-- Card 3: Account & Billing -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--blue" aria-hidden="true">
              <!-- Credit card -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="6" width="20" height="14" rx="3" stroke="#3B82F6" stroke-width="1.8"/>
                <rect x="2" y="10" width="20" height="3" fill="#3B82F6"/>
                <rect x="5" y="15" width="4" height="2" rx="1" fill="#3B82F6"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Account &amp; Billing</h3>
              <p class="topic-card__text">Manage your account, subscription and payments.</p>
            </div>
          </article>

          <!-- Card 4: Languages -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--blue" aria-hidden="true">
              <!-- Globe -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="#3B82F6" stroke-width="1.8"/>
                <ellipse cx="12" cy="12" rx="4" ry="9" stroke="#3B82F6" stroke-width="1.5"/>
                <path d="M3 12H21" stroke="#3B82F6" stroke-width="1.5"/>
                <path d="M5 8H19M5 16H19" stroke="#3B82F6" stroke-width="1.2"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Languages</h3>
              <p class="topic-card__text">Find supported languages and dialect information.</p>
            </div>
          </article>

          <!-- Card 5: Privacy & Security -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--green" aria-hidden="true">
              <!-- Shield -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L4 7V12C4 16.4 7.4 20.5 12 21C16.6 20.5 20 16.4 20 12V7L12 3Z" fill="#15803d"/>
                <path d="M9 12L11 14L15 10" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Privacy &amp; Security</h3>
              <p class="topic-card__text">Learn how we protect your data and privacy.</p>
            </div>
          </article>

          <!-- Card 6: Technical Support -->
          <article class="topic-card" role="listitem">
            <div class="topic-card__icon topic-card__icon--green" aria-hidden="true">
              <!-- Headset -->
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12C5 8.13 8.13 5 12 5C15.87 5 19 8.13 19 12" stroke="#15803d" stroke-width="1.8" stroke-linecap="round"/>
                <rect x="3" y="11" width="4" height="6" rx="2" fill="#15803d"/>
                <rect x="17" y="11" width="4" height="6" rx="2" fill="#15803d"/>
                <path d="M21 15 Q22 20 17 21" stroke="#15803d" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <circle cx="16" cy="21.5" r="1.2" fill="#15803d"/>
              </svg>
            </div>
            <div class="topic-card__body">
              <h3 class="topic-card__title">Technical Support</h3>
              <p class="topic-card__text">Troubleshoot technical issues and bugs.</p>
            </div>
          </article>

        </div>
      </section>

      <!-- ------------------------------------------------
           BOTTOM BANNER
      ------------------------------------------------ -->
      <aside class="banner" aria-label="Contact support banner">
        <div class="banner__text">
          <p class="banner__title">Can't find what you're looking for?</p>
          <p class="banner__sub">Contact our support team and we'll get back to you as soon as possible.</p>
        </div>
        <a href="contact.php" class="btn btn--green">Contact Support</a>
      </aside>

    </div><!-- /container -->
  </main>

  <!-- FOOTER -->
  
<?php
include 'includes/footer.php';
?>
