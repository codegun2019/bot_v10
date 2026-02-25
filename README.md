Update 65.0.0 - 25 February, 2026

Implemented New Block: Bandcamp Track / Album Embed with customization features (Pro Blocks plugin).
Implemented IOS widget via Scriptable app (available for AltumCode Club subscribers).
Implemented search & filters support for all API endpoints data retrieval endpoints.
Implemented the new Unsubscribe function for Newsletter (broadcast) emails that offers a 1 click unsubscribe option for your users.
Implemented the ability to enable/disable the Broadcasts system completely.
Implemented the new payment proof secure viewing page on the offline payment gateway. No more direct links to the uploaded resources will be used for viewing payment proofs.
Implemented the new Favicon Getter reverse proxy. Favicon links will now be automatically retrieved from Duckduckgo, stored, cached and returned in a more performant and private way.
Notification Handlers - email handlers will now be required to confirm their emails when added to prevent abuse of adding unwanted emails.
Implemented the new API Links Statistics endpoint to get statistics per whole account or per project.
Improved Service Block: It now supports the ability to set a minimum price & allow custom prices to be set by the user.
Improved Calendar Block generation performance.
Improved Biolink page blocks re-ordering performance by dropping the time it takes by 85-95%. This is felt most on pages with many blocks.
Improved Biolink page blocks re-ordering shows instant preview (without waiting for the backend update) while you move the selected block.
Improved Biolink Themes system to support the background attachment parameter.
Added the Bandcamp social link for the Social Block on biolink pages.
Improved security in regard to passwords - to prevent long password attacks.
Improved security in regards to SVG images uploads.
Improved security in regards to Session Fixation potential attacks.
Consistency improvement: All altumcode products now use the same style of menu, footer & border roundness.
Better memory usage by improving certain used functions across the product.
Cookie consent dependency upgraded to the latest version 3.1.
All php dependencies available were upgraded to their latest releases.
Performance improvements on the client-side javascript (less unnecesssary queries and event handlers).
Highly improved language cache generation based on available / enabled features (many non-used strings will now be removed in caching if not used).
Highly improved performance on statistics cleanup cron jobs.
Improved Stripe payment gateway: You can now enable automatic tax handling via Stripe.
Small improvements over the look on the API documentation pages.
Fixed issue with the image slider block bugging out on certain devices.
Fixed issue with Modal Text image that would disappear after updating the block.
Fixed issues with payment blocks modals for payment generation.
Fixed cookie consent popup showing in biolink template preview.
Fixed issue with caching not resetting when needed in the Weather Block.
Fixed issue certain QR codes not generating.
Fixed issue with links not properly attaching themselves to the selected custom domain.
Fixed display issue with the Country map usage across the product where stats are displayed.
Fixed rare issue with Razorpay due to usage of decimals.
Fixed issue duplicated modals being inserted in certain pages causing a potential slow down.
Fixed issue with missing email verification page.
Fixed issue with social icons not displaying properly in certain browsers.
