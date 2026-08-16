<?php
require_once 'includes/cms_helper.php';
$pageTitle  = cms('site_name') . ' – Contact Us / Feedback';
$extraCss   = ['contact.css'];
$extraJs    = ['contact.js'];
$activePage = 'contact';
include 'includes/header.php';
?>
<main class="page-bg">
    <div class="container">

      <!-- 2-column layout: info left, form right -->
      <div class="contact-layout">

        <!-- ================================================
             LEFT COLUMN – Contact Information
        ================================================ -->
        <section class="contact-info" aria-labelledby="contact-info-heading">
          <h1 id="contact-info-heading" class="contact-info__heading"><?= cms('contact_hero_title') ?></h1>
          <p class="contact-info__sub"><?= cms('contact_hero_desc') ?></p>

          <!-- Contact list -->
          <ul class="contact-list" role="list">

            <!-- Email -->
            <li class="contact-item">
              <div class="contact-item__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="6" width="18" height="13" rx="2" stroke="#15803d" stroke-width="1.8"/>
                  <path d="M3 8L12 14L21 8" stroke="#15803d" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="contact-item__body">
                <span class="contact-item__title">Email Us</span>
                <span class="contact-item__value"><?= cms('contact_email') ?></span>
              </div>
            </li>

            <!-- Phone -->
            <li class="contact-item">
              <div class="contact-item__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5 4H9L11 9L8.5 10.5C9.57 12.73 11.27 14.43 13.5 15.5L15 13L20 15V19C20 19.55 19.55 20 19 20C10.16 20 3 12.84 3 4C3 3.45 3.45 3 4 3H8L5 4Z" fill="#15803d"/>
                </svg>
              </div>
              <div class="contact-item__body">
                <span class="contact-item__title">Call Us</span>
                <span class="contact-item__value"><?= cms('contact_phone') ?></span>
              </div>
            </li>

            <!-- Location -->
            <li class="contact-item">
              <div class="contact-item__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2C8.69 2 6 4.69 6 8C6 12.5 12 21 12 21C12 21 18 12.5 18 8C18 4.69 15.31 2 12 2Z" fill="#15803d"/>
                  <circle cx="12" cy="8" r="2.5" fill="white"/>
                </svg>
              </div>
              <div class="contact-item__body">
                <span class="contact-item__title">Location</span>
                <span class="contact-item__value"><?= cms('contact_location') ?></span>
              </div>
            </li>

            <!-- Response time -->
            <li class="contact-item">
              <div class="contact-item__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12" cy="12" r="9" stroke="#15803d" stroke-width="1.8"/>
                  <path d="M12 7V12L15 14" stroke="#15803d" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="contact-item__body">
                <span class="contact-item__title">Response Time</span>
                <span class="contact-item__value"><?= cms('contact_response_time') ?></span>
              </div>
            </li>

          </ul><!-- /contact-list -->

          <!-- Rating box -->
          <div class="rating-box" aria-label="Rate CamLingua">
            <!-- Shield / lock icon -->
            <div class="rating-box__icon" aria-hidden="true">
              <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 3L5 8V15C5 21 9.8 26.5 16 28C22.2 26.5 27 21 27 15V8L16 3Z" fill="#15803d"/>
                <!-- Lock body -->
                <rect x="11" y="15" width="10" height="8" rx="2" fill="white"/>
                <!-- Lock shackle -->
                <path d="M13 15V13C13 11.34 14.34 10 16 10C17.66 10 19 11.34 19 13V15" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <!-- Keyhole -->
                <circle cx="16" cy="18.5" r="1.5" fill="#15803d"/>
                <rect x="15.2" y="18.5" width="1.6" height="2.5" fill="#15803d"/>
              </svg>
            </div>
            <div class="rating-box__text">
              <p class="rating-box__title">Love CamLingua?</p>
              <p class="rating-box__sub">Rate us and let others know about your experience</p>
            </div>
            <!-- 5 stars -->
            <div class="rating-box__stars" role="img" aria-label="5 star rating">
              <span class="star" aria-hidden="true">★</span>
              <span class="star" aria-hidden="true">★</span>
              <span class="star" aria-hidden="true">★</span>
              <span class="star" aria-hidden="true">★</span>
              <span class="star" aria-hidden="true">★</span>
            </div>
          </div><!-- /rating-box -->

        </section><!-- /contact-info -->

        <!-- RIGHT COLUMN – Contact Form -->
        <section class="contact-form-wrap" aria-labelledby="form-heading">
          <h2 id="form-heading" class="contact-form-wrap__heading">Send us a message</h2>

          <form class="contact-form" novalidate>

            <!-- Full Name -->
            <div class="form-group">
              <label class="form-label" for="full-name">Full Name</label>
              <input
                type="text"
                id="full-name"
                name="full_name"
                class="form-input"
                placeholder="Enter your name"
                autocomplete="name"
              />
            </div>

            <!-- Email Address -->
            <div class="form-group">
              <label class="form-label" for="email">Email Address</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="Enter your email"
                autocomplete="email"
              />
            </div>

            <!-- Subject dropdown -->
            <div class="form-group">
              <label class="form-label" for="subject">Subject</label>
              <div class="select-wrapper">
                <select id="subject" name="subject" class="form-select">
                  <option value="" disabled selected>Select a subject</option>
                  <option value="getting-started">Getting Started</option>
                  <option value="translation">Translation Issues</option>
                  <option value="billing">Account &amp; Billing</option>
                  <option value="languages">Languages</option>
                  <option value="privacy">Privacy &amp; Security</option>
                  <option value="technical">Technical Support</option>
                  <option value="other">Other</option>
                </select>
                <!-- Custom chevron -->
                <span class="select-wrapper__chevron" aria-hidden="true">
                  <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6l4 4 4-4" stroke="#6B7280" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
              </div>
            </div>

            <!-- Message textarea -->
            <div class="form-group">
              <label class="form-label" for="message">Message</label>
              <div class="textarea-wrapper">
                <textarea
                  id="message"
                  name="message"
                  class="form-textarea"
                  placeholder="Type your message here..."
                  maxlength="1000"
                  rows="5"
                  aria-describedby="char-count"
                ></textarea>
                <span class="char-count" id="char-count" aria-live="polite">0/1000</span>
              </div>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn--green btn--full">
              Send Message
              <!-- Paper airplane icon -->
              <svg class="btn__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M22 2L11 13" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>

          </form>
        </section><!-- /contact-form-wrap -->

      </div><!-- /contact-layout -->

    </div><!-- /container -->
  </main>

  <script src="assets/js/contact.js"></script>

<?php include 'includes/footer.php'; ?>
