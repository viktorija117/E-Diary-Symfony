# Sistem za upravljanje studentima i profesorima

Ovaj projekat omogućava upravljanje studentima, profesorima i njihovim ocenama. Direktor ima pristup za pregled, ažuriranje i preuzimanje podataka o profesorima i studentima, dok profesori i studenti imaju ograničen pristup zavisno od njihovih privilegija.

## Stranice i pristup

### 1. **Generisanje baze podataka**
   - Posetite [http://127.0.0.1:8000/baza](http://127.0.0.1:8000/baza) da biste generisali osnovnu bazu podataka sa studentima, profesorima i predmetima.
   
### 2. **Logovanje kao direktor**
   - Idite na [http://127.0.0.1:8000/logovanje](http://127.0.0.1:8000/logovanje).
   - Korisničko ime: `direktorPera`
   - Lozinka: `direktorPera`
   - Kao direktor, možete:
     - Pregledati, ažurirati i preuzeti podatke o profesorima i studentima.
     - Upisati ili ispisati studenta.
     - Zapostaviti ili otpustiti profesora (ako postoji zamena).

### 3. **Rad sa predmetima**
   - Predmeti imaju ID od 1 do 10. Poznato je koji predmet ima koji ID.
   - Direktor ima mogućnost upravljanja predmetima i njihovim povezivanjem sa studentima i profesorima.

### 4. **Logovanje kao profesor**
   - Nakon što se prijavite kao direktor, možete se odjaviti i ponovo prijaviti kao profesor.
   - Kao profesor, možete:
     - Pregledati studente koji su upisani na predmete koje predajete.
     - Pregledati ocene studenata.
     - Menjati ocene studentima.

### 5. **Logovanje kao student**
   - Nakon što se prijavite kao direktor, možete se odjaviti i ponovo prijaviti kao student.
   - Kao student, možete:
     - Pregledati sve svoje ocene iz predmeta.


## Autor

- Viktorija
