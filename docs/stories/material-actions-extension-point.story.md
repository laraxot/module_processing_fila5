---
type: story
tags: [processing, estensione, roosko, audit-funzionale]
slug: material-actions-extension-point
title: "Azioni native sul materiale spegnibili dall'installazione"
status: review
phase: REVIEW
owner: Processing
depends_on: []
related: []
created: 2026-11-05
updated: 2026-11-05
---

# Azioni native sul materiale spegnibili dall'installazione

## Story

Come **installazione che usa Processing dentro Roosko**
voglio **poter nascondere i bottoni "Aggiungi input" e "Registra output" del pannello Processing**
così che **gli operatori usino solo le azioni di Roosko, che scrivono lotto, QR e registro di carico e scarico**.

## Perché / evidenze

Audit funzionale (2026-11-05), sezione 1.4:

- `InputsRelationManager` → `AddProcessingInputHeaderAction` → `AddProcessingInputAction`: consuma l'unità
  **senza** scarico di registro (`internal_treatment`) e senza controllo di autorizzazione. Il registro va
  fuori giacenza.
- `OutputsRelationManager` → `RegisterProcessingOutputHeaderAction`: crea unità senza `WasteLot`, QR e registro;
  l'unità poi non si può stoccare, caricare né spedire da Roosko. La select delle posizioni mostrava le
  posizioni di tutti i magazzini.

Processing da solo (senza Roosko) deve continuare a funzionare con i suoi bottoni.

## Soluzione

- Config `processing.material_actions_enabled` (default `true`), documentata in `config/config.php` e nel README.
- `ProcessingOrderAction::materialActionsEnabled()`; i due bottoni sono visibili solo se attivo e la callback
  rifiuta l'esecuzione se spento (`ensureMaterialActionsEnabled()`, messaggio tradotto).
- Le Actions di dominio restano disponibili: Roosko le compone nelle proprie azioni con registro.
- *Registra output*: la select posizioni mostra solo le posizioni attive del magazzino scelto.

## Acceptance criteria

1. Senza configurazione i bottoni compaiono come prima (default `true`).
2. Con `processing.material_actions_enabled = false` i bottoni sono nascosti e una chiamata diretta non esegue nulla.
3. Il punto di estensione è documentato nel README (sezione *Punti di estensione*) con l'esempio per Roosko.

## Test

`tests/Feature/ProcessingOrderActionAuthorizationTest.php` (gruppo *punto di estensione*).

## Dev agent record

- Agente: Claude (sessione audit Roosko, compito Processing "azioni native").
- File: `config/config.php`, `app/Filament/Actions/ProcessingOrderAction.php`,
  `app/Filament/Actions/AddProcessingInputHeaderAction.php`, `app/Filament/Actions/RegisterProcessingOutputHeaderAction.php`,
  `lang/{it,en}/processing_order.php`, `docs/README.md`.
- Verifica: `php -l`, controllo dei riferimenti; test non eseguiti (ambiente senza vendor).
- Handoff Roosko: nel `boot()` del ServiceProvider `config(['processing.material_actions_enabled' => false]);`
  e un'azione Roosko "Aggiungi input" che chiami `AddProcessingInputAction` + scarico di registro.
