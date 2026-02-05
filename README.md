# Zadanie rekrutacyjne – aplikacja full-stack

Projekt stanowi aplikację webową stworzoną w ramach zadania rekrutacyjnego.  
Składa się z backendu opartego o Symfony oraz frontendu napisanego w Vue.js.  
Aplikacja realizuje proces wieloetapowego formularza rejestracyjnego z walidacją danych i komunikacją przez REST API.

---

## Funkcjonalności

### Główne cechy

- Wieloetapowy formularz – intuicyjny wizard rejestracji użytkownika
- Walidacja danych – kompleksowa walidacja po stronie frontendu i backendu
- RESTful API – czytelne endpointy do komunikacji frontend–backend
- Architektura MVC – przejrzysta i łatwa do utrzymania struktura kodu

---

## Struktura danych

Aplikacja obsługuje następujące grupy danych:

- Dane podstawowe:
  - imię
  - nazwisko
  - data urodzenia
- Dane kontaktowe:
  - adres e-mail
  - numer telefonu
- Doświadczenie zawodowe:
  - historia zatrudnienia

---

## Technologie

### Backend

- Symfony 7.3 – framework PHP
- Doctrine ORM 3.5 – mapowanie obiektowo-relacyjne
- PHP 8.2+ – nowoczesny PHP z typami
- SQLite – baza danych
- Nelmio CORS – obsługa polityki CORS

### Frontend

- Vue.js 3 – framework JavaScript
- Vite – narzędzie do budowania aplikacji
- Axios – komunikacja HTTP z API
- CSS3 – nowoczesne style wraz z animacjami


### Endpoint: przesłanie danych użytkownika

**URL:** `/api/user/submit`  
**Metoda:** `POST`  
**Opis:** Endpoint przyjmuje komplet danych z wieloetapowego formularza rejestracyjnego.

#### Przykładowe żądanie (Request Body)

```json
{
  "basic": {
    "firstName": "Jan",
    "lastName": "Kowalski",
    "birthDate": "1990-01-01"
  },
  "contact": {
    "phone": "+48123456789",
    "email": "jan.kowalski@example.com"
  },
  "experience": [
    {
      "company": "Przykładowa Firma",
      "position": "Developer",
      "dateFrom": "2020-01-01",
      "dateTo": "2023-12-31"
    }
  ]
}
```
