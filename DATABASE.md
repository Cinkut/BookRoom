# BookRoom - Dokumentacja Bazy Danych

## Informacje Ogólne

- **System:** PostgreSQL 15
- **Nazwa bazy:** bookroom
- **User:** dbuser
- **Normalizacja:** 3NF (Third Normal Form)

## Schemat Bazy Danych

### 1. Tabele

#### `roles` - Role użytkowników
| Kolumna    | Typ          | Opis                          |
|------------|--------------|-------------------------------|
| id         | SERIAL (PK)  | Identyfikator roli            |
| name       | VARCHAR(50)  | Nazwa roli (admin, user)      |
| created_at | TIMESTAMP    | Data utworzenia               |

**Dane:** admin, user

---

#### `users` - Użytkownicy systemu
| Kolumna    | Typ           | Opis                                    |
|------------|---------------|-----------------------------------------|
| id         | SERIAL (PK)   | Identyfikator użytkownika               |
| email      | VARCHAR(255)  | Email (UNIQUE, walidacja regex)         |
| password   | VARCHAR(255)  | Hash hasła (bcrypt)                     |
| role_id    | INTEGER (FK)  | FK do roles.id                          |
| created_at | TIMESTAMP     | Data rejestracji                        |

**Indeks:** idx_users_email (optymalizacja logowania)

**Constraint:** 
- Walidacja formatu email: `email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$'`

---

#### `rooms` - Sale konferencyjne
| Kolumna     | Typ           | Opis                                |
|-------------|---------------|-------------------------------------|
| id          | SERIAL (PK)   | Identyfikator sali                  |
| name        | VARCHAR(100)  | Nazwa sali (UNIQUE)                 |
| capacity    | INTEGER       | Pojemność (CHECK > 0)               |
| description | TEXT          | Opis sali                           |
| image_path  | VARCHAR(255)  | Ścieżka do zdjęcia                  |
| created_at  | TIMESTAMP     | Data dodania                        |

**Dane przykładowe:**
- Executive Suite (12 osób)
- Innovation Lab (8 osób)
- Board Room (20 osób)

---

#### `equipment` - Wyposażenie sal
| Kolumna    | Typ           | Opis                                |
|------------|---------------|-------------------------------------|
| id         | SERIAL (PK)   | Identyfikator wyposażenia           |
| name       | VARCHAR(100)  | Nazwa sprzętu (UNIQUE)              |
| icon_name  | VARCHAR(50)   | Nazwa ikony (np. 'projector')       |
| created_at | TIMESTAMP     | Data dodania                        |

**Dane przykładowe:** 
- Projector, Whiteboard, WiFi, Video Conference, Air Conditioning, Coffee Machine

---

#### `room_equipment` - Relacja M:N (sala ↔ wyposażenie)
| Kolumna      | Typ           | Opis                           |
|--------------|---------------|--------------------------------|
| room_id      | INTEGER (FK)  | FK do rooms.id                 |
| equipment_id | INTEGER (FK)  | FK do equipment.id             |

**Primary Key:** (room_id, equipment_id)

**CASCADE:** ON DELETE CASCADE - usunięcie sali usuwa połączenia

---

#### `bookings` - Rezerwacje sal
| Kolumna    | Typ           | Opis                                       |
|------------|---------------|--------------------------------------------|
| id         | SERIAL (PK)   | Identyfikator rezerwacji                   |
| user_id    | INTEGER (FK)  | FK do users.id                             |
| room_id    | INTEGER (FK)  | FK do rooms.id                             |
| date       | DATE          | Data rezerwacji                            |
| start_time | TIME          | Godzina rozpoczęcia                        |
| end_time   | TIME          | Godzina zakończenia                        |
| status     | VARCHAR(20)   | Status (confirmed, cancelled, pending)     |
| created_at | TIMESTAMP     | Data utworzenia rezerwacji                 |

**Indeksy:**
- idx_bookings_date
- idx_bookings_room_date (optymalizacja sprawdzania dostępności)
- idx_bookings_user

**Constraints:**
- `CHECK (start_time < end_time)` - walidacja poprawności czasu
- `CHECK (status IN ('confirmed', 'cancelled', 'pending'))`

---

### 2. Widoki (Views) - Zaawansowane Obiekty SQL

#### `v_room_details` - Szczegóły sal z wyposażeniem
Łączy dane sal z ich wyposażeniem przy użyciu `STRING_AGG()`.

**Kolumny:**
- id, name, capacity, description, image_path
- equipment_list (string) - lista wyposażenia oddzielona przecinkami
- equipment_count (integer) - liczba elementów wyposażenia

**SQL:**
```sql
SELECT r.id, r.name, r.capacity, r.description, r.image_path,
       COALESCE(STRING_AGG(e.name, ', ' ORDER BY e.name), 'Brak wyposażenia') AS equipment_list,
       COUNT(DISTINCT re.equipment_id) AS equipment_count
FROM rooms r
LEFT JOIN room_equipment re ON r.id = re.room_id
LEFT JOIN equipment e ON re.equipment_id = e.id
GROUP BY r.id, r.name, r.capacity, r.description, r.image_path;
```

**Wymóg WDPAI:** ✅ Zaawansowany obiekt SQL #1

---

#### `v_upcoming_bookings` - Nadchodzące rezerwacje
Pokazuje przyszłe rezerwacje z pełnymi danymi użytkownika i sali.

**Kolumny:**
- booking_id, date, start_time, end_time, status
- user_id, user_email
- room_id, room_name, room_capacity
- booking_created_at

**Filtrowanie:**
- `date >= CURRENT_DATE` - tylko przyszłe rezerwacje
- `status != 'cancelled'` - pomijanie anulowanych

**Wymóg WDPAI:** ✅ Zaawansowany obiekt SQL #2

---

### 3. Trigger - Zapobieganie nakładającym się rezerwacjom

#### Funkcja: `fn_prevent_overlapping_bookings()`
Sprawdza czy sala jest dostępna w wybranym czasie.

**Logika:**
1. Sprawdza czy istnieje rezerwacja dla tej samej sali i daty
2. Ignoruje rezerwacje anulowane (`status != 'cancelled'`)
3. Wykrywa nakładanie się przedziałów czasowych:
   - Nowa rezerwacja zaczyna się w trakcie istniejącej
   - Nowa rezerwacja kończy się w trakcie istniejącej
   - Nowa rezerwacja obejmuje całą istniejącą

**Reakcja na konflikt:**
```sql
RAISE EXCEPTION 'Sala % jest już zarezerwowana w tym czasie (%, %-%)'
```

#### Trigger: `trg_prevent_overlapping_bookings`
- **Typ:** BEFORE INSERT OR UPDATE
- **Tabela:** bookings
- **Funkcja:** fn_prevent_overlapping_bookings()

**Wymóg WDPAI:** ✅ Zaawansowany obiekt SQL #3 (Trigger)

---

## Dane Testowe (Seed Data)

### Użytkownicy
| Email                  | Hasło    | Rola  |
|------------------------|----------|-------|
| admin@bookroom.com     | admin123 | admin |
| user@bookroom.com      | admin123 | user  |
| john.doe@example.com   | admin123 | user  |

### Sale
1. **Executive Suite** - 12 osób  
   Wyposażenie: Projector, WiFi, Video Conference, Air Conditioning, Coffee Machine

2. **Innovation Lab** - 8 osób  
   Wyposażenie: Whiteboard, WiFi, Projector

3. **Board Room** - 20 osób  
   Wyposażenie: Projector, WiFi, Video Conference, Whiteboard, Air Conditioning

### Przykładowe rezerwacje
- 26.01.2026, 09:00-11:00 - Executive Suite (user@bookroom.com)
- 27.01.2026, 14:00-16:00 - Innovation Lab (john.doe@example.com)
- 28.01.2026, 10:00-12:00 - Board Room (user@bookroom.com)

---

## Weryfikacja Struktury

### Sprawdzenie widoków:
```sql
SELECT * FROM v_room_details;
SELECT * FROM v_upcoming_bookings;
```

### Test triggera (powinien zwrócić błąd):
```sql
INSERT INTO bookings (user_id, room_id, date, start_time, end_time) 
VALUES (3, 1, '2026-01-26', '09:30', '10:30');
-- ERROR: Sala 1 jest już zarezerwowana w tym czasie
```

### Połączenie z bazą:
```bash
docker exec -it bookroom_db psql -U dbuser -d bookroom
```

---

## Zgodność z Wymaganiami

### RULES.md ✅
- [x] Schemat zgodny z 3NF
- [x] Hasła jako hash bcrypt
- [x] Relacja M:N (room_equipment)
- [x] Walidacja danych (CHECK constraints)

### WDPAI - Zaawansowane Obiekty SQL ✅
- [x] **Widok 1:** v_room_details z STRING_AGG()
- [x] **Widok 2:** v_upcoming_bookings z filtrami
- [x] **Trigger:** prevent_overlapping_bookings z logiką biznesową

### Security Bingo ✅
- [x] Hasła jako hash (nie plain text)
- [x] Walidacja formatu email
- [x] Indeksy dla optymalizacji zapytań
- [x] ON DELETE CASCADE/RESTRICT dla integralności danych
