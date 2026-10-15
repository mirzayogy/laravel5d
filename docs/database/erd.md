## 1. Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has one"
    USERS ||--o{ TEAMS : "owns"
    USERS ||--o{ BOOKINGS : "makes"
    VENUES ||--o{ FIELDS : "has"
    FIELDS ||--o{ BOOKINGS : "booked in"
    FIELDS ||--o{ FACILITY_FIELD : "equipped via"
    FACILITIES ||--o{ FACILITY_FIELD : "provided via"
    TEAMS ||--o{ PLAYERS : "has"
    TEAMS ||--o{ TEAM_TOURNAMENT : "joins via"
    TOURNAMENTS ||--o{ TEAM_TOURNAMENT : "hosts via"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }
    PROFILES {
        bigint id PK
        bigint user_id FK "unique"
        string phone
        string address
        string avatar
    }
    VENUES {
        bigint id PK
        string name
        string address
        string city
    }
    FIELDS {
        bigint id PK
        bigint venue_id FK
        string name
        string surface
        int price_per_hour
        boolean is_active
    }
    FACILITIES {
        bigint id PK
        string name UK
        string icon
    }
    FACILITY_FIELD {
        bigint field_id FK
        bigint facility_id FK
    }
    TEAMS {
        bigint id PK
        bigint user_id FK
        string name
        string logo
    }
    PLAYERS {
        bigint id PK
        bigint team_id FK
        string name
        tinyint jersey_number
        string position
    }
    TOURNAMENTS {
        bigint id PK
        string name
        date start_date
        date end_date
        int registration_fee
    }
    TEAM_TOURNAMENT {
        bigint team_id FK
        bigint tournament_id FK
        timestamp registered_at
        string status
        string group_name
    }
    BOOKINGS {
        bigint id PK
        bigint user_id FK
        bigint field_id FK
        date booking_date
        time start_time
        time end_time
        int total_price
        string status
    }
```
    2. Relationship SummaryTypeRelationshipEloquentOne-to-OneUser and ProfilehasOne / belongsToOne-to-ManyVenue to FieldhasMany / belongsToOne-to-ManyUser to TeamhasMany / belongsToOne-to-ManyTeam to PlayerhasMany / belongsToOne-to-ManyUser to BookinghasMany / belongsToOne-to-ManyField to BookinghasMany / belongsToMany-to-ManyField and Facility (pivot facility_field)belongsToManyMany-to-Many + pivot dataTeam and Tournament (pivot team_tournament, columns registered_at, status, group_name)belongsToMany + withPivotHas-Many-ThroughVenue to Booking through FieldhasManyThroughHas-Many-ThroughUser to Player through TeamhasManyThrough
3. Design Notes
Unique constraints: profiles.user_id, facilities.name, and (team_tournament.team_id, team_tournament.tournament_id) so a team joins a tournament only once.

Cascade rules: deleting a user cascades to their profile, teams, and bookings. Deleting a venue is restricted while fields still exist.