# Technical Questions Answer

## 1. Timezone Conflicts

- **Store all appointment datetimes in UTC**  
  The system should save all datetime values in UTC to maintain consistency across all users and regions.  
  Example:
  ```php
  // Convert user local time to UTC before saving
  $startUtc = Carbon::parse($request->start, $user->preferred_timezone)->setTimezone('UTC');
  ```
- **Maintain each user’s preferred timezone**  
  Store `preferred_timezone` during registration. When retrieving appointments, convert UTC → user timezone (already handled in the `index` method).

- **Participant timezone handling**  
  - Always display times in each viewer’s local timezone so everyone sees correct local time.  
  - When checking participant availability or business-hour constraints, convert all local windows to UTC and compute overlaps in UTC.  
  - Optionally store the creator’s original timezone or per-event timezone metadata for context.

- **Conflict detection / common time finding**  
  Collect participants’ working hours in their timezones, convert them to UTC, compute intersections, and suggest suitable meeting slots.

## 2. Database Optimization (Fetching User-Specific Appointments)

- **Eager load relations** to avoid N+1 queries.  
  *(Current implementation already uses `with('users')`)*

- **Add indexes** for better query performance:
  - `appointments(start)` → used for `ORDER BY`.
  - `appointments(creator_id)` → improves creator lookups.
  - Pivot table: `appointment_user(appointment_id)` and `appointment_user(user_id)` for join efficiency.
  - Composite index on `(user_id, start)` if queries often filter by both.

- **Use `whereHas('users', ...)`** for filtering user-related appointments, but prefer **SQL joins** or query builder when counting or aggregating large datasets.

## 3. Additional Features (Product Roadmap Ideas)

- **Email/Push Notifications:**  
  Automatically remind users of upcoming meetings or schedule changes.

- **Calendar Integration:**  
  Sync appointments with Google Calendar or Microsoft Outlook to provide a seamless scheduling experience.

- **Recurring Appointments:**  
  Allow users to create weekly or monthly repeating meetings to reduce manual scheduling.

- **Meeting Notes and Attachments:**  
  Let participants add meeting summaries, notes, or share files directly within the appointment.

- **Role-Based Access Control (RBAC):**  
  Define different permissions for admins, creators, and participants to ensure proper access management and data integrity.

**Reason:**  
These features would enhance usability, improve automation, and strengthen collaboration among users, making the system more practical and production-ready.

## 4. Session Management (Secure + Lightweight)

- **Use Laravel Sanctum opaque tokens** (current code uses `Sanctum::createToken`) or short-lived tokens:
  - Keep token payloads minimal — do **not** embed user data directly in the token.
  - Store session state server-side (opaque tokens) instead of using large JWTs.
  - Set short expirations (e.g., 60 minutes via `SESSION_LIFETIME=60`) and optionally implement refresh tokens.

- **For SPA (Single Page Applications):**
  - Prefer cookie-based stateful authentication with `HttpOnly`, `Secure`, and `SameSite` flags.
  - Enable CSRF protection for browser-based flows.
  - For third-party clients, issue **scoped bearer tokens** with limited abilities.

- **Token hygiene:**
  - Store tokens in the `personal_access_tokens` table (Sanctum default) for visibility and revocation.
  - Allow token revocation on logout or by admin action.
  - Rotate tokens during critical operations and limit token scopes to minimize exposure.

- **Transport & rate limiting:**
  - Always use **HTTPS** to protect tokens in transit.
  - Apply **rate limiting** on authentication endpoints and sensitive actions.

- **Monitoring & auditing:**
  - Log and monitor failed login attempts, token misuse, and other suspicious authentication activity.

This approach ensures secure, lightweight session handling suitable for both SPAs and API clients.