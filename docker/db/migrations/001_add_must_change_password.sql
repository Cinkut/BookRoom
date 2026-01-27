-- =====================================================
-- Migration: Add must_change_password column
-- Data: 2026-01-27
-- Opis: Dodaje flagę wymuszającą zmianę hasła przy pierwszym logowaniu
-- =====================================================

-- Dodaj kolumnę must_change_password do tabeli users
ALTER TABLE users 
ADD COLUMN must_change_password BOOLEAN DEFAULT FALSE;

-- Dodaj komentarz do kolumny
COMMENT ON COLUMN users.must_change_password IS 'Flaga wymuszająca zmianę hasła przy następnym logowaniu. Używana gdy admin tworzy konto.';

-- Ustaw flagę TRUE dla wszystkich istniejących użytkowników testowych (oprócz admina)
UPDATE users 
SET must_change_password = TRUE 
WHERE role_id = 2; -- tylko dla zwykłych użytkowników

-- Wyświetl podsumowanie
DO $$ 
BEGIN
    RAISE NOTICE '=========================================';
    RAISE NOTICE 'Migration: must_change_password - Complete!';
    RAISE NOTICE '=========================================';
    RAISE NOTICE 'Users requiring password change: %', (SELECT COUNT(*) FROM users WHERE must_change_password = TRUE);
END $$;
