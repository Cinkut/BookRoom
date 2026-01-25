-- =====================================================
-- BookRoom - Database Initialization Script
-- PostgreSQL 15
-- 
-- Projekt WDPAI - Zaawansowane obiekty SQL:
-- 1. Schemat zgodny z 3NF
-- 2. Widoki (Views): v_room_details, v_upcoming_bookings
-- 3. Wyzwalacz (Trigger): prevent_overlapping_bookings
-- =====================================================

-- Usunięcie istniejących obiektów (jeśli istnieją)
DROP TRIGGER IF EXISTS trg_prevent_overlapping_bookings ON bookings;
DROP FUNCTION IF EXISTS fn_prevent_overlapping_bookings();
DROP VIEW IF EXISTS v_upcoming_bookings;
DROP VIEW IF EXISTS v_room_details;
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS room_equipment CASCADE;
DROP TABLE IF EXISTS equipment CASCADE;
DROP TABLE IF EXISTS rooms CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- =====================================================
-- 1. SCHEMAT BAZY DANYCH (3NF)
-- =====================================================

-- Tabela: roles (słownik ról użytkowników)
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: users (użytkownicy systemu)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hash bcrypt
    role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_email_format CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$')
);

-- Indeks na email dla szybkiego wyszukiwania przy logowaniu
CREATE INDEX idx_users_email ON users(email);

-- Tabela: rooms (sale konferencyjne)
CREATE TABLE rooms (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    capacity INTEGER NOT NULL CHECK (capacity > 0),
    description TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: equipment (słownik wyposażenia)
CREATE TABLE equipment (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon_name VARCHAR(50), -- Nazwa ikony (np. 'projector', 'wifi')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: room_equipment (relacja M:N - sala <-> wyposażenie)
CREATE TABLE room_equipment (
    room_id INTEGER NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    equipment_id INTEGER NOT NULL REFERENCES equipment(id) ON DELETE CASCADE,
    PRIMARY KEY (room_id, equipment_id)
);

-- Tabela: bookings (rezerwacje sal)
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    room_id INTEGER NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status VARCHAR(20) DEFAULT 'confirmed' CHECK (status IN ('confirmed', 'cancelled', 'pending')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Sprawdzenie poprawności czasu (start_time < end_time)
    CONSTRAINT chk_time_order CHECK (start_time < end_time)
);

-- Indeksy dla optymalizacji zapytań
CREATE INDEX idx_bookings_date ON bookings(date);
CREATE INDEX idx_bookings_room_date ON bookings(room_id, date);
CREATE INDEX idx_bookings_user ON bookings(user_id);

-- =====================================================
-- 2. ZAAWANSOWANE OBIEKTY SQL - WIDOKI (VIEWS)
-- =====================================================

-- WIDOK 1: v_room_details
-- Łączy sale z ich wyposażeniem, wyświetla listę sprzętu jako string
CREATE VIEW v_room_details AS
SELECT 
    r.id,
    r.name,
    r.capacity,
    r.description,
    r.image_path,
    COALESCE(
        STRING_AGG(e.name, ', ' ORDER BY e.name),
        'Brak wyposażenia'
    ) AS equipment_list,
    COUNT(DISTINCT re.equipment_id) AS equipment_count
FROM rooms r
LEFT JOIN room_equipment re ON r.id = re.room_id
LEFT JOIN equipment e ON re.equipment_id = e.id
GROUP BY r.id, r.name, r.capacity, r.description, r.image_path;

-- WIDOK 2: v_upcoming_bookings
-- Pokazuje przyszłe rezerwacje z danymi użytkownika i sali
CREATE VIEW v_upcoming_bookings AS
SELECT 
    b.id AS booking_id,
    b.date,
    b.start_time,
    b.end_time,
    b.status,
    u.id AS user_id,
    u.email AS user_email,
    r.id AS room_id,
    r.name AS room_name,
    r.capacity AS room_capacity,
    b.created_at AS booking_created_at
FROM bookings b
INNER JOIN users u ON b.user_id = u.id
INNER JOIN rooms r ON b.room_id = r.id
WHERE b.date >= CURRENT_DATE
  AND b.status != 'cancelled'
ORDER BY b.date, b.start_time;

-- =====================================================
-- 3. ZAAWANSOWANE OBIEKTY SQL - TRIGGER
-- =====================================================

-- Funkcja: fn_prevent_overlapping_bookings
-- Sprawdza czy sala jest wolna w wybranym czasie
CREATE OR REPLACE FUNCTION fn_prevent_overlapping_bookings()
RETURNS TRIGGER AS $$
BEGIN
    -- Sprawdzenie czy istnieje konflikt z innymi rezerwacjami
    IF EXISTS (
        SELECT 1 
        FROM bookings
        WHERE room_id = NEW.room_id
          AND date = NEW.date
          AND status != 'cancelled'
          AND id != COALESCE(NEW.id, 0) -- Ignoruj aktualną rezerwację przy UPDATE
          AND (
              -- Nowa rezerwacja zaczyna się w trakcie istniejącej
              (NEW.start_time >= start_time AND NEW.start_time < end_time)
              OR
              -- Nowa rezerwacja kończy się w trakcie istniejącej
              (NEW.end_time > start_time AND NEW.end_time <= end_time)
              OR
              -- Nowa rezerwacja obejmuje całą istniejącą
              (NEW.start_time <= start_time AND NEW.end_time >= end_time)
          )
    ) THEN
        RAISE EXCEPTION 'Sala % jest już zarezerwowana w tym czasie (%, %-%)', 
            NEW.room_id, NEW.date, NEW.start_time, NEW.end_time;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger: trg_prevent_overlapping_bookings
-- Uruchamia funkcję przed INSERT lub UPDATE na tabeli bookings
CREATE TRIGGER trg_prevent_overlapping_bookings
BEFORE INSERT OR UPDATE ON bookings
FOR EACH ROW
EXECUTE FUNCTION fn_prevent_overlapping_bookings();

-- =====================================================
-- 4. DANE STARTOWE (SEED DATA)
-- =====================================================

-- Role użytkowników
INSERT INTO roles (name) VALUES 
    ('admin'),
    ('user');

-- Użytkownik administratora
-- Hasło: admin123 (hash bcrypt)
INSERT INTO users (email, password, role_id) VALUES 
    ('admin@bookroom.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Użytkownicy testowi
INSERT INTO users (email, password, role_id) VALUES 
    ('user@bookroom.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
    ('john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- Sale konferencyjne
INSERT INTO rooms (name, capacity, description, image_path) VALUES 
    (
        'Executive Suite',
        12,
        'Elegancka sala konferencyjna idealna dla spotkań zarządu. Wyposażona w nowoczesny sprzęt prezentacyjny i wygodne meble.',
        '/assets/img/executive-suite.jpg'
    ),
    (
        'Innovation Lab',
        8,
        'Kreatywna przestrzeń do burzy mózgów i warsztatów. Elastyczne ustawienie, tablica suchościeralna i projektor multimedialny.',
        '/assets/img/innovation-lab.jpg'
    ),
    (
        'Board Room',
        20,
        'Przestronna sala konferencyjna dla większych grup. Idealna do prezentacji, szkoleń i spotkań całego zespołu.',
        '/assets/img/board-room.jpg'
    );

-- Wyposażenie sal
INSERT INTO equipment (name, icon_name) VALUES 
    ('Projector', 'projector'),
    ('Whiteboard', 'whiteboard'),
    ('WiFi', 'wifi'),
    ('Video Conference', 'video'),
    ('Air Conditioning', 'ac'),
    ('Coffee Machine', 'coffee');

-- Przypisanie wyposażenia do sal (M:N)
-- Executive Suite: Projector, WiFi, Video Conference, Air Conditioning, Coffee Machine
INSERT INTO room_equipment (room_id, equipment_id) VALUES 
    (1, 1), -- Projector
    (1, 3), -- WiFi
    (1, 4), -- Video Conference
    (1, 5), -- Air Conditioning
    (1, 6); -- Coffee Machine

-- Innovation Lab: Whiteboard, WiFi, Projector
INSERT INTO room_equipment (room_id, equipment_id) VALUES 
    (2, 2), -- Whiteboard
    (2, 3), -- WiFi
    (2, 1); -- Projector

-- Board Room: Projector, WiFi, Video Conference, Whiteboard, Air Conditioning
INSERT INTO room_equipment (room_id, equipment_id) VALUES 
    (3, 1), -- Projector
    (3, 3), -- WiFi
    (3, 4), -- Video Conference
    (3, 2), -- Whiteboard
    (3, 5); -- Air Conditioning

-- Przykładowe rezerwacje (dla testów)
INSERT INTO bookings (user_id, room_id, date, start_time, end_time, status) VALUES 
    (2, 1, CURRENT_DATE + INTERVAL '1 day', '09:00', '11:00', 'confirmed'),
    (3, 2, CURRENT_DATE + INTERVAL '2 days', '14:00', '16:00', 'confirmed'),
    (2, 3, CURRENT_DATE + INTERVAL '3 days', '10:00', '12:00', 'confirmed');

-- =====================================================
-- 5. PODSUMOWANIE
-- =====================================================

-- Wyświetlenie statystyk bazy danych
DO $$ 
BEGIN
    RAISE NOTICE '===========================================';
    RAISE NOTICE 'BookRoom Database Initialization Complete!';
    RAISE NOTICE '===========================================';
    RAISE NOTICE 'Tables created: 6';
    RAISE NOTICE 'Views created: 2';
    RAISE NOTICE 'Triggers created: 1';
    RAISE NOTICE '';
    RAISE NOTICE 'Seed data:';
    RAISE NOTICE '- Roles: %', (SELECT COUNT(*) FROM roles);
    RAISE NOTICE '- Users: %', (SELECT COUNT(*) FROM users);
    RAISE NOTICE '- Rooms: %', (SELECT COUNT(*) FROM rooms);
    RAISE NOTICE '- Equipment: %', (SELECT COUNT(*) FROM equipment);
    RAISE NOTICE '- Bookings: %', (SELECT COUNT(*) FROM bookings);
    RAISE NOTICE '';
    RAISE NOTICE 'Admin credentials:';
    RAISE NOTICE '  Email: admin@bookroom.com';
    RAISE NOTICE '  Password: admin123';
    RAISE NOTICE '===========================================';
END $$;
