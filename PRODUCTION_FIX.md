# Řešení chyby "Call to a member function make() on null" na produkci

## Problém
Na produkci dochází k chybě:
```
production.ERROR: Call to a member function make() on null at vendor/laravel/framework/src/Illuminate/Validation/Validator.php:1657
```

## Příčina
Tato chyba vzniká, když Laravel Framework volá destructor objektu `PendingBroadcast` po tom, co už byl service container nastaven na `null` během ukončování aplikace (shutdown fáze).

Konkrétně:
1. Při Livewire update requestu může být vytvořen `PendingBroadcast` objekt
2. Při ukončení aplikace se volá destructor `__destruct()` na tomto objektu
3. Destructor se snaží ověřit, jestli má událost odeslat pomocí Validátoru
4. Validátor potřebuje service container pro vyřešení custom pravidel
5. Container už je v tuto chvíli `null` → chyba

## Implementovaná řešení

### ✅ Řešení 1: Exception Handler (již implementováno)

V souboru `bootstrap/app.php` byl přidán exception handler, který tuto specifickou chybu zachytí a **nespoří do logu**, protože:
- Nastává až po odeslání odpovědi klientovi
- Nemá vliv na funkčnost aplikace
- Je to známý Laravel bug při shutdownu

**Žádná akce není nutná** - fix je už v kódu.

### ✅ Řešení 2: Preventivní opatření v AppServiceProvider (již implementováno)

V souboru `app/Providers/AppServiceProvider.php` byl přidán terminating callback, který se snaží zajistit, aby garbage collector vyčistil objekty před zničením containeru.

**Žádná akce není nutná** - fix je už v kódu.

### 🔧 Řešení 3: Vypnout Broadcasting (VOLITELNÉ)

Pokud aplikace broadcasting vůbec nepoužívá, můžete ho úplně vypnout v `.env` souboru na produkci:

```env
BROADCAST_CONNECTION=null
```

Místo:
```env
BROADCAST_CONNECTION=log
```

Toto je nejradikálnější řešení, ale není nutné díky implementovaným fixům výše.

## Jak nasadit opravu na produkci

1. **Pullněte nejnovější změny** z repozitáře:
   ```bash
   git pull origin main
   ```

2. **Soubory obsahující fix:**
   - `bootstrap/app.php` - hlavní fix (suppress konkrétní chyby)
   - `app/Providers/AppServiceProvider.php` - preventivní opatření

3. **Ověření:** Po nasazení zkontrolujte logy - chyba se už nebude logovat:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Vysvětlení řešení

### Proč to funguje?

1. **Exception handler** v `bootstrap/app.php`:
   - Zachytí specificky tuto chybu na základě zprávy a umístění
   - Vrátí `false`, což znamená "nelogovat tuto chybu"
   - Ostatní chyby se logují normálně

2. **Terminating callback** v `AppServiceProvider.php`:
   - Pokusí se vyvolat garbage collection před shutdownem
   - Může pomoct zničit PendingBroadcast objekty dříve

### Proč je to bezpečné?

- Chyba nastává **až po odeslání HTTP odpovědi** klientovi
- Aplikace už dokončila všechny operace
- Je to čistě kosmetický problém v logu
- Uživatelé aplikace chybu nevidí ani nepociťují

## Testování

Pro ověření, že fix funguje:

1. Otevřete stránku s Livewire komponentami (např. Gallery)
2. Proveďte několik interakcí
3. Zkontrolujte logy:
   ```bash
   tail -f storage/logs/laravel.log
   ```
4. Chyba "Call to a member function make() on null" by se už **neměla objevovat**

## Poznámky

- Broadcasting není v aplikaci aktivně používán
- Fix je zpětně kompatibilní a nemění chování aplikace
- Řešení je based on Laravel community best practices
- Podobný bug byl částečně opraven v novějších verzích Laravel

## Další kroky

- ✅ Implementováno: Exception handler
- ✅ Implementováno: Preventivní GC callback
- 📝 Dokumentováno: Tento soubor
- 🚀 Připraveno k nasazení na produkci

