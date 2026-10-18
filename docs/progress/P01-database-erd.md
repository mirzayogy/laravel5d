erDiagram
    %% ERD Website Penjualan Kopi (Mermaid)

    %% ===== USER & AKSES =====
    roles ||--o{ users : "memiliki"
    users ||--o{ addresses : "punya"

    %% ===== KATALOG =====
    categories ||--o{ categories : "sub-kategori"
    categories ||--o{ products : "mengelompokkan"
    brands ||--o{ products : "memproduksi"
    origins ||--o{ products : "asal"
    products ||--o{ product_variants : "memiliki"
    products ||--o{ product_images : "memiliki"
    products ||--o{ product_tags : "ditandai"
    tags ||--o{ product_tags : "dipakai"

    %% ===== STOK =====
    product_variants ||--|| inventories : "stok"
    product_variants ||--o{ stock_movements : "mutasi"
    suppliers ||--o{ stock_movements : "memasok"
    users ||--o{ stock_movements : "mencatat"

    %% ===== KERANJANG & WISHLIST =====
    users |o--o{ carts : "memiliki"
    carts ||--o{ cart_items : "berisi"
    product_variants ||--o{ cart_items : "dimasukkan"
    users ||--o{ wishlists : "menyimpan"
    products ||--o{ wishlists : "disimpan"

    %% ===== PROMO =====
    coupons ||--o{ coupon_usages : "dipakai"
    users ||--o{ coupon_usages : "memakai"
    orders ||--o{ coupon_usages : "menggunakan"
    coupons |o--o{ orders : "diterapkan"

    %% ===== PESANAN =====
    users ||--o{ orders : "memesan"
    addresses ||--o{ orders : "tujuan kirim"
    orders ||--|{ order_items : "berisi"
    product_variants ||--o{ order_items : "dipesan"
    orders ||--o{ order_status_histories : "riwayat"
    users |o--o{ order_status_histories : "mengubah"

    %% ===== PEMBAYARAN =====
    orders ||--o{ payments : "dibayar"
    payment_methods ||--o{ payments : "metode"
    payments ||--o{ refunds : "dikembalikan"
    orders ||--o{ refunds : "refund"

    %% ===== PENGIRIMAN =====
    couriers ||--o{ shipping_services : "layanan"
    shipping_services ||--o{ shipments : "digunakan"
    orders ||--o| shipments : "dikirim"

    %% ===== ULASAN =====
    products ||--o{ reviews : "diulas"
    users ||--o{ reviews : "menulis"
    order_items ||--o| reviews : "verifikasi"
    reviews ||--o{ review_images : "foto"

    %% ===== LANGGANAN =====
    users ||--o{ subscriptions : "berlangganan"
    product_variants ||--o{ subscriptions : "dilanggan"
    addresses ||--o{ subscriptions : "alamat kirim"
    payment_methods |o--o{ subscriptions : "pembayaran"

    %% ===== KONTEN & SISTEM =====
    users ||--o{ articles : "menulis"
    article_categories |o--o{ articles : "mengelompokkan"
    users |o--o{ contact_messages : "mengirim"
    users ||--o{ notifications : "menerima"
    users |o--o{ activity_logs : "melakukan"

    roles {
        int id PK
        string name UK "admin, staff, customer"
        string description
    }

    users {
        bigint id PK
        int role_id FK
        string name
        string email UK
        string phone
        string password_hash
        string avatar_url
        datetime email_verified_at
        boolean is_active
        datetime last_login_at
        datetime created_at
        datetime updated_at
    }

    addresses {
        bigint id PK
        bigint user_id FK
        string label "Rumah, Kantor"
        string recipient_name
        string phone
        string province
        string city
        string district
        string postal_code
        text full_address
        decimal latitude
        decimal longitude
        boolean is_default
    }

    categories {
        int id PK
        int parent_id FK "nullable"
        string name
        string slug UK
        text description
        string image_url
        boolean is_active
    }

    origins {
        int id PK
        string country
        string region "Gayo, Toraja, Kintamani"
        int altitude_masl
        text description
    }

    brands {
        int id PK
        string name
        string slug UK
        string logo_url
        text description
    }

    products {
        bigint id PK
        int category_id FK
        int brand_id FK
        int origin_id FK
        string name
        string slug UK
        string sku_prefix
        string short_description
        text description
        string coffee_type "arabica, robusta, liberica, blend"
        string roast_level "light, medium, dark"
        string process "washed, natural, honey"
        string flavor_notes
        tinyint acidity "1-5"
        tinyint body "1-5"
        date roast_date
        boolean is_featured
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    product_variants {
        bigint id PK
        bigint product_id FK
        string sku UK
        int weight_gram "100, 250, 500, 1000"
        string grind_type "whole bean, espresso, v60, tubruk"
        decimal price
        decimal compare_at_price
        decimal cost_price
        boolean is_active
    }

    product_images {
        bigint id PK
        bigint product_id FK
        string image_url
        string alt_text
        int sort_order
        boolean is_primary
    }

    tags {
        int id PK
        string name UK
    }

    product_tags {
        bigint product_id PK, FK
        int tag_id PK, FK
    }

    suppliers {
        int id PK
        string name
        string contact_person
        string phone
        string email
        text address
    }

    inventories {
        bigint id PK
        bigint variant_id FK, UK
        int quantity
        int reserved_quantity
        int low_stock_threshold
        datetime updated_at
    }

    stock_movements {
        bigint id PK
        bigint variant_id FK
        int supplier_id FK
        bigint user_id FK
        string type "in, out, adjustment, return"
        int quantity
        string reference_type "order, purchase, manual"
        bigint reference_id
        string note
        datetime created_at
    }

    carts {
        bigint id PK
        bigint user_id FK "null jika guest"
        string session_id
        datetime created_at
        datetime updated_at
    }

    cart_items {
        bigint id PK
        bigint cart_id FK
        bigint variant_id FK
        int quantity
        datetime added_at
    }

    wishlists {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
        datetime created_at
    }

    coupons {
        int id PK
        string code UK
        string description
        string discount_type "percent, fixed, free_shipping"
        decimal discount_value
        decimal min_purchase
        decimal max_discount
        int usage_limit
        int usage_limit_per_user
        datetime starts_at
        datetime expires_at
        boolean is_active
    }

    coupon_usages {
        bigint id PK
        int coupon_id FK
        bigint user_id FK
        bigint order_id FK
        decimal discount_amount
        datetime used_at
    }

    orders {
        bigint id PK
        string order_number UK
        bigint user_id FK
        bigint shipping_address_id FK
        int coupon_id FK
        string status "pending, paid, processing, shipped, delivered, completed, cancelled, refunded"
        decimal subtotal
        decimal discount_amount
        decimal shipping_cost
        decimal tax_amount
        decimal grand_total
        text customer_note
        string cancelled_reason
        datetime placed_at
        datetime updated_at
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint variant_id FK
        string product_name "snapshot"
        string variant_label "snapshot"
        decimal unit_price
        int quantity
        decimal subtotal
    }

    order_status_histories {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        string status
        string note
        datetime created_at
    }

    payment_methods {
        int id PK
        string name "Transfer BCA, QRIS, GoPay, COD"
        string type "bank_transfer, ewallet, qris, cod, card"
        string provider "midtrans, xendit, manual"
        string account_info
        boolean is_active
    }

    payments {
        bigint id PK
        bigint order_id FK
        int payment_method_id FK
        string transaction_id UK
        decimal amount
        string status "pending, paid, failed, expired, refunded"
        string proof_url
        datetime paid_at
        datetime expired_at
        text raw_response
        datetime created_at
    }

    refunds {
        bigint id PK
        bigint payment_id FK
        bigint order_id FK
        decimal amount
        string reason
        string status "requested, approved, rejected, processed"
        datetime processed_at
        datetime created_at
    }

    couriers {
        int id PK
        string code UK "jne, jnt, sicepat, gosend"
        string name
        boolean is_active
    }

    shipping_services {
        int id PK
        int courier_id FK
        string service_code "REG, YES, OKE"
        string service_name
        string estimated_days
    }

    shipments {
        bigint id PK
        bigint order_id FK, UK
        int shipping_service_id FK
        string tracking_number
        int weight_gram
        string status "packing, shipped, in_transit, delivered, returned"
        datetime shipped_at
        datetime delivered_at
    }

    reviews {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        bigint order_item_id FK, UK
        tinyint rating "1-5"
        string title
        text comment
        boolean is_approved
        datetime created_at
    }

    review_images {
        bigint id PK
        bigint review_id FK
        string image_url
    }

    subscriptions {
        bigint id PK
        bigint user_id FK
        bigint variant_id FK
        bigint address_id FK
        int payment_method_id FK
        int quantity
        int interval_days "7, 14, 30"
        string status "active, paused, cancelled"
        date next_delivery_date
        datetime created_at
    }

    article_categories {
        int id PK
        string name
        string slug UK
    }

    articles {
        bigint id PK
        bigint author_id FK
        int article_category_id FK
        string title
        string slug UK
        string excerpt
        text content
        string thumbnail_url
        string status "draft, published"
        datetime published_at
        datetime created_at
    }

    banners {
        int id PK
        string title
        string image_url
        string link_url
        string position "home_hero, home_mid, sidebar"
        int sort_order
        datetime starts_at
        datetime ends_at
        boolean is_active
    }

    newsletter_subscribers {
        int id PK
        string email UK
        boolean is_active
        datetime subscribed_at
    }

    contact_messages {
        bigint id PK
        bigint user_id FK
        string name
        string email
        string subject
        text message
        string status "new, read, replied"
        datetime created_at
    }

    notifications {
        bigint id PK
        bigint user_id FK
        string type "order_status, promo, stock, system"
        string title
        text body
        boolean is_read
        datetime created_at
    }

    settings {
        int id PK
        string key UK
        text value
        string description
    }

    activity_logs {
        bigint id PK
        bigint user_id FK
        string action
        string entity_type
        bigint entity_id
        string ip_address
        string user_agent
        datetime created_at
    }
