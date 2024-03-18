<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>
</div><!-- .col-full -->
</div><!-- #content -->
<?php do_action( 'storefront_before_footer' ); ?>
<footer id="colophon" class="custom-site-footer">
    <div class="footer-icons-container">
        <div class="footer-icons-grid">
            <div class="footer-icon-box">
                <img class="footer-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/guarantee.svg" alt="">
                <p class="footer-icon-text">Secure Payment</p>
                <p class="footer-icon-subtext">Your security is our priority. All payments are 100% secure.</p>
            </div>
            <div class="footer-icon-box">
                <img class="footer-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/guarantee.svg" alt="">
                <p class="footer-icon-text">30 day return policy</p>
                <p class="footer-icon-subtext">With our 30 day return policy, you can get a refund</p>
            </div>
            <div class="footer-icon-box">
                <img class="footer-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/support.svg" alt="">
                <p class="footer-icon-text">Customer service</p>
                <p class="footer-icon-subtext">From Monday to Saturday from 8am to 5pm</p>
            </div>
        </div>
    </div>
    <div class="footer-stripe-box">
        <img class="footer-stripe" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/footer.svg" alt="">
    </div>
    <div class="footer-box">
        <div class="footer-wrapper">
            <div class="upper-footer">
                <div>
                    <h4 class="site-footer-h4">Follow us on</h4>
                </div>
                <div class="social-icons-gid">
                    <img class="social-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/facebook.svg" alt="">
                    <img class="social-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/instagram.svg" alt="">
                    <img class="social-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/youtube.svg" alt="">
                    <img class="social-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/tiktok.svg" alt="">
                </div>
            </div>
            <div class="main-footer">
                <!-- Blocks -->
                <div class="footer-text">
                    <div class="footer-logo">
						<?php storefront_site_title_or_logo(); ?>
                    </div>
                    <div class="site-footer-rte">
                        <p class="no-margin">At TheKiddoSpace, our mission is to
                            <strong>disrupt and innovate </strong>in the
                            <strong>kids niche </strong>by bringing unique and forward-thinking products that are both stimulating, instructive and useful for children and can
                            <strong>ease parenting life.</strong>We're committed to providing top-notch organizational products, educational toys and other innovative offerings that address the ever-changing needs of parents and their kids.
                        </p>
                    </div>
                    <ul class="no-bullets-footer">
                        <li>
                            <a href="mailto:info@hanselberry.com" class="footer-link">
                                <span class="icon-width" aria-hidden="true">E-mail: </span>
                                info@hanselberry.com
                            </a>
                        </li>
                        <li>
                            <a href="tel:7412600158" class="footer-link">
                                <span class="icon-width" aria-hidden="true">Phone: </span>
                                000123456789
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-link-list">
                    <h4 class="site-footer-h4">Shop</h4>
                    <ul class="no-bullets-footer">
                        <li>
                            <a href="../collections/stamps" class="footer-link" id="Stamps---Accessories">
                                Stamps &amp; Accessories
                            </a>
                        </li>
                        <li>
                            <a href="../collections/organizers" class="footer-link" id="Organizers">
                                Organizers
                            </a>
                        </li>
                        <li>
                            <a href="../collections/educational" class="footer-link" id="Toys---Educational">
                                Toys &amp; Educational
                            </a>
                        </li>
                        <li>
                            <a href="../collections/arts-crafts" class="footer-link" id="Arts---Crafts">
                                Arts &amp; Crafts
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-link-list">
                    <h4 class="site-footer-h4">Support</h4>
                    <ul class="no-bullets-footer">
                        <li>
                            <a href="../pages/affiliate-program" class="footer-link" id="Affiliate-Program">
                                Affiliate Program
                            </a>
                        </li>
                        <li>
                            <a href="/apps/parcelpanel" class="footer-link" id="Track-your-order">
                                Track your order
                            </a>
                        </li>
                        <li>
                            <a href="/pages/faqs-1" class="footer-link" id="FAQs">
                                FAQs
                            </a>
                        </li>
                        <li>
                            <a href="/pages/our-story" class="footer-link" id="About-us">
                                About us
                            </a>
                        </li>
                        <li>
                            <a href="/pages/contact-us" class="footer-link" id="Contact-us">
                                Contact us
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-link-list">
                    <h4 class="site-footer-h4">Legal Pages</h4>
                    <ul class="no-bullets-footer">
                        <li>
                            <a href="/policies/privacy-policy" class="footer-link" id="Privacy-Policy">
                                Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="/policies/refund-policy" class="footer-link" id="Return---Refund-Policy">
                                Return &amp; Refund Policy
                            </a>
                        </li>
                        <li>
                            <a href="/policies/shipping-policy" class="footer-link" id="Shipping-Policy">
                                Shipping Policy
                            </a>
                        </li>
                        <li>
                            <a href="/pages/cookies-policy" class="footer-link" id="Cookies-Policy">
                                Cookies Policy
                            </a>
                        </li>
                        <li>
                            <a href="/policies/terms-of-service" class="footer-link" id="Terms-of-Service">
                                Terms of Service
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="secondary-footer">
                <div class="footer-text small-gap">
                    <p class="credential">
                        Copyright © 2024 TheKiddoSpace UK | Name Stamps for Clothes, Organizers & Toys | TheKiddoSpace UK is a trade name of SN ECOMMERCE LLP | SN ECOMMERCE LLP - OC441736 - 128 City Road, London, UK EC1V 2NX
                    </p>
                </div>
                <div class="footer-text small-gap">
                    <p class="credential">Payment methods:</p>
                    <div class="payment-grid">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/cod-pay.svg" alt="">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/amex.svg?v=1" alt="">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/maestro.svg" alt="">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/mastercard.svg" alt="">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/paypal.svg" alt="">
                        <img class="payment-icon" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/icons/visa.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->
<?php do_action( 'storefront_after_footer' ); ?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
