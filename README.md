เพื่อให้ **`README.md`** ดูสวยงามและมีข้อมูลที่เข้าใจง่าย พร้อมคำอธิบายที่ชัดเจนเกี่ยวกับการอัปเดตล่าสุด คุณสามารถใช้โครงสร้างดังนี้:

---

# Project Name: AltumCode - Update 65.0.0 - 25 February, 2026

## 🆕 New Features & Enhancements

### 🌟 **Implemented New Features:**

1. **Bandcamp Track / Album Embed Block**

   * A new block has been added for embedding Bandcamp tracks and albums with customization features.
     *(Pro Blocks plugin)*

2. **iOS Widget via Scriptable App**

   * iOS widget functionality is now available through the Scriptable app.
     *(Exclusive for AltumCode Club subscribers)*

3. **Search & Filters Support for API Endpoints**

   * Added search and filter capabilities to all data retrieval endpoints for enhanced usability.

4. **Unsubscribe Feature for Newsletters**

   * Implemented a one-click unsubscribe option for users, allowing them to easily opt-out of newsletters.

5. **Broadcast System Management**

   * You can now enable/disable the Broadcasts system entirely, allowing for more control.

6. **Payment Proof Secure Viewing**

   * A secure viewing page has been added for offline payment gateways. Direct links to resources for viewing payment proofs will no longer be used.

7. **Favicon Getter Reverse Proxy**

   * Favicon links are now automatically retrieved from DuckDuckGo, stored, cached, and served in a more performant and private way.

8. **Email Handlers - Confirmation Required**

   * Email handlers must confirm their email addresses when added to prevent abuse.

9. **API Links Statistics Endpoint**

   * New API endpoint added for retrieving statistics per account or per project.

10. **Improved Service Block**

* The Service Block now allows for setting a minimum price and custom prices by users.

### 🚀 **Performance & Security Improvements:**

1. **Calendar Block Performance**

   * Improved generation performance for the Calendar Block.

2. **Biolink Page Block Re-ordering**

   * Dramatically improved the re-ordering performance of Biolink page blocks (85-95% faster).
   * Instant preview is now shown when moving blocks, without waiting for backend updates.

3. **Biolink Themes System**

   * Enhanced the Biolink Themes system to support the background attachment parameter.

4. **Security Enhancements:**

   * Improved security regarding passwords to prevent long password attacks.
   * Improved security for SVG image uploads.
   * Enhanced protection against Session Fixation attacks.

5. **Improved Consistency**

   * All AltumCode products now use the same style for menus, footers, and border roundness.

6. **Memory Optimization**

   * Better memory usage by improving certain functions across the product.

7. **Cookie Consent**

   * The cookie consent dependency has been upgraded to version 3.1.

8. **PHP Dependencies**

   * All PHP dependencies have been upgraded to their latest versions.

9. **Client-Side Performance**

   * Performance improvements on client-side JavaScript by reducing unnecessary queries and event handlers.

10. **Language Cache Optimization**

    * Enhanced language cache generation based on available/enabled features. Non-used strings are now removed during caching.

11. **Statistics Cleanup Cron Jobs**

    * Highly improved the performance of statistics cleanup cron jobs.

12. **Stripe Payment Gateway**

    * Added support for automatic tax handling through Stripe.

### 🐛 **Bug Fixes:**

1. **Image Slider Block Bug**

   * Fixed a bug that caused the image slider block to malfunction on certain devices.

2. **Modal Text Image**

   * Fixed an issue where modal text images would disappear after updating the block.

3. **Payment Blocks Modals**

   * Fixed issues with payment blocks modals during payment generation.

4. **Cookie Consent Popup in Biolink Template Preview**

   * Fixed an issue where the cookie consent popup appeared in the Biolink template preview.

5. **Weather Block Caching Issue**

   * Fixed an issue where caching was not resetting properly in the Weather Block.

6. **QR Code Generation**

   * Fixed the issue where certain QR codes were not generating.

7. **Link Attachment to Custom Domain**

   * Fixed an issue where links were not properly attaching themselves to the selected custom domain.

8. **Country Map Display Issue**

   * Fixed a display issue with the Country Map usage across the product where statistics are shown.

9. **Razorpay Decimal Issue**

   * Fixed a rare issue with Razorpay due to the usage of decimals.

10. **Duplicated Modals**

    * Fixed an issue where duplicated modals were being inserted in certain pages, causing potential slowdowns.

11. **Missing Email Verification Page**

    * Fixed an issue with missing email verification page.

12. **Social Icons Display**

    * Fixed an issue with social icons not displaying properly in certain browsers.

---

### 🔧 **Technical Changes**

* **File Ignored for Git**:

  ```bash
  install/
  vendor/
  uploads/
  app/includes/GeoLite2-City.mmdb
  app/includes/GeoLite2-Country.mmdb
  *.log
  *.tmp
  *.bak
  *.mmdb
  ```

---

### 📦 **Installation / Upgrade Instructions**

To upgrade to the latest version of AltumCode, follow these instructions:

1. **Backup Your Data**
   Before upgrading, ensure that you have a full backup of your database and files.

2. **Upgrade Steps**

   * Update your repository with the latest changes:

     ```bash
     git pull origin main
     ```

   * Run any necessary migrations or setup steps as instructed in the documentation.

3. **Clear Cache**
   After upgrading, clear the cache to ensure the latest changes take effect:

   ```bash
   php artisan cache:clear
   ```

---

### 🛠️ **Contact & Support**

If you encounter any issues, feel free to reach out to us:

* Email: [support@altumcode.com](mailto:support@altumcode.com)
* Support Forum: [https://altumcode.com/forum](https://altumcode.com/forum)

---

### **Thank you for using AltumCode!**

---

### ข้อแนะนำในการปรับแต่ง:

* **หัวข้อหลัก:** ใช้หัวข้อที่ชัดเจน เช่น **New Features**, **Performance Improvements**, **Bug Fixes**, และ **Installation Instructions** เพื่อให้ผู้ใช้สามารถเข้าใจและค้นหาข้อมูลได้ง่ายขึ้น
* **รายการ Bullet Points:** ใช้ bullet points เพื่อให้ข้อมูลดูสบายตาและอ่านง่าย
* **ขั้นตอนการอัพเกรด:** ให้คำแนะนำการอัพเกรดในส่วนของ **Installation / Upgrade Instructions** เพื่อให้ผู้ใช้สามารถอัปเดตโปรเจ็กต์ได้สะดวก

ด้วยรูปแบบนี้ **README.md** จะดูสวยงามและช่วยให้ผู้ใช้งานสามารถเข้าใจการอัปเดตได้ง่ายครับ!
