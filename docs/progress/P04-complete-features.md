# P04: Photos, Payments, Reports, and Indonesian Interface

| | |
|---|---|
| **Status** | ✅ Done |
| **Branch** | `feature/p04-complete-features` |
| **Builds on** | P03 (`feature/p03-ui-and-crud`) |

## Goal
Finish every feature that was listed as "not done yet" and make the app feel like a real cafe system: menu photos, simulated payments, order type and table number, stock, item preferences, receipts, sales reports, password reset, email verification, live updates, an Indonesian interface, and a large set of demo data.

## Jobs

| Code | Job | Status |
|---|---|---|
| J1 | Database: photo and stock on menus, checkout and payment fields on orders, item note | ✅ Done |
| J2 | Menu photos (upload, placeholder, shipped photos, `menu:link-photos`) and stock | ✅ Done |
| J3 | Checkout: order type, table number, item preferences, payment method | ✅ Done |
| J4 | Simulated payment (QRIS, virtual account, e-wallet, cash with change) and receipt | ✅ Done |
| J5 | Sales reports with CSV export, server-side menu and order search | ✅ Done |
| J6 | Password reset and email verification | ✅ Done |
| J7 | Live order updates, Indonesian and English interface, responsive polish | ✅ Done |
| J8 | Demo data: 72 menus, 3 cashiers, 40 customers, 1000 orders with payments | ✅ Done |
| J9 | Automated tests and documentation | ✅ Done |

## Notes per job

- **J1** Migrations `2026_11_06_100001` to `100003`. `menus.stock` is nullable (`null` means unlimited).
- **J2** A photo can be uploaded in **Manage menu**, or shipped as `public/images/menu/<menu-slug>.jpg` (see `docs/menu-photos.md` for the filename and a search keyword for each of the 72 menus). Menus without a photo show a colored placeholder with the category icon. A menu with zero stock is shown as sold out, a low stock (5 or less) shows a warning, and cancelling an order gives the stock back.
- **J3** The cart now has order type (dine in or takeaway), table number, payment method, and a preference per item (sugar, ice, or spice level).
- **J4** `PaymentService` only changes the order record; no gateway is called. QRIS shows a generated QR-looking picture, bank transfer shows a virtual account number with a copy button, e-wallet shows a payment code, and cash is confirmed by the cashier who enters the money received and sees the change. Cancelling a paid order marks the payment as refunded. `/orders/{id}/receipt` is a printable receipt.
- **J5** `/admin/reports` shows revenue, average order, items sold, revenue per day, per payment method, per order type, per category, and the top 10 menus for any date range, and exports the orders of the range as CSV. The admin menu list and the order queue filter on the server with pagination.
- **J6** Laravel's password broker and `MustVerifyEmail`. Ordering requires a verified email. Mail uses the `log` driver, so the links are in `storage/logs/laravel.log`; in the `local` environment a "Verify now (demo)" button is shown. Users created by an admin are already verified.
- **J7** The order page refreshes itself when the cashier changes the status or payment, and the cashier sees a banner when new orders arrive. The language switch (ID/EN) uses `lang/id.json` and `lang/en.json`.
- **J8** `php artisan migrate:fresh --seed` creates 72 menus in 6 categories, 6 tags, 1 admin, 3 cashiers, 40 customers with Indonesian names and addresses, and 1000 orders over the last 60 days with realistic hours, statuses, payment methods, and notes. Set `config('seeding.orders')` and `config('seeding.customers')` to change the amounts.
- **J9** New tests: `CheckoutAndPaymentTest`, `StockAndPhotoTest`, `AuthExtrasTest`, `ReportTest`, and an updated seeder test.
