-- =====================================================
-- Migration: Add user_profiles table (1:1 relationship)
-- Data: 2026-01-29
-- Opis: Dodaje tabelę user_profiles połączoną relacją 1:1 z tabelą users
-- =====================================================

-- 1. Utworzenie tabeli user_profiles
-- Relacja 1:1 jest osiągnięta przez:
-- a) user_id jest kluczem głównym (PRIMARY KEY) - każdy profil musi być unikalny
-- b) user_id jest kluczem obcym (FOREIGN KEY) do users(id) - profil musi należeć do użytkownika
CREATE TABLE user_profiles (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone_number VARCHAR(20),
    avatar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Trigger do aktualizacji pola updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_user_profiles_updated_at
    BEFORE UPDATE ON user_profiles
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- 3. Utworzenie profili dla istniejących użytkowników
-- Wstawiamy puste profile dla każdego istniejącego użytkownika, który jeszcze go nie ma
INSERT INTO user_profiles (user_id, first_name, last_name)
SELECT id, 'User', 'Name' -- Domyślne wartości
FROM users
WHERE id NOT IN (SELECT user_id FROM user_profiles);

-- 4. Aktualizacja przykładowych danych (dla admina)
UPDATE user_profiles
SET first_name = 'Admin', last_name = 'Systemowy', phone_number = '123-456-789'
WHERE user_id = (SELECT id FROM users WHERE email = 'admin@bookroom.com');

-- 5. Podsumowanie
DO $$ 
BEGIN
    RAISE NOTICE '===========================================';
    RAISE NOTICE 'Migration: user_profiles (1:1) - Complete!';
    RAISE NOTICE '===========================================';
    RAISE NOTICE 'Profiles created: %', (SELECT COUNT(*) FROM user_profiles);
END $$;
